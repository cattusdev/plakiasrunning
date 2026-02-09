<?php

class Availability
{
    private Database $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Get Rules for specific day/therapist/package
     */
    public function getRulesForDay(int $therapistId, int $weekday, array $allowedRuleTypes, int $targetPackageId = null): array
    {
        $params = [':tid' => $therapistId, ':wd' => $weekday];

        $inType = '';
        if (!empty($allowedRuleTypes)) {
            $placeholders = [];
            foreach ($allowedRuleTypes as $i => $t) {
                $ph = ':t' . $i;
                $placeholders[] = $ph;
                $params[$ph] = $t;
            }
            $inType = " AND (appointment_type IS NULL OR appointment_type IN (" . implode(',', $placeholders) . "))";
        }

        // Φέρνουμε και το Max Attendants από το πακέτο του κανόνα
        $sql = "SELECT r.start_time, r.end_time, r.appointment_type, r.package_id, 
                       p.max_attendants, p.title as package_title
                FROM therapist_availability_rules r
                LEFT JOIN packages p ON r.package_id = p.id
                WHERE r.therapist_id = :tid
                  AND r.weekday = :wd
                  AND r.is_active = 1
                  {$inType}
                ORDER BY r.start_time ASC";

        $rows = $this->db->query($sql, $params);
        if (!$rows) return [];

        $out = [];
        foreach ($rows as $r) {
            // Φιλτράρισμα Πακέτου (Hard Filtering)
            // Αν ζητάμε συγκεκριμένο πακέτο ($targetPackageId), κρατάμε μόνο Rules που:
            // 1. Έχουν το ίδιο package_id
            // 2. Ή έχουν package_id = NULL (General Availability)
            if ($targetPackageId && $r->package_id !== null && $r->package_id != $targetPackageId) {
                continue;
            }

            $out[] = [
                'start' => $r->start_time,
                'end'   => $r->end_time,
                'package_id' => $r->package_id,
                // Αν ο κανόνας δεν έχει πακέτο, υποθέτουμε Personal (1 άτομο) ή παίρνουμε default
                'max_attendants' => $r->max_attendants ?: 1
            ];
        }
        return $out;
    }

    /**
     * Φέρνει ΟΛΕΣ τις κρατήσεις της ημέρας με τον αριθμό ατόμων
     */
    public function getDayBookings(int $therapistId, string $dayStart, string $dayEnd): array
    {
        // ΔΙΟΡΘΩΣΗ: Προσθέσαμε το 'b.' μπροστά από τα πεδία για να μην υπάρχει ασάφεια
        $sql = "SELECT b.start_datetime, b.end_datetime, b.attendees_count, b.package_id, p.buffer_minutes
                FROM bookings b
                LEFT JOIN packages p ON b.package_id = p.id
                WHERE b.therapist_id = :tid
                  AND b.status != 'canceled'
                  AND b.end_datetime > :ds
                  AND b.start_datetime < :de";

        $rows = $this->db->query($sql, [':tid' => $therapistId, ':ds' => $dayStart, ':de' => $dayEnd]);

        $out = [];
        if ($rows) {
            foreach ($rows as $r) {
                // Υπολογισμός Buffer
                $buffer = isset($r->buffer_minutes) ? (int)$r->buffer_minutes : 0;
                $endTs = strtotime($r->end_datetime) + ($buffer * 60);

                $out[] = [
                    'start' => strtotime($r->start_datetime),
                    'end'   => $endTs,
                    'pax'   => (int)$r->attendees_count
                ];
            }
        }
        return $out;
    }

    /**
     * Επιστρέφει therapist IDs που μπορούν να αναλάβουν το package (και είναι active)
     */
    public function getTherapistsForPackage(int $packageId): array
    {
        $sql = "SELECT t.id
                FROM package_therapists pt
                JOIN therapists t ON t.id = pt.therapist_id
                WHERE pt.package_id = :pid
                  AND t.is_active = 1";
        $rows = $this->db->query($sql, [':pid' => $packageId]);

        if (!$rows) return [];

        // Επιστρέφουμε ένα array μόνο με τα IDs (π.χ. [1, 5, 8])
        return array_map(fn($r) => (int)$r->id, $rows);
    }

    /**
     * CORE FUNCTION: Compute Availability with Capacity
     */
    public function computeAvailableStartTimesForTherapist(
        int $therapistId,
        string $dateStr,
        int $durationMinutes,
        array $allowedRuleTypes,
        int $stepMinutes = 30,
        int $bufferMinutes = 0,
        int $packageId = null
    ): array {

        $weekday = (int)date('w', strtotime($dateStr));
        $rules = $this->getRulesForDay($therapistId, $weekday, $allowedRuleTypes, $packageId);
        if (empty($rules)) return [];

        // Φέρνουμε τα Bookings και τα Blocks
        $dayStart = $dateStr . ' 00:00:00';
        $dayEnd   = $dateStr . ' 23:59:59';

        $bookings = $this->getDayBookings($therapistId, $dayStart, $dayEnd);
        $blocks = $this->getTimeBlocks($therapistId, $dayStart, $dayEnd); // Από προηγούμενο κώδικα (απλά blocks)

        $results = [];

        // Για κάθε κανόνα (availability window)
        foreach ($rules as $rule) {
            $wStart = strtotime($dateStr . ' ' . $rule['start']);
            $wEnd   = strtotime($dateStr . ' ' . $rule['end']);

            // Το Capacity ορίζεται από τον Κανόνα (αν είναι συνδεδεμένος με πακέτο)
            // Αλλιώς, αν ο πελάτης ψάχνει συγκεκριμένο πακέτο, παίρνουμε το capacity εκείνου.
            // Αν είναι general slot και δεν ξέρουμε πακέτο, default 1.
            $slotCapacity = 1;
            if ($rule['max_attendants'] > 1) {
                $slotCapacity = $rule['max_attendants'];
            } elseif ($packageId) {
                // Fetch package capacity manually if needed, or assume passed somehow.
                // Για απλότητα, αν ο κανόνας είναι General, συνήθως λειτουργεί ως Exclusive (1 άτομο).
                // Εκτός αν θέλουμε να επιτρέψουμε Group σε General slot (πιο σπάνιο).
                $slotCapacity = 1;
            }

            // Υπολογισμός πιθανών slots
            $lastStart = $wEnd - ($durationMinutes * 60);

            // Loop ανά step
            for ($t = $wStart; $t <= $lastStart; $t += ($stepMinutes * 60)) {
                $candStart = $t;
                $candEnd   = $t + ($durationMinutes * 60) + ($bufferMinutes * 60);

                // 1. Check Hard Blocks (Άδειες, Αργίες) - Αυτά κλείνουν το slot τελείως
                if ($this->overlapsBlock($candStart, $candEnd, $blocks)) {
                    continue;
                }

                // 2. Check Bookings Capacity
                $currentPax = 0;
                foreach ($bookings as $b) {
                    // Overlap logic
                    if ($candStart < $b['end'] && $candEnd > $b['start']) {
                        $currentPax += $b['pax'];
                    }
                }

                // 3. Decision
                if ($currentPax < $slotCapacity) {
                    $results[] = [
                        'start_datetime' => date('Y-m-d H:i:s', $candStart),
                        'end_datetime'   => date('Y-m-d H:i:s', $candEnd),
                        'available_spots' => $slotCapacity - $currentPax
                    ];
                }
            }
        }

        return $results;
    }


    /**
     * ΒΕΛΤΙΣΤΟΠΟΙΗΜΕΝΗ ΑΝΑΖΗΤΗΣΗ:
     * Αντί να ελέγχουμε κάθε μέρα, ελέγχουμε πρώτα αν ο Guide δουλεύει εκείνη τη μέρα (Rules).
     * Έτσι γλιτώνουμε το 80% των βαριών υπολογισμών.
     */
    public function findFirstAvailableDate(int $therapistId, int $packageId, int $duration, int $lookAheadDays = 90): ?string
    {
        // 1. Φέρνουμε ΟΛΑ τα Rules του θεραπευτή ΜΙΑ φορά (Caching logic)
        // Θέλουμε να ξέρουμε ποιες μέρες της εβδομάδας (0-6) δουλεύει γενικά.
        $sql = "SELECT DISTINCT weekday, package_id 
                FROM therapist_availability_rules 
                WHERE therapist_id = :tid AND is_active = 1";
        $rules = $this->db->query($sql, [':tid' => $therapistId]);

        if (empty($rules)) return null; // Δεν δουλεύει καθόλου

        // Φτιάχνουμε έναν χάρτη: [0 => true, 1 => true, ...] για τις μέρες που δουλεύει
        $workingWeekdays = [];
        foreach ($rules as $r) {
            // Αν ο κανόνας είναι για συγκεκριμένο πακέτο, πρέπει να ταιριάζει με το ζητούμενο
            // Ή να είναι NULL (General Availability)
            if ($r->package_id === null || $r->package_id == $packageId) {
                $workingWeekdays[(int)$r->weekday] = true;
            }
        }

        // Αν δεν βρέθηκαν μέρες που να εξυπηρετούν αυτό το πακέτο
        if (empty($workingWeekdays)) return null;

        // 2. "Ελαφρύ" Loop σε PHP (χωρίς DB calls στις κενές μέρες)
        $date = new DateTime();

        // Αν είναι αργά σήμερα (π.χ. βράδυ), ίσως πρέπει να ξεκινήσουμε από αύριο;
        // Για την ώρα το αφήνουμε από σήμερα.

        for ($i = 0; $i < $lookAheadDays; $i++) {
            $wd = (int)$date->format('w'); // 0 (Sun) - 6 (Sat)

            // ΕΛΕΓΧΟΣ 1 (Στιγμιαίος): Δουλεύει αυτή τη μέρα;
            if (!isset($workingWeekdays[$wd])) {
                $date->modify('+1 day');
                continue; // Skip χωρίς να ρωτήσουμε τη βάση!
            }

            // ΕΛΕΓΧΟΣ 2 (Βαρύς): Υπάρχει συγκεκριμένο κενό ώρας;
            // Μπαίνουμε εδώ ΜΟΝΟ αν είναι εργάσιμη μέρα.
            $dateStr = $date->format('Y-m-d');

            // Καλούμε την compute... αλλά μπορούμε να την κάνουμε πιο ελαφριά
            // ζητώντας να σταματήσει στο πρώτο που θα βρει (αν μπορούσαμε),
            // αλλά έστω κι έτσι, την καλούμε πολύ λιγότερες φορές.
            $slots = $this->computeAvailableStartTimesForTherapist(
                $therapistId,
                $dateStr,
                $duration,
                ['mixed', 'online', 'inPerson'],
                $duration,
                0,
                $packageId
            );

            if (!empty($slots)) {
                return $dateStr; // Βρέθηκε!
            }

            $date->modify('+1 day');
        }

        return null;
    }
    
    // Helpers
    private function overlapsBlock($s, $e, $blocks)
    {
        foreach ($blocks as $b) {
            $bs = strtotime($b['start']);
            $be = strtotime($b['end']);
            if ($b['kind'] == 'block' && $s < $be && $e > $bs) return true;
        }
        return false;
    }

    // (Existing getTimeBlocks method...)
    public function getTimeBlocks(int $therapistId, string $rangeStart, string $rangeEnd): array
    {
        $sql = "SELECT start_datetime, end_datetime, kind, notes
                FROM therapist_time_blocks
                WHERE therapist_id = :tid
                  AND start_datetime < :re
                  AND end_datetime > :rs
                ORDER BY start_datetime ASC";
        $rows = $this->db->query($sql, [':tid' => $therapistId, ':rs' => $rangeStart, ':re' => $rangeEnd]);
        $out = [];
        if ($rows) {
            foreach ($rows as $r) {
                $out[] = ['start' => $r->start_datetime, 'end' => $r->end_datetime, 'kind' => $r->kind];
            }
        }
        return $out;
    }

    /**
     * Helper: allowed rule types based on package.type
     * Καθορίζει τι είδους rules (online/inPerson) επιτρέπονται βάσει του τύπου του πακέτου.
     */
    public static function allowedRuleTypesForPackageType(string $packageType): array
    {
        $packageType = trim($packageType);
        
        // Αν το πακέτο είναι Online, ψάχνουμε κανόνες Online ή Mixed
        if ($packageType === 'online')   return ['online', 'mixed'];
        
        // Αν το πακέτο είναι InPerson (τρέξιμο), ψάχνουμε InPerson ή Mixed
        if ($packageType === 'inPerson') return ['inPerson', 'mixed'];
        
        // Αν είναι Mixed ή κάτι άλλο, επιστρέφουμε τα πάντα
        return ['online', 'inPerson', 'mixed'];
    }
} 

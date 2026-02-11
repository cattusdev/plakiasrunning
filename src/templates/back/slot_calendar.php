<link href="/assets/vendor/plugins/fcallendar/lib/main.css" rel="stylesheet" />

<div class="container-fluid my-5">
    <div class="row">
        <div class="col-12 mb-3">
            <div class="d-flex flex-wrap justify-content-between align-items-start gap-3">
                <div>
                    <h2 class="mb-1"><i class="bi bi-calendar-range me-2"></i>Πρόγραμμα & Διαθεσιμότητα</h2>
                    <p class="text-muted small mb-0">Διαχείριση προγραμμάτων Guides και προβολή κρατήσεων.</p>
                </div>

                <div class="d-flex flex-wrap align-items-end gap-2">
                    <div style="min-width: 280px;">
                        <label class="form-label small text-muted mb-1">Επιλογή Guide (Φίλτρο)</label>
                        <select id="therapistSelect" class="form-select form-select-sm">
                            <option value="" selected>-- Φόρτωση... --</option>
                        </select>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-outline-primary btn-sm" id="openWeeklyRulesBtn">
                            <i class="bi bi-gear-wide-connected me-1"></i> Ρύθμιση Προγράμματος
                        </button>
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-sm btn-outline-danger" id="toggleBlockModeBtn" data-active="0">
                                <i class="bi bi-slash-circle me-1"></i> <span id="blockModeText">Block Mode: OFF</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-body p-0 p-md-2">
                    <div id="calendar"></div>
                </div>
            </div>
        </div>

        <div class="col-12">
            <input type="hidden" name="csrf_token" id="csrf_token_input"
                value="<?php echo htmlspecialchars(Token::genToken('csrf_token')); ?>">
        </div>
    </div>
</div>

<div class="modal fade" id="weeklyRulesModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-light">
                <h5 class="modal-title"><i class="bi bi-clock-history me-2"></i>Διαχείριση Προγράμματος Guide</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body p-0">
                <ul class="nav nav-tabs nav-fill" id="rulesModalTabs" role="tablist">
                    <li class="nav-item">
                        <button class="nav-link active" id="tab-rules-link" data-bs-toggle="tab" data-bs-target="#tab-rules" type="button">
                            <i class="bi bi-calendar-week me-1"></i> Εβδομαδιαίο Ωράριο
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link" id="tab-policies-link" data-bs-toggle="tab" data-bs-target="#tab-policies" type="button">
                            <i class="bi bi-sliders me-1"></i> Ρυθμίσεις / Πολιτικές
                        </button>
                    </li>
                </ul>

                <div class="tab-content p-3">

                    <div class="tab-pane fade show active" id="tab-rules">
                        <div class="alert alert-light border small mb-3">
                            <i class="bi bi-info-circle-fill text-primary me-1"></i>
                            Ορίστε πότε εργάζεται ο Guide.
                            Αν επιλέξετε <strong>Route</strong>, η ώρα δεσμεύεται αποκλειστικά για αυτό (π.χ. City Run).
                            Αν το αφήσετε κενό (General), είναι διαθέσιμος για όλα.
                        </div>

                        <div class="d-flex justify-content-between align-items-center mb-2 px-1">
                            <div class="small text-muted">
                                Guide: <strong id="weeklyRulesTherapistLabel" class="text-dark fs-6">—</strong>
                            </div>
                            <button class="btn btn-sm btn-primary" id="addRuleRowBtn" type="button">
                                <i class="bi bi-plus-lg me-1"></i> Προσθήκη Slot
                            </button>
                        </div>

                        <div class="table-responsive border rounded" style="max-height: 400px; overflow-y: auto;">
                            <table class="table table-sm align-middle table-hover mb-0" id="rulesTable">
                                <thead class="table-light sticky-top">
                                    <tr class="small text-muted text-uppercase">
                                        <th style="width: 20%;">Ημέρα</th>
                                        <th style="width: 15%;">Έναρξη</th>
                                        <th style="width: 15%;">Λήξη</th>
                                        <th style="width: 35%;">Ανάθεση (Route/Event)</th>
                                        <th style="width: 15%;"></th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white"></tbody>
                            </table>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="tab-policies">
                        <div class="row g-4 p-2">
                            <div class="col-md-6">
                                <div class="card h-100 border-light bg-light">
                                    <div class="card-body">
                                        <h6 class="fw-bold text-dark mb-3"><i class="bi bi-calendar-range me-2"></i>Παράθυρο Κρατήσεων</h6>
                                        <label class="form-label small text-muted">Πόσες μέρες μπροστά ανοίγει το πρόγραμμα;</label>
                                        <div class="input-group">
                                            <input type="number" class="form-control" id="policyWindow" min="1" placeholder="60">
                                            <span class="input-group-text">ημέρες</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="card h-100 border-light bg-light">
                                    <div class="card-body">
                                        <h6 class="fw-bold text-dark mb-3"><i class="bi bi-hourglass-split me-2"></i>Minimum Notice</h6>
                                        <label class="form-label small text-muted">Ελάχιστη προειδοποίηση πριν την κράτηση;</label>
                                        <div class="input-group">
                                            <input type="number" class="form-control" id="policyNotice" min="0" placeholder="12">
                                            <span class="input-group-text">ώρες</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <div class="modal-footer bg-light">
                <button class="btn btn-outline-secondary" data-bs-dismiss="modal">Άκυρο</button>
                <button class="btn btn-primary px-4" id="saveWeeklyRulesBtn" type="button">
                    <i class="bi bi-save-fill me-2"></i> Αποθήκευση Προγράμματος
                </button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="blockModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-danger text-white py-2">
                <h6 class="modal-title"><i class="bi bi-slash-circle me-2"></i>Block Time</h6>
                <button type="button" class="btn-close m-0" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="small text-muted mb-2">Guide: <strong id="blockTherapistLabel" class="text-dark">—</strong></div>
                <div class="mb-2">
                    <label class="form-label small fw-bold">Από - Έως</label>
                    <div class="d-flex gap-2">
                        <input type="text" class="form-control form-control-sm" id="blockStart" readonly>
                        <input type="text" class="form-control form-control-sm" id="blockEnd" readonly>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold">Αιτιολογία (Optional)</label>
                    <textarea class="form-control form-control-sm" id="blockNotes" rows="2" placeholder="π.χ. Ρεπό, Ασθένεια..."></textarea>
                </div>
                <div class="d-grid">
                    <button class="btn btn-danger btn-sm" id="saveBlockBtn" type="button">Επιβεβαίωση Block</button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="sessionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">

            <div class="modal-header text-white" id="sessionModalHeader" style="background-color: #6610f2;">
                <div>
                    <h5 class="modal-title fw-bold" id="sessionModalTitle">...</h5>
                    <div class="small opacity-75" id="sessionModalTime">...</div>
                </div>
                <button type="button" class="btn-close m-0" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body p-0">
                <div class="bg-light p-3 border-bottom d-flex justify-content-between align-items-center">
                    <div>
                        <span class="text-muted small text-uppercase fw-bold">Χωρητικότητα</span>
                        <div class="d-flex align-items-center gap-2">
                            <h3 class="mb-0 fw-bold" id="sessionCapacityCount">0/0</h3>
                            <span class="badge bg-success" id="sessionStatusBadge">Open</span>
                        </div>
                    </div>
                    <div class="text-end">
                        <button class="btn btn-primary shadow-sm" id="btnSessionAddBooking">
                            <i class="bi bi-person-plus-fill me-1"></i> Προσθήκη Κράτησης
                        </button>
                    </div>
                </div>

                <div class="p-0" style="max-height: 50vh; overflow-y: auto;">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light sticky-top">
                            <tr class="small text-muted text-uppercase">
                                <th class="ps-4">Συμμετέχων</th>
                                <th>Επικοινωνία</th>
                                <th>Κατάσταση</th>
                                <th class="text-end pe-4">Ενέργειες</th>
                            </tr>
                        </thead>
                        <tbody id="sessionAttendeesList">
                        </tbody>
                    </table>
                </div>

                <div id="sessionEmptyState" class="text-center py-5 d-none">
                    <div class="text-muted mb-2"><i class="bi bi-people fs-1 opacity-25"></i></div>
                    <p class="text-muted">Δεν υπάρχουν κρατήσεις ακόμα.</p>
                </div>
            </div>

            <div class="modal-footer bg-light justify-content-between">
                <button type="button" class="btn btn-outline-secondary btn-sm" id="btnEditSessionDetails">
                    <i class="bi bi-gear me-1"></i> Επεξεργασία Session
                </button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Κλείσιμο</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="bookingModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="bookingModalTitle">
                    Διαχείριση Κράτησης
                    <span id="modalStatusBadge" class="badge bg-light text-dark ms-2 fs-6"></span>
                </h5>
                <button type="button" class="btn-close m-0" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body p-0">
                <ul class="nav nav-tabs nav-fill bg-light" id="bookingTabs" role="tablist">
                    <li class="nav-item">
                        <button class="nav-link active" id="tab-details" data-bs-toggle="tab" data-bs-target="#pane-details" type="button"><i class="bi bi-calendar-check me-2"></i>Στοιχεία</button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link" id="tab-payments" data-bs-toggle="tab" data-bs-target="#pane-payments" type="button" disabled><i class="bi bi-currency-euro me-2"></i>Πληρωμές</button>
                    </li>
                </ul>

                <div class="tab-content p-3">

                    <div class="tab-pane fade show active" id="pane-details">
                        <form id="bookingForm">
                            <input type="hidden" id="bookingId">

                            <div class="row g-3 mb-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Πελάτης</label>
                                    <div class="input-group">
                                        <select class="form-select" id="client_id" style="width:85%" required></select>
                                        <button class="btn btn-outline-primary" type="button" id="btnQuickAddClient"><i class="bi bi-person-plus-fill"></i></button>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Θεραπευτής (Guide)</label>
                                    <select class="form-select" id="therapist_id" required></select>
                                </div>
                            </div>

                            <div class="row g-3 mb-3">
                                <div class="col-md-9">
                                    <label class="form-label fw-bold">Πακέτο / Διαδρομή</label>
                                    <select class="form-select" id="package_id" disabled></select>
                                    <input type="hidden" id="package_duration">
                                    <input type="hidden" id="booking_price">
                                    <input type="hidden" id="appointment_type" value="inPerson">
                                </div>

                                <div class="col-md-3">
                                    <label class="form-label fw-bold">Άτομα (Pax)</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-white"><i class="bi bi-people-fill"></i></span>
                                        <input type="number" class="form-control text-center fw-bold" id="attendees_count" value="1" min="1" required>
                                    </div>
                                </div>
                            </div>

                            <div id="group_info_panel" class="alert alert-info d-none">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="alert-heading fw-bold mb-1"><i class="bi bi-people-fill me-2"></i>Ομαδικό Event</h6>
                                        <small id="group_time_display">...</small>
                                    </div>
                                    <div class="text-end">
                                        <span class="badge bg-light text-dark border fs-6" id="group_capacity_badge">0 / 0</span>
                                        <div class="mt-1">
                                            <button type="button" class="btn btn-xs btn-link text-decoration-none p-0" id="btnViewAttendees">
                                                Προβολή Λίστας
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div id="slot_picker_panel" class="mb-4">
                                <label class="form-label fw-bold">Επιλογή Ημερομηνίας</label>
                                <input type="date" class="form-control mb-3" id="slot_date_picker">

                                <div id="slots_loader" class="text-center d-none text-muted my-3">
                                    <div class="spinner-border spinner-border-sm"></div> Ψάχνω κενά...
                                </div>

                                <label class="form-label small text-muted">Διαθέσιμες Ώρες:</label>
                                <div id="slots_container" class="d-flex flex-wrap gap-2 p-2 border rounded bg-light" style="min-height: 60px;">
                                    <span class="text-muted small align-self-center mx-auto">Επιλέξτε Θεραπευτή & Πακέτο</span>
                                </div>
                            </div>

                            <div class="row g-2 mb-3 bg-white border p-2 rounded align-items-end">
                                <div class="col-5">
                                    <label class="small text-muted">Έναρξη</label>
                                    <input type="datetime-local" class="form-control form-control-sm" id="start_datetime" readonly>
                                </div>
                                <div class="col-2 text-center pb-1">
                                    <i class="bi bi-arrow-right text-muted"></i>
                                </div>
                                <div class="col-5">
                                    <label class="small text-muted">Λήξη</label>
                                    <input type="datetime-local" class="form-control form-control-sm" id="end_datetime">
                                </div>
                            </div>

                            <div class="mb-3">
                                <div class="row g-2">
                                    <div class="col-md-8">
                                        <label class="form-label">Σημειώσεις</label>
                                        <textarea class="form-control" id="booking_notes" rows="1"></textarea>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Status Κράτησης</label>
                                        <select class="form-select" id="booking_status">
                                            <option value="booked">Booked (Ενεργή)</option>
                                            <option value="canceled">Canceled (Ακυρώθηκε)</option>
                                            <option value="completed">Completed (Ολοκληρώθηκε)</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-end pt-2 border-top">
                                <button type="submit" class="btn btn-success" id="saveBookingBtn">Αποθήκευση</button>
                            </div>
                        </form>
                    </div>

                    <div class="tab-pane fade" id="pane-payments">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <h6 class="fw-bold mb-0">Ιστορικό Πληρωμών</h6>
                                <span id="tabStatusText" class="small fw-bold text-muted">Status: -</span>
                            </div>
                            <button class="btn btn-sm btn-outline-primary" type="button" id="btnAddManualPayment">
                                <i class="bi bi-plus-lg"></i> Προσθήκη
                            </button>
                        </div>

                        <div class="table-responsive border rounded mb-3" style="max-height: 200px;">
                            <table class="table table-sm table-hover mb-0" id="paymentsTable">
                                <thead class="table-light">
                                    <tr>
                                        <th>Ημ/νία</th>
                                        <th>Ποσό</th>
                                        <th>Τρόπος</th>
                                        <th>Σημείωση</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody id="paymentsList">
                                </tbody>
                            </table>
                        </div>

                        <div class="d-flex justify-content-end gap-4 fw-bold">
                            <div class="text-secondary">Σύνολο: <span id="pay_total" class="text-dark">€0.00</span></div>
                        </div>

                        <div id="addPaymentForm" class="d-none mt-3 p-3 bg-light border rounded shadow-sm">
                            <input type="hidden" id="pay_id">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h6 class="small fw-bold text-primary mb-0" id="pay_form_title">Νέα Καταχώρηση</h6>
                                <button type="button" class="btn-close btn-close small" aria-label="Close" onclick="resetPaymentForm()"></button>
                            </div>

                            <div class="row g-2">
                                <div class="col-md-4">
                                    <label class="form-label small text-muted mb-0">Ποσό (€)</label>
                                    <input type="number" class="form-control form-control-sm fw-bold" id="pay_amount" placeholder="0.00" step="0.01">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small text-muted mb-0">Τρόπος</label>
                                    <select class="form-select form-select-sm" id="pay_method">
                                        <option value="Cash">Μετρητά</option>
                                        <option value="POS">POS / Κάρτα</option>
                                        <option value="Bank">Τράπεζα</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small text-muted mb-0">Ημερομηνία</label>
                                    <input type="date" class="form-control form-control-sm" id="pay_date">
                                </div>

                                <div class="col-12">
                                    <input type="text" class="form-control form-control-sm" id="pay_note" placeholder="Σημείωση (π.χ. Αριθμός συναλλαγής)">
                                </div>

                                <div class="col-12">
                                    <div class="p-2 border rounded bg-white">
                                        <label class="small fw-bold text-dark mb-1 d-block">
                                            <i class="bi bi-arrow-repeat me-1"></i>Ενημέρωση Booking Status σε:
                                        </label>
                                        <select class="form-select form-select-sm border-warning" id="payment_status">
                                            <option value="" class="text-muted">-- Χωρίς Αλλαγή Status --</option>
                                            <option value="paid" class="text-success fw-bold">Εξοφλήθηκε (Paid)</option>
                                            <option value="partially_paid" class="text-warning fw-bold">Μερική Εξόφληση (Partial)</option>
                                            <option value="unpaid" class="text-danger fw-bold">Απλήρωτο (Unpaid)</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-12 text-end mt-2 pt-2 border-top">
                                    <button class="btn btn-sm btn-light border me-1" type="button" onclick="resetPaymentForm()">Άκυρο</button>
                                    <button class="btn btn-sm btn-success px-4" type="button" id="btnSavePayment">
                                        <i class="bi bi-check-lg me-1"></i>Αποθήκευση
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="quickClientModal" tabindex="-1" aria-hidden="true" style="z-index: 1060;">
    <div class="modal-dialog modal-md modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-light py-2">
                <h6 class="modal-title fw-bold"><i class="bi bi-person-plus-fill me-2"></i>Γρήγορη Προσθήκη Πελάτη</h6>
                <button type="button" class="btn-close" onclick="$('#quickClientModal').modal('hide')"></button>
            </div>
            <div class="modal-body">
                <form id="quickClientForm">
                    <div class="row g-2 mb-2">
                        <div class="col-6">
                            <label class="form-label small fw-bold text-muted">Όνομα <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="qc_fname" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-bold text-muted">Επώνυμο <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="qc_lname" required>
                        </div>
                    </div>

                    <div class="row g-2 mb-2">
                        <div class="col-6">
                            <label class="form-label small fw-bold text-muted">Τηλέφωνο <span class="text-danger">*</span></label>
                            <input type="tel" class="form-control" id="qc_phone" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-bold text-muted">Email</label>
                            <input type="email" class="form-control" id="qc_email">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Σημείωση</label>
                        <textarea class="form-control" id="qc_note" rows="2" placeholder="π.χ. Σύσταση από..."></textarea>
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary" id="btnSaveQuickClient">Δημιουργία & Επιλογή</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="attendeesModal" tabindex="-1" aria-hidden="true" style="z-index: 1070;">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title fw-bold"><i class="bi bi-people-fill me-2"></i>Λίστα Συμμετεχόντων</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Ονοματεπώνυμο</th>
                                <th>Τηλέφωνο</th>
                                <th>Κατάσταση</th>
                            </tr>
                        </thead>
                        <tbody id="attendeesList">
                        </tbody>
                    </table>
                </div>
                <div id="attendeesLoader" class="text-center py-3 d-none">
                    <div class="spinner-border text-warning" role="status"></div>
                </div>
                <div id="noAttendeesMsg" class="text-center py-3 text-muted d-none">
                    Κανένας συμμετέχων ακόμα.
                </div>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Κλείσιμο</button>
            </div>
        </div>
    </div>
</div>
<style>
    /* New Calendar Styles for Outdoor Theme */
    .fc-event {
        border: none;
        border-radius: 4px;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
        padding: 2px 4px;
    }

    /* 1. Availability Slots (Routes) */
    .fc-bg-event.bg-availability {
        background: repeating-linear-gradient(45deg,
                rgba(25, 135, 84, 0.08),
                rgba(25, 135, 84, 0.08) 10px,
                rgba(25, 135, 84, 0.15) 10px,
                rgba(25, 135, 84, 0.15) 20px) !important;
        opacity: 1 !important;
        border-left: 3px solid #198754 !important;
    }

    .fc-bg-event .fc-event-title {
        color: #157347;
        font-weight: bold;
        font-size: 0.85em;
        padding: 4px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    /* 2. Group Events */
    .fc-event.bg-group-event {
        background-color: #6610f2 !important;
        /* Indigo */
        color: white !important;
        border-left: 4px solid #4806b3 !important;
    }

    /* 3. Bookings */
    .fc-event.bg-booked {
        background-color: #0d6efd !important;
        /* Blue */
        color: white !important;
        border-left: 4px solid #084298 !important;
    }

    /* 4. Blocks */
    .fc-event.bg-block {
        background-color: #dc3545 !important;
        /* Red */
        opacity: 0.8;
        background-image: url('data:image/svg+xml;utf8,<svg width="10" height="10" viewBox="0 0 10 10" xmlns="http://www.w3.org/2000/svg"><line x1="0" y1="10" x2="10" y2="0" stroke="white" stroke-width="1" opacity="0.3"/></svg>');
    }

    /* Badges inside events */
    .event-capacity-badge {
        background: rgba(0, 0, 0, 0.2);
        border-radius: 4px;
        padding: 1px 4px;
        font-size: 0.75em;
        margin-left: 4px;
        font-weight: 600;
    }
</style>


<style>
    .fc table {
        font-size: 1.05rem;
    }

    .fc .fc-button {
        /* border-radius: 999px; */
        padding: 6px 12px;
    }

    .fc .fc-button:focus {
        outline: 0;
        box-shadow: none !important;
    }

    /* Events */
    .fc-event {
        /* border-radius: 999px; */
        padding: 2px 8px;
        border: 1px solid #c5cece;
    }

    .fc-event.bg-group-event {
        background-color: #6610f2 !important;
        border-color: #520dc2 !important;
        color: #fff !important;
    }

    .fc-event.bg-block {
        background-color: #e95965 !important;
        border-color: #f1aeb5 !important;
        color: #000 !important;
    }

    .fc-event.bg-booked {
        background-color: #4f9c79 !important;
        border-color: #4f9c79 !important;
        color: #000 !important;
    }

    .fc-event.bg-availability {
        background-color: rgba(25, 135, 84, 0.45) !important;
        /* πιο έντονο */
        border: none !important;
    }

    .fc .fc-resource-area {
        border-right: 1px solid #e5e7eb;
    }

    .fc .fc-resource-area .fc-datagrid-cell-main {
        padding: 8px 10px;
        font-weight: 600;
    }

    /* πιο καθαρό resource header (ονόματα πάνω) */
    .fc .fc-col-header-cell-cushion {
        font-weight: 800;
        letter-spacing: .2px;
    }

    /* γραμμές/διαχωριστικά για να ξεχωρίζουν οι στήλες */
    .fc .fc-timegrid-col {
        border-left: 1px solid #e5e7eb;
    }

    .fc .fc-timegrid-col:first-child {
        border-left: none;
    }

    /* resource area (αριστερά) πιο καθαρό */
    .fc .fc-resource-area {
        border-right: 1px solid #e5e7eb;
    }

    .fc-license-message {
        display: none !important;
    }

    /* πιο έντονο header στα resources */
    .fc .fc-col-header-cell-cushion {
        font-weight: 800;
    }

    /* διαχωριστικά στις στήλες */
    .fc .fc-timegrid-col {
        border-left: 1px solid #e5e7eb;
    }

    .fc .fc-timegrid-col:first-child {
        border-left: none;
    }

    /* availability bg πιο εμφανές */
    .fc-event.bg-availability {
        background-color: rgba(25, 135, 84, 0.35) !important;
        border: none !important;
    }

    /* Month day status coloring */
    .fc .fc-daygrid-day.day-available {
        background: rgba(25, 135, 84, 0.14);
    }

    .fc .fc-daygrid-day.day-partial {
        background: rgba(255, 193, 7, 0.14);
    }

    .fc .fc-daygrid-day.day-full {
        background: rgba(220, 53, 69, 0.14);
    }

    .fc .fc-daygrid-day.day-none {
        background: rgba(108, 117, 125, 0.06);
    }

    /* Avatar stack TOP-LEFT inside month cell */
    .day-avatars {
        position: absolute;
        left: 6px;
        top: 6px;
        display: flex;
        align-items: center;
        pointer-events: none;
    }

    .avatar-chip {
        width: 20px;
        height: 20px;
        border-radius: 999px;
        border: 2px solid #fff;
        background: #e9ecef;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 10px;
        font-weight: 800;
        margin-left: -6px;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.12);
        color: #111;
    }

    .avatar-chip:first-child {
        margin-left: 0;
    }

    .avatar-chip.has-photo {
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
        color: transparent;
    }

    .avatar-chip.more {
        background: #0d6efd;
        color: #fff;
    }

    th.fc-col-header-cell.fc-resource {
        background-color: #dddddd;
        border: 1px solid #303e50;
    }

    .fc-v-event .fc-event-title {
        font-size: 11px;
    }
</style>
<style>
    /* --- MODERN CALENDAR STYLES --- */

    /* 1. Γενικό Στυλ Event (Flat Card Look) */
    .fc-event {
        border: none !important;
        border-radius: 6px !important;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        /* Ελαφριά σκιά */
        transition: transform 0.1s, box-shadow 0.1s;
        overflow: hidden;
        margin-bottom: 2px !important;
    }

    .fc-event:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        z-index: 50;
    }

    /* 2. BLOCKED EVENTS (Κόκκινο Ριγέ - Ξεχωρίζει αμέσως) */
    .fc-event.bg-block {
        background-color: #fff5f5 !important;
        color: #d63384 !important;
        border-left: 4px solid #dc3545 !important;
        /* Μοτίβο με ρίγες */
        background-image: repeating-linear-gradient(45deg,
                rgba(220, 53, 69, 0.03),
                rgba(220, 53, 69, 0.03) 10px,
                rgba(220, 53, 69, 0.08) 10px,
                rgba(220, 53, 69, 0.08) 20px);
    }

    /* 3. SESSIONS / GROUP EVENTS (Λευκή Κάρτα με αριστερή μπάρα) */
    .fc-event.bg-session,
    .fc-event.bg-group-event {
        background-color: #fbfffc !important;
        border: 1px solid #e9ecef !important;
        border-left: 4px solid #6610f2 !important;
        /* Indigo */
        color: #212529 !important;
    }

    .fc-v-event .fc-event-main {
        color: unset;
        height: 100%;
    }

    /* 4. BOOKINGS (Ατομικά Ραντεβού - Μπλε) */
    .fc-event.bg-booked {
        background-color: #f0f7ff !important;
        border-left: 4px solid #0d6efd !important;
        color: #084298 !important;
    }

    /* 5. AVAILABILITY BACKGROUNDS (Πολύ απαλό πράσινο) */
    .fc-bg-event.bg-availability {
        background: rgba(25, 135, 84, 0.06) !important;
        opacity: 1;
    }

    /* --- FULLCALENDAR UI TWEAKS --- */

    /* Μεγαλύτερος τίτλος μήνα */
    .fc-toolbar-title {
        font-size: 1.6rem !important;
        font-weight: 700;
        color: #343a40;
        letter-spacing: -0.5px;
    }

    /* Κουμπιά πιο μοντέρνα (Bootstrap style) */
    .fc-button-primary {
        background-color: #ffffff !important;
        border-color: #dee2e6 !important;
        color: #495057 !important;
        font-weight: 600;
        text-transform: capitalize;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
    }

    .fc-button-primary:hover,
    .fc-button-primary.fc-button-active {
        background-color: #f8f9fa !important;
        color: #0d6efd !important;
        border-color: #0d6efd !important;
        box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.05);
    }

    /* Στο Month View, να φαίνονται λίγο πιο μεγάλα τα κελιά */
    .fc-daygrid-day-frame {
        min-height: 100px;
    }
</style>

<?php
function hook_end_scripts()
{
?>
    <!-- FullCalendar Styles & Scripts -->
    <!-- <link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js"></script> -->
    <!-- Date Range Picker -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css">
    <script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>

    <script src='/assets/vendor/plugins/fcallendar/lib/main.js'></script>

    <!-- <script src="js/genFunctions.js"></script> -->

    <script src="/assets/js/back/slot_calendar.js?300"></script>
<?php
}
?>
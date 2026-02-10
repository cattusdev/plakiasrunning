/* ============================================================
   GLOBAL VARIABLES & CACHE
   ============================================================ */
let calendar;
const bookingModalEl = document.getElementById('bookingModal');
// Χρησιμοποιούμε getOrCreateInstance για ασφάλεια
const bookingModal = new bootstrap.Modal(bookingModalEl);

// Cache & State
let therapistsCache = [];
let currentViewType = 'resourceTimeGridWeek';
let monthCellMap = {};
let fetchAbortController = null;

let isBlockMode = false;
let availablePackagesList = [];
/* ============================================================
   GLOBAL HELPER FUNCTIONS (Must be outside DOMContentLoaded)
   ============================================================ */

function getCsrfToken() {
    return document.querySelector('meta[name="csrf_token"]')?.getAttribute('content') || '';
}

// Wrapper για AJAX calls
async function apiPostGlobal(formData) {
    const csrf = getCsrfToken();
    if (csrf) formData.append('csrf_token', csrf);
    const res = await fetch('includes/admin/ajax.php', { method: 'POST', body: formData });
    return await res.json();
}

function escapeHtml(text) {
    if (!text) return "";
    return text.replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;");
}

function ymdLocal(dateObj) {
    const y = dateObj.getFullYear();
    const m = String(dateObj.getMonth() + 1).padStart(2, '0');
    const d = String(dateObj.getDate()).padStart(2, '0');
    return `${y}-${m}-${d}`;
}

function todayYmdLocal() {
    return ymdLocal(new Date());
}

function isPastYmd(dateStr) {
    // safe compare because YYYY-MM-DD
    return dateStr && dateStr < todayYmdLocal();
}


// Μετατροπή JS Date σε string για input type="datetime-local" (YYYY-MM-DDTHH:mm)
function formatDateToInput(date) {
    if (!date) return ''; // <--- ΝΕΟΣ ΕΛΕΓΧΟΣ: Αν είναι null, επιστρέφει κενό αντί να σκάσει
    const pad = n => n < 10 ? '0' + n : n;
    return date.getFullYear() + '-' +
        pad(date.getMonth() + 1) + '-' +
        pad(date.getDate()) + 'T' +
        pad(date.getHours()) + ':' +
        pad(date.getMinutes());
}

// --- 1. RESET FORM (Reset Panels correctly) ---
function resetForm() {
    document.getElementById('bookingForm').reset();
    $('#bookingId').val('');
    $('#client_id').val(null).trigger('change');

    // Reset UI Panels
    $('#group_info_panel').addClass('d-none');
    $('#slot_picker_panel').removeClass('d-none');
    $('#slots_container').html('<span class="text-muted small mx-auto">Επιλέξτε Πακέτο</span>');

    // Reset Inputs state
    $('#end_datetime').prop('readonly', false).removeClass('bg-light');
    $('#package_id').html('<option value="">(Επιλέξτε Θεραπευτή)</option>').prop('disabled', true);
    $('#saveBookingBtn').prop('disabled', false).text('Καταχώρηση');

    // Reset Payments Tab
    $('#tab-payments').prop('disabled', true);
    $('#paymentsList').empty();
    $('#pay_total').text('€0.00');

    // Switch back to first tab
    const firstTabBtn = document.querySelector('#tab-details');
    if (firstTabBtn) {
        const tab = new bootstrap.Tab(firstTabBtn);
        tab.show();
    }
}

function resetPaymentForm() {
    $('#addPaymentForm').addClass('d-none');
    $('#pay_id').val('');
    $('#pay_amount').val('');
    $('#pay_note').val('');
    $('#pay_booking_status').val('');
    $('#pay_form_title').text('Νέα Καταχώρηση');
    $('#btnSavePayment').html('<i class="bi bi-check-lg me-1"></i>Αποθήκευση');
}

// --- 2. OPEN GROUP MODAL (Global) ---
async function openGroupModal(packageId) {
    const fd = new FormData();
    fd.append('action', 'fetchPackage');
    fd.append('id', packageId);

    // Reset UI before showing
    document.getElementById('groupTitle').value = 'Loading...';
    document.getElementById('groupPkgId').value = '';

    // Show Modal
    const groupModal = new bootstrap.Modal(document.getElementById('groupEventModal'));
    groupModal.show();

    const res = await apiPostGlobal(fd);
    if (res.success && res.data) {
        const pkg = res.data;
        document.getElementById('groupPkgId').value = pkg.id;
        document.getElementById('groupTitle').value = pkg.title;
        document.getElementById('groupPrice').value = pkg.price;

        // Date formatting
        const d = new Date(pkg.start_datetime.replace(' ', 'T'));
        document.getElementById('groupStart').value = d.toLocaleString('el-GR');

        // --- ΥΠΟΛΟΓΙΣΜΟΣ ΣΥΜΜΕΤΕΧΟΝΤΩΝ ---
        const manual = parseInt(pkg.manual_bookings || 0, 10); // Τηλεφωνικά / Offline
        const dbCount = parseInt(pkg.db_bookings_count || 0, 10); // Από τη βάση (Active Bookings)
        const max = parseInt(pkg.max_attendants || 0, 10);

        const total = manual + dbCount; // Σύνολο

        // 1. Στο Input βάζουμε ΜΟΝΟ τα manual (αυτά επεξεργάζεται ο χρήστης εδώ)
        document.getElementById('groupManualBookings').value = manual;

        // 2. Στο Badge βάζουμε το ΣΥΝΟΛΟ
        const badge = document.getElementById('groupCapacityBadge');
        badge.textContent = `${total} / ${max}`;

        // Χρωματισμός Badge
        if (max > 0 && total >= max) {
            badge.className = 'badge bg-danger'; // Full
        } else {
            badge.className = 'badge bg-success'; // Available
        }
    }
}

// --- 3. OPEN BOOKING EDIT MODAL (Global) ---
function openBookingEditModal(id) {
    resetForm();
    const fd = new FormData();
    fd.append('action', 'getBookingDetails');
    fd.append('id', id);

    apiPostGlobal(fd).then(res => {
        if (res.success) {
            const d = res.data;
            const modal = bootstrap.Modal.getOrCreateInstance(bookingModalEl);

            $('#bookingId').val(d.id);
            $('#bookingModalTitle').text('Επεξεργασία Κράτησης');

            // Set Client
            const clientOption = new Option(d.client_text, d.client_id, true, true);
            $('#client_id').append(clientOption).trigger('change');

            $('#appointment_type').val(d.appointment_type || 'online');

            // Fields
            $('#booking_status').val(d.status);
            $('#booking_notes').val(d.notes);
            $('#booking_price').val(d.price);
            $('#payment_status').val(d.payment_status);
            if (typeof updateStatusUI === 'function') updateStatusUI(d.payment_status);

            // Set Therapist
            $('#therapist_id').val(d.therapist_id);

            // Payments Tab
            $('#tab-payments').prop('disabled', false);
            if (typeof loadBookingPayments === 'function') loadBookingPayments(d.id);

            // Load Packages & Dates
            loadPackagesForTherapist(d.therapist_id, d.package_id || "", {
                start: d.start_iso,
                end: d.end_iso
            });

            modal.show();
        } else {
            alert('Error loading booking data');
        }
    });
}

// --- 4. SMART FLOW LOGIC FUNCTIONS ---

function loadPackagesForTherapist(tid, preSelectPkgId = null, savedDates = null) {
    const pkgSelect = $('#package_id');
    $('#slots_container').html('<span class="text-muted small mx-auto">Επιλέξτε Πακέτο</span>');
    pkgSelect.html('<option value="">Φόρτωση...</option>').prop('disabled', true);

    if (!tid) {
        pkgSelect.html('<option value="">(Επιλέξτε πρώτα Θεραπευτή)</option>');
        return;
    }

    $.post('includes/admin/ajax.php', {
        action: 'getTherapistPackages',
        therapist_id: tid,
        csrf_token: getCsrfToken()
    }, function (res) {
        let html = '<option value="">-- Επιλογή Πακέτου --</option>';
        html += '<option value="custom" data-is-group="0" data-duration="60">Personal Session (Custom)</option>';

        if (res.success && res.data) {
            res.data.forEach(p => {
                let label = p.title;
                if (p.is_group == 1 && p.start_datetime) {
                    const d = new Date(p.start_datetime.replace(' ', 'T'));
                    const dateStr = d.toLocaleDateString('el-GR', { day: 'numeric', month: 'short' });
                    label += ` (Group: ${dateStr})`;
                } else {
                    label += ` (${p.duration_minutes}')`;
                }
                const startData = p.start_datetime ? p.start_datetime.replace(' ', 'T') : '';
                html += `<option value="${p.id}" 
                    data-is-group="${p.is_group}" 
                    data-duration="${p.duration_minutes}"
                    data-price="${p.price}"
                    data-max="${p.max_attendants || 0}"
                    data-current="${p.current_bookings || 0}"
                    data-start="${startData}"
                    data-type="${p.type}"> ${label}
                </option>`;
            });
        }
        pkgSelect.html(html).prop('disabled', false);

        if (preSelectPkgId) {
            // 1. Αν έχουμε αποθηκευμένη ημερομηνία, τη βάζουμε ΠΡΙΝ το trigger change
            // Αυτό αποτρέπει την "αυτόματη αναζήτηση επόμενου κενού" που θα έτρεχε αν το πεδίο ήταν άδειο.
            if (savedDates && savedDates.start) {
                const datePart = savedDates.start.split('T')[0];
                $('#slot_date_picker').val(datePart);
            }

            // 2. Επιλογή Πακέτου & Trigger Change (για να τρέξουν οι listeners τιμής, διάρκειας κλπ)
            pkgSelect.val(preSelectPkgId).trigger('change');

            // 3. Επαναφορά των σωστών ωρών (start/end)
            // Βάζουμε ένα μικρό timeout γιατί το 'change' event του package μπορεί να προσπαθήσει να αλλάξει τις ώρες (π.χ. σε Group)
            if (savedDates && savedDates.start) {
                setTimeout(() => {
                    $('#start_datetime').val(savedDates.start);
                    $('#end_datetime').val(savedDates.end);

                    // Αν είναι Group, κλειδώνουμε το end date ξανά (για σιγουριά)
                    const selectedOpt = pkgSelect.find('option:selected');
                    if (selectedOpt.data('is-group') == 1) {
                        $('#end_datetime').prop('readonly', true).addClass('bg-light');
                    }
                }, 100);
            }
        }
    }, 'json');
}

// ===== ΔΙΟΡΘΩΜΕΝΗ ΣΥΝΑΡΤΗΣΗ ΓΙΑ ΤΑ SLOTS =====
// ===== ΔΙΟΡΘΩΜΕΝΗ ΣΥΝΑΡΤΗΣΗ ΓΙΑ ΤΑ SLOTS (Με Ώρα & Περίοδο) =====
function fetchAvailableSlots(tid, date, duration) {
    const container = $('#slots_container');
    const loader = $('#slots_loader'); // Αν υπάρχει
    container.html('<div class="spinner-border spinner-border-sm text-secondary"></div>');

    $.post('includes/admin/ajax.php', {
        action: 'getAvailableSlots',
        therapist_id: tid,
        date: date,
        duration: duration,
        csrf_token: getCsrfToken()
    }, function (res) {
        if (res.success && res.slots && res.slots.length > 0) {
            let html = '';
            res.slots.forEach(slotItem => {
                let startIso = '';
                let spotsBadge = '';

                // Ανάκτηση ISO string (είτε είναι object είτε string)
                if (typeof slotItem === 'object') {
                    startIso = slotItem.start;
                    if (slotItem.available_spots > 1) {
                        spotsBadge = ` <span class="badge bg-white text-primary border ms-1 rounded-pill">${slotItem.available_spots}</span>`;
                    }
                } else {
                    startIso = slotItem; // Fallback
                }

                // --- ΝΕΟ: Χρήση της helper function για Ώρα/Περίοδο ---
                // Προσοχή: Το startIso είναι π.χ. "2024-05-10T09:00:00"
                // Φτιάχνουμε Date object για να δουλέψει η helper
                const dateObj = new Date(startIso);
                const tInfo = getTimeInfo(dateObj);

                html += `<button type="button" class="btn btn-outline-primary btn-sm time-slot-btn m-1 d-flex align-items-center gap-2" style="min-width: 90px;">
                            <div class="text-start" style="line-height:1.1;">
                                <div class="fw-bold fs-6">${tInfo.time}</div>
                                <div class="small opacity-75" style="font-size:0.65em;">${tInfo.period}</div>
                            </div>
                            ${spotsBadge}
                         </button>`;
            });
            container.html(html);
        } else {
            container.html('<span class="text-danger small mx-auto fw-bold"><i class="bi bi-x-circle"></i> Δεν υπάρχουν κενά</span>');
        }
    }, 'json');
}
// =============================================

// --- 5. PAYMENT & UI FUNCTIONS ---

function updateStatusUI(status) {
    const badge = $('#modalStatusBadge');
    const tabText = $('#tabStatusText');
    const dropdown = $('#payment_status');

    badge.removeClass('bg-success bg-warning bg-danger bg-light text-dark text-white');

    let text = '-', badgeClass = 'bg-light text-dark', tabColor = 'text-muted';

    if (status === 'paid') {
        text = 'ΕΞΟΦΛΗΘΗΚΕ'; badgeClass = 'bg-success text-white'; tabColor = 'text-success';
    } else if (status === 'partially_paid') {
        text = 'ΜΕΡΙΚΗ ΕΞΟΦΛΗΣΗ'; badgeClass = 'bg-warning text-dark'; tabColor = 'text-warning';
    } else if (status === 'unpaid') {
        text = 'ΕΚΚΡΕΜΕΙ'; badgeClass = 'bg-danger text-white'; tabColor = 'text-danger';
    }

    badge.text(text).addClass(badgeClass);
    tabText.text('Status: ' + text).removeClass('text-success text-warning text-danger text-muted').addClass(tabColor);

    if (dropdown.val() !== status) dropdown.val(status);
}

function loadBookingPayments(bookingId) {
    const list = $('#paymentsList');
    const addBtn = $('#btnAddManualPayment');
    list.html('<tr><td colspan="5" class="text-center">Φόρτωση...</td></tr>');

    $.post('includes/admin/ajax.php', {
        action: 'getBookingPayments',
        booking_id: bookingId,
        csrf_token: getCsrfToken()
    }, function (res) {
        list.empty();
        let total = 0;

        if (res.success && res.data.length > 0) {
            addBtn.addClass('d-none');
            res.data.forEach(p => {
                const amount = parseFloat(p.amount_paid || p.amount_total);
                total += amount;

                // --- FIX START: Safe Date Parsing ---
                const rawDate = p.payed_at || p.created_at;
                let dateDisplay = '-';
                let dateOnly = '';

                if (rawDate) {
                    // 1. Fix SQL format (replace space with T for Safari/iOS compatibility)
                    // e.g. "2023-10-25 12:00:00" -> "2023-10-25T12:00:00"
                    const safeDateStr = rawDate.replace(' ', 'T');
                    const d = new Date(safeDateStr);

                    // 2. Check if date is valid
                    if (!isNaN(d.getTime())) {
                        dateDisplay = d.toLocaleDateString('el-GR');
                        // Use try-catch or safe split just in case
                        try {
                            dateOnly = d.toISOString().split('T')[0];
                        } catch (e) {
                            dateOnly = rawDate.substring(0, 10); // Fallback to string slicing
                        }
                    }
                }
                // --- FIX END ---

                const row = `
                <tr>
                    <td>${dateDisplay}</td>
                    <td class="fw-bold text-success">€${amount.toFixed(2)}</td>
                    <td><span class="badge bg-light text-dark border">${p.payment_method || '-'}</span></td>
                    <td class="small text-muted">${p.note || '-'}</td>
                    <td class="text-end">
                        <button class="btn btn-xs text-primary edit-payment me-1" 
                                data-id="${p.id}" 
                                data-amount="${amount}" 
                                data-method="${p.payment_method}" 
                                data-date="${dateOnly}" 
                                data-note="${escapeHtml(p.note || '')}">
                            <i class="bi bi-pencil-square"></i>
                        </button>
                        <button class="btn btn-xs text-danger del-payment" data-id="${p.id}">
                            <i class="bi bi-trash"></i>
                        </button>
                    </td>
                </tr>`;
                list.append(row);
            });
        } else {
            addBtn.removeClass('d-none');
            list.html('<tr><td colspan="5" class="text-center text-muted small">Καμία πληρωμή ακόμα.</td></tr>');
        }
        $('#pay_total').text('€' + total.toFixed(2));
    }, 'json');
}

function showAttendees(packageId) {
    const list = document.getElementById('attendeesList');
    const loader = document.getElementById('attendeesLoader');
    const noMsg = document.getElementById('noAttendeesMsg');

    // Reset
    list.innerHTML = '';
    loader.classList.remove('d-none');
    noMsg.classList.add('d-none');

    const modal = new bootstrap.Modal(document.getElementById('attendeesModal'));
    modal.show();

    $.post('includes/admin/ajax.php', {
        action: 'fetchGroupAttendees',
        package_id: packageId,
        csrf_token: getCsrfToken()
    }, function (res) {
        loader.classList.add('d-none');
        let hasAttendees = false;

        if (res.success && res.data.length > 0) {
            hasAttendees = true;
            res.data.forEach(row => {
                const fullName = escapeHtml(`${row.first_name} ${row.last_name}`);
                const statusBadge = row.payment_status === 'paid'
                    ? '<span class="badge bg-success">Paid</span>'
                    : '<span class="badge bg-warning text-dark">Unpaid</span>';

                list.insertAdjacentHTML('beforeend', `<tr><td class="fw-bold">${fullName}</td><td><a href="tel:${row.phone}">${row.phone || '-'}</a></td><td>${statusBadge}</td></tr>`);
            });
        }

        if (res.manual_count > 0) {
            hasAttendees = true;
            list.insertAdjacentHTML('beforeend', `<tr class="table-warning"><td class="fw-bold text-secondary"><i class="bi bi-person-fill-lock me-2"></i>Manual / Offline</td><td colspan="2" class="fw-bold text-dark">${res.manual_count} άτομα</td></tr>`);
        }

        if (!hasAttendees) noMsg.classList.remove('d-none');
    }, 'json').fail(function () {
        loader.classList.add('d-none');
        list.innerHTML = '<tr><td colspan="3" class="text-center text-danger">Error loading.</td></tr>';
    });
}

// --- Helper: Format Time & Period (Πρωί/Απόγευμα) ---
function getTimeInfo(dateInput) {
    // Αν είναι string (ISO), φτιάχνουμε Date. Αν είναι ήδη Date, το κρατάμε.
    const d = (typeof dateInput === 'string') ? new Date(dateInput) : dateInput;

    const h = d.getHours();
    const m = String(d.getMinutes()).padStart(2, '0');
    const timeStr = `${String(h).padStart(2, '0')}:${m}`;

    let period = 'Βράδυ';
    if (h >= 5 && h < 12) period = 'Πρωί';
    else if (h >= 12 && h < 17) period = 'Μεσημέρι';
    else if (h >= 17 && h < 21) period = 'Απόγευμα';

    return { time: timeStr, period: period };
}

/* ============================================================
   MAIN INITIALIZATION (DOMContentLoaded)
   ============================================================ */
document.addEventListener('DOMContentLoaded', function () {
    const calendarEl = document.getElementById('calendar');
    const therapistSelect = document.getElementById('therapistSelect');

    // --- Helpers Local ---
    async function apiPost(formData, signal = null) {
        const csrf = getCsrfToken();
        if (csrf) formData.append('csrf_token', csrf);
        const options = { method: 'POST', body: formData };
        if (signal) options.signal = signal;
        const res = await fetch('includes/admin/ajax.php', options);
        return await res.json();
    }

    function getSelectedTherapistId() {
        const v = therapistSelect?.value;
        if (!v) return null;
        const id = parseInt(v, 10);
        return Number.isFinite(id) ? id : null;
    }

    function therapistTitle(t) {
        const id = t.id ?? t.therapist_id;
        if (t.first_name || t.last_name) return `${t.first_name || ''} ${t.last_name || ''}`.trim();
        return t.email || `Therapist #${id}`;
    }

    async function fetchTherapists() {
        const fd = new FormData();
        fd.append('action', 'fetchTherapistsData');
        const res = await apiPost(fd);
        therapistsCache = Array.isArray(res) ? res : (res.data || []);
        return therapistsCache;
    }

    function buildResourcesFromCache(filterTherapistId = null) {
        const resources = [];
        // Removed Unassigned:
        // resources.push({ id: 'unassigned', title: '(Unassigned)' }); 

        therapistsCache.forEach(t => {
            const id = String(t.id ?? t.therapist_id);
            if (filterTherapistId !== null && String(filterTherapistId) !== id) return;
            resources.push({
                id: id,
                title: therapistTitle(t),
                extendedProps: { color: t.color || null }
            });
        });
        return resources;
    }

    function findTopEventAt(dateObj, resourceId = null) {
        const t = dateObj.getTime();
        const events = calendar.getEvents();

        // προτεραιότητα: booking > group_event > block (ό,τι θες)
        const priority = { booking: 3, group_event: 2, block: 1 };

        let best = null;
        let bestScore = -1;

        events.forEach(ev => {
            const p = ev.extendedProps || {};
            const src = p.source;

            if (!src || src === 'availability_bg' || ev.display === 'background') return;

            // filter by resource if provided (resource views)
            if (resourceId && ev.getResources && ev.getResources().length) {
                const evRes = ev.getResources()[0]?.id;
                if (evRes && String(evRes) !== String(resourceId)) return;
            }

            const s = ev.start ? ev.start.getTime() : null;
            const e = ev.end ? ev.end.getTime() : (s ? s + 1 : null);
            if (s === null || e === null) return;

            // overlap check
            if (t < e && t >= s) {
                const score = priority[src] ?? 0;
                if (score > bestScore) {
                    bestScore = score;
                    best = ev;
                }
            }
        });

        return best;
    }


    async function loadTherapistsIntoSelect() {
        if (!therapistSelect) return;
        therapistSelect.innerHTML = '<option value="">-- Όλοι --</option>';
        therapistsCache.forEach(t => {
            const id = t.id ?? t.therapist_id;
            therapistSelect.insertAdjacentHTML('beforeend', `<option value="${id}">${therapistTitle(t)}</option>`);
        });
    }

    /* ============================================================
        CALENDAR INITIALIZATION (With Drag Support)
        ============================================================ */
    calendar = new FullCalendar.Calendar(calendarEl, {
        locale: 'el',
        timeZone: 'local',

        // --- VIEW & HEADER ---
        initialView: 'dayGridMonth',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,resourceTimeGridWeek,listWeek'
        },
        buttonText: {
            today: 'Σήμερα',
            month: 'Μήνας',
            week: 'Εβδομάδα',
            day: 'Ημέρα',
            list: 'Λίστα'
        },

        // --- SETTINGS ---
        height: 'auto',
        expandRows: true,
        resourceAreaWidth: '220px',
        stickyHeaderDates: true,
        slotMinTime: '07:00:00',
        slotMaxTime: '23:00:00',
        slotDuration: '00:30:00',
        allDaySlot: false,

        // --- DRAG & DROP SETTINGS ---
        selectable: true,      // Επιτρέπει το Drag
        selectMirror: true,    // Δείχνει το ghost event καθώς σέρνεις
        editable: false,       // Δεν θέλουμε να μετακινούνται τα υπάρχοντα events (μόνο create)
        nowIndicator: true,
        dayMaxEvents: 4,

        // --- RESOURCES ---
        resources: function (fetchInfo, successCallback) {
            if (therapistsCache.length === 0) {
                fetchTherapists().then(() => {
                    successCallback(buildResourcesFromCache(getSelectedTherapistId()));
                });
            } else {
                successCallback(buildResourcesFromCache(getSelectedTherapistId()));
            }
        },

        // --- EVENT CONTENT (Card Look) ---
        // --- 5. EVENT CONTENT (Η εμφάνιση "Κάρτας") ---
        eventContent: function (arg) {
            let p = arg.event.extendedProps;

            // A. BUFFER
            if (p.source === 'buffer') return { html: '' };

            // B. BLOCKED
            if (p.source === 'block') {
                return {
                    html: `
                    <div class="d-flex align-items-center h-100 px-2 overflow-hidden">
                        <i class="bi bi-slash-circle-fill me-2" style="font-size: 1em;"></i>
                        <div class="fw-bold text-uppercase small" style="letter-spacing:0.5px; font-size:0.75em;">Blocked</div>
                        ${p.notes ? `<div class="ms-1 small opacity-75 text-truncate">(${escapeHtml(p.notes)})</div>` : ''}
                    </div>`
                };
            }

            // C. SESSION / GROUP EVENT
            if (p.source === 'session' || p.source === 'group_event') {
                let current = parseInt(p.current_bookings || 0);
                let max = parseInt(p.max_capacity || 0);

                // Colors
                let percentage = max > 0 ? (current / max) * 100 : 0;
                let barColor = 'bg-success';
                let textColor = 'text-muted';

                if (percentage >= 100) { barColor = 'bg-danger'; textColor = 'text-danger fw-bold'; }
                else if (percentage >= 75) { barColor = 'bg-warning'; textColor = 'text-dark fw-bold'; }

                // --- ΝΕΟ: Υπολογισμός Ώρας ---
                const tInfo = getTimeInfo(arg.event.start);

                // HTML Structure
                let html = `
                <div class="p-1 h-100 d-flex flex-column justify-content-center">
                    
                    <div class="d-flex justify-content-between align-items-center mb-1" style="line-height:1;">
                        <span class="badge bg-light text-secondary border shadow-sm" style="font-size:0.7em;">
                            ${tInfo.time} <span class="fw-normal opacity-75 ms-1">(${tInfo.period})</span>
                        </span>
                    </div>

                    <div class="fw-bold text-truncate text-dark mb-1" style="font-size:0.85em;">
                        ${arg.event.title}
                    </div>
                    
                    <div class="d-flex align-items-center" style="gap: 6px;">
                        <div class="progress flex-grow-1 bg-light border" style="height: 5px;">
                            <div class="progress-bar ${barColor}" role="progressbar" style="width: ${percentage}%"></div>
                        </div>
                        <span class="small ${textColor}" style="font-size:0.75em; min-width: 35px; text-align:right;">
                            ${current}/${max}
                        </span>
                    </div>
                </div>
                `;
                return { html: html };
            }

            return { html: `<div class="p-1 text-truncate">${arg.event.title}</div>` };
        },

        // --- EVENT CLICK ---
        eventClick: function (info) {
            const p = info.event.extendedProps;
            if (p.source === 'block') {
                const realId = String(info.event.id).replace('bl_', '');
                if (confirm('Διαγραφή block;')) deleteBlock(realId);
                return;
            }
            if (p.source === 'session' || p.source === 'group_event') {
                const sessionId = info.event.id;
                openSessionCommandCenter(sessionId, info.event);
            }
        },

        // --- NEW: SELECT (DRAG TO CREATE) ---
        select: function (info) {
            // info.start, info.end, info.resource (αν είμαστε σε resource view)
            const therapistId = info.resource ? info.resource.id : getSelectedTherapistId();

            // 1. BLOCK MODE
            if (isBlockMode) {
                if (!therapistId || therapistId === 'unassigned') {
                    alert('Παρακαλώ επιλέξτε συγκεκριμένο θεραπευτή (από τη λίστα ή το φίλτρο) για να βάλετε Block.');
                    calendar.unselect();
                    return;
                }

                const blockModalEl = document.getElementById('blockModal');
                blockModalEl.dataset.therapistId = therapistId;
                document.getElementById('blockTherapistLabel').textContent = info.resource ? info.resource.title : $('#therapistSelect option:selected').text();

                // Format dates YYYY-MM-DD HH:mm (χωρίς T)
                let startStr = formatDateToInput(info.start).replace('T', ' ');
                let endStr = formatDateToInput(info.end).replace('T', ' ');

                // Αν είμαστε σε Month View, το drag δίνει ολόκληρη μέρα (00:00 - 00:00 επόμενης).
                // Βάζουμε default ώρες για ευκολία αν θες, αλλιώς το αφήνουμε 00:00
                if (info.view.type === 'dayGridMonth') {
                    startStr = ymdLocal(info.start) + ' 09:00';
                    endStr = ymdLocal(info.start) + ' 10:00';
                }

                document.getElementById('blockStart').value = startStr;
                document.getElementById('blockEnd').value = endStr;
                document.getElementById('blockNotes').value = '';

                new bootstrap.Modal(blockModalEl).show();
                calendar.unselect();
                return;
            }

            // 2. NORMAL MODE (New Booking)
            resetForm();
            $('#bookingModalTitle').text('Νέα Κράτηση');

            // Format dates for datetime-local input (YYYY-MM-DDTHH:mm)
            let startIso = formatDateToInput(info.start);
            let endIso = formatDateToInput(info.end);

            if (info.view.type === 'dayGridMonth') {
                // Στο Month view, αν πατήσεις μια μέρα, ας βάλουμε default 1 ώρα
                startIso = formatDateToInput(info.start).split('T')[0] + 'T09:00';
                let endDateObj = new Date(info.start);
                endDateObj.setHours(10);
                endIso = formatDateToInput(endDateObj).split('T')[0] + 'T10:00';
            }

            $('#start_datetime').val(startIso);
            $('#end_datetime').val(endIso);

            const datePart = startIso.split('T')[0];
            $('#slot_date_picker').val(datePart);

            if (therapistId) {
                // Load packages but don't trigger change yet to keep dates
                $('#therapist_id').val(therapistId);
                loadPackagesForTherapist(therapistId, null, { start: startIso, end: endIso });
            }

            bookingModal.show();
            calendar.unselect();
        },

        // --- DATA SOURCE ---
        events: async function (fetchInfo, successCallback, failureCallback) {
            if (fetchAbortController) fetchAbortController.abort();
            fetchAbortController = new AbortController();

            try {
                const fd = new FormData();
                fd.append('action', 'calendar_getDataV2');
                fd.append('start', fetchInfo.startStr);
                fd.append('end', fetchInfo.endStr);
                fd.append('therapist_id', getSelectedTherapistId() || '');

                const res = await apiPost(fd, fetchAbortController.signal);

                if (res.success) {
                    const events = res.data.map(item => {
                        let className = (item.source === 'block') ? 'bg-block' :
                            (item.source === 'session' || item.source === 'group_event') ? 'bg-session' : '';

                        return {
                            id: item.id,
                            title: item.title,
                            start: item.start_datetime,
                            end: item.end_datetime,
                            resourceId: item.therapist_id,
                            className: className,
                            extendedProps: {
                                source: item.source,
                                current_bookings: parseInt(item.current_bookings || 0),
                                max_capacity: parseInt(item.max_capacity || 0),
                                package_id: item.package_id,
                                therapist_id: item.therapist_id,
                                price: item.price,
                                notes: item.notes
                            }
                        };
                    });
                    successCallback(events);
                } else {
                    failureCallback([]);
                }
            } catch (err) {
                if (err.name !== 'AbortError') failureCallback([]);
            }
        }
    });

    calendar.render();

    // --- OPEN SESSION COMMAND CENTER ---
    async function openSessionCommandCenter(sessionId, eventObj) {
        const modalEl = document.getElementById('sessionModal');
        const modal = new bootstrap.Modal(modalEl);
        const btnAdd = document.getElementById('btnSessionAddBooking');
        const p = eventObj.extendedProps;

        // --- 1. ΑΠΟΘΗΚΕΥΣΗ ΔΕΔΟΜΕΝΩΝ ΣΤΟ ΚΟΥΜΠΙ (ΓΙΑ PRE-FILL) ---

        // A. Therapist ID
        const tid = p.therapist_id || '';
        btnAdd.setAttribute('data-therapist-id', tid);

        // B. Package ID
        let pkgId = p.package_id || '';
        // Αν είναι Group Event (grp_15), το Package ID είναι το 15
        if ((!pkgId || pkgId == 0) && sessionId.toString().startsWith('grp_')) {
            let parts = sessionId.split('_');
            if (parts[1]) pkgId = parts[1];
        }
        btnAdd.setAttribute('data-package-id', pkgId);

        // C. Dates & Fallback Logic
        let startObj = eventObj.start;
        let endObj = eventObj.end;

        // Αν το end είναι null (συμβαίνει συχνά στο FullCalendar), υπολόγισε +60 λεπτά
        if (!endObj) {
            endObj = new Date(startObj.getTime() + 60 * 60000);
        }

        btnAdd.setAttribute('data-start', formatDateToInput(startObj));
        btnAdd.setAttribute('data-end', formatDateToInput(endObj));


        // --- 2. UI Updates (Header, Time, Capacity) ---
        document.getElementById('sessionModalTitle').innerText = eventObj.title;

        const dateStr = startObj.toLocaleDateString('el-GR', { weekday: 'short', day: 'numeric', month: 'short' });
        const timeStr = `${String(startObj.getHours()).padStart(2, '0')}:${String(startObj.getMinutes()).padStart(2, '0')} - ${String(endObj.getHours()).padStart(2, '0')}:${String(endObj.getMinutes()).padStart(2, '0')}`;
        document.getElementById('sessionModalTime').innerText = `${dateStr} • ${timeStr}`;

        // Reset List & Loaders
        const tbody = document.getElementById('sessionAttendeesList');
        tbody.innerHTML = '<tr><td colspan="4" class="text-center py-4"><div class="spinner-border text-primary"></div></td></tr>';

        // Initial Capacity
        document.getElementById('sessionCapacityCount').innerText = `${p.current_bookings}/${p.max_capacity}`;

        // --- 3. EDIT BUTTON LOGIC ---
        const btnEdit = document.getElementById('btnEditSessionDetails');
        if (pkgId) {
            btnEdit.classList.remove('d-none');
            btnEdit.onclick = function () { window.location.href = `/packages.php?id=${pkgId}`; };
        } else {
            btnEdit.classList.add('d-none');
        }

        modal.show();

        // --- 4. Fetch Details (Attendees) ---
        const fd = new FormData();
        fd.append('action', 'getSessionDetails');
        fd.append('session_id', sessionId);
        fd.append('csrf_token', getCsrfToken());

        const res = await apiPostGlobal(fd);

        if (res.success) {
            const attendees = res.attendees || [];
            const manualCount = parseInt(res.manual_count || 0);
            tbody.innerHTML = '';

            if (attendees.length === 0 && manualCount === 0) {
                document.getElementById('sessionEmptyState').classList.remove('d-none');
            } else {
                document.getElementById('sessionEmptyState').classList.add('d-none');

                // Render Attendees
                attendees.forEach(att => {
                    let statusBadge = att.payment_status === 'paid'
                        ? '<span class="badge bg-success">Paid</span>'
                        : '<span class="badge bg-warning text-dark">Unpaid</span>';

                    let paxBadge = att.attendees_count > 1 ? ` <span class="badge bg-info text-dark border ms-1">+${att.attendees_count - 1}</span>` : '';

                    let row = `
                    <tr>
                        <td class="ps-4">
                            <div class="fw-bold text-dark">${escapeHtml(att.client_name)}${paxBadge}</div>
                            <div class="small text-muted">${att.notes || ''}</div>
                        </td>
                        <td><div class="small"><i class="bi bi-telephone me-1"></i> ${att.phone || '-'}</div></td>
                        <td>${statusBadge}</td>
                        <td class="text-end pe-4">
                            <button class="btn btn-sm btn-light border edit-booking-btn" data-booking-id="${att.booking_id}"><i class="bi bi-pencil-square text-primary"></i></button>
                            <button class="btn btn-sm btn-light border text-danger delete-booking-btn" data-booking-id="${att.booking_id}"><i class="bi bi-x-lg"></i></button>
                        </td>
                    </tr>`;
                    tbody.insertAdjacentHTML('beforeend', row);
                });

                // Render Manual
                if (manualCount > 0) {
                    tbody.insertAdjacentHTML('beforeend', `
                    <tr class="table-warning border-warning">
                        <td class="ps-4"><div class="fw-bold text-dark">Manual / Offline</div></td>
                        <td colspan="2"><span class="badge bg-white text-dark border fw-bold">${manualCount} άτομα</span></td>
                        <td></td>
                    </tr>`);
                }

                // Update Header Count
                let totalRealPax = manualCount;
                attendees.forEach(a => totalRealPax += parseInt(a.attendees_count || 1));
                document.getElementById('sessionCapacityCount').innerText = `${totalRealPax}/${p.max_capacity}`;
            }
        } else {
            tbody.innerHTML = `<tr><td colspan="4" class="text-center text-danger">Error fetching details.</td></tr>`;
        }
    }

    // --- WIRING UP BUTTONS ---

    // 1. Edit Booking (From within the List)
    $(document).on('click', '.edit-booking-btn', function () {
        const bookingId = $(this).data('booking-id');
        // We can use your existing Booking Modal logic here!
        // But we need to make sure when it closes, it refreshes the Session Modal, not just the calendar.
        $('#sessionModal').modal('hide'); // Hide session modal temporarily
        openBookingEditModal(bookingId);
    });

    // 2. Add Booking (From Session Header)
    $('#btnSessionAddBooking').click(function () {
        $('#sessionModal').modal('hide'); // Κλείσε το session modal

        // Reset Booking Form
        resetForm();
        $('#bookingModalTitle').text('Νέα Κράτηση (από Session)');

        // Ανάκτηση δεδομένων από το κουμπί
        // Χρησιμοποιούμε .attr() για να είμαστε σίγουροι ότι διαβάζουμε το DOM update
        const btn = $(this);
        const tid = btn.attr('data-therapist-id');
        const pkgId = btn.attr('data-package-id');
        const startIso = btn.attr('data-start'); // "YYYY-MM-DDTHH:mm"
        const endIso = btn.attr('data-end');

        console.log("Adding Booking Params:", { tid, pkgId, startIso, endIso }); // Για έλεγχο στην κονσόλα

        // 1. Επιλογή Θεραπευτή
        if (tid) {
            // --- Η ΔΙΟΡΘΩΣΗ ΕΙΝΑΙ ΕΔΩ ---
            // Βάζουμε ΜΟΝΟ την τιμή. ΔΕΝ κάνουμε .trigger('change')
            // για να μην ξεκινήσει αυτόματη (κενή) φόρτωση πακέτων.
            $('#therapist_id').val(tid);
        }

        // 2. Φόρτωση Πακέτων & Pre-select
        // Καλούμε εμείς χειροκίνητα τη φόρτωση με τις σωστές παραμέτρους
        if (tid) {
            loadPackagesForTherapist(tid, pkgId, {
                start: startIso,
                end: endIso
            });
        }

        // 3. Ενημέρωση του γενικού Date Picker (για οπτικούς λόγους)
        if (startIso) {
            const datePart = startIso.split('T')[0];
            $('#slot_date_picker').val(datePart);
        }

        // Άνοιγμα Modal
        bookingModal.show();
    });

    // -----------------------------------------------------------
    // INTEGRATED BOOKING LOGIC
    // -----------------------------------------------------------

    // Init Client Select2
    $('#client_id').select2({
        theme: 'bootstrap-5',
        dropdownParent: $('#bookingModal'),
        width: 'resolve',
        ajax: {
            url: 'includes/admin/ajax.php',
            type: 'POST',
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return { action: 'searchClientsSelect2', q: params.term, csrf_token: getCsrfToken() };
            }
        },
        placeholder: 'Αναζήτηση Πελάτη...',
        minimumInputLength: 1
    });

    // Populate Booking Modal Therapists
    (function initModalTherapists() {
        setTimeout(async () => {
            const ts = await fetchTherapists();
            let html = '<option value="">-- Επιλογή --</option>';
            ts.forEach(t => {
                const name = t.first_name ? `${t.first_name} ${t.last_name}` : t.email;
                html += `<option value="${t.id || t.therapist_id}">${name}</option>`;
            });
            $('#therapist_id').html(html);
        }, 500);
    })();

    function isPastDate(dateStr) {
        if (!dateStr) return false; // empty => let auto-find handle
        // dateStr = YYYY-MM-DD
        const todayStr = new Date().toISOString().slice(0, 10);
        return dateStr < todayStr; // safe string compare in this format
    }

    // Smart Flow Setup
    (function setupSmartBookingFlow() {
        $('#therapist_id').on('change', function () { loadPackagesForTherapist($(this).val()); });

        $('#package_id').on('change', function () {
            const selectedOpt = $(this).find('option:selected');
            const isGroup = selectedOpt.data('is-group') == 1;
            const duration = selectedOpt.data('duration') || 60;
            const price = selectedOpt.data('price');
            const pkgType = selectedOpt.data('type');

            // Χρειαζόμαστε αυτά για το AJAX
            const tid = $('#therapist_id').val();
            const pkgId = $(this).val();

            if (price) $('#booking_price').val(price);
            $('#package_duration').val(duration);

            const typeSelect = $('#appointment_type');
            typeSelect.find('option').prop('disabled', false); // Reset

            if (pkgType === 'online') {
                typeSelect.val('online');
                typeSelect.find('option[value="inPerson"]').prop('disabled', true);
            } else if (pkgType === 'inPerson') {
                typeSelect.val('inPerson');
                typeSelect.find('option[value="online"]').prop('disabled', true);
            } else {
                // Mixed: Default to online if nothing selected or invalid
                if (!typeSelect.val()) typeSelect.val('online');
            }

            if (isGroup) {
                $('#slot_picker_panel').addClass('d-none');
                $('#group_info_panel').removeClass('d-none');
                $('#end_datetime').prop('readonly', true).addClass('bg-light');

                const startStr = selectedOpt.data('start');
                if (startStr) {
                    const startDate = new Date(startStr);
                    const endDate = new Date(startDate.getTime() + duration * 60000);
                    $('#group_time_display').text(`${startDate.toLocaleString()} - ${endDate.toLocaleTimeString()}`);
                    $('#start_datetime').val(formatDateToInput(startDate));
                    $('#end_datetime').val(formatDateToInput(endDate));
                }

                const max = parseInt(selectedOpt.data('max'));
                const current = parseInt(selectedOpt.data('current'));
                $('#group_capacity_badge').text(`${current} / ${max}`);
                if (current >= max) $('#saveBookingBtn').prop('disabled', true).text('Workshop Full');
                else $('#saveBookingBtn').prop('disabled', false).text('Καταχώρηση');

            } else {
                // --- PERSONAL BOOKING ---
                $('#group_info_panel').addClass('d-none');
                $('#slot_picker_panel').removeClass('d-none');
                $('#end_datetime').prop('readonly', false).removeClass('bg-light');
                $('#saveBookingBtn').prop('disabled', false).text('Καταχώρηση');

                // --- ΝΕΟ: ΑΥΤΟΜΑΤΗ ΕΥΡΕΣΗ ΠΡΩΤΗΣ ΔΙΑΘΕΣΙΜΗΣ ---
                // Αν δεν έχει επιλεγεί ήδη ημερομηνία, ψάξε την πρώτη ελεύθερη
                const pickedDate = $('#slot_date_picker').val();

                // ✅ Αν δεν υπάρχει ημερομηνία Ή είναι στο παρελθόν -> auto-find next available (όχι past)
                if (!pickedDate || isPastDate(pickedDate)) {

                    // καθάρισμα ώστε να ΜΗΝ κάνει refresh στην παλιά μέρα
                    $('#slot_date_picker').val('');
                    $('#start_datetime').val('');
                    $('#end_datetime').val('');

                    $('#slots_container').html(
                        '<div class="text-center my-3">' +
                        '<div class="spinner-border spinner-border-sm text-primary"></div>' +
                        '<div class="small text-muted mt-1">Αναζήτηση επόμενης διαθεσιμότητας...</div>' +
                        '</div>'
                    );

                    $.post('includes/admin/ajax.php', {
                        action: 'findFirstAvailableDate',
                        therapist_id: tid,
                        package_id: pkgId,
                        duration: duration,
                        csrf_token: getCsrfToken()
                    }, function (res) {
                        if (res.success && res.date) {
                            $('#slot_date_picker').val(res.date).trigger('change');
                        } else {
                            $('#slots_container').html('<div class="text-danger small fw-bold mt-2 text-center">Δεν βρέθηκε διαθεσιμότητα σύντομα.</div>');
                        }
                    }, 'json');

                } else {
                    // ✅ έχει valid (σήμερα/μέλλον) ημερομηνία -> refresh slots
                    $('#slot_date_picker').trigger('change');
                }
            }
        });

        $('#slot_date_picker').on('change', function () {
            const tid = $('#therapist_id').val();
            const date = $(this).val();
            const duration = parseInt($('#package_duration').val()) || 60;
            if (tid && date && !$('#slot_picker_panel').hasClass('d-none')) {
                fetchAvailableSlots(tid, date, duration);
            }
        });

        // ΔΙΟΡΘΩΜΕΝΟ CLICK HANDLER ΓΙΑ ΤΑ SLOTS (Πλέον περιέχουν και κείμενο)
        $(document).on('click', '.time-slot-btn', function () {
            $('.time-slot-btn').removeClass('btn-primary text-white').addClass('btn-outline-primary');
            $(this).removeClass('btn-outline-primary').addClass('btn-primary text-white');

            // Παίρνουμε ΜΟΝΟ την ώρα, αγνοώντας το badge μέσα στο κουμπί
            // Χρησιμοποιούμε text node ή trim/split
            const rawText = $(this).text();
            // Αν το text είναι "10:00 (5)", το split(' ')[0] θα δώσει "10:00"
            // Αλλά αν το κείμενο είναι καθαρό, θα δώσει πάλι την ώρα.
            // Ασφαλέστερο: Παίρνουμε το text του πρώτου child node αν υπάρχει (συνήθως είναι η ώρα)
            // ή απλά καθαρίζουμε τα πάντα που δεν είναι ώρα.
            // Εδώ απλοϊκά:
            const time = rawText.trim().split(' ')[0]; // Υποθέτουμε ότι η ώρα είναι το πρώτο πράγμα

            const date = $('#slot_date_picker').val();
            const duration = parseInt($('#package_duration').val()) || 60;
            const startObj = new Date(`${date}T${time}:00`);
            const endObj = new Date(startObj.getTime() + duration * 60000);
            $('#start_datetime').val(formatDateToInput(startObj));
            $('#end_datetime').val(formatDateToInput(endObj));
        });
    })();

    // Save Booking
    $('#bookingForm').on('submit', function (e) {
        e.preventDefault();
        const btn = $('#saveBookingBtn');
        if (!$('#client_id').val() || !$('#therapist_id').val()) { alert('Select Client & Therapist'); return; }

        btn.prop('disabled', true).html('Saving...');

        const fd = new FormData(this);
        fd.append('action', 'saveBooking');
        fd.append('csrf_token', getCsrfToken());

        // Manual append για πεδία που μπορεί να μην έχουν name ή να είναι disabled/select2
        fd.set('client_id', $('#client_id').val());
        fd.set('therapist_id', $('#therapist_id').val());
        fd.set('package_id', $('#package_id').val());
        fd.set('appointment_type', $('#appointment_type').val());
        fd.set('id', $('#bookingId').val());

        // --- FIX: Προσθήκη ημερομηνιών χειροκίνητα ---
        fd.set('start', $('#start_datetime').val());
        fd.set('end', $('#end_datetime').val());

        // Προαιρετικά, αν θες να είσαι σίγουρος και για τα άλλα:
        fd.set('status', $('#booking_status').val());
        fd.set('notes', $('#booking_notes').val());
        fd.set('price', $('#booking_price').val());
        fd.set('payment_status', $('#payment_status').val());

        apiPost(fd).then(res => {
            btn.prop('disabled', false).text('Καταχώρηση');
            if (res.success) {
                bookingModal.hide();
                calendar.refetchEvents();
            } else {
                alert('Error: ' + (res.errors || []).join('\n'));
            }
        });
    });

    // --- PAYMENTS & UI LISTENERS ---
    $('#btnAddManualPayment').click(function () {
        resetPaymentForm();
        $('#addPaymentForm').removeClass('d-none');
        $('#pay_date').val(new Date().toISOString().split('T')[0]);
        const currentStatus = $('#payment_status').val();
        if (currentStatus) $('#pay_booking_status').val(currentStatus);
        $('#pay_amount').focus();
    });

    $('#btnSavePayment').click(function () {
        const btn = $(this);
        const bid = $('#bookingId').val();
        const pid = $('#pay_id').val();
        const amt = $('#pay_amount').val();
        const meth = $('#pay_method').val();
        const dat = $('#pay_date').val();
        const not = $('#pay_note').val();
        const targetStatus = $('#pay_booking_status').val();

        if (!amt || !dat) { alert('Required fields missing'); return; }

        const actionName = pid ? 'updateManualPayment' : 'addManualPayment';
        btn.prop('disabled', true).text('...');

        $.post('includes/admin/ajax.php', {
            action: actionName,
            payment_id: pid,
            booking_id: bid,
            client_id: $('#client_id').val(),
            amount: amt,
            method: meth,
            date: dat,
            note: not,
            manual_status_update: targetStatus,
            csrf_token: getCsrfToken()
        }, function (res) {
            btn.prop('disabled', false);
            if (res.success) {
                resetPaymentForm();
                loadBookingPayments(bid);
                if (res.new_status) {
                    updateStatusUI(res.new_status);
                    calendar.refetchEvents();
                }
            } else {
                alert('Error: ' + res.error);
            }
        }, 'json');
    });

    $(document).on('click', '.edit-payment', function () {
        const id = $(this).data('id');
        $('#pay_id').val(id);
        $('#pay_amount').val($(this).data('amount'));
        $('#pay_method').val($(this).data('method'));
        $('#pay_date').val($(this).data('date'));
        $('#pay_note').val($(this).data('note'));

        let currentStatus = $('#payment_status').val();
        if (currentStatus) {
            $('#pay_booking_status').val(currentStatus.toLowerCase().trim());
        }

        $('#pay_form_title').text('Επεξεργασία #' + id);
        $('#btnSavePayment').html('<i class="bi bi-save me-1"></i>Ενημέρωση');
        $('#addPaymentForm').removeClass('d-none');
    });

    $(document).on('click', '.del-payment', function () {
        const id = $(this).data('id');
        if (!confirm('Delete payment?')) return;
        $.post('includes/admin/ajax.php', { action: 'delPayment', paymentID: id, csrf_token: getCsrfToken() }, function (res) {
            if (res.success) {
                loadBookingPayments($('#bookingId').val());
                if (res.new_status) { updateStatusUI(res.new_status); calendar.refetchEvents(); }
            }
        }, 'json');
    });

    // Quick Client
    $('#btnQuickAddClient').click(function () {
        $('#quickClientForm')[0].reset();
        $('#quickClientModal').modal('show');
    });

    $('#quickClientForm').on('submit', function (e) {
        e.preventDefault();

        // --- FIX: Manual Append επειδή λείπουν τα name attributes στο HTML ---
        const fd = new FormData();
        fd.append('action', 'addClient');
        fd.append('returnID', 1);
        fd.append('csrf_token', getCsrfToken());

        // Παίρνουμε τις τιμές από τα ID
        fd.append('fname', $('#qc_fname').val());
        fd.append('lname', $('#qc_lname').val());
        fd.append('phone', $('#qc_phone').val());
        fd.append('email', $('#qc_email').val());
        fd.append('clientNote', $('#qc_note').val()); // Πρόσεξε το όνομα στο backend (συνήθως clientNote ή notes)

        apiPost(fd).then(res => {
            if (res.success) {
                $('#quickClientModal').modal('hide');
                const newOption = new Option(`${$('#qc_fname').val()} ${$('#qc_lname').val()}`, res.client_id || res.id, true, true);
                $('#client_id').append(newOption).trigger('change');
            } else {
                // Καλό είναι να δείχνουμε και το error αν αποτύχει
                let msg = Array.isArray(res.errors) ? res.errors.join('\n') : (res.errors || 'Error adding client');
                alert(msg);
            }
        });
    });

    // --- SMART RULES: AUTO-CALCULATE END TIME ---

    // 1. Όταν αλλάζει το Πακέτο -> Υπολόγισε το End Time
    $(document).on('change', '.rule-package', function () {
        const tr = $(this).closest('tr');
        const pkgId = $(this).val();
        const startVal = tr.find('.rule-start').val();

        if (pkgId && startVal && availablePackagesList.length > 0) {
            // Βρες το πακέτο στη λίστα για να πάρεις τη διάρκεια
            // (Χρησιμοποιούμε == για loose equality σε περίπτωση string/int)
            const pkg = availablePackagesList.find(p => p.id == pkgId);

            if (pkg && pkg.duration_minutes) {
                const newEnd = addMinutesToTime(startVal, pkg.duration_minutes);
                tr.find('.rule-end').val(newEnd);

                // Προαιρετικό: Ένα οπτικό εφέ ότι άλλαξε
                tr.find('.rule-end').addClass('bg-warning-subtle');
                setTimeout(() => tr.find('.rule-end').removeClass('bg-warning-subtle'), 500);
            }
        }
    });

    // 2. Όταν αλλάζει η Έναρξη (και υπάρχει επιλεγμένο πακέτο) -> Ενημέρωσε το End Time
    $(document).on('change', '.rule-start', function () {
        const tr = $(this).closest('tr');
        const pkgId = tr.find('.rule-package').val();
        const startVal = $(this).val();

        if (pkgId && startVal && availablePackagesList.length > 0) {
            const pkg = availablePackagesList.find(p => p.id == pkgId);

            if (pkg && pkg.duration_minutes) {
                const newEnd = addMinutesToTime(startVal, pkg.duration_minutes);
                tr.find('.rule-end').val(newEnd);
            }
        }
    });


    // Calendar UI Listeners
    therapistSelect?.addEventListener('change', function () {
        calendar.refetchResources();
        calendar.refetchEvents();
    });

    document.getElementById('saveBlockBtn')?.addEventListener('click', async function () {
        const modalEl = document.getElementById('blockModal');
        let startVal = document.getElementById('blockStart').value;
        let endVal = document.getElementById('blockEnd').value;
        if (startVal.length === 16) startVal += ':00';
        if (endVal.length === 16) endVal += ':00';
        await saveBlock(modalEl.dataset.therapistId, startVal, endVal, document.getElementById('blockNotes').value);
        bootstrap.Modal.getInstance(modalEl).hide();
    });

    document.getElementById('saveGroupEventBtn')?.addEventListener('click', async function () {
        const id = document.getElementById('groupPkgId').value;
        const manual = document.getElementById('groupManualBookings').value;
        const fd = new FormData();
        fd.append('action', 'updateManualBookings');
        fd.append('id', id);
        fd.append('manual_bookings', manual);
        const res = await apiPost(fd);
        if (res.success) {
            bootstrap.Modal.getInstance(document.getElementById('groupEventModal')).hide();
            calendar.refetchEvents();
        }
    });

    $(document).on('click', '#btnViewAttendees', function (e) {
        e.preventDefault();
        const pkgId = $('#package_id').val();
        if (pkgId) showAttendees(pkgId);
    });

    $(document).on('click', '#btnViewAttendees2', function (e) {
        e.preventDefault();
        const pkgId = $('#groupPkgId').val();
        if (pkgId) showAttendees(pkgId);
    });

    // Month Summary Logic
    async function fetchMonthSummary(startStr, endStr) {
        const fd = new FormData();
        fd.append('action', 'calendar_getMonthSummaryV2');
        fd.append('start', startStr);
        fd.append('end', endStr);
        const tid = getSelectedTherapistId();
        if (tid !== null) fd.append('therapist_id', tid);

        Object.values(monthCellMap).forEach(el => {
            el.classList.remove('day-available', 'day-partial', 'day-full', 'day-none');
            const av = el.querySelector('.day-avatars');
            if (av) av.innerHTML = '';
        });

        const res = await apiPost(fd);
        if (!res.success) return;
        const data = res.data || {};

        // Need to refetch therapists map if cache is empty
        const tMap = {};
        therapistsCache.forEach(t => {
            tMap[String(t.id ?? t.therapist_id)] = { name: therapistTitle(t), avatar: t.avatar };
        });

        for (const dateStr in data) {
            const cell = monthCellMap[dateStr];
            if (!cell) continue;
            const info = data[dateStr];
            if (info.status === 'available') cell.classList.add('day-available');
            else cell.classList.add('day-none');

            const ids = info.therapist_ids || [];
            const av = cell.querySelector('.day-avatars');
            if (av && ids.length > 0) {
                const limit = 4;
                ids.slice(0, limit).forEach((id, idx) => {
                    const t = tMap[String(id)];
                    if (!t) return;
                    const chip = document.createElement('div');
                    chip.className = 'avatar-chip';
                    chip.style.zIndex = 10 + idx;
                    chip.title = t.name;
                    if (t.avatar) {
                        chip.style.backgroundImage = `url('${t.avatar}')`;
                        chip.classList.add('has-photo');
                    } else {
                        const parts = (t.name || '').split(' ');
                        const ini = (parts[0]?.[0] || '') + (parts[1]?.[0] || '');
                        chip.textContent = (ini || 'T').toUpperCase();
                    }
                    av.appendChild(chip);
                });
                if (ids.length > limit) {
                    const more = document.createElement('div');
                    more.className = 'avatar-chip more';
                    more.textContent = '+' + (ids.length - limit);
                    av.appendChild(more);
                }
            }
        }
    }

    // Weekly Rules & Block Actions (Helpers)
    async function saveBlock(therapistId, startStr, endStr, notes) {
        const fd = new FormData();
        fd.append('action', 'timeBlocks_add');
        fd.append('therapist_id', therapistId);
        fd.append('start_datetime', startStr);
        fd.append('end_datetime', endStr);
        fd.append('kind', 'block');
        fd.append('notes', notes);
        const res = await apiPost(fd);
        if (!res.success) alert((res.errors || []).join('\n'));
        else calendar.refetchEvents();
    }

    async function deleteBlock(id) {
        const fd = new FormData();
        fd.append('action', 'timeBlocks_delete');
        fd.append('id', id);
        const res = await apiPost(fd);
        if (!res.success) alert('Error');
        else calendar.refetchEvents();
    }


    function addMinutesToTime(timeStr, minutes) {
        if (!timeStr) return '';
        let [h, m] = timeStr.split(':').map(Number);

        let date = new Date();
        date.setHours(h);
        date.setMinutes(m + parseInt(minutes));

        // Format back to HH:mm
        let newH = String(date.getHours()).padStart(2, '0');
        let newM = String(date.getMinutes()).padStart(2, '0');
        return `${newH}:${newM}`;
    }



    // --- Weekly Rules Modal Handlers ---
    const weeklyRulesModalEl = document.getElementById('weeklyRulesModal');
    const rulesTbody = document.querySelector('#rulesTable tbody');
    // --- Helper: Generate Rule Row HTML ---
    // --- Helper: Generate Rule Row HTML ---
    function ruleRowHtml(rule = {}) {
        const wd = (rule.weekday ?? 1);
        const st = (rule.start_time ?? '09:00').slice(0, 5);
        const en = (rule.end_time ?? '17:00').slice(0, 5);
        const pid = rule.package_id || ''; // Το ID του πακέτου από τον κανόνα

        const wdays = ['Κυριακή', 'Δευτέρα', 'Τρίτη', 'Τετάρτη', 'Πέμπτη', 'Παρασκευή', 'Σάββατο'];

        // Χτίσιμο επιλογών (Options)
        let pkgOptions = `<option value="" ${!pid ? 'selected' : ''} class="fw-bold text-muted">-- General Availability --</option>`;

        if (Array.isArray(availablePackagesList) && availablePackagesList.length > 0) {
            availablePackagesList.forEach(p => {
                // Χρησιμοποιούμε == (όχι ===) για να πιάσουμε την ισότητα "5" == 5
                let sel = (p.id == pid) ? 'selected' : '';
                pkgOptions += `<option value="${p.id}" ${sel}>${p.title}</option>`;
            });
        }

        return `<tr>
         <td>
            <select class="form-select form-select-sm rule-weekday border-0 bg-light">
               ${wdays.map((d, i) => `<option value="${i}" ${i == wd ? 'selected' : ''}>${d}</option>`).join('')}
            </select>
         </td>
         <td><input type="time" class="form-control form-control-sm rule-start border-0" value="${st}"></td>
         <td><input type="time" class="form-control form-control-sm rule-end border-0" value="${en}"></td>
         <td>
            <select class="form-select form-select-sm rule-package border-0" style="font-size:0.9em;">
               ${pkgOptions}
            </select>
         </td>
         <td class="text-end"><button type="button" class="btn btn-sm text-danger delRuleRowBtn"><i class="bi bi-x-lg"></i></button></td>
        </tr>`;
    }

    // --- OPEN MODAL (Load Rules & Policies) ---
    document.getElementById('openWeeklyRulesBtn')?.addEventListener('click', async function () {
        const tid = getSelectedTherapistId();
        if (tid === null) { alert('Παρακαλώ επιλέξτε Θεραπευτή.'); return; }

        // UI Updates (Loader)
        document.getElementById('weeklyRulesTherapistLabel').textContent = therapistSelect.options[therapistSelect.selectedIndex].text;
        rulesTbody.innerHTML = `<tr><td colspan="5" class="text-center py-4 text-muted"><div class="spinner-border spinner-border-sm me-2"></div> Φόρτωση...</td></tr>`;

        // Reset Inputs
        document.getElementById('policyWindow').value = '';
        document.getElementById('policyNotice').value = '';

        try {
            // 1. Προετοιμασία requests

            // Α. Φόρτωση Πακέτων (για να γεμίσει το dropdown)
            const fdPkg = new FormData();
            fdPkg.append('action', 'getTherapistPackages');
            fdPkg.append('therapist_id', tid);
            fdPkg.append('csrf_token', getCsrfToken());

            // Β. Φόρτωση Κανόνων (Rules)
            const fdRules = new FormData();
            fdRules.append('action', 'availabilityRules_get');
            fdRules.append('therapist_id', tid);
            fdRules.append('csrf_token', getCsrfToken());

            // 2. Εκτέλεση παράλληλα (Promise.all)
            const [resPkg, resRules] = await Promise.all([
                apiPost(fdPkg),
                apiPost(fdRules)
            ]);

            // 3. Ενημέρωση Global λίστας πακέτων
            // Το getTherapistPackages επιστρέφει {success: true, data: [...]}
            availablePackagesList = resPkg.data || [];

            console.log("Packages Loaded:", availablePackagesList); // Για έλεγχο στην κονσόλα

            // 4. Εμφάνιση Κανόνων (Τώρα η ruleRowHtml θα βρει τα πακέτα)
            rulesTbody.innerHTML = '';
            const rules = resRules.data || [];

            if (!rules.length) {
                rulesTbody.insertAdjacentHTML('beforeend', ruleRowHtml({ weekday: 1 }));
            } else {
                rules.forEach(r => rulesTbody.insertAdjacentHTML('beforeend', ruleRowHtml(r)));
            }

            // 5. Εμφάνιση Πολιτικών
            if (resRules.policies) {
                document.getElementById('policyWindow').value = resRules.policies.booking_window_days || 60;
                document.getElementById('policyNotice').value = resRules.policies.min_notice_hours || 12;
            }

            // 6. Άνοιγμα Modal
            new bootstrap.Modal(weeklyRulesModalEl).show();

        } catch (error) {
            console.error(error);
            rulesTbody.innerHTML = `<tr><td colspan="5" class="text-center text-danger">Σφάλμα φόρτωσης δεδομένων.</td></tr>`;
        }
    });

    document.getElementById('addRuleRowBtn')?.addEventListener('click', function () { rulesTbody.insertAdjacentHTML('beforeend', ruleRowHtml({})); });
    rulesTbody?.addEventListener('click', function (e) { if (e.target.closest('.delRuleRowBtn')) e.target.closest('tr').remove(); });

    // --- SAVE ACTION (Save Rules & Policies) ---
    // --- SAVE ACTION (Save Rules & Policies) ---
    document.getElementById('saveWeeklyRulesBtn')?.addEventListener('click', async function () {
        const tid = getSelectedTherapistId();
        if (tid === null) return;

        const btn = this;
        const originalText = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Αποθήκευση...';

        // 1. Prepare Rules Data
        const rows = Array.from(rulesTbody.querySelectorAll('tr'));
        const rules = rows.map(tr => ({
            weekday: parseInt(tr.querySelector('.rule-weekday').value, 10),
            start_time: tr.querySelector('.rule-start').value + ':00',
            end_time: tr.querySelector('.rule-end').value + ':00',

            // --- ΔΙΑΒΑΖΟΥΜΕ ΤΟ ΠΑΚΕΤΟ ---
            package_id: tr.querySelector('.rule-package').value || null,

            is_active: 1
        }));

        const fdRules = new FormData();
        fdRules.append('action', 'availabilityRules_saveBulk');
        fdRules.append('therapist_id', tid);
        fdRules.append('rules_json', JSON.stringify(rules));
        fdRules.append('csrf_token', getCsrfToken());

        // 2. Prepare Policies Data
        const fdPolicies = new FormData();
        fdPolicies.append('action', 'saveTherapistPolicies');
        fdPolicies.append('therapist_id', tid);
        fdPolicies.append('booking_window_days', document.getElementById('policyWindow').value);
        fdPolicies.append('min_notice_hours', document.getElementById('policyNotice').value);
        fdPolicies.append('csrf_token', getCsrfToken());

        try {
            const [resRules, resPolicies] = await Promise.all([
                apiPost(fdRules),
                apiPost(fdPolicies)
            ]);

            if (resRules.success && resPolicies.success) {
                bootstrap.Modal.getInstance(weeklyRulesModalEl).hide();
                calendar.refetchEvents();
            } else {
                alert('Σφάλμα αποθήκευσης.');
            }
        } catch (e) {
            console.error(e);
        } finally {
            btn.disabled = false;
            btn.innerHTML = originalText;
        }
    });

    // --- BLOCK MODE TOGGLE ---
    $('#toggleBlockModeBtn').click(function () {
        isBlockMode = !isBlockMode;
        const btn = $(this);
        const txt = $('#blockModeText');

        if (isBlockMode) {
            btn.removeClass('btn-outline-danger').addClass('btn-danger text-white');
            txt.text('Block Mode: ON');
            // Προαιρετικά: Αλλάζουμε τον κέρσορα στο calendar για να δείχνει ότι κάτι άλλαξε
            $('.fc-view-harness').css('cursor', 'no-drop');
        } else {
            btn.addClass('btn-outline-danger').removeClass('btn-danger text-white');
            txt.text('Block Mode: OFF');
            $('.fc-view-harness').css('cursor', 'default');
        }
    });


    // Start
    (async function init() {
        await fetchTherapists();
        await loadTherapistsIntoSelect();
        calendar.refetchResources();
        calendar.refetchEvents();
    })();

});
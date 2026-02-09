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

// Μετατροπή JS Date σε string για input type="datetime-local" (YYYY-MM-DDTHH:mm)
function formatDateToInput(date) {
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
            pkgSelect.val(preSelectPkgId).trigger('change');
            if (savedDates && savedDates.start) {
                setTimeout(() => {
                    $('#start_datetime').val(savedDates.start);
                    $('#end_datetime').val(savedDates.end);
                    const datePart = savedDates.start.split('T')[0];
                    $('#slot_date_picker').val(datePart);

                    // Show slots logic only if not group
                    const selectedOpt = pkgSelect.find('option:selected');
                    const isGroup = selectedOpt.data('is-group') == 1;
                    if (!isGroup) {
                        const duration = selectedOpt.data('duration') || 60;
                        $('#package_duration').val(duration);
                        fetchAvailableSlots(tid, datePart, duration);
                    }
                }, 100);
            }
        }
    }, 'json');
}

// ===== ΔΙΟΡΘΩΜΕΝΗ ΣΥΝΑΡΤΗΣΗ ΓΙΑ ΤΑ SLOTS =====
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
                // ΕΛΕΓΧΟΣ: Αν είναι Object {start:..., available_spots:...} ή απλό String
                let timeStr = '';
                let spotsBadge = '';

                if (typeof slotItem === 'object') {
                    // Παίρνουμε την ώρα (10:00) από το ISO string
                    timeStr = slotItem.start.substring(11, 16);
                    // Δείχνουμε τις θέσεις αν είναι > 1
                    if (slotItem.available_spots > 1) {
                        spotsBadge = ` <small style="font-size:0.75em;">(${slotItem.available_spots})</small>`;
                    }
                } else {
                    // Fallback για παλιά δεδομένα
                    timeStr = slotItem;
                }

                html += `<button type="button" class="btn btn-outline-primary btn-sm time-slot-btn m-1" style="min-width: 70px;">
                            ${timeStr}${spotsBadge}
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

    async function loadTherapistsIntoSelect() {
        if (!therapistSelect) return;
        therapistSelect.innerHTML = '<option value="">-- Όλοι --</option>';
        therapistsCache.forEach(t => {
            const id = t.id ?? t.therapist_id;
            therapistSelect.insertAdjacentHTML('beforeend', `<option value="${id}">${therapistTitle(t)}</option>`);
        });
    }

    // -----------------------------------------------------------
    // CALENDAR INITIALIZATION
    // -----------------------------------------------------------
    calendar = new FullCalendar.Calendar(calendarEl, {
        locale: 'el',
        timeZone: 'local',
        initialView: 'resourceTimeGridWeek',
        headerToolbar: {
            left: 'today prev,next',
            center: 'title',
            right: 'dayGridMonth,resourceTimeGridWeek,resourceTimeGridDay'
        },
        height: 'auto',
        expandRows: true,
        resourceAreaWidth: '220px',
        resourceAreaHeaderContent: 'Θεραπευτές',
        stickyHeaderDates: true,
        slotMinTime: '07:00:00',
        slotMaxTime: '23:00:00',
        slotDuration: '00:30:00',
        allDaySlot: false,
        selectable: true,
        editable: false,
        nowIndicator: true,

        dayCellDidMount: function (info) {
            if (info.view.type === 'dayGridMonth') {
                const dateStr = info.date.toISOString().slice(0, 10);
                monthCellMap[dateStr] = info.el;
                info.el.style.position = 'relative';
                if (!info.el.querySelector('.day-avatars')) {
                    const wrap = document.createElement('div');
                    wrap.className = 'day-avatars';
                    info.el.appendChild(wrap);
                }
            }
        },

        datesSet: function (info) {
            currentViewType = info.view.type;
            if (currentViewType === 'dayGridMonth') {
                document.querySelectorAll('.day-avatars').forEach(el => el.innerHTML = '');
                fetchMonthSummary(info.startStr, info.endStr);
            }
        },

        resources: function (fetchInfo, successCallback) {
            if (therapistsCache.length === 0) {
                fetchTherapists().then(() => {
                    successCallback(buildResourcesFromCache(getSelectedTherapistId()));
                });
            } else {
                successCallback(buildResourcesFromCache(getSelectedTherapistId()));
            }
        },

        // --- EVENTS FETCH ---
        events: async function (fetchInfo, successCallback, failureCallback) {
            if (fetchAbortController) fetchAbortController.abort();
            fetchAbortController = new AbortController();
            const signal = fetchAbortController.signal;

            try {
                const fd = new FormData();
                fd.append('action', 'calendar_getDataV2');
                fd.append('start', fetchInfo.startStr);
                fd.append('end', fetchInfo.endStr);

                const isResourceView = (currentViewType.includes('resource'));
                fd.append('include_availability', isResourceView ? '1' : '0');

                const selectedTid = getSelectedTherapistId();
                if (selectedTid !== null) fd.append('therapist_id', selectedTid);

                const res = await apiPost(fd, signal);

                if (!res.success) { failureCallback([]); return; }

                const processedGroupsInMonth = new Set();

                const events = (res.data || []).map(item => {
                    const source = item.source;
                    const isBg = (source === 'availability_bg' || item.display === 'background');

                    if (currentViewType === 'dayGridMonth' && isBg) return null;
                    if (selectedTid !== null && item.therapist_id == null && !isBg) return null;

                    // De-duplicate groups in month view
                    if (currentViewType === 'dayGridMonth' && source === 'group_event') {
                        const m = String(item.id).match(/^grp_(\d+)/);
                        const pkgId = m ? m[1] : item.id;
                        if (processedGroupsInMonth.has(pkgId)) return null;
                        processedGroupsInMonth.add(pkgId);
                    }

                    const startIso = item.start_datetime ? item.start_datetime.replace(' ', 'T') : null;
                    const endIso = item.end_datetime ? item.end_datetime.replace(' ', 'T') : null;
                    const resourceId = (item.therapist_id == null) ? 'unassigned' : String(item.therapist_id);

                    const classNames = [];
                    if (source === 'group_event') classNames.push('bg-group-event');
                    if (source === 'block') classNames.push('bg-block');
                    if (source === 'booking') classNames.push('bg-booked');
                    if (source === 'availability_bg') classNames.push('bg-availability');

                    // Buffer Styling
                    if (source === 'buffer') classNames.push('bg-light', 'text-muted', 'small', 'fst-italic', 'opacity-75');

                    return {
                        id: item.id,
                        start: startIso,
                        end: endIso,
                        title: item.title || '',
                        classNames: classNames,
                        display: item.display || (isBg ? 'background' : 'auto'),
                        resourceId: resourceId,
                        backgroundColor: (source === 'buffer') ? '#e9ecef' : undefined,
                        borderColor: (source === 'buffer') ? '#dee2e6' : undefined,
                        extendedProps: {
                            source,
                            isGroup: item.is_group ? 1 : 0,
                            notes: item.notes,
                            therapistId: item.therapist_id,
                            appointment_type: item.appointment_type
                        }
                    };
                }).filter(Boolean);

                successCallback(events);

            } catch (err) { if (err.name !== 'AbortError') failureCallback([]); }
        },

        // --- EVENT CONTENT ---
        eventContent: function (arg) {
            if (arg.event.extendedProps.source === 'buffer') {
                return { html: '<div class="fc-event-main-frame h-100 d-flex align-items-center justify-content-center"><i class="bi bi-hourglass-split text-secondary"></i></div>' };
            }
        },
        eventDidMount: function (info) {
            const p = info.event.extendedProps;
            const titleEl = info.el.querySelector('.fc-event-title');

            if (titleEl && p.source !== 'buffer') {
                if (p.source === 'group_event') {
                    titleEl.innerHTML = `<i class="bi bi-people-fill me-1"></i> ${info.event.title}`;
                }
                if (p.source === 'block') {
                    // --- ENHANCED BLOCK DISPLAY ---
                    let html = `<i class="bi bi-slash-circle me-1"></i> Block`;
                    // Αν υπάρχει σημείωση, την προσθέτουμε
                    if (p.notes) {
                        html += ` <span class="fw-normal opacity-75">(${escapeHtml(p.notes)})</span>`;
                    }
                    titleEl.innerHTML = html;

                    // Add Tooltip for long notes (optional but helpful)
                    titleEl.title = p.notes || 'Block';
                }
                if (p.source === 'booking') {
                    // Default Icon (Generic)
                    let icon = '<i class="bi bi-person-check-fill me-1"></i>';

                    // Logic based on Appointment Type (passed from backend)
                    // Ensure your backend 'calendar_getDataV2' returns 'appointment_type' in extendedProps!
                    const type = p.appointment_type; // e.g. 'online' or 'inPerson'

                    if (type === 'online') {
                        icon = '<i class="bi bi-camera-video-fill me-1 text-white"></i>'; // Video Icon
                    } else if (type === 'inPerson') {
                        icon = '<i class="bi bi-geo-alt-fill me-1 text-white"></i>'; // Location Icon
                    }

                    titleEl.innerHTML = `${icon} ${info.event.title}`;
                }
            }
        },

        // --- SELECT: ADD NEW ---
        select: function (info) {
            const resObj = info.resource;
            const therapistId = resObj ? resObj.id : getSelectedTherapistId();

            // 1. ΑΝ ΕΙΝΑΙ BLOCK MODE -> ΑΝΟΙΓΕ BLOCK MODAL
            if (isBlockMode) {
                if (!therapistId || therapistId === 'unassigned') {
                    alert('Παρακαλώ επιλέξτε συγκεκριμένο θεραπευτή (στήλη ή φίλτρο) για να βάλετε Block.');
                    calendar.unselect();
                    return;
                }

                // Γέμισμα του Block Modal
                const blockModalEl = document.getElementById('blockModal');
                // Αποθηκεύουμε τον θεραπευτή στο dataset
                blockModalEl.dataset.therapistId = therapistId;

                // Εμφάνιση ονόματος
                const tName = resObj ? resObj.title : $('#therapistSelect option:selected').text();
                document.getElementById('blockTherapistLabel').textContent = tName;

                // Ημερομηνίες (τις κόβουμε για να μην έχουν T)
                // To input type="text" του block θέλει YYYY-MM-DD HH:mm:ss ή παρόμοιο. 
                // Εδώ βάζουμε local string για ευκολία ή formatISO.
                // Ας χρησιμοποιήσουμε το helper formatDateToInput που φτιάξαμε, αλλά με replace 'T' -> ' ' για ομορφιά
                document.getElementById('blockStart').value = formatDateToInput(info.start).replace('T', ' ');
                document.getElementById('blockEnd').value = formatDateToInput(info.end).replace('T', ' ');

                document.getElementById('blockNotes').value = ''; // Καθαρισμός

                const blModal = new bootstrap.Modal(blockModalEl);
                blModal.show();
                calendar.unselect();
                return; // Σταματάμε εδώ, δεν ανοίγουμε booking
            }

            // 2. ΑΛΛΙΩΣ -> ΚΑΝΟΝΙΚΗ ΚΡΑΤΗΣΗ (Ο παλιός κώδικας)
            resetForm();
            $('#bookingModalTitle').text('Νέα Κράτηση');

            $('#start_datetime').val(formatDateToInput(info.start));
            $('#end_datetime').val(formatDateToInput(info.end));

            const datePart = info.startStr.split('T')[0];
            $('#slot_date_picker').val(datePart);

            if (therapistId && therapistId !== 'unassigned') {
                setTimeout(() => { $('#therapist_id').val(therapistId).trigger('change'); }, 100);
            }

            $('#tab-payments').prop('disabled', true);
            $('#paymentsList').empty();
            $('#pay_total').text('€0.00');

            // Show details tab
            const triggerEl = document.querySelector('#tab-details');
            if (triggerEl) bootstrap.Tab.getOrCreateInstance(triggerEl).show();

            bookingModal.show();
            calendar.unselect();
        },

        // --- EVENT CLICK ---
        eventClick: function (info) {
            const p = info.event.extendedProps;

            if (p.source === 'group_event') {
                const m = String(info.event.id).match(/^grp_(\d+)/);
                const pkgId = m ? parseInt(m[1], 10) : 0;
                if (pkgId) openGroupModal(pkgId);
            } else if (p.source === 'block') {
                const realId = String(info.event.id).replace('bl_', '');
                if (confirm('Διαγραφή block;')) deleteBlock(realId);
            } else if (p.source === 'booking') {
                const bookingId = info.event.id;
                const cleanId = String(bookingId).replace('bk_', '');
                openBookingEditModal(cleanId);
            }
        }
    });

    calendar.render();

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
                if (!$('#slot_date_picker').val()) {
                    $('#slots_container').html('<div class="text-center my-3"><div class="spinner-border spinner-border-sm text-primary"></div><div class="small text-muted mt-1">Αναζήτηση διαθεσιμότητας...</div></div>');

                    $.post('includes/admin/ajax.php', {
                        action: 'findFirstAvailableDate',
                        therapist_id: tid,
                        package_id: pkgId,
                        duration: duration,
                        csrf_token: getCsrfToken()
                    }, function (res) {
                        if (res.success && res.date) {
                            // Βρέθηκε! Βάζουμε την ημερομηνία και ενεργοποιούμε το change για να φέρει τα slots
                            $('#slot_date_picker').val(res.date).trigger('change');
                        } else {
                            $('#slots_container').html('<div class="text-danger small fw-bold mt-2 text-center">Δεν βρέθηκε διαθεσιμότητα σύντομα.</div>');
                        }
                    }, 'json');
                } else {
                    // Αν έχει ήδη ημερομηνία, απλά ανανέωσε τα slots
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

    // --- Weekly Rules Modal Handlers ---
    const weeklyRulesModalEl = document.getElementById('weeklyRulesModal');
    const rulesTbody = document.querySelector('#rulesTable tbody');
    function ruleRowHtml(rule = {}) {
        const wd = (rule.weekday ?? 1);
        const st = (rule.start_time ?? '10:00:00').slice(0, 5);
        const en = (rule.end_time ?? '14:00:00').slice(0, 5);
        const at = rule.appointment_type ?? '';
        const wdays = ['Κυριακή', 'Δευτέρα', 'Τρίτη', 'Τετάρτη', 'Πέμπτη', 'Παρασκευή', 'Σάββατο'];
        return `<tr>
         <td><select class="form-select form-select-sm rule-weekday">
           ${wdays.map((d, i) => `<option value="${i}" ${i == wd ? 'selected' : ''}>${d}</option>`).join('')}
         </select></td>
         <td><input type="time" class="form-control form-control-sm rule-start" value="${st}"></td>
         <td><input type="time" class="form-control form-control-sm rule-end" value="${en}"></td>
         <td><select class="form-select form-select-sm rule-type">
           <option value="" ${at == '' ? 'selected' : ''}>(όλα)</option>
           <option value="inPerson" ${at == 'inPerson' ? 'selected' : ''}>inPerson</option>
           <option value="online" ${at == 'online' ? 'selected' : ''}>online</option>
           <option value="mixed" ${at == 'mixed' ? 'selected' : ''}>mixed</option>
         </select></td>
         <td class="text-end"><button type="button" class="btn btn-sm btn-outline-danger delRuleRowBtn"><i class="bi bi-trash"></i></button></td>
        </tr>`;
    }

    // --- OPEN MODAL (Load Rules & Policies) ---
    document.getElementById('openWeeklyRulesBtn')?.addEventListener('click', async function () {
        const tid = getSelectedTherapistId();
        if (tid === null) { alert('Παρακαλώ επιλέξτε Θεραπευτή.'); return; }

        // UI Updates
        document.getElementById('weeklyRulesTherapistLabel').textContent = therapistSelect.options[therapistSelect.selectedIndex].text;
        rulesTbody.innerHTML = `<tr><td colspan="5" class="text-center py-4 text-muted"><div class="spinner-border spinner-border-sm me-2"></div> Φόρτωση...</td></tr>`;

        // Reset Inputs
        document.getElementById('policyWindow').value = '';
        document.getElementById('policyNotice').value = '';

        // Fetch Data
        const fd = new FormData();
        fd.append('action', 'availabilityRules_get');
        fd.append('therapist_id', tid);

        const res = await apiPost(fd);

        // 1. Populate Table (Rules)
        rulesTbody.innerHTML = '';
        const rules = res.data || [];
        if (!rules.length) {
            rulesTbody.insertAdjacentHTML('beforeend', ruleRowHtml({ weekday: 1 }));
        } else {
            rules.forEach(r => rulesTbody.insertAdjacentHTML('beforeend', ruleRowHtml(r)));
        }

        // 2. Populate Policies (New)
        if (res.policies) {
            document.getElementById('policyWindow').value = res.policies.booking_window_days || 60;
            document.getElementById('policyNotice').value = res.policies.min_notice_hours || 12;
        }

        // Show Modal
        new bootstrap.Modal(weeklyRulesModalEl).show();
    });

    document.getElementById('addRuleRowBtn')?.addEventListener('click', function () { rulesTbody.insertAdjacentHTML('beforeend', ruleRowHtml({})); });
    rulesTbody?.addEventListener('click', function (e) { if (e.target.closest('.delRuleRowBtn')) e.target.closest('tr').remove(); });

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
            appointment_type: tr.querySelector('.rule-type').value || null,
            is_active: 1
        }));

        const fdRules = new FormData();
        fdRules.append('action', 'availabilityRules_saveBulk');
        fdRules.append('therapist_id', tid);
        fdRules.append('rules_json', JSON.stringify(rules));

        // 2. Prepare Policies Data
        const fdPolicies = new FormData();
        fdPolicies.append('action', 'saveTherapistPolicies');
        fdPolicies.append('therapist_id', tid);
        fdPolicies.append('booking_window_days', document.getElementById('policyWindow').value);
        fdPolicies.append('min_notice_hours', document.getElementById('policyNotice').value);

        try {
            // Send both requests in parallel
            const [resRules, resPolicies] = await Promise.all([
                apiPost(fdRules),
                apiPost(fdPolicies)
            ]);

            if (resRules.success && resPolicies.success) {
                bootstrap.Modal.getInstance(weeklyRulesModalEl).hide();
                calendar.refetchEvents();
                // Optional: Show success toast
            } else {
                let errorMsg = 'Σφάλμα κατά την αποθήκευση:\n';
                if (resRules.errors) errorMsg += 'Ωράριο: ' + resRules.errors.join(', ') + '\n';
                if (resPolicies.errors) errorMsg += 'Πολιτικές: ' + resPolicies.errors.join(', ');
                alert(errorMsg);
            }
        } catch (e) {
            console.error(e);
            alert('Σφάλμα επικοινωνίας.');
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
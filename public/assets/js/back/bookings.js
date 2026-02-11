let bookingsTable;
const bookingModalEl = document.getElementById('bookingModal');
const bookingModal = new bootstrap.Modal(bookingModalEl);

document.addEventListener("DOMContentLoaded", function () {
    initDataTable();
    initSelect2();
    loadTherapists();
    setupSmartBookingFlow();
    setupActionListeners();
});

// ============================================================
// 1. DATA TABLE
// ============================================================
function initDataTable() {
    bookingsTable = $("#bookings_table").DataTable({
        paging: true,
        responsive: true,
        processing: true,
        autoWidth: false,
        lengthMenu: [[25, 50, 100, -1], [25, 50, 100, "Όλα"]],
        dom: "rtip",
        order: [[3, "desc"]],

        ajax: {
            url: "includes/admin/ajax.php",
            type: "POST",
            data: {
                action: "fetchJoinedBookings",
                csrf_token: getCsrfToken()
            },
            dataSrc: function (res) {
                if (!res || res.errors) {
                    console.error("Fetch Error:", res);
                    return [];
                }
                return res.data || [];
            }
        },
        columns: [
            { data: "booking_id", visible: false },
            {
                data: "client_fname",
                title: "Πελάτης",
                render: function (data, type, row) {
                    const full = escapeHtml(`${row.client_fname} ${row.client_lname}`);
                    let paxBadge = '';
                    if (row.attendees_count > 1) {
                        paxBadge = ` <span class="badge bg-info text-dark border" title="Σύνολο άτομα: ${row.attendees_count}">+${row.attendees_count - 1}</span>`;
                    }
                    return `<div class="fw-bold text-primary">${full}${paxBadge}</div><small class="text-muted">${row.client_phone || ''}</small>`;
                }
            },
            {
                data: "therapist_fname",
                title: "Guide",
                render: function (data, type, row) {
                    return row.therapist_fname ? escapeHtml(`${row.therapist_fname} ${row.therapist_lname}`) : '-';
                }
            },
            {
                data: "start_datetime",
                title: "Έναρξη",
                render: function (data) { return formatDisplayDate(data); }
            },
            {
                data: "end_datetime",
                title: "Λήξη",
                render: function (data) { return formatDisplayDate(data); }
            },
            {
                data: "package_title",
                title: "Διαδρομή",
                render: function (data, type, row) {
                    let typeBadge = '';
                    if (row.appointment_type === 'online') {
                        typeBadge = `<span class="badge bg-primary-subtle text-primary border border-primary-subtle me-1"><i class="bi bi-camera-video-fill"></i></span>`;
                    } else {
                        typeBadge = `<span class="badge bg-warning-subtle text-dark border border-warning-subtle me-1"><i class="bi bi-geo-alt-fill"></i></span>`;
                    }
                    const pkgName = data ? `<span class="small fw-bold">${escapeHtml(data)}</span>` : '<span class="text-muted">-</span>';
                    return `<div class="d-flex align-items-center">${typeBadge} ${pkgName}</div>`;
                }
            },
            {
                data: "booking_status",
                title: "Status",
                render: function (data) {
                    let color = 'secondary';
                    if (data === 'booked' || data === 'confirmed') color = 'success';
                    if (data === 'canceled') color = 'danger';
                    if (data === 'completed') color = 'primary';

                    const map = {
                        booked: 'Κρατημένο',
                        confirmed: 'Επιβεβαιωμένο',
                        canceled: 'Ακυρωμένο',
                        completed: 'Ολοκληρωμένο'
                    };

                    const label = map[data] || data || '-';
                    return `<span class="badge bg-${color}">${label}</span>`;
                }
            },
            {
                data: null,
                title: "",
                orderable: false,
                className: "text-end",
                render: function (data, type, row) {
                    return `
                        <button class="btn btn-sm btn-light text-primary edit-booking" data-id="${row.booking_id}"><i class="bi bi-pencil-square"></i></button>
                        <button class="btn btn-sm btn-light text-danger del-booking" data-id="${row.booking_id}"><i class="bi bi-trash"></i></button>
                    `;
                }
            }
        ]
    });

    $('#searchBookings').on('keyup', function () { bookingsTable.search(this.value).draw(); });
}

// ============================================================
// 2. SELECT2
// ============================================================
function initSelect2() {
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
}

// ============================================================
// 3. SMART FLOW
// ============================================================
function loadTherapists() {
    $.post('includes/admin/ajax.php', { action: 'fetchTherapistsData', csrf_token: getCsrfToken() }, function (res) {
        let html = '<option value="">-- Επιλογή --</option>';
        const list = Array.isArray(res) ? res : (res.data || []);
        list.forEach(t => {
            const name = t.first_name ? `${t.first_name} ${t.last_name}` : t.email;
            html += `<option value="${t.id}">${escapeHtml(name)}</option>`;
        });
        $('#therapist_id').html(html);
    }, 'json');
}

function loadPackagesForTherapist(tid, preSelectPkgId = null, savedDates = null) {
    const pkgSelect = $('#package_id');
    $('#slots_container').html('<span class="text-muted small mx-auto">Επιλέξτε Πακέτο</span>');
    pkgSelect.html('<option value="">Φόρτωση...</option>').prop('disabled', true);

    if (!tid) { pkgSelect.html('<option value="">(Επιλέξτε πρώτα Guide)</option>'); return; }

    $.post('includes/admin/ajax.php', {
        action: 'getTherapistPackages',
        therapist_id: tid,
        csrf_token: getCsrfToken()
    }, function (res) {
        let html = '<option value="">-- Επιλογή Διαδρομής --</option>';
        html += '<option value="custom" data-is-group="0" data-duration="60">Custom Run</option>';

        if (res.success && res.data) {
            res.data.forEach(p => {
                let label = p.title;
                if (p.is_group == 1 && p.start_datetime) {
                    const d = new Date(p.start_datetime.replace(' ', 'T'));
                    const dateStr = d.toLocaleDateString('el-GR', { day: 'numeric', month: 'short' });
                    label += ` (Event: ${dateStr})`;
                } else {
                    label += ` (${p.duration_minutes}')`;
                }
                // Store metadata in data attributes
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

            // Αν έχουμε ήδη αποθηκευμένες ημερομηνίες (περίπτωση Edit)
            if (savedDates && savedDates.start && savedDates.end) {
                setTimeout(() => {
                    $('#start_datetime').val(savedDates.start);
                    $('#end_datetime').val(savedDates.end);
                    const datePart = savedDates.start.split('T')[0];
                    $('#slot_date_picker').val(datePart);

                    const selectedOpt = pkgSelect.find('option:selected');
                    const duration = selectedOpt.data('duration') || 60;
                    const isGroup = selectedOpt.data('is-group') == 1;
                    const pkgId = selectedOpt.val();

                    if (!isGroup) {
                        $('#package_duration').val(duration);
                        fetchAvailableSlots(tid, datePart, duration, pkgId);
                    }
                }, 100);
            }
        }
    }, 'json');
}

function setupSmartBookingFlow() {
    $('#therapist_id').on('change', function () { loadPackagesForTherapist($(this).val()); });

    $('#package_id').on('change', function (e) {
        const selectedOpt = $(this).find('option:selected');
        const isGroup = selectedOpt.data('is-group') == 1;
        const duration = selectedOpt.data('duration') || 60;
        const price = selectedOpt.data('price');
        const pkgType = selectedOpt.data('type');

        const tid = $('#therapist_id').val();
        const pkgId = $(this).val();

        const isUserChange = !!e.originalEvent;
        if (isUserChange) {
            $('#start_datetime').val('');
            $('#end_datetime').val('');
            $('.time-slot-btn').removeClass('btn-primary text-white').addClass('btn-outline-primary');
            $('#btnSlotAttendees')
                .prop('disabled', true)
                .addClass('btn-outline-secondary')
                .removeClass('btn-outline-primary');

            // μόνο αν ΔΕΝ έχει ημερομηνία, τότε άστο άδειο ώστε να τρέξει auto-find
            if (!$('#slot_date_picker').val()) {
                $('#slots_container').html('<span class="text-muted small mx-auto">Αναζήτηση διαθεσιμότητας...</span>');
            }
        }

        if (price) $('#booking_price').val(price);
        $('#package_duration').val(duration);

        const typeSelect = $('#appointment_type');
        typeSelect.find('option').prop('disabled', false);

        if (pkgType === 'online') {
            typeSelect.val('online');
            typeSelect.find('option[value="inPerson"]').prop('disabled', true);
        } else if (pkgType === 'inPerson') {
            typeSelect.val('inPerson');
            typeSelect.find('option[value="online"]').prop('disabled', true);
        } else {
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

            // ✅ AUTO-FIND DATE LOGIC (θα τρέξει πλέον και μετά από αλλαγή πακέτου, γιατί το date καθαρίστηκε)
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
                        $('#slot_date_picker').val(res.date).trigger('change');
                    } else {
                        $('#slots_container').html('<div class="text-danger small fw-bold mt-2 text-center">Δεν βρέθηκε διαθεσιμότητα.</div>');
                    }
                }, 'json');
            } else {
                $('#slot_date_picker').trigger('change');
            }
        }
    });


    $('#slot_date_picker').on('change', function () {
        const tid = $('#therapist_id').val();
        const pid = $('#package_id').val();
        const date = $(this).val();
        const duration = parseInt($('#package_duration').val()) || 60;

        if (tid && date && !$('#slot_picker_panel').hasClass('d-none')) {
            fetchAvailableSlots(tid, date, duration, pid);
        }
    });

    $(document).on('click', '.time-slot-btn', function () {
        $('.time-slot-btn').removeClass('btn-primary text-white').addClass('btn-outline-primary');
        $(this).removeClass('btn-outline-primary').addClass('btn-primary text-white');

        // Parse time: Παίρνουμε το πρώτο κομμάτι πριν το κενό για να αγνοήσουμε το (15)
        const time = $(this).text().split(' ')[0].trim();

        const date = $('#slot_date_picker').val();
        const duration = parseInt($('#package_duration').val()) || 60;
        const startObj = new Date(`${date}T${time}:00`);
        const endObj = new Date(startObj.getTime() + duration * 60000);
        $('#start_datetime').val(formatDateToInput(startObj));
        $('#end_datetime').val(formatDateToInput(endObj));

        $('#btnSlotAttendees').prop('disabled', false).removeClass('btn-outline-secondary').addClass('btn-outline-primary');
    });
}

// ===== ΔΙΟΡΘΩΜΕΝΗ FETCH SLOTS ΓΙΑ BOOKING.JS =====
function fetchAvailableSlots(tid, date, duration, packageId) {
    const container = $('#slots_container');
    const loader = $('#slots_loader');
    container.addClass('opacity-50');
    // Ensure loader exists
    if (container.find('.spinner-border').length === 0) {
        container.html('<div class="spinner-border spinner-border-sm text-secondary"></div>');
    }

    $.post('includes/admin/ajax.php', {
        action: 'getAvailableSlots',
        therapist_id: tid,
        package_id: packageId,
        date: date,
        duration: duration,
        csrf_token: getCsrfToken()
    }, function (res) {
        container.removeClass('opacity-50');

        if (res.success && res.slots && res.slots.length > 0) {
            let html = '';
            res.slots.forEach(slotItem => {
                let timeStr = '';
                let spotsBadge = '';

                // --- FIX: Έλεγχος Object vs String ---
                if (typeof slotItem === 'object') {
                    // Extract HH:mm from YYYY-MM-DD HH:mm:ss
                    timeStr = slotItem.start.substring(11, 16);
                    if (slotItem.available_spots > 1) {
                        spotsBadge = ` <span style="font-size:0.75em; opacity:0.8">(${slotItem.available_spots})</span>`;
                    }
                } else {
                    timeStr = slotItem; // Fallback
                }

                html += `<button type="button" class="btn btn-outline-primary btn-sm time-slot-btn m-1" style="width: auto; min-width: 70px;">
                            ${timeStr}${spotsBadge}
                         </button>`;
            });
            container.html(html);
        } else {
            container.html('<span class="text-danger small mx-auto fw-bold"><i class="bi bi-x-circle"></i> Δεν υπάρχουν κενά</span>');
        }
    }, 'json').fail(function () {
        container.html('<span class="text-danger small">Error connecting.</span>');
    });
}

// --- PAYMENT FUNCTIONS ---
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
                const rawDate = p.payed_at || p.created_at;
                const dateOnly = rawDate ? rawDate.split(' ')[0] : '';
                const d = rawDate ? new Date(rawDate.replace(' ', 'T')) : new Date();

                list.append(`
                    <tr>
                        <td>${d.toLocaleDateString('el-GR')}</td>
                        <td class="fw-bold text-success">€${amount.toFixed(2)}</td>
                        <td><span class="badge bg-light text-dark border">${p.payment_method}</span></td>
                        <td class="small text-muted">${p.note || '-'}</td>
                        <td class="text-end">
                            <button class="btn btn-xs text-primary edit-payment me-1" 
                                data-id="${p.id}" data-amount="${amount}" data-method="${p.payment_method}" 
                                data-date="${dateOnly}" data-note="${p.note || ''}"><i class="bi bi-pencil-square"></i></button>
                            <button class="btn btn-xs text-danger del-payment" data-id="${p.id}"><i class="bi bi-trash"></i></button>
                        </td>
                    </tr>
                `);
            });
        } else {
            addBtn.removeClass('d-none');
            list.html('<tr><td colspan="5" class="text-center text-muted small">Καμία πληρωμή ακόμα.</td></tr>');
        }
        $('#pay_total').text('€' + total.toFixed(2));
    }, 'json');
}

// --- ATTENDEES FUNCTION ---
function showAttendees(packageId, slotDate = null) {
    const modalEl = document.getElementById('attendeesModal');
    const modal = new bootstrap.Modal(modalEl);
    const list = document.getElementById('attendeesList');
    const loader = document.getElementById('attendeesLoader');
    const noMsg = document.getElementById('noAttendeesMsg');

    const reqData = {
        action: 'fetchGroupAttendees',
        package_id: packageId,
        csrf_token: getCsrfToken()
    };
    if (slotDate) reqData.date = slotDate;

    list.innerHTML = '';
    loader.classList.remove('d-none');
    noMsg.classList.add('d-none');
    modal.show();

    $.post('includes/admin/ajax.php', reqData, function (res) {
        loader.classList.add('d-none');
        let hasData = false;

        if (res.data && res.data.length > 0) {
            hasData = true;
            res.data.forEach(row => {
                const statusBadge = row.payment_status === 'paid'
                    ? '<span class="badge bg-success">Paid</span>'
                    : '<span class="badge bg-warning text-dark">Unpaid</span>';

                let paxBadge = '';
                if (row.attendees_count > 1) {
                    paxBadge = ` <span class="badge bg-info text-dark border">+${row.attendees_count - 1}</span>`;
                }

                list.insertAdjacentHTML('beforeend', `
                    <tr>
                        <td class="fw-bold">${escapeHtml(row.first_name + ' ' + row.last_name)}${paxBadge}</td>
                        <td><a href="tel:${row.phone}" class="text-decoration-none text-body">${row.phone || '-'}</a></td>
                        <td>${statusBadge}</td>
                    </tr>
                `);
            });
        }
        if (res.manual_count > 0) {
            hasData = true;
            list.insertAdjacentHTML('beforeend', `<tr class="table-warning"><td colspan="3"><strong>${res.manual_count}</strong> Manual/Offline Runners</td></tr>`);
        }
        if (!hasData) noMsg.classList.remove('d-none');
    }, 'json');
}


// ============================================================
// 4. CRUD LISTENERS
// ============================================================

function setupActionListeners() {
    $('#addNewBooking').click(function () {
        resetForm();
        resetPanels();
        $('#attendees_count').val(1);
        $('#bookingModalTitle').text('Νέα Κράτηση');
        // document.getElementById('slot_date_picker').valueAsDate = new Date(); // Don't set date, let auto-find do it
        $('#slots_container').html('<span class="text-muted small mx-auto">Επιλέξτε Guide</span>');
        $('#tab-payments').prop('disabled', true);
        new bootstrap.Tab(document.querySelector('#tab-details')).show();
        bookingModal.show();
    });

    // --- SLOT LIST BUTTON LISTENER ---
    $('#btnSlotAttendees').click(function () {
        const pkgId = $('#package_id').val();
        const startVal = $('#start_datetime').val();

        if (pkgId && startVal) {
            const sqlDate = startVal.replace('T', ' ') + ':00';
            showAttendees(pkgId, sqlDate);
        }
    });

    $('#bookingForm').on('submit', function (e) {
        e.preventDefault();
        const btn = $('#saveBookingBtn');
        if (!$('#client_id').val() || !$('#therapist_id').val()) {
            if (typeof uiAlert === 'function') uiAlert('Έλεγχος στοιχείων', 'Επιλέξτε Πελάτη και Guide.');
            return;
        }


        btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span>');

        const formData = {
            action: 'saveBooking',
            id: $('#bookingId').val(),
            client_id: $('#client_id').val(),
            therapist_id: $('#therapist_id').val(),
            package_id: $('#package_id').val(),
            attendees_count: $('#attendees_count').val(),
            appointment_type: $('#appointment_type').val(),
            start: $('#start_datetime').val(),
            end: $('#end_datetime').val(),
            status: $('#booking_status').val(),
            notes: $('#booking_notes').val(),
            price: $('#booking_price').val(),
            payment_status: $('#payment_status').val(),
            csrf_token: getCsrfToken()
        };

        $.post('includes/admin/ajax.php', formData, function (res) {
            btn.prop('disabled', false).text('Καταχώρηση');
            if (res.success) {
                bookingModal.hide();
                bookingsTable.ajax.reload(null, false);
            } else {
                if (!res.success) {
                    const msg = (res.errors || []).join('\n') || 'Αποτυχία αποθήκευσης κράτησης.';
                    if (typeof uiAlert === 'function') uiAlert('Σφάλμα', msg);
                }
            }
        }, 'json').fail(function () {
            btn.prop('disabled', false).text('Καταχώρηση');
            if (typeof uiAlert === 'function') uiAlert('Σφάλμα', 'Σφάλμα επικοινωνίας με τον server.');
        });
    });

    $(document).on('click', '.edit-booking', function () {
        const id = $(this).data('id');
        resetForm();
        $.post('includes/admin/ajax.php', { action: 'getBookingDetails', id: id, csrf_token: getCsrfToken() }, function (res) {
            if (res.success) {
                const d = res.data;
                $('#tab-payments').prop('disabled', false);
                loadBookingPayments(d.id);

                $('#bookingId').val(d.id);
                $('#bookingModalTitle').text('Επεξεργασία Κράτησης');
                $('#appointment_type').val(d.appointment_type || 'inPerson');
                $('#attendees_count').val(d.attendees_count || 1);

                const clientOption = new Option(d.client_text, d.client_id, true, true);
                $('#client_id').append(clientOption).trigger('change');
                $('#booking_status').val(d.status);
                $('#booking_notes').val(d.notes);
                $('#booking_price').val(d.price);
                $('#payment_status').val(d.payment_status);
                updateStatusUI(d.payment_status);
                $('#therapist_id').val(d.therapist_id);

                loadPackagesForTherapist(d.therapist_id, d.package_id || "", { start: d.start_iso, end: d.end_iso });

                $('#btnSlotAttendees').prop('disabled', false).removeClass('btn-outline-secondary').addClass('btn-outline-primary');

                $('#bookingModal').modal('show');
            }
        }, 'json');
    });

    $(document).on('click', '.del-booking', async function () {
        const id = $(this).data('id');

        const ok = (typeof uiConfirm === 'function')
            ? await uiConfirm('Διαγραφή Κράτησης', 'Είστε σίγουροι ότι θέλετε να διαγράψετε αυτή την κράτηση;')
            : false;

        if (!ok) return;

        $.post('includes/admin/ajax.php', {
            action: 'deleteBooking',
            id: id,
            csrf_token: getCsrfToken()
        }, function (res) {
            if (res.success) {
                bookingsTable.ajax.reload(null, false);
            } else {
                if (typeof uiAlert === 'function') uiAlert('Σφάλμα', 'Αποτυχία διαγραφής.');
            }
        }, 'json').fail(function () {
            if (typeof uiAlert === 'function') uiAlert('Σφάλμα', 'Σφάλμα επικοινωνίας με τον server.');
        });
    });


    // --- PAYMENTS LISTENERS ---
    $('#btnAddManualPayment').click(function () {
        resetPaymentForm();
        $('#addPaymentForm').removeClass('d-none');
        $('#pay_date').val(new Date().toISOString().split('T')[0]);
    });

    $(document).on('click', '.edit-payment', function () {
        const btn = $(this);
        $('#pay_id').val(btn.data('id'));
        $('#pay_amount').val(btn.data('amount'));
        $('#pay_method').val(btn.data('method'));
        $('#pay_date').val(btn.data('date'));
        $('#pay_note').val(btn.data('note'));
        $('#pay_form_title').text('Επεξεργασία Πληρωμής');
        $('#addPaymentForm').removeClass('d-none');
    });

    $(document).on('click', '.del-payment', async function () {
        const ok = (typeof uiConfirm === 'function')
            ? await uiConfirm('Διαγραφή Πληρωμής', 'Είστε σίγουροι ότι θέλετε να διαγράψετε αυτή την πληρωμή;')
            : false;

        if (!ok) return;

        const pid = $(this).data('id');
        const bid = $('#bookingId').val();

        $.post('includes/admin/ajax.php', {
            action: 'delPayment',
            paymentID: pid,
            csrf_token: getCsrfToken()
        }, function (res) {
            if (res.success) {
                loadBookingPayments(bid);
                if (res.new_status) updateStatusUI(res.new_status);
            } else {
                if (typeof uiAlert === 'function') uiAlert('Σφάλμα', 'Αποτυχία διαγραφής πληρωμής.');
            }
        }, 'json').fail(function () {
            if (typeof uiAlert === 'function') uiAlert('Σφάλμα', 'Σφάλμα επικοινωνίας με τον server.');
        });
    });


    $('#btnSavePayment').click(function () {
        const bid = $('#bookingId').val();
        const pid = $('#pay_id').val();
        const action = pid ? 'updateManualPayment' : 'addManualPayment';

        $.post('includes/admin/ajax.php', {
            action: action, payment_id: pid, booking_id: bid,
            amount: $('#pay_amount').val(), method: $('#pay_method').val(),
            date: $('#pay_date').val(), note: $('#pay_note').val(),
            manual_status_update: $('#payment_status').val(),
            csrf_token: getCsrfToken()
        }, function (res) {
            if (res.success) {
                resetPaymentForm();
                loadBookingPayments(bid);
                if (res.new_status) {
                    updateStatusUI(res.new_status);
                    bookingsTable.ajax.reload(null, false);
                }
            } else {
                if (typeof uiAlert === 'function') uiAlert('Σφάλμα', 'Αποτυχία αποθήκευσης πληρωμής.');
            }
        }, 'json');
    });

    // --- ATTENDEES LISTENER ---
    $('#btnViewAttendees').click(function (e) {
        e.preventDefault();
        const pkgId = $('#package_id').val();
        if (pkgId) showAttendees(pkgId);
    });
}

function getCsrfToken() { return document.querySelector('meta[name="csrf_token"]')?.getAttribute('content') || ''; }
function resetForm() {
    document.getElementById('bookingForm').reset();
    $('#bookingId').val('');
    $('#client_id').val(null).trigger('change');
    $('#slots_container').empty();
    $('#package_id').html('<option value="">(Επιλέξτε Guide)</option>').prop('disabled', true);
}
function resetPaymentForm() {
    $('#addPaymentForm').addClass('d-none');
    $('#pay_id').val('');
    $('#pay_amount').val('');
    $('#pay_note').val('');
    $('#pay_form_title').text('Νέα Καταχώρηση');
}
function resetPanels() {
    $('#group_info_panel').addClass('d-none');
    $('#slot_picker_panel').removeClass('d-none');
    $('#slots_container').empty();
}
function updateStatusUI(status) { /* ... same as before ... */ }
function escapeHtml(text) { if (!text) return ""; return text.replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;"); }
function formatDisplayDate(sqlDate) {
    if (!sqlDate) return '-';
    const d = new Date(sqlDate.replace(' ', 'T'));
    return d.toLocaleString('el-GR', { day: '2-digit', month: '2-digit', year: 'numeric', hour: '2-digit', minute: '2-digit' });
}
function formatDateToInput(date) {
    const pad = n => n < 10 ? '0' + n : n;
    return date.getFullYear() + '-' + pad(date.getMonth() + 1) + '-' + pad(date.getDate()) + 'T' + pad(date.getHours()) + ':' + pad(date.getMinutes());
}
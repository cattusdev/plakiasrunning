/**
 * slots.js - Final Complete Version
 * Features: Multi-Therapist, Smart Bulk Create (No Buffers), Preview, Package Filtering
 */

let calendar;
let currentTherapistFilter = 'all';
let sourceDayCopy = null;

// Modal Instances
let bulkModal = null;
let copyModal = null;
let detailModal = null;

document.addEventListener("DOMContentLoaded", function () {

    // 1. Initialize Modals (Bootstrap 5)
    bulkModal = new bootstrap.Modal(document.getElementById('bulkScheduleModal'));
    copyModal = new bootstrap.Modal(document.getElementById('copyHoursModal'));
    detailModal = new bootstrap.Modal(document.getElementById('slotDetailModal'));

    // 2. Initialize Components
    initCalendar();
    loadTherapistsData();
    loadPackagesData();
    renderWeeklyScheduleBuilder();
    initDateRangePicker();

    // 3. Event Listeners

    // A. Therapist Selection in Modal -> Filter Packages
    $(document).on('change', '.modal-therapist-cb', function () {
        filterPackagesByTherapists();
    });

    // B. Filter Calendar (Top Bar)
    document.getElementById('calendarFilter').addEventListener('change', function () {
        currentTherapistFilter = this.value;
        calendar.refetchEvents();
    });

    // C. Open Bulk Modal
    document.getElementById('openBulkModalBtn').addEventListener('click', () => {
        bulkModal.show();
        // Trigger preview on open
        setTimeout(renderBulkPreview, 200);
    });

    // D. Bulk Create Submit
    document.getElementById('bulkCreateBtn').addEventListener('click', handleBulkCreate);

    // E. Dynamic Clicks (Copy, Add/Remove Interval)
    document.addEventListener('click', function (e) {
        // Copy Button
        if (e.target.closest('.copyHoursBtn')) {
            sourceDayCopy = e.target.closest('.copyHoursBtn').getAttribute('data-day');
            openCopyModal(sourceDayCopy);
        }
        // Add Interval
        if (e.target.closest('.addIntervalBtn')) {
            addTimeInterval(e.target.closest('.addIntervalBtn').getAttribute('data-day'));
            setTimeout(renderBulkPreview, 100); // Update preview
        }
        // Remove Interval
        if (e.target.closest('.removeIntervalBtn')) {
            e.target.closest('.time-row').remove();
            renderBulkPreview(); // Update preview
        }
    });

    // F. Confirm Copy
    document.getElementById('copyConfirmBtn').addEventListener('click', function () {
        executeCopyHours();
        setTimeout(renderBulkPreview, 100); // Update preview
    });

    // G. Delete Slot
    document.getElementById('btnDeleteSlot').addEventListener('click', handleDeleteSlot);

    // H. Preview Listeners (Update when settings change)
    $('#startTimeIncrement, #maxEventsPerDay').on('change', renderBulkPreview);

    // Update preview when times change
    $(document).on('change', '.start-time, .end-time', renderBulkPreview);
});

// ==========================================
// A. FULLCALENDAR
// ==========================================
function initCalendar() {
    var calendarEl = document.getElementById('calendar');
    calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'timeGridWeek',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay'
        },
        locale: 'el',
        firstDay: 1,
        slotMinTime: '08:00:00',
        slotMaxTime: '23:00:00',
        allDaySlot: false,
        height: 'auto',

        // Data Source
        events: function (info, successCallback, failureCallback) {
            $.ajax({
                url: 'includes/admin/ajax.php',
                type: 'POST',
                data: {
                    action: 'fetchCalendarEvents',
                    start: info.startStr,
                    end: info.endStr,
                    therapist_id: currentTherapistFilter,
                    csrf_token: getCsrfToken()
                },
                success: function (res) {
                    let events = (typeof res === 'string') ? JSON.parse(res) : res;
                    successCallback(events);
                },
                error: failureCallback
            });
        },

        // Render Content
        eventContent: function (arg) {
            let props = arg.event.extendedProps;
            let icon = props.status === 'booked' ? '<i class="bi bi-check-circle-fill"></i>' : '';
            return {
                html: `<div class="fc-content p-1 small lh-1">
                        <div class="fw-bold">${icon} ${arg.event.title}</div>
                       </div>`
            };
        },

        // Click Event
        eventClick: function (info) {
            if (info.event.extendedProps.isGroup) {
                alert("Group Event details coming soon.");
            } else {
                openSlotModal(info.event.extendedProps.raw_id);
            }
        }
    });
    calendar.render();
}

// ==========================================
// B. DATA LOADING & FILTERING
// ==========================================
function loadTherapistsData() {
    $.ajax({
        url: "includes/admin/ajax.php",
        type: "POST",
        data: { action: "fetchTherapists", csrf_token: getCsrfToken() },
        success: function (res) {
            if (res.success && res.data) {
                let filterSelect = $('#calendarFilter');
                let bulkContainer = $('#bulkTherapistCheckboxes');

                bulkContainer.empty();

                res.data.forEach(t => {
                    let name = t.username || (t.first_name + ' ' + t.last_name);

                    // A. Populate Main Filter
                    if (filterSelect.find(`option[value="${t.id}"]`).length === 0) {
                        filterSelect.append(`<option value="${t.id}">${name}</option>`);
                    }

                    // B. Populate Modal Checkboxes
                    bulkContainer.append(`
                        <div class="form-check form-check-inline border rounded px-2 py-1 bg-white shadow-sm">
                            <input class="form-check-input modal-therapist-cb" type="checkbox" name="therapist_ids[]" value="${t.id}" id="t_${t.id}">
                            <label class="form-check-label hand" for="t_${t.id}">${name}</label>
                        </div>
                    `);
                });
            }
        }
    });
}

function loadPackagesData() {
    $.ajax({
        url: "includes/admin/ajax.php",
        type: "POST",
        data: { action: "fetchPackages", csrf_token: getCsrfToken() },
        success: function (res) {
            let container = $('#bulkPackagesList');
            container.empty();

            if (res && res.length > 0) {
                res.forEach(p => {
                    if (p.is_group == 1) return;

                    // Linked Therapists String (e.g., ",1,5,")
                    let linked = p.linked_therapists ? `,${p.linked_therapists},` : "";

                    container.append(`
                        <div class="form-check package-wrapper" data-linked-therapists="${linked}" style="display:none;">
                            <input class="form-check-input modal-package-cb" type="checkbox" value="${p.id}" id="p_${p.id}">
                            <label class="form-check-label" for="p_${p.id}">
                                ${escapeHtml(p.title)} <small class="text-muted">(${parseInt(p.duration_minutes)}min)</small>
                            </label>
                        </div>
                    `);
                });
                // Initial Message
                container.append('<div id="no-therapist-msg" class="text-muted small fst-italic">Επιλέξτε θεραπευτή για να δείτε τα πακέτα.</div>');
            }
        }
    });
}

function filterPackagesByTherapists() {
    let selectedIds = $('.modal-therapist-cb:checked').map((_, el) => $(el).val()).get();

    if (selectedIds.length === 0) {
        $('.package-wrapper').hide();
        $('.modal-package-cb').prop('checked', false);
        $('#no-therapist-msg').show();
        return;
    }

    $('#no-therapist-msg').hide();

    $('.package-wrapper').each(function () {
        let pkgTherapists = $(this).attr('data-linked-therapists');
        let isVisible = false;

        // Union Logic: Show if supported by AT LEAST ONE selected therapist
        selectedIds.forEach(tid => {
            if (pkgTherapists.includes(`,${tid},`)) {
                isVisible = true;
            }
        });

        if (isVisible) {
            $(this).show();
        } else {
            $(this).hide();
            $(this).find('input').prop('checked', false);
        }
    });
}

// ==========================================
// C. SCHEDULE BUILDER UI
// ==========================================
function renderWeeklyScheduleBuilder() {
    const days = [
        { name: "Δευτέρα", val: 1 }, { name: "Τρίτη", val: 2 }, { name: "Τετάρτη", val: 3 },
        { name: "Πέμπτη", val: 4 }, { name: "Παρασκευή", val: 5 }, { name: "Σάββατο", val: 6 }, { name: "Κυριακή", val: 7 }
    ];

    let container = $('.daysslots');
    let copyTarget = $('#copyTargetDays');
    container.empty();
    copyTarget.empty();

    days.forEach(d => {
        // Day Card
        container.append(`
            <div class="col-12 col-md-6 col-lg-4 mb-3 day-section" data-day="${d.val}">
                <div class="card h-100 border shadow-sm">
                    <div class="card-header bg-light d-flex justify-content-between align-items-center py-2">
                        <div class="form-check m-0">
                            <input class="form-check-input dayCheck" type="checkbox" id="d_${d.val}" value="${d.val}">
                            <label class="form-check-label fw-bold" for="d_${d.val}">${d.name}</label>
                        </div>
                        <button type="button" class="btn btn-sm btn-link copyHoursBtn p-0" data-day="${d.val}" title="Αντιγραφή"><i class="bi bi-copy"></i></button>
                    </div>
                    <div class="card-body p-2" id="dayIntervals_${d.val}"></div>
                    <div class="card-footer bg-white border-top-0 p-1 text-center">
                        <button type="button" class="btn btn-sm btn-outline-primary w-100 addIntervalBtn" data-day="dayIntervals_${d.val}"><i class="bi bi-plus"></i></button>
                    </div>
                </div>
            </div>
        `);

        // Copy Modal Item
        copyTarget.append(`
            <div class="form-check mb-2">
                <input class="form-check-input copy-cb" type="checkbox" value="${d.val}" id="cp_${d.val}">
                <label class="form-check-label" for="cp_${d.val}">${d.name}</label>
            </div>
        `);
    });
}

function addTimeInterval(containerId, start = '09:00', end = '17:00') {
    let container = document.getElementById(containerId);
    let div = document.createElement('div');
    div.className = 'd-flex align-items-center mb-2 time-row gap-2';
    div.innerHTML = `
        <input type="time" class="form-control form-control-sm start-time" value="${start}">
        <span>-</span>
        <input type="time" class="form-control form-control-sm end-time" value="${end}">
        <button type="button" class="btn btn-sm text-danger removeIntervalBtn p-0"><i class="bi bi-x-circle-fill"></i></button>
    `;
    container.appendChild(div);
    $(container).closest('.day-section').find('.dayCheck').prop('checked', true);
}

function openCopyModal(day) {
    $('.copy-cb').prop('checked', false).prop('disabled', false);
    $(`#cp_${day}`).prop('disabled', true);
    copyModal.show();
}

function executeCopyHours() {
    let targets = $('.copy-cb:checked').map((_, el) => $(el).val()).get();
    if (targets.length === 0) return;

    // Get source intervals
    let sourceSec = $(`.day-section[data-day="${sourceDayCopy}"]`);
    let intervals = [];
    sourceSec.find('.time-row').each(function () {
        intervals.push({
            start: $(this).find('.start-time').val(),
            end: $(this).find('.end-time').val()
        });
    });

    if (intervals.length === 0) { alert("Η πηγή δεν έχει ώρες."); return; }

    // Apply to targets
    targets.forEach(dayVal => {
        let targetId = `dayIntervals_${dayVal}`;
        let targetContainer = document.getElementById(targetId);
        targetContainer.innerHTML = ''; // Clear existing
        intervals.forEach(int => addTimeInterval(targetId, int.start, int.end));
    });

    copyModal.hide();
}

// ==========================================
// D. PREVIEW LOGIC (NEW)
// ==========================================
function renderBulkPreview() {
    const container = $('#slotsPreviewContainer');
    container.empty();

    // 1. Find the first available interval from the builder
    let firstInterval = null;
    $('.time-row').each(function () {
        let s = $(this).find('.start-time').val();
        let e = $(this).find('.end-time').val();
        if (s && e) {
            firstInterval = { start: s, end: e };
            return false; // break
        }
    });

    if (!firstInterval) {
        container.html('<span class="text-muted small fst-italic p-2">Δεν βρέθηκε ενεργό ωράριο για προεπισκόπηση.</span>');
        return;
    }

    // 2. Settings (Clean, No Buffers)
    const duration = parseInt($('#startTimeIncrement').val()) || 30;
    let maxEvents = parseInt($('#maxEventsPerDay').val()) || 0;
    if (maxEvents <= 0) maxEvents = 9999;

    // 3. Simulation
    let baseDate = "2000-01-01"; // Arbitrary date
    let currentTime = moment(`${baseDate} ${firstInterval.start}`);
    let endTime = moment(`${baseDate} ${firstInterval.end}`);

    let count = 0;
    let html = '';

    while (currentTime.isBefore(endTime)) {
        if (count >= maxEvents) break;

        // Start
        let slotStart = currentTime.clone();

        // End (Start + Duration)
        let slotEnd = slotStart.clone().add(duration, 'minutes');

        // Check Limit
        if (slotEnd.isAfter(endTime)) break;

        // Render Badge
        html += `
            <span class="badge bg-white text-dark border shadow-sm p-2">
                <i class="bi bi-clock text-primary"></i> ${slotStart.format('HH:mm')} - ${slotEnd.format('HH:mm')}
            </span>
        `;

        count++;

        // Next Slot starts exactly where previous ended
        currentTime = slotEnd;
    }

    if (html === '') html = '<span class="text-danger small p-2">Δεν χωράνε slots με αυτές τις ρυθμίσεις.</span>';
    container.html(html);
}

// ==========================================
// E. BULK CREATE LOGIC
// ==========================================
function initDateRangePicker() {
    $('#bulkDateRange').daterangepicker({
        locale: { format: 'DD/MM/YYYY', firstDay: 1 },
        opens: 'right',
        autoUpdateInput: false
    });
    $('#bulkDateRange').on('apply.daterangepicker', function (ev, picker) {
        $(this).val(picker.startDate.format('DD/MM/YYYY') + ' - ' + picker.endDate.format('DD/MM/YYYY'));
        $('#bulkStartDate').val(picker.startDate.format('YYYY-MM-DD'));
        $('#bulkEndDate').val(picker.endDate.format('YYYY-MM-DD'));
    });
}

function handleBulkCreate() {
    // 1. Gather Data
    let therapists = $('.modal-therapist-cb:checked').map((_, el) => $(el).val()).get();
    let dateRange = $('#bulkDateRange').val();
    let step = parseInt($('#startTimeIncrement').val());
    let status = $('#bulkStatus').val();
    let packageIds = $('.modal-package-cb:checked').map((_, el) => $(el).val()).get();
    let maxEvents = parseInt($('#maxEventsPerDay').val()) || 0;

    // Validation
    if (therapists.length === 0) { alert("Επιλέξτε τουλάχιστον έναν Θεραπευτή."); return; }
    if (!dateRange) { alert("Επιλέξτε Ημερομηνίες."); return; }
    if (packageIds.length === 0) { alert("Πρέπει να επιλέξετε τουλάχιστον ένα Πακέτο."); return; }

    // 2. Dates
    let [startStr, endStr] = dateRange.split(' - ');
    let startDate = moment(startStr, 'DD/MM/YYYY').format('YYYY-MM-DD');
    let endDate = moment(endStr, 'DD/MM/YYYY').format('YYYY-MM-DD');

    // 3. Schedule Map
    let daySchedules = {};
    $('.day-section').each(function () {
        let dayVal = $(this).data('day');
        if ($(this).find('.dayCheck').is(':checked')) {
            let intervals = [];
            $(this).find('.time-row').each(function () {
                intervals.push({
                    start: $(this).find('.start-time').val(),
                    end: $(this).find('.end-time').val()
                });
            });
            if (intervals.length > 0) daySchedules[dayVal] = intervals;
        }
    });

    if (Object.keys(daySchedules).length === 0) {
        alert("Ορίστε τουλάχιστον μία ημέρα και ώρα.");
        return;
    }

    // 4. Send
    let btn = document.getElementById('bulkCreateBtn');
    let oldText = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Creating...';

    let fd = new FormData();
    fd.append('action', 'bulkCreateSlots');
    fd.append('start_date', startDate);
    fd.append('end_date', endDate);
    fd.append('schedules', JSON.stringify(daySchedules));
    fd.append('step', step);
    fd.append('status', status);
    fd.append('package_ids', JSON.stringify(packageIds));
    fd.append('max_events', maxEvents); // Only limit sent
    fd.append('csrf_token', getCsrfToken());

    therapists.forEach(id => fd.append('therapist_ids[]', id));

    $.ajax({
        url: "includes/admin/ajax.php",
        type: "POST",
        data: fd,
        processData: false,
        contentType: false,
        success: function (res) {
            if (typeof res === 'string') res = JSON.parse(res);

            if (res.success) {
                bulkModal.hide();
                calendar.refetchEvents();
                alert(res.message);
            } else {
                alert("Error: " + res.error);
            }
            btn.disabled = false; btn.innerHTML = oldText;
        },
        error: function () {
            alert("Server Error");
            btn.disabled = false; btn.innerHTML = oldText;
        }
    });
}

// ==========================================
// F. DETAILS & DELETE
// ==========================================
function openSlotModal(id) {
    $('#btnDeleteSlot').data('id', id);
    $('#detailTherapistName').text('Loading...');

    $.ajax({
        url: "includes/admin/ajax.php",
        type: "POST",
        data: { action: 'getSlotDetails', id: id, csrf_token: getCsrfToken() },
        success: function (res) {
            if (res.success && res.data) {
                let s = res.data.slot;
                let d = new Date(s.start_datetime);

                $('#slotDetailTitle').text(d.toLocaleString('el-GR'));
                if (s.color) $('#slotDetailHeader').css('background-color', s.color);

                $('#detailTherapistName').text(s.first_name + ' ' + s.last_name);
                $('#detailStatus').text(s.status);
                if (s.avatar) $('#detailAvatar').attr('src', '/' + s.avatar);

                // Packages
                let pkgContainer = $('#detailPackagesList');
                pkgContainer.empty();
                if (res.data.packages) {
                    res.data.packages.forEach(p => {
                        pkgContainer.append(`<span class="badge bg-light text-dark border">${p.title}</span>`);
                    });
                }

                detailModal.show();
            }
        }
    });
}

function handleDeleteSlot() {
    let id = $('#btnDeleteSlot').data('id');
    if (!confirm("Διαγραφή;")) return;

    $.ajax({
        url: "includes/admin/ajax.php",
        type: "POST",
        data: { action: 'delSlot', slot_id: id, csrf_token: getCsrfToken() },
        success: function (res) {
            if (res.success) {
                detailModal.hide();
                calendar.refetchEvents();
            } else {
                alert(res.error);
            }
        }
    });
}

// Utils
function getCsrfToken() {
    return document.getElementById('csrf_token')?.value || '';
}

function escapeHtml(text) {
    if (!text) return "";
    return text.replace(/[\"&'\/<>]/g, function (a) {
        return {
            '"': "&quot;", "&": "&amp;", "'": "&#39;", "/": "&#47;", "<": "&lt;", ">": "&gt;"
        }[a];
    });
}
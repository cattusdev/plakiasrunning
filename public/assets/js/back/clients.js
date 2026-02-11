/**
 * clients.js (UI-friendly redesign, CoreBase-safe)
 * - 1 main “card” column + created_at
 * - Row click opens Edit
 * - Search + clear + counter
 * - Modal (details + history tab)
 * - Keeps endpoints/actions intact:
 *   fetchClients, addClient, updClient, delClients, fetchClientHistory
 */

let mainTable;
const tableName = "#clients_table";
const ajaxAction = "fetchClients";

// Bootstrap modal instance (BS5-safe)
const clientsModalEl = document.getElementById('clientsModal');
const clientsModal = clientsModalEl ? new bootstrap.Modal(clientsModalEl) : null;

document.addEventListener("DOMContentLoaded", function () {

    // ----------------------------
    // DataTable Init
    // ----------------------------
    mainTable = $(tableName).DataTable({
        paging: true,
        responsive: true,
        processing: true,
        autoWidth: false,
        lengthChange: true,
        lengthMenu: [[25, 50, 100, -1], [25, 50, 100, 'Όλα']],
        pageLength: 25,
        dom: 'rtip',
        stateSave: true,
        order: [[0, "desc"]], // id hidden but sortable

        language: {
            emptyTable: "Δεν βρέθηκαν πελάτες.",
            zeroRecords: "Δεν βρέθηκαν αποτελέσματα.",
            info: "Εμφάνιση _START_ έως _END_ από _TOTAL_ πελάτες",
            infoEmpty: "Εμφάνιση 0 έως 0 από 0 πελάτες",
            loadingRecords: "Φόρτωση...",
            processing: "Επεξεργασία...",
            paginate: { first: "Αρχική", last: "Τελευταία", next: "›", previous: "‹" }
        },

        ajax: {
            url: '/includes/admin/ajax.php',
            type: 'POST',
            data: function (d) {
                d.action = ajaxAction;
                d.csrf_token = getCsrfToken();
            },
            dataSrc: function (res) {
                if (!res) return [];
                if (Array.isArray(res)) return res;
                if (res.success && Array.isArray(res.data)) return res.data;
                return Array.isArray(res.data) ? res.data : [];
            }
        },

        columns: [
            { data: "id", title: "ID", visible: false },

            // Client “card” column
            {
                data: null,
                title: "Πελάτης",
                responsivePriority: 1,
                render: function (data, type, row) {
                    const first = (row.first_name || '').trim();
                    const last = (row.last_name || '').trim();
                    const fullNameRaw = (first + ' ' + last).trim();
                    const fullName = escapeHtml(fullNameRaw || '—');

                    const initials = ((first[0] || '') + (last[0] || '')).toUpperCase();
                    const initialsSafe = escapeHtml(initials || '—');

                    const phone = row.phone ? escapeHtml(row.phone) : '';
                    const email = row.email ? escapeHtml(row.email) : '';

                    // For sorting/searching give text
                    if (type === 'sort' || type === 'type' || type === 'filter') {
                        return `${fullNameRaw} ${row.phone || ''} ${row.email || ''} ${row.created_at || ''}`;
                    }

                    const phoneHtml = row.phone
                        ? `<span class="me-3"><i class="bi bi-telephone me-1"></i><a href="tel:${escapeHtml(row.phone)}" class="text-decoration-none text-dark">${phone}</a></span>`
                        : `<span class="me-3 text-muted"><i class="bi bi-telephone me-1"></i>—</span>`;

                    const emailHtml = row.email
                        ? `<span class="d-none d-md-inline"><i class="bi bi-envelope me-1"></i><a href="mailto:${escapeHtml(row.email)}" class="text-decoration-none">${email}</a></span>`
                        : `<span class="d-none d-md-inline text-muted"><i class="bi bi-envelope me-1"></i>—</span>`;

                    return `
                        <div class="d-flex align-items-start justify-content-between gap-3">
                            <div class="d-flex align-items-center gap-3 min-w-0">
                                <span class="client-initials">${initialsSafe}</span>

                                <div class="min-w-0">
                                    <div class="fw-bold text-dark text-truncate">${fullName}</div>
                                    <div class="client-meta text-truncate">
                                        ${phoneHtml}
                                        ${emailHtml}
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex align-items-center gap-1 client-actions">
                                <button type="button" class="btn btn-sm btn-light text-primary border edit-client" title="Επεξεργασία">
                                    <i class="bi bi-pencil-square"></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-light text-danger border del-client" title="Διαγραφή">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </div>
                    `;
                }
            },

            // Hidden columns for search/filter consistency
            { data: "phone", title: "Τηλέφωνο", visible: false },
            { data: "email", title: "Email", visible: false },

            {
                data: "created_at",
                title: "Εγγραφή",
                className: "text-muted small text-end",
                width: "150px",
                responsivePriority: 2,
                render: function (data, type) {
                    if (!data) return (type === 'sort' || type === 'type') ? '' : '—';
                    if (type === 'sort' || type === 'type') return data;

                    const d = new Date(String(data).replace(' ', 'T'));
                    if (isNaN(d.getTime())) return '—';
                    return d.toLocaleDateString('el-GR', { day: '2-digit', month: 'short', year: 'numeric' });
                }
            }
        ],

        createdRow: function (row) {
            row.classList.add('client-row');
        },

        initComplete: function () {
            updateClientsCount();
        }
    });

    // ----------------------------
    // Search + Clear (avoid double bindings)
    // ----------------------------
    $(document)
        .off('keyup.clientsSearch', '#searchClients')
        .on('keyup.clientsSearch', '#searchClients', function () {
            mainTable.search(this.value).draw();
        });

    $(document)
        .off('click.clientsClear', '#clearSearchClients')
        .on('click.clientsClear', '#clearSearchClients', function () {
            $('#searchClients').val('');
            mainTable.search('').draw();
        });

    // Counter updates
    mainTable.on('draw', updateClientsCount);
    mainTable.on('xhr', updateClientsCount);

    // Row click -> edit (but not when click actions)
    $('#clients_table tbody')
        .off('click.clientsRow')
        .on('click.clientsRow', 'tr', function (e) {
            if ($(e.target).closest('a,button,.client-actions,.edit-client,.del-client').length) return;

            const tr = $(this).hasClass('child') ? $(this).prev() : $(this);
            const rowData = mainTable.row(tr).data();
            if (!rowData) return;

            $(tr).find('.edit-client').trigger('click');
        });

    // Export dropdown (optional)
    $(document)
        .off('change.clientsExport', '#exportOptions')
        .on('change.clientsExport', '#exportOptions', function () {
            const val = $(this).val();
            if (!val) return;

            try {
                if (val === 'print') mainTable.button('.buttons-print').trigger();
                else mainTable.button(`.buttons-${val}`).trigger();
            } catch (e) {
                console.warn("Export buttons not initialized.");
            }
            $(this).val("");
        });

});


// =======================
// Actions (NEW / EDIT / SAVE / DELETE)
// =======================

$(document).off('click.clientsAdd', '#addNewClient').on("click.clientsAdd", "#addNewClient", function () {
    document.getElementById("clientsForm").reset();
    $("#clientID").val("");

    $("#clientsModalLongTitle").text("Προσθήκη Νέου Πελάτη");
    $("#clientsActionBtn").text("Δημιουργία").attr("data-action", "addClient");
    $("#clientUpdated").text("");

    // History reset
    $('#clientHistoryTable tbody').empty();
    $('#historyEmpty').removeClass('d-none');
    $('#clientHistoryTable').parent().addClass('d-none');
    $('#historyLoader').addClass('d-none');

    // Reset tab to Details
    const detailsTabBtn = document.querySelector('#details-tab');
    if (detailsTabBtn) new bootstrap.Tab(detailsTabBtn).show();

    if (clientsModal) clientsModal.show();
    else $("#clientsModal").modal("show");
});


$(document).off('click.clientsEdit', '.edit-client').on("click.clientsEdit", ".edit-client", function (e) {
    e.preventDefault();

    const tr = $(this).closest('tr').hasClass('child') ? $(this).closest('tr').prev() : $(this).closest('tr');
    const rowData = mainTable.row(tr).data();
    if (!rowData) return;

    $("#fname").val(rowData.first_name || '');
    $("#lname").val(rowData.last_name || '');
    $("#email").val(rowData.email || '');
    $("#phone").val(rowData.phone || '');
    $("#clientNote").val(rowData.client_note || '');
    $("#clientID").val(rowData.id);

    const updatedDate = rowData.updated_at
        ? new Date(String(rowData.updated_at).replace(' ', 'T')).toLocaleDateString('el-GR')
        : '-';
    $("#clientUpdated").html(`<i class="bi bi-clock-history"></i> Τελευταία ενημέρωση: ${updatedDate}`);

    $("#clientsModalLongTitle").text(`Επεξεργασία: ${(rowData.first_name || '')} ${(rowData.last_name || '')}`.trim());
    $("#clientsActionBtn").text("Ενημέρωση").attr("data-action", "updClient");

    // Load history (async)
    loadClientHistory(rowData.id);

    // force details tab
    const detailsTabBtn = document.querySelector('#details-tab');
    if (detailsTabBtn) new bootstrap.Tab(detailsTabBtn).show();

    if (clientsModal) clientsModal.show();
    else $("#clientsModal").modal("show");
});


$(document).off('click.clientsSave', '#clientsActionBtn').on("click.clientsSave", "#clientsActionBtn", function (e) {
    e.preventDefault();

    const btn = $(this);
    const action = btn.attr('data-action'); // addClient / updClient
    const form = $("#clientsForm");

    // Basic Validation
    if (!$("#fname").val() || !$("#lname").val() || !$("#phone").val()) {
        setNotificationSafe("Προσοχή", "Συμπληρώστε Όνομα, Επώνυμο και Τηλέφωνο.", "warning");
        return;
    }

    btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span>');

    const payload = form.serialize() +
        `&action=${encodeURIComponent(action)}` +
        `&csrf_token=${encodeURIComponent(getCsrfToken())}` +
        `&clientID=${encodeURIComponent($("#clientID").val() || '')}`;

    $.ajax({
        type: "POST",
        url: '/includes/admin/ajax.php',
        data: payload,
        dataType: 'json',
        success: function (res) {
            btn.prop('disabled', false).text(action === 'addClient' ? 'Δημιουργία' : 'Ενημέρωση');

            if (res && res.success) {
                if (clientsModal) clientsModal.hide();
                else $("#clientsModal").modal("hide");

                if (mainTable) mainTable.ajax.reload(null, false);

                setNotificationSafe("Επιτυχία", res.message || "Η ενέργεια ολοκληρώθηκε.", "success");
            } else {
                let errorMsg = "";
                if (res && Array.isArray(res.errors)) errorMsg = res.errors.join("<br>");
                else errorMsg = (res && res.errors) ? res.errors : "Άγνωστο σφάλμα";

                setNotificationSafe("Σφάλμα", errorMsg, "error");
            }
        },
        error: function () {
            btn.prop('disabled', false).text("Προσπάθεια ξανά");
            setNotificationSafe("Σφάλμα", "Υπήρξε πρόβλημα επικοινωνίας με τον server.", "error");
        }
    });
});


$(document).off('click.clientsDel', '.del-client').on("click.clientsDel", ".del-client", async function (e) {
    e.preventDefault();

    const tr = $(this).closest('tr').hasClass('child') ? $(this).closest('tr').prev() : $(this).closest('tr');
    const rowData = mainTable.row(tr).data();
    if (!rowData) return;

    const fullName = `${escapeHtml(rowData.first_name || '')} ${escapeHtml(rowData.last_name || '')}`.trim();

    // Prefer your global confirm helper; fallback to confirm()
    let confirmed = true;
    if (typeof handleNotificationAction === 'function') {
        confirmed = await handleNotificationAction(
            'Διαγραφή Πελάτη',
            `Είστε σίγουροι ότι θέλετε να διαγράψετε τον πελάτη <strong>${fullName}</strong>;`
        );
    } else {
        confirmed = confirm(`Να διαγραφεί ο πελάτης ${rowData.first_name || ''} ${rowData.last_name || ''};`);
    }
    if (!confirmed) return;

    $.ajax({
        type: 'POST',
        url: '/includes/admin/ajax.php',
        dataType: 'json',
        data: {
            action: "delClients", // backend soft delete
            clientID: rowData.id,
            csrf_token: getCsrfToken()
        },
        success: function (res) {
            if (res && res.success) {
                if (mainTable) mainTable.ajax.reload(null, false);
                setNotificationSafe("Επιτυχία", "Ο πελάτης διαγράφηκε (απενεργοποιήθηκε).", "success");
            } else {
                setNotificationSafe("Σφάλμα", "Δεν ήταν δυνατή η διαγραφή.", "error");
            }
        },
        error: function () {
            setNotificationSafe("Σφάλμα", "Network Error", "error");
        }
    });
});


// =======================
// History loader
// =======================
function loadClientHistory(clientId) {
    const tbody = $('#clientHistoryTable tbody');
    const loader = $('#historyLoader');
    const emptyMsg = $('#historyEmpty');
    const tableWrap = $('#clientHistoryTable').parent();

    tbody.empty();
    loader.removeClass('d-none');
    emptyMsg.addClass('d-none');
    tableWrap.addClass('d-none');

    $.post('/includes/admin/ajax.php', {
        action: 'fetchClientHistory',
        client_id: clientId,
        csrf_token: getCsrfToken()
    }, function (res) {
        loader.addClass('d-none');

        if (res && res.success && Array.isArray(res.history) && res.history.length > 0) {
            tableWrap.removeClass('d-none');

            res.history.forEach(row => {
                const d = row.start_datetime ? new Date(String(row.start_datetime).replace(' ', 'T')) : null;
                const dateStr = d && !isNaN(d.getTime())
                    ? d.toLocaleDateString('el-GR', { day: '2-digit', month: '2-digit', year: '2-digit' })
                    : '—';
                const timeStr = d && !isNaN(d.getTime())
                    ? d.toLocaleTimeString('el-GR', { hour: '2-digit', minute: '2-digit' })
                    : '';

                let statusBadge = '<span class="badge bg-secondary-subtle text-dark border border-secondary-subtle">Προγραμματισμένο</span>';
                if (row.status === 'completed') statusBadge = '<span class="badge bg-success-subtle text-success border border-success-subtle">Ολοκληρώθηκε</span>';
                if (row.status === 'canceled') statusBadge = '<span class="badge bg-danger-subtle text-danger border border-danger-subtle">Ακυρώθηκε</span>';

                const tName = (row.t_fname || row.t_lname) ? `${row.t_fname || ''} ${row.t_lname || ''}`.trim() : '—';
                const isGroupPkg = (row.package_is_group !== undefined && row.package_is_group !== null)
                    ? (parseInt(row.package_is_group) === 1)
                    : null;

                const typeBadge = (isGroupPkg === null) ? '' : (
                    isGroupPkg
                        ? '<span class="badge bg-warning-subtle text-dark border border-warning-subtle ms-2"><i class="bi bi-calendar-event me-1"></i>Event</span>'
                        : '<span class="badge bg-light text-dark border ms-2"><i class="bi bi-repeat me-1"></i>Recurring</span>'
                );

                const pkgName = row.package_title
                    ? `${escapeHtml(row.package_title)} ${typeBadge}`
                    : `<span class="fst-italic text-muted">Custom / Personal</span>`;

                const displayPrice = parseFloat(row.total_paid || row.package_price || 0);
                const priceStr = isNaN(displayPrice) ? '0.00€' : `${displayPrice.toFixed(2)}€`;

                tbody.append(`
                    <tr>
                        <td>
                            <div class="fw-bold">${dateStr}</div>
                            <div class="small text-muted">${escapeHtml(timeStr)}</div>
                        </td>
                        <td>${pkgName}</td>
                        <td class="small">${escapeHtml(tName)}</td>
                        <td>${statusBadge}</td>
                        <td class="text-end fw-bold text-dark">${escapeHtml(priceStr)}</td>
                    </tr>
                `);
            });
        } else {
            emptyMsg.removeClass('d-none');
        }

    }, 'json').fail(function () {
        loader.addClass('d-none');
        tableWrap.removeClass('d-none');
        tbody.html('<tr><td colspan="5" class="text-center text-danger small">Σφάλμα φόρτωσης.</td></tr>');
    });
}


// =======================
// Small helpers
// =======================
function updateClientsCount() {
    const el = document.getElementById('clientsCount');
    if (!el || !mainTable) return;

    const info = mainTable.page.info();
    const txt = (info.recordsDisplay === info.recordsTotal)
        ? `${info.recordsTotal} πελάτες`
        : `${info.recordsDisplay} / ${info.recordsTotal} πελάτες`;
    el.textContent = txt;
}

function getCsrfToken() {
    return document.querySelector('meta[name="csrf_token"]')?.getAttribute('content')
        || document.querySelector('input[name="csrf_token"]')?.value
        || "";
}

function escapeHtml(text) {
    if (!text) return "";
    return String(text)
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;")
        .replace(/'/g, "&#039;");
}

function setNotificationSafe(title, msg, type) {
    if (typeof setNotification === 'function') return setNotification(title, msg, type);
    // fallback
    console.log(`[${type || 'info'}] ${title}: ${msg}`);
}

// Safe fallbacks (avoid crashes)
if (typeof handleNotificationAction === 'undefined') {
    window.handleNotificationAction = function (_t, _m) {
        return new Promise(resolve => resolve(true));
    };
}

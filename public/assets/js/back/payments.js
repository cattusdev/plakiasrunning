/* ==========================================================
   Payments (Back) - Redesigned UI but same core endpoints
   Requires: jQuery, DataTables, moment (already in your stack)
   ========================================================== */

let paymentsTable;

(function () {
    "use strict";

    function getCsrf() {
        return document.getElementsByName("csrf_token")[0]?.getAttribute("content")
            || document.querySelector('meta[name="csrf_token"]')?.getAttribute("content")
            || "";
    }

    function escapeHtml(text) {
        if (!text) return "";
        return String(text).replace(/[\"&'\/<>]/g, function (a) {
            return {
                '"': "&quot;",
                "&": "&amp;",
                "'": "&#39;",
                "/": "&#47;",
                "<": "&lt;",
                ">": "&gt;"
            }[a];
        });
    }

    function fmtMoney(v, currency) {
        const n = parseFloat(v);
        const c = currency ? ` ${currency}` : "";
        if (isNaN(n)) return `0${c}`;
        return `${n.toFixed(2)}${c}`;
    }

    function statusBadge(status) {
        const s = String(status || '').toUpperCase();
        let cls = "bg-light text-dark border";
        let label = status || "—";

        if (s === "CAPTURED" || s === "AUTHORIZED" || s === "COMPLETE") cls = "bg-success-subtle text-success border border-success-subtle";
        if (s === "FAILED" || s === "CANCELED" || s === "CANCELLED") cls = "bg-danger-subtle text-danger border border-danger-subtle";
        if (s === "PENDING") cls = "bg-warning-subtle text-warning border border-warning-subtle";

        return `<span class="pay-pill ${cls}">${escapeHtml(label)}</span>`;
    }

    function cardIcon(cardType) {
        const t = String(cardType || '').toLowerCase();
        if (t === 'visa') return '<i class="fab fa-cc-visa me-2"></i>';
        if (t === 'mastercard') return '<i class="fab fa-cc-mastercard me-2"></i>';
        return '<i class="fa fa-credit-card me-2"></i>';
    }

    function countryCell(code) {
        if (!code) return `<span class="text-muted">—</span>`;
        const x = escapeHtml(code);
        return `<span class="d-inline-flex align-items-center gap-2">
            <img src="assets/images/countries/${x}.png" class="img-flag" alt="">
            <span>${x}</span>
        </span>`;
    }

    function calcDue(total, paid) {
        const t = parseFloat(total || 0);
        const p = parseFloat(paid || 0);
        const due = (isNaN(t) ? 0 : t) - (isNaN(p) ? 0 : p);
        return Math.max(0, due);
    }

    function setModalTopBadges(row) {
        const statusText = row?.status || '—';
        const amountText = fmtMoney(row?.amount_paid, row?.currency);
        const methodText = row?.payment_method || row?.card_type || '—';

        $("#uiPayStatus").html(statusBadge(statusText));
        $("#uiPayAmount").text(amountText);
        $("#uiPayMethod").text(methodText);
    }

    function openEditModal(rowData) {
        // IMPORTANT: keep IDs as you used before
        $("#paymentID").val(rowData.id); // fix: store payment id (not first_name)
        $("#clientName").text(`${rowData.first_name || ''} ${rowData.last_name || ''}`.trim() || '—');
        $("#reservationID").text(rowData.reservation_id ? `#${rowData.reservation_id}` : '—');
        $("#paymentRef").text(rowData.paymentRef || '—');
        $("#transactionID").text(rowData.token || '—');

        $("#amount_paid").text(fmtMoney(rowData.amount_paid, rowData.currency));
        $("#total_amount").text(fmtMoney(rowData.amount_total, rowData.currency));
        const due = calcDue(rowData.amount_total, rowData.amount_paid);
        $("#amount_due").text(fmtMoney(due, rowData.currency));

        $("#clientCountry").text(rowData.billing_country || '—');
        $("#paymentStatus").html(statusBadge(rowData.status));
        $("#createdAt").text(rowData.created_at ? moment(rowData.created_at).format('MMM D YYYY, HH:mm') : '—');

        $("#billingCity").text(rowData.billing_city || '—');
        $("#billingZip").text(rowData.billing_zip || '—');
        $("#billingAddress").text(rowData.billing_address || '—');
        $("#payerEmail").text(rowData.payer_email || '—');
        $("#paymentMethod").text(rowData.payment_method || '—');

        $("#paymentNote").val(rowData.note || '');

        $("#paymentsModalLongTitle").html(`Payment #${escapeHtml(rowData.id)}`);

        setModalTopBadges(rowData);

        $("#paymentsActionBtn").attr('data-key', 'payments');
        $("#paymentsActionBtn").attr('data-action', 'updPayment');

        $("#paymentsModal").modal("show");
    }

    async function confirmDelete(rowData) {
        const title = `Διαγραφή πληρωμής`;
        const msg = `Είστε σίγουροι ότι θέλετε να διαγράψετε την πληρωμή <b>#${escapeHtml(rowData.id)}</b>;`;
        if (typeof uiConfirm === 'function') return await uiConfirm(title, msg);
        return confirm(`Are you sure you want to delete payment '#${rowData.id}'?`);
    }

    function bindExportButtons() {
        // keep your existing behavior, but safer
        $(document).off('click.paymentsExport', '.optionsExport').on('click.paymentsExport', '.optionsExport', function () {
            const $select = $(this).next('select');
            const val = $select.val();
            if (!paymentsTable) return;
            paymentsTable.buttons(`.buttons-${val}`).trigger();
        });

        $(document).off('click.paymentsPrint', '.optionsPrint').on('click.paymentsPrint', '.optionsPrint', function () {
            if (!paymentsTable) return;
            paymentsTable.buttons('.buttons-print').trigger();
        });
    }

    function initTable() {
        const csrf_token = getCsrf();

        const columnDefs = [
            // Payment ID
            {
                type: "num",
                data: "id",
                title: "Payment",
                responsivePriority: 2,
                width: "90px",
                render: function (data) {
                    return `<span class="pay-pill bg-info-subtle text-info border border-info-subtle">#${escapeHtml(data)}</span>`;
                }
            },

            // Client / Reservation (clean card cell)
            {
                type: "text",
                data: "reservation_id",
                title: "Reservation",
                responsivePriority: 1,
                render: function (data, type, row) {
                    const full = `${row.first_name || ''} ${row.last_name || ''}`.trim() || '—';
                    const resId = row.reservation_id ? `#${escapeHtml(row.reservation_id)}` : '—';
                    const email = row.payer_email ? escapeHtml(row.payer_email) : '';

                    return `
                        <div class="d-flex flex-column gap-1">
                            <div class="pay-client">${escapeHtml(full)}
                                <span class="ms-2 pay-pill bg-light text-dark border">${resId}</span>
                            </div>
                            ${email ? `<div class="pay-sub"><i class="bi bi-envelope me-1"></i>${email}</div>` : `<div class="pay-sub">—</div>`}
                        </div>
                    `;
                }
            },

            // Status
            {
                type: "text",
                data: "status",
                title: "Status",
                width: "120px",
                render: function (data) { return statusBadge(data); }
            },

            // Paid
            {
                type: "num",
                data: "amount_paid",
                title: "Paid",
                className: "text-end fw-bold",
                width: "110px",
                render: function (data, type, row) {
                    return `<span class="pay-pill bg-white text-dark border">${escapeHtml(fmtMoney(data, row.currency))}</span>`;
                }
            },

            // Total
            {
                type: "num",
                data: "amount_total",
                title: "Total",
                className: "text-end",
                width: "110px",
                render: function (data, type, row) {
                    return `<span class="pay-pill bg-light text-dark border">${escapeHtml(fmtMoney(data, row.currency))}</span>`;
                }
            },

            // Card type
            {
                type: "text",
                data: "card_type",
                title: "Card",
                width: "120px",
                render: function (data) {
                    return `<span class="d-inline-flex align-items-center">${cardIcon(data)}<span>${escapeHtml(data || '—')}</span></span>`;
                }
            },

            // Payment Reference
            {
                type: "text",
                data: "paymentRef",
                title: "Reference",
                responsivePriority: 3,
                render: function (data) {
                    return `<span class="text-muted"><i class="bi bi-credit-card me-1"></i>${escapeHtml(data || '—')}</span>`;
                }
            },

            // Country
            {
                type: "text",
                data: "billing_country",
                title: "Country",
                width: "130px",
                render: function (data) { return countryCell(data); }
            },

            // Created
            {
                type: "text",
                data: "created_at",
                title: "Created",
                width: "160px",
                render: function (data) {
                    return data ? `<span class="text-muted">${escapeHtml(moment(data).format('MMM D YYYY, HH:mm'))}</span>` : '—';
                }
            },

            // Actions (EDIT/DELETE here, not inside Reservation cell)
            {
                data: null,
                title: "",
                orderable: false,
                searchable: false,
                width: "120px",
                className: "text-end pay-actions",
                render: function (data, type, row) {
                    return `
                        <button class="btn btn-sm btn-light text-primary border editPayments" data-paymentid="${escapeHtml(row.id)}" title="Επεξεργασία">
                            <i class="bi bi-pencil-square"></i>
                        </button>
                        <button class="btn btn-sm btn-light text-danger border delPayment" data-paymentid="${escapeHtml(row.id)}" title="Διαγραφή">
                            <i class="bi bi-trash"></i>
                        </button>
                    `;
                }
            },

            // Hidden columns (responsive details)
            { type: "text", data: "billing_city", title: "City", className: "none" },
            { type: "text", data: "billing_zip", title: "Zip", className: "none" },
            { type: "text", data: "billing_address", title: "Address", className: "none" },
            { type: "text", data: "payer_email", title: "Payer Email", className: "none" },
            { type: "text", data: "payment_method", title: "Payment Method", className: "none" },
            { type: "text", data: "updated_at", title: "Updated", className: "none" },
            { type: "text", data: "note", title: "Note", className: "none" }
        ];

        paymentsTable = $("#payments_table").DataTable({
            paging: true,
            responsive: { details: true },
            lengthChange: true,
            autoWidth: false,
            info: true,
            ordering: true,
            searching: true,
            stateSave: true,

            lengthMenu: [[25, 50, 100, -1], [25, 50, 100, "All"]],
            dom: "Blrtip",

            buttons: [
                { extend: "excel", text: "excel", className: "d-none" },
                { extend: "csv", text: "csv", className: "d-none" },
                { extend: "pdf", className: "d-none", orientation: "landscape", pageSize: "A4" },
                { title: 'Επεξεργασία', extend: "print", text: "print", className: "d-none" }
            ],

            language: {
                emptyTable: "Δεν υπάρχουν στοιχεία προς εμφάνιση.",
                zeroRecords: "Δεν βρέθηκαν αποτελέσματα.",
                search: "Αναζήτηση",
                info: "Εμφάνιση _START_ έως _END_ από _TOTAL_ καταχωρήσεις",
                infoEmpty: "Εμφάνιση 0 έως 0 από 0",
                paginate: { next: "›", previous: "‹" },
                lengthMenu: "Καταχωρήσεις _MENU_"
            },

            processing: false,
            columns: columnDefs,

            ajax: {
                type: 'POST',
                url: 'includes/admin/ajax.php',
                data: function (d) {
                    d.action = 'fetchPayments';
                    d.format = 'json';
                    d.csrf_token = csrf_token;
                },
                dataSrc: function (responseText) {
                    return responseText || [];
                }
            },

            initComplete: function () {
                // Total counter (if you have #totalPayments somewhere else)
                if ($("#totalPayments").length) {
                    $("#totalPayments").html(paymentsTable.rows().count());
                }
            }
        });
    }

    function bindEvents() {
        // Search (fix: use paymentsTable, not mainTable)
        $(document).off('keyup.paySearch click.paySearch', '#searchPayments')
            .on('keyup.paySearch click.paySearch', '#searchPayments', function () {
                if (!paymentsTable) return;
                paymentsTable.search($(this).val()).draw();
            });

        // Edit
        $(document).off('click.payEdit', '#payments_table .editPayments')
            .on('click.payEdit', '#payments_table .editPayments', function (e) {
                e.preventDefault();
                const tr = $(this).closest('tr');
                const rowData = paymentsTable.row(tr).data();
                if (!rowData) return;
                openEditModal(rowData);
            });

        // Delete
        $(document).off('click.payDel', '#payments_table .delPayment')
            .on('click.payDel', '#payments_table .delPayment', async function (e) {
                e.preventDefault();
                const tr = $(this).closest('tr');
                const rowData = paymentsTable.row(tr).data();
                if (!rowData) return;

                const ok = await confirmDelete(rowData);
                if (!ok) return;

                $.ajax({
                    type: 'POST',
                    url: 'includes/admin/ajax.php',
                    dataType: 'json',
                    data: {
                        action: "delPayment",
                        csrf_token: getCsrf(),
                        paymentID: rowData.id
                    },
                    success: function (response) {
                        if (response?.success) {
                            paymentsTable.ajax.reload(null, false);
                            if (typeof setNotification === 'function') setNotification('Action', response.message || 'Deleted', 'success');
                        } else {
                            let errorMessage = '';
                            if (Array.isArray(response?.errors)) errorMessage = response.errors.join("<br>");
                            else errorMessage = response?.errors || "Delete failed.";
                            if (typeof setNotification === 'function') setNotification('Warning', errorMessage, 'warning');
                        }
                    }
                });
            });

        // Save note/update
        $(document).off('click.paySave', '#paymentsActionBtn')
            .on('click.paySave', '#paymentsActionBtn', function (e) {
                e.preventDefault();
                const $btn = $("#paymentsActionBtn");
                $btn.prop('disabled', true);

                const key = $btn.attr('data-key');
                const action = $btn.attr('data-action');

                const paymentID = $("#paymentID").val() || ""; // now correct id
                const csrf_token = getCsrf();

                $.ajax({
                    type: "POST",
                    url: 'includes/admin/ajax.php',
                    data: $("#paymentsForm").serialize()
                        + '&action=' + encodeURIComponent(action)
                        + '&key=' + encodeURIComponent(key)
                        + '&csrf_token=' + encodeURIComponent(csrf_token)
                        + '&paymentID=' + encodeURIComponent(paymentID),
                    success: function (responsedata) {
                        // old code expects 'success' string sometimes
                        $btn.prop('disabled', false);

                        if (responsedata !== 'success') {
                            if (typeof setNotification === 'function') setNotification('Warning', responsedata, true);
                            return;
                        }

                        if (typeof setNotification === 'function') setNotification('Action', 'Payment updated successfully!', 'success');

                        $('#paymentsModal').modal('hide');
                        paymentsTable.ajax.reload(null, false);
                    },
                    error: function () {
                        $btn.prop('disabled', false);
                        if (typeof setNotification === 'function') setNotification('Warning', 'Server error.', 'warning');
                    }
                });
            });

        // Reset modal on close
        $('#paymentsModal').off('hidden.bs.modal.payReset').on('hidden.bs.modal.payReset', function () {
            document.getElementById("paymentsForm")?.reset();
            $("#paymentID").val('');
            $("#paymentNote").val('');
        });

        bindExportButtons();
    }

    // Public helper (same name you had)
    window.reloadTables = function () {
        if (paymentsTable) paymentsTable.ajax.reload(null, false);
    };

    document.addEventListener("DOMContentLoaded", function () {
        initTable();
        bindEvents();
    });

})();

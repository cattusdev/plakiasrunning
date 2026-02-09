
document.addEventListener("DOMContentLoaded", function () {



    let csrf_token = document.getElementsByName("csrf_token")[0].getAttribute("content");

    var columnDefs = [{
        type: "num",
        data: "id",
        title: "Payment ID",
        responsivePriority: 2,
        render: function (data, type, row, meta) {
            return `<span class="badge bg-info text-white p-1 m-1">#${data}</span>`;
        },
    },
    {
        type: "text",
        data: "reservation_id",
        title: "Reservation",
        responsivePriority: 1,
        
        render: function (data, type, row) {
            return `<strong>${escapeHtml(row.first_name + ` ` + row.last_name)}</strong><span class="badge bg-tag p-1 m-1">#` + row.reservation_id + `</span>
                        <div class="edit-actions d-inline-block ms-3">
                            <span class="edit">
                                <a href="#" class="editPayments" data-paymentid="${row.id}" title="Επεξεργασία">
                                    <i class="bi bi-pencil-square font-md"></i>
                                </a>
                            </span>
                            <span class="delete"> |
                                <a href="#" class="delPayment" aria-label="Delete Payment" data-paymentid="${row.id}" title="Διαγραφή">
                                    <i class="bi bi-trash font-md"></i>
                                </a>
                            </span>
                        </div>
                    `;
        }
    },
    {
        type: "text",
        data: "card_type",
        title: "Card type",
        createdCell: function (td, cellData, rowData, row, col) {

            if (cellData == 'visa') {
                $(td).prepend('<span class="fab fa-cc-visa me-2"> </span>');
            } else if (cellData == 'mastercard') {
                $(td).prepend('<span class="fab fa-cc-mastercard me-2"> </span>');
            } else {
                $(td).prepend('<span class="fa fa-credit-card me-2"> </span>');
            }

        }
        //
        // render: function (data, type, row, meta) {
        //     return data +
        //         `
        // 	<div class="edit-actions"><a href="mailto:` + row.email + `"><i class="bi bi-envelope"></i></a></div>
        // 	`;
        // },
    },
    {
        type: "text",
        data: "paymentRef",
        title: "Payment Reference",
        render: function (data, type, row, meta) {
            return `<i class="bi bi-credit-card me-1"> </i>` + data;
        },
    },
    {
        type: "text",
        data: "amount_paid",
        title: "Amount Paid",
        render: function (data, type, row, meta) {
            return data + ' ' + row.currency;
        },
    },
    {
        type: "text",
        data: "amount_total",
        title: "Amount Total",
        render: function (data, type, row, meta) {
            return data + ' ' + row.currency;
        },
    },
    {
        type: "text",
        data: "billing_country",
        defaultContent: "",
        render: function (data, type, row, meta) {
            if (data !== null && row.billing_country !== null) {
                return '<span><img src="assets/images/countries/' + row.billing_country + '.png" class="img-flag" alt=""/> ' + row.billing_country + '</span>';
            }

        },
        title: "Country",
    },
    {
        type: "text",
        data: "billing_city",
        title: "City",
    },
    {
        type: "text",
        data: "billing_zip",
        title: "Zip",
    },
    {
        type: "text",
        data: "billing_address",
        title: "Address",
    },
    {
        type: "text",
        data: "payer_email",
        title: "Payer Email"
    },
    {
        type: "text",
        data: "payment_method",
        title: "Payment Method",
    },
    {
        type: "text",
        data: "created_at",
        title: "Created",
        render: function (data, type, row, meta) {
            return moment(data).format('MMM D YYYY, hh:mm a');
        },
    },
    {
        type: "text",
        data: "status",
        title: "Status",
        render: function (data, type, row, meta) {
            return data;
        },

        createdCell: function (td, cellData, rowData, row, col) {

            if (cellData == 'CAPTURED') {
                $(td).addClass('bg-confirmed');
                $(td).addClass('text-white');
            }
            if (cellData == 'AUTHORIZED') {
                $(td).addClass('bg-confirmed');
                $(td).addClass('text-white');
            }
            if (cellData == 'COMPLETE') {
                $(td).addClass('bg-confirmed');
                $(td).addClass('text-white');
                $(td).css('text-transform', 'capitalize');
            }
        }
    },

    {
        type: "text",
        data: "updated_at",
        title: "Updated",
        className: 'none',
        // render: function (data, type, row, meta) {
        //     if (data) {
        //         return moment(data).format('MMM D YYYY, hh:mm a');

        //     }
        // },
    },
    {
        type: "text",
        data: "note",
        title: "Note",
        className: 'none',
    },
    ];

    $('.optionsExport').click(function () {
        let datatableID = '#' + $(this).parent().parent().next('.dataTables_wrapper').find('table').attr('id');
        $(datatableID).DataTable().buttons('.buttons-' + $(this).next('select').val()).trigger();
    });
    $('.optionsPrint').click(function () {
        let datatableID = '#' + $(this).parent().parent().next('.dataTables_wrapper').find('table').attr('id');
        $(datatableID).DataTable().buttons('.buttons-print').trigger();
    });

    var paymentsTable = $("#payments_table").DataTable({
        paging: true,
        responsive: { details: true },
        lengthChange: true,
        autoWidth: false,
        info: true,
        ordering: true,
        searching: true,
        lengthMenu: [
            [25, 50, 100, -1],
            [25, 50, 100, "All"],
        ],
        dom: "Blrtip",
        stateSave: true,
        buttons: [
            { extend: "excel", text: "excel", className: "d-none" },
            { extend: "csv", text: "csv", className: "d-none" },
            { extend: "pdf", className: "d-none", orientation: "landscape", pageSize: "A4" },
            { title: 'Επεξεργασία', extend: "print", text: "print", className: "d-none" },
        ],
        language: {
            emptyTable: "Δεν υπάρχουν στοιχεία προς εμφάνιση.",
            search: "Αναζήτηση",
            info: "Εμφάνιση _START_ έως _END_ από _TOTAL_ καταχωρήσεις",
            paginate: { next: "Επόμενη", previous: "Προηγούμενη" },
            lengthMenu: "Καταχωρήσεις _MENU_",
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
                return responseText;
            }
        },
        

        onPreRefresh: function (datatable) { },

        onRefresh: function (datatable) {

        },

        initComplete: function (settings) {
            $("#totalPayments").html($("#payments_table").DataTable().rows().count());
        }
    });



});


$('#searchPayments').on('keyup click', function () {
    mainTable.search(
        $('#searchPayments').val()
    ).draw();
});

$(document).on("click", "#payments_table .edit-actions .editPayments", function () {

    let row = $(this).parents('tr')[0];
    let rowData = $(this).parents('table').DataTable().row(row).data();



    $("#paymentID").val(rowData.first_name);
    $("#clientName").text(`${rowData.first_name} ${rowData.last_name}`);
    $("#reservationID").text('#' + rowData.reservation_id);
    $("#paymentRef").text(rowData.paymentRef);
    $("#transactionID").text(rowData.token);
    $("#amount_paid").text(`${rowData.amount_paid} ${rowData.currency}`);
    let amountDue = rowData.amount_total - rowData.amount_paid;
    $("#amount_due").text(`${amountDue.toFixed(2)} ${rowData.currency}`);
    $("#total_amount").text(`${rowData.amount_total} ${rowData.currency}`);
    $("#clientCountry").text(rowData.billing_country);
    $("#paymentStatus").text(rowData.status);
    $("#paymentNote").text(rowData.note);
    $("#createdAt").text(rowData.created_at);

    $("#billingCity").text(rowData.billing_city);
    $("#billingZip").text(rowData.billing_zip);
    $("#billingAddress").text(rowData.billing_address);
    $("#payerEmail").text(rowData.payer_email);
    $("#paymentMethod").text(rowData.payment_method);

    $("#paymentsModalLongTitle").html('Payment #' + rowData.id);

    $("#paymentsActionBtn").attr('data-key', 'payments');
    $("#paymentsActionBtn").attr('data-action', 'updPayment');
    $("#paymentsActionBtn").val('Update');

    $("#paymentsModal").modal("show");
});


$(document).on("click", ".delPayment", function () {
    let csrf_token = document.getElementsByName('csrf_token')[0]?.getAttribute('content');
    let row = $(this).parents('tr')[0];
    let rowData = $(this).parents('table').DataTable().row(row).data();

    if (confirm("Are you sure you want to delete payment '#" + rowData.id + "'? ")) {
        $.ajax({
            type: 'post',
            url: 'includes/admin/ajax.php',
            data: {
                action: "delPayment",
                csrf_token: csrf_token,
                paymentID: rowData.id,
            },
            success: function (response) {
                
                if (response.success) {
                    $('#payments_table').DataTable().ajax.reload();
                    setNotification('Action', response.message, 'success');
                    $("#addProductBtn").prop('disabled', false);
                } else {
                    var errorMessage = '';
                    $.each(response.errors, function (index, error) {
                        errorMessage += error + "<br>";
                    });
                    setNotification('Warning', errorMessage, 'warning');
                    $("#addProductBtn").prop('disabled', false);
                }
            }
            
        });

    }
});


function reloadTables() {
    $('#payments_table').DataTable().ajax.reload();

}


$('body').on('click', '#paymentsActionBtn', function (e) {
    e.preventDefault();
    $("#paymentsActionBtn").prop('disabled', true);
    var key = $(this).attr('data-key');
    var action = $(this).attr('data-action');
    var paymentID = $("#paymentID").val();
    var form = $("#paymentsForm");
    let csrf_token = document.getElementsByName('csrf_token')[0]?.getAttribute('content');

    $.ajax({
        type: "POST",
        url: 'includes/admin/ajax.php',
        data: form.serialize() + '&action=' + action + '&key=' + key + '&csrf_token=' + csrf_token + "&paymentID=" + paymentID,
        success: function (responsedata) {
            if (responsedata != 'success') {
                setNotification('Warning', responsedata, true)
                $("#paymentsActionBtn").prop('disabled', false);
            } else {
                $("#paymentsActionBtn").prop('disabled', false);
                $('#paymentsModal').find('input:text').val('');
                $('#paymentsModal').modal('hide');
                if (action == 'addPayment') {
                    setNotification('Action', 'Payment added successfully!')
                }
                if (action == 'updPayment') {
                    setNotification('Action', 'Payment updated successfully!')
                }

                $('#payments_table').DataTable().ajax.reload();

            }

        }
    });
});

function escapeHtml(text) {
    if (!text) return "";
    return text.replace(/[\"&'\/<>]/g, function (a) {
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
$('#paymentsModal').on('hidden.bs.modal', function () {
    document.getElementById("paymentsForm").reset();
})

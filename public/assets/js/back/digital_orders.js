let mainTable;
let tableName = "#orders_table";

document.addEventListener("DOMContentLoaded", function () {

    // Στήλες Πίνακα
    var columnDefs = [
        {
            data: "id",
            title: "ID",
            visible: false
        },
        {
            data: null,
            title: "Πελάτης",
            render: function (data, type, row) {
                return `
                    <div class="d-flex flex-column">
                        <span class="fw-bold">${row.first_name} ${row.last_name}</span>
                        <small class="text-muted">${row.email}</small>
                    </div>`;
            }
        },
        {
            data: "product_title",
            title: "eBook",
            render: function (data) {
                return `<span class="text-primary fw-bold">${data}</span>`;
            }
        },
        {
            data: "price",
            title: "Ποσό",
            render: function (data) {
                return data ? `${parseFloat(data).toFixed(2)} €` : "-";
            }
        },
        {
            data: "downloads_count",
            title: "Λήψεις",
            className: "text-center",
            render: function (data) {
                let color = data >= 5 ? 'danger' : (data > 0 ? 'success' : 'secondary');
                return `<span class="badge bg-${color}">${data} / 5</span>`;
            }
        },
        {
            data: "created_at",
            title: "Ημερομηνία",
            render: function (data) {
                if (!data) return "-";
                let date = new Date(data);
                return date.toLocaleDateString('el-GR');
            }
        },
        {
            // ΝΕΑ ΣΤΗΛΗ: Actions
            data: null,
            title: "Ενέργειες",
            orderable: false,
            className: "text-end",
            render: function (data, type, row) {
                return `<button class="btn btn-sm btn-light border edit-order-btn" data-id="${row.id}">
                            <i class="bi bi-pencil-square text-primary"></i>
                        </button>`;
            }
        }
    ];

    // DataTables Init
    mainTable = $(tableName).DataTable({
        paging: true,
        responsive: true,
        lengthChange: true,
        autoWidth: false,
        ordering: true,
        order: [[5, 'desc']],
        // dom: "Blrtip",
        language: {
            emptyTable: "Δεν υπάρχουν παραγγελίες.",
            search: "Αναζήτηση",
            paginate: { next: ">", previous: "<" },
            info: "Εμφάνιση _START_ έως _END_ από _TOTAL_ παραγγελίες"
        },
        columns: columnDefs,
        ajax: {
            type: "POST",
            url: "includes/admin/ajax.php",
            data: {
                action: "fetchDigitalOrders",
                format: "json",
                csrf_token: document.getElementsByName("csrf_token")[0].getAttribute("content"),
            },
            dataSrc: ""
        },
        initComplete: function () {
            $("#totalOrders").text(mainTable.rows().count());
        }
    });

    $('#searchOrders').on('keyup click', function () {
        mainTable.search($('#searchOrders').val()).draw();
    });

    // --- EDIT MODAL LOGIC ---

    // 1. Open Modal
    $(document).on('click', '.edit-order-btn', function () {
        let orderId = $(this).data('id');
        $('#modalOrderId').text(orderId);
        $('#selectedOrderId').val(orderId);
        $('#editOrderModal').modal('show');
    });

    // 2. Handle Reset Buttons
    $('.reset-action').on('click', function () {
        let type = $(this).data('type'); // 'downloads' or 'expiration'
        let orderId = $('#selectedOrderId').val();
        let csrf_token = document.getElementsByName("csrf_token")[0].getAttribute("content");

        let btn = $(this);
        btn.prop('disabled', true);

        $.ajax({
            type: "POST",
            url: "includes/admin/ajax.php",
            data: {
                action: "resetDigitalOrder",
                csrf_token: csrf_token,
                orderID: orderId,
                reset_type: type
            },
            success: function (response) {
                btn.prop('disabled', false);
                if (response.success) {
                    $('#editOrderModal').modal('hide');
                    mainTable.ajax.reload(null, false); // Refresh table

                    // Χρήση της υπάρχουσας συνάρτησης ειδοποίησης αν υπάρχει
                    if (typeof setNotification === 'function') {
                        setNotification("Επιτυχία", response.message, "success");
                    } else {
                        alert(response.message);
                    }
                } else {
                    alert("Σφάλμα: " + (response.errors ? response.errors[0] : 'Άγνωστο σφάλμα'));
                }
            },
            error: function () {
                btn.prop('disabled', false);
                alert("Σφάλμα δικτύου.");
            }
        });
    });
});
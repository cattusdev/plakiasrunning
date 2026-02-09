//table & prefix options
let mainTable;
let tableName = "#newsletter_table";
let editTitle = "";
let dataTitle = "item_name";
let genPrefix = "newsletter"
let genSuffix = "Newsletter"

//Ajax options
let dataKey = "newsletter"
let ajaxAction = "fetchSubscriptions";



let mainImage = null;
const mainImageInput = document.getElementById('mainImage');
const mainImagePreview = document.getElementById('mainImagePreview');
const mainImageUploadBtn = document.getElementById('mainImageUploadBtn');
const removeMainImageBtn = document.getElementById('removeMainImageBtn');

document.addEventListener("DOMContentLoaded", function () {

    //Table columns options
    var columnDefs = [
        {
            type: "num",
            data: "id",
            title: "ID",
            readonly: true,
            responsivePriority: 2,
            visible: false
        },
        {
            type: "text",
            data: "email",
            title: "Email",
            responsivePriority: 1,
            render: function (data, type, row, meta) {
                return `<span title="${data + row.item_name}">${data}</span>` +
                    `<div class="edit-actions d-inline-block ms-3">
                <span class="edit"">
                        <a data-bs-toggle="modal" data-bs-target="#newsletterModal"><i class="bi bi-pencil-square font-md"></i></a>
                </span>
                <span class="delete"> |
                    <a class="delSubscriber" aria-label="Διαγραφή ${editTitle}"><i class="bi bi-trash font-md"></i></a>
                </span>
            </div>`;
                // return moment(data).format('MMM D YYYY, hh:mm');
            },
            createdCell: function (td, cellData, rowData, row, col) {

            }
        },
        {
            data: "created",
            title: "Ενημέρωση",
            className: "text-nowrap",
            render: function (data) {
                return moment(data).format('MMM D YYYY, hh:mm');
            }
        }
    ];


    //Export options
    $('.optionsExport').click(function () {
        let datatableID = '#' + $(this).parent().parent().next('.dataTables_wrapper').find('table').attr('id');
        $(datatableID).DataTable().buttons('.buttons-' + $(this).next('select').val()).trigger();
    });
    $('.optionsPrint').click(function () {
        let datatableID = '#' + $(this).parent().parent().next('.dataTables_wrapper').find('table').attr('id');
        $(datatableID).DataTable().buttons('.buttons-print').trigger();
    });



    //Main table options
    mainTable = $(tableName).DataTable({
        paging: true,
        responsive: {
            details: true
        },
        fnDrawCallback: function (oSettings) {
            $("#total" + genSuffix).html($(tableName).DataTable().rows().count());
        },
        lengthChange: true,
        autoWidth: false,
        info: true,
        ordering: true,
        searching: true,
        lengthMenu: [
            [25, 50, 100, -1],
            [25, 50, 100, 'All']
        ],
        dom: 'Blrtip',
        stateSave: true,
        stateDuration: 7200,
        // dom: 'Bfrtip',
        buttons: [
            {
                extend: 'excel',
                text: 'excel',
                className: 'd-none',
            },
            {
                extend: 'csv',
                text: 'csv',
                className: 'd-none',
                fieldSeparator: ';'
            },
            {
                extend: 'pdf',
                className: 'd-none',
                orientation: 'landscape',
                pageSize: 'A2'
            },
            {
                title: editTitle,
                extend: 'print',
                text: 'print',
                className: 'd-none',
            },
        ],
        language: {
            "emptyTable": "Δεν υπάρχουν στοιχεία προς εμφάνιση.",
            search: "Αναζήτηση",
            info: "Εμφάνιση _START_ έως _END_ από _TOTAL_ καταχωρήσεις",
            paginate: {
                "next": "Επόμενη",
                "previous": "Προηγούμενη",

            },
            lengthMenu: "Kαταχωρήσεις _MENU_"
        },
        processing: false,
        columns: columnDefs,
        columnDefs: [
            {

                targets: '_all',
                render: function (data, type, row) {
                    if (data === null || data === '' || data === 'null' || (type === 'date' && isNaN(Date.parse(data)) && isNaN(data))) {
                        return '-';
                    }
                    return data;
                }
            },
            { "width": "100px", "targets": 0 },
        ],
        ajax: {
            type: 'POST',
            url: '/includes/admin/ajax.php',
            data: {
                action: ajaxAction,
                format: 'json',
                csrf_token: document.getElementsByName('csrf_token')[0].getAttribute('content')
            },
            dataSrc: function (responseText) {
                return responseText;
            }
        },

        onPreRefresh: function (datatable) { },

        onRefresh: function (datatable) {

        },

        initComplete: function (settings) {
            //Row Count
            $("#total" + genSuffix).html($(tableName).DataTable().rows().count());
        }
    });

});

$('#searchNewsletter').on('keyup click', function () {
    mainTable.search(
        $('#searchNewsletter').val()
    ).draw();
});


//Add new on modal
$(document).on("click", "#addNewItem", function () {
    document.getElementById(genPrefix + "Form").reset();
    $("#newsletterCreated").html(``);
    $("#newsletterUpdated").html(``);
    $("#" + genPrefix + "ActionBtn").attr('data-key', dataKey);
    $("#" + genPrefix + "ActionBtn").attr('data-action', 'addSubscriber');
    $("#" + genPrefix + "ActionBtn").text('Αποθήκευση');
    $("#" + genPrefix + "ModalLongTitle").text('Προσθήκη');

});



//Edit on modal
$(document).on("click", "#newsletter_table .edit-actions", function () {

    //Get row data
    let row = $(this).parents('tr')[0];
    let rowData = mainTable.row(row).data();

    $("#newsletterUpdated").html(`<strong>Ενημερώθηκε:</strong> ${rowData.updated_at}`);


    $("#email").val(rowData.email);
    $("#mainID").val(rowData.id);

    $("#" + genPrefix + "ModalLongTitle").html(`Επεξεργασία <strong>${rowData.email} </strong><br><span class="font-xs float-end badge bg-info"><strong>Δημιουργήθηκε:</strong> ${rowData.created}</span>`);
    // $("#" + genPrefix + "ModalLongTitle").html(`Επεξεργασία <strong>${rowData.name} </strong>`);

    $("#" + genPrefix + "ActionBtn").attr('data-key', dataKey);
    $("#" + genPrefix + "ActionBtn").attr('data-action', 'updSubscriber');
    $("#" + genPrefix + "ActionBtn").text('Ενημέρωση');

});


$('body').on('click', '#' + genPrefix + 'ActionBtn', function (e) {
    e.preventDefault();
    $("#" + genPrefix + "ActionBtn").prop('disabled', true);
    var action = $(this).attr('data-action');
    var mainID = $("#mainID").val();

    // Create FormData object
    var formData = new FormData(document.getElementById(genPrefix + "Form"));

    // Append additional data
    formData.append('action', action);
    formData.append('mainID', mainID);

    // CSRF token
    let csrf_token = document.getElementsByName('csrf_token')[0].getAttribute('content');
    formData.append('csrf_token', csrf_token);

    showLoader('#newsletterForm');

    $.ajax({
        type: "POST",
        url: '/includes/admin/ajax.php',
        data: formData,
        contentType: false, 
        processData: false,
        success: function (response) {
            $("#" + genPrefix + "ActionBtn").prop('disabled', false);
            if (response.success) {
                showLoader('#newsletterForm', 'success');
                $("#" + genPrefix + "Modal").modal("hide");
                mainTable.ajax.reload(null, false);
                setNotification('Action', response.message, 'success');
            } else {
                var errorMessage = '';
                $.each(response.errors, function (index, error) {
                    errorMessage += error + "<br>";
                });
                showLoader('#newsletterForm', 'error');
                $("#" + genPrefix + "ActionBtn").prop("disabled", false);
                setNotification('Warning', errorMessage, 'warning');
            }
        },
        error: function (error) {
            $("#" + genPrefix + "ActionBtn").prop("disabled", false);
            var errorMessage = '';
            $.each(error.errors, function (index, error) {
                errorMessage += error + "<br>";
            });
            showLoader('#newsletterForm', 'error');
            setNotification('Error', errorMessage, 'error');
        }
    });
});


//Delete
$(document).on("click", ".delSubscriber", async function () {
    let row = $(this).parents('tr')[0];
    let rowData = mainTable.row(row).data();
    let csrf_token = document.getElementsByName('csrf_token')[0].getAttribute('content');
    const actionConfirmed = await handleNotificationAction('Επιβεβαίωση Ενέργειας', 'Διαγραφή; Αυτή η πράξη δε μπορεί να αναιρεθεί.');
    if (actionConfirmed) {
        $.ajax({
            type: 'post',
            url: '/includes/admin/ajax.php',
            data: {
                action: "delSubscriber",
                csrf_token: csrf_token,
                mainID: rowData.id,
            },
            success: function (response) {
                if (response.success) {
                    $("#" + genPrefix + "Modal").modal("hide");
                    mainTable.ajax.reload(null, false);
                    setNotification('Action', response.message, 'success');
                } else {
                    var errorMessage = '';
                    $.each(response.errors, function (index, error) {
                        errorMessage += error + "<br>";
                    });
                    setNotification('Warning', errorMessage, 'warning');
                }
            },
            error: function (error) {
                var errorMessage = '';
                $.each(error.errors, function (index, error) {
                    errorMessage += error + "<br>";
                });
                setNotification('Error', errorMessage, 'error');
            },

        });

    }
});


function reloadTables() {
    mainTable.ajax.reload(null, false);
}
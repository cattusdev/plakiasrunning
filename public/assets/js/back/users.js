//table & prefix options
let mainTable;
let tableName = "#users_table";
let editTitle = "User(s)";
let dataTitle = "title";
let genPrefix = "users"
let genSuffix = "Users"

//Ajax options
let dataKey = "users"
let ajaxAction = "fetchUsers";

document.addEventListener("DOMContentLoaded", function () {

    //Table columns options
    var columnDefs = [{
        type: "num",
        data: "id",
        title: "ID",
        readonly: true,
        responsivePriority: 2,
        visible: false
    },
    {
        type: "text",
        data: "firstName",
        title: "Όνομα",
        responsivePriority: 1,
        render: function (data, type, row, meta) {
            return `<span title="${data + row.firstName}">${data}</span>
     
        <div class="edit-actions d-inline-block ms-3">
            <span class="edit">
                <a href="edit-user?userID=${row.id}"><i class="bi bi-pencil-square font-md"></i></a>
            </span>
            <span class="delete"> |
                <a class="del${genSuffix}" aria-label="Διαγραφή ${editTitle}"><i class="bi bi-trash font-md"></i></a>
            </span>
        </div>`;

            // return moment(data).format('MMM D YYYY, hh:mm');
        },
        createdCell: function (td, cellData, rowData, row, col) {

        }
    },
    {
        type: "text",
        data: "lastName",
        title: "Επώνυμο",
    },
    {
        type: "text",
        data: "access",
        title: "Ρόλος",
        createdCell: function (td, cellData, rowData, row, col) {
            var spanClass = '';
            var statusText = '';
            switch (cellData) {
                case 1:
                    spanClass = 'badge bg-rejected';
                    statusText = 'Διαχειριστής';
                    break;
                case 2:
                    spanClass = 'badge bg-canceled text-white';
                    statusText = 'Υπάλληλος';
                    break;
                default:
                    statusText = '-';
                    break;
            }
            var span = $('<span>', {
                'class': spanClass,
                'text': statusText
            });

            $(td).empty().append(span);
        }
    },
    {
        type: "date",
        data: "last_login_attempt",
        title: "Συνδέθηκε",
        render: function (data, type, row, meta) {
            return moment(data).format('MMM D YYYY, hh:mm');
        },
    },
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
            }
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
            },
            complete: function (data) {
                console.log(data['responseJSON']);
                const notificationId = getNotificationIdFromUrl();
                if (notificationId) {
                    openModalForNotification(notificationId);
                }
            },
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
function getNotificationIdFromUrl() {
    const urlParams = new URLSearchParams(window.location.search);
    return urlParams.get('notification');
}

$('#searchUsers').on('keyup click', function () {
    mainTable.search(
        $('#searchUsers').val()
    ).draw();
});


//Add new on modal
$(document).on("click", "#addNewUser", function () {
    $("#" + genPrefix + "ActionBtn").attr('data-key', dataKey);
    $("#" + genPrefix + "ActionBtn").attr('data-action', 'addUser');
    $("#" + genPrefix + "ActionBtn").text('Αποθήκευση');
    $("#" + genPrefix + "ModalLongTitle").text('Προσθήκη');

});



//Edit on modal
// $(document).on("click", "#users_table .edit-actions .edit", function () {

//     //Get row data
//     document.getElementById("usersForm").reset();
//     let row = $(this).parents('tr')[0];
//     let rowData = mainTable.row(row).data();
//     $('#sendEmail').prop('checked', false);
//     $("#registerEmail").val(rowData.email);
//     $("#registerFirstName").val(rowData.firstName);
//     $("#registerLastName").val(rowData.lastName);
//     $("#userRole").val(rowData.access);

//     $("#userUpdated").html(`<strong>Τελευταία Ενημέρωση:</strong> ${rowData.updated_at}`);


//     $("#mainID").val(rowData.id);

//     $("#" + genPrefix + "ModalLongTitle").html(`Επεξεργασία <strong>${rowData.lastName} </strong><br><span class="font-xs float-end badge bg-info"><strong>Δημιουργήθηκε:</strong> ${rowData.created_at}</span>`);
//     // $("#" + genPrefix + "ModalLongTitle").html(`Επεξεργασία <strong>${rowData.title} </strong>`);

//     $("#" + genPrefix + "ActionBtn").attr('data-key', dataKey);
//     $("#" + genPrefix + "ActionBtn").attr('data-action', 'updUser');
//     $("#" + genPrefix + "ActionBtn").text('Επεξεργασία');

// });

function showpwd() {
    $('.is-pass').each(function () {
        var value = $(this).attr('type');
        if (value == 'password' && $(this).hasClass('is-pass')) {
            $(this).attr('type', 'text');
            $(this).prop('title', 'Hide Password');

        } else {
            if (value == 'text' && $(this).hasClass('is-pass')) {
                $(this).attr('type', 'password');
                $(this).prop('title', 'Show Password');
            }
        }
    });
    $('.toggle-password').toggleClass('bi-eye-slash bi-eye')
}


$('body').on('click', '#' + genPrefix + 'ActionBtn', function (e) {
    e.preventDefault();
    $("#" + genPrefix + "ActionBtn").prop('disabled', true);
    var key = $(this).attr('data-key');
    var action = $(this).attr('data-action');
    var form = $("#" + genPrefix + "Form");

    var mainID = $("#mainID").val();

    let csrf_token = document.getElementsByName('csrf_token')[0].getAttribute('content');
    showLoader('#usersForm');



    $.ajax({
        type: "POST",
        url: '/includes/admin/ajax.php',
        data: form.serialize() + `&action=${action}&mainID=${mainID}&csrf_token=${csrf_token}`,
        success: function (response) {
            $("#" + genPrefix + "ActionBtn").prop('disabled', false);
            if (response.success) {
                showLoader('#usersForm', 'success');
                $("#" + genPrefix + "Modal").modal("hide");
                mainTable.ajax.reload(null, false);
                setNotification('Action', response.message, 'success');
            } else {
                var errorMessage = '';
                $.each(response.errors, function (index, error) {
                    errorMessage += error + "<br>";
                });
                showLoader('#usersForm', 'error');
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
            showLoader('#usersForm', 'error');
            setNotification('Error', errorMessage, 'error');
        }
    });
});


//Delete
$(document).on("click", ".del" + genSuffix, async function () {
    let row = $(this).parents('tr')[0];
    let rowData = mainTable.row(row).data();
    let csrf_token = document.getElementsByName('csrf_token')[0].getAttribute('content');
    const actionConfirmed = await handleNotificationAction('Επιβεβαίωση Ενέργειας', 'Διαγραφή; Αυτή η πράξη δε μπορεί να αναιρεθεί.');
    if (actionConfirmed) {
        $.ajax({
            type: 'post',
            url: '/includes/admin/ajax.php',
            data: {
                action: "del" + genSuffix,
                csrf_token: csrf_token,
                mainID: rowData.id,
            },
            success: function (response) {
                if (response.success) {
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

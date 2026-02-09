// --- Options & Globals ---
let mainTable;
const tableName = "#clients_table";
const dataKey = "clients";
const ajaxAction = "fetchClients"; // Το PHP case που επιστρέφει τα active=1

document.addEventListener("DOMContentLoaded", function () {

    // --- DataTable Setup ---
    mainTable = $(tableName).DataTable({
        paging: true,
        responsive: true,
        processing: true, // Show processing indicator
        autoWidth: false,
        lengthChange: true,
        lengthMenu: [[25, 50, 100, -1], [25, 50, 100, 'Όλα']],
        pageLength: 25,
        dom: 'rtip', // Κρύβουμε το default search/buttons του DT γιατί έχουμε custom
        stateSave: true,

        language: {
            "emptyTable": "Δεν βρέθηκαν πελάτες.",
            "info": "Εμφάνιση _START_ έως _END_ από _TOTAL_ πελάτες",
            "infoEmpty": "Εμφάνιση 0 έως 0 από 0 πελάτες",
            "loadingRecords": "Φόρτωση...",
            "processing": "Επεξεργασία...",
            "paginate": {
                "first": "Αρχική",
                "last": "Τελευταία",
                "next": "Επόμενη",
                "previous": "Προηγούμενη"
            }
        },

        ajax: {
            url: '/includes/admin/ajax.php',
            type: 'POST',
            data: function (d) {
                d.action = ajaxAction;
                d.csrf_token = getCsrfToken();
            },
            dataSrc: function (json) {
                // Αν επιστρέψει error ή false
                if (!json || json.error) {
                    console.error("Data error", json);
                    return [];
                }
                return json;
            }
        },

        columns: [
            {
                data: "id",
                title: "ID",
                visible: false
            },
            {
                data: "first_name",
                title: "Ονοματεπώνυμο",
                render: function (data, type, row) {
                    // Συνδυασμός ονόματος + ενεργειών
                    const fullName = escapeHtml(row.first_name) + " " + escapeHtml(row.last_name);
                    return `
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="fw-bold text-primary">${fullName}</div>
                            <div class="actions">
                                <button class="btn btn-sm btn-light text-primary edit-client" title="Επεξεργασία">
                                    <i class="bi bi-pencil-square"></i>
                                </button>
                                <button class="btn btn-sm btn-light text-danger del-client" title="Διαγραφή">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </div>
                    `;
                }
            },
            {
                data: "phone",
                title: "Τηλέφωνο",
                render: function (data) {
                    return data ? `<a href="tel:${data}" class="text-decoration-none text-dark">${escapeHtml(data)}</a>` : '-';
                }
            },
            {
                data: "email",
                title: "Email",
                render: function (data) {
                    return data ? `<a href="mailto:${data}" class="text-decoration-none">${escapeHtml(data)}</a>` : '-';
                }
            },
            {
                data: "created_at",
                title: "Εγγραφή",
                className: "text-muted small",
                render: function (data) {
                    if (!data) return '-';
                    // Format: 24 Ιαν 2024
                    const d = new Date(data.replace(' ', 'T')); // Fix SQL date format for JS
                    return d.toLocaleDateString('el-GR', { day: 'numeric', month: 'short', year: 'numeric' });
                }
            }
        ]
    });

    // --- Custom Search ---
    $('#searchClients').on('keyup', function () {
        mainTable.search(this.value).draw();
    });

    // --- Export Actions ---
    $('#exportOptions').on('change', function () {
        const val = $(this).val();
        if (!val) return;

        // Εδώ χρειάζεσαι το Buttons extension του DataTables
        // Αν δεν το έχεις φορτώσει, αυτό δεν θα δουλέψει.
        // Αν το έχεις, ενεργοποίησέ το στο 'dom' ή κάλεσε το api instance.
        try {
            mainTable.button(`.buttons-${val}`).trigger();
        } catch (e) {
            console.warn("Export buttons not initialized or missing.");
        }
        $(this).val(""); // Reset select
    });
});

// --- Actions ---

// 1. Open Modal for NEW Client
$(document).on("click", "#addNewClient", function () {
    document.getElementById("clientsForm").reset();
    $("#clientID").val(""); // Clear ID implies CREATE

    $("#clientsModalLongTitle").text("Προσθήκη Νέου Πελάτη");
    $("#clientsActionBtn").text("Δημιουργία").attr("data-action", "addClient");
    $("#clientUpdated").text("");

    $("#clientsModal").modal("show");
});

// 2. Open Modal for EDIT Client
$(document).on("click", ".edit-client", function () {
    const tr = $(this).closest('tr');
    const rowData = mainTable.row(tr).data();

    $("#fname").val(rowData.first_name);
    $("#lname").val(rowData.last_name);
    $("#email").val(rowData.email);
    $("#phone").val(rowData.phone);
    $("#clientNote").val(rowData.client_note);
    $("#clientID").val(rowData.id);

    // Show updated info
    const updatedDate = rowData.updated_at ? new Date(rowData.updated_at.replace(' ', 'T')).toLocaleDateString('el-GR') : '-';
    $("#clientUpdated").html(`<i class="bi bi-clock-history"></i> Τελευταία ενημέρωση: ${updatedDate}`);

    $("#clientsModalLongTitle").text(`Επεξεργασία: ${rowData.first_name} ${rowData.last_name}`);
    $("#clientsActionBtn").text("Ενημέρωση").attr("data-action", "updClient");

    $("#clientsModal").modal("show");
});

// 3. Save (Add or Update)
$(document).on("click", "#clientsActionBtn", function (e) {
    e.preventDefault();
    const btn = $(this);
    const action = btn.attr('data-action'); // addClient or updClient
    const form = $("#clientsForm");

    // Basic Validation
    if (!$("#fname").val() || !$("#lname").val() || !$("#phone").val()) {
        setNotification("Προσοχή", "Συμπληρώστε Όνομα, Επώνυμο και Τηλέφωνο.", "warning");
        return;
    }

    btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span>');

    const payload = form.serialize() +
        `&action=${action}` +
        `&csrf_token=${getCsrfToken()}` +
        `&clientID=${$("#clientID").val()}`; // needed for update

    $.ajax({
        type: "POST",
        url: '/includes/admin/ajax.php',
        data: payload,
        dataType: 'json',
        success: function (res) {
            btn.prop('disabled', false).text(action === 'addClient' ? 'Δημιουργία' : 'Ενημέρωση');

            if (res.success) {
                $("#clientsModal").modal("hide");
                mainTable.ajax.reload(null, false); // Reload table without resetting paging
                setNotification("Επιτυχία", res.message || "Η ενέργεια ολοκληρώθηκε.", "success");
            } else {
                let errorMsg = "";
                if (Array.isArray(res.errors)) errorMsg = res.errors.join("<br>");
                else errorMsg = res.errors || "Άγνωστο σφάλμα";
                setNotification("Σφάλμα", errorMsg, "error");
            }
        },
        error: function () {
            btn.prop('disabled', false).text("Προσπάθεια ξανά");
            setNotification("Σφάλμα", "Υπήρξε πρόβλημα επικοινωνίας με τον server.", "error");
        }
    });
});

// 4. Delete (Soft Delete)
$(document).on("click", ".del-client", async function () {
    const tr = $(this).closest('tr');
    const rowData = mainTable.row(tr).data();

    // Use your existing confirmation helper
    const confirmed = await handleNotificationAction(
        'Διαγραφή Πελάτη',
        `Είστε σίγουροι ότι θέλετε να διαγράψετε τον πελάτη <strong>${rowData.first_name} ${rowData.last_name}</strong>;`
    );

    if (confirmed) {
        $.ajax({
            type: 'POST',
            url: '/includes/admin/ajax.php',
            data: {
                action: "delClients", // Calls the soft delete in backend
                clientID: rowData.id,
                csrf_token: getCsrfToken()
            },
            success: function (res) {
                if (res.success) {
                    mainTable.ajax.reload(null, false);
                    setNotification("Επιτυχία", "Ο πελάτης διαγράφηκε (απενεργοποιήθηκε).", "success");
                } else {
                    setNotification("Σφάλμα", "Δεν ήταν δυνατή η διαγραφή.", "error");
                }
            },
            error: function () {
                setNotification("Σφάλμα", "Network Error", "error");
            }
        });
    }
});

// --- Helpers ---

function getCsrfToken() {
    // Try meta tag first, then input
    return document.querySelector('meta[name="csrf_token"]')?.getAttribute('content') ||
        document.querySelector('input[name="csrf_token"]')?.value || '';
}

function escapeHtml(text) {
    if (!text) return "";
    return text
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;")
        .replace(/'/g, "&#039;");
}

// Global notification wrapper (Assumes you have a setNotification function in your main layout js)
// If not, here is a simple fallback:
if (typeof setNotification === 'undefined') {
    window.setNotification = function (title, msg, type) {
        alert(`${title}: ${msg}`); // Replace with Toastr or SweetAlert
    };
}

// Confirmation wrapper (Assumes you have handleNotificationAction)
// If not, simple fallback:
if (typeof handleNotificationAction === 'undefined') {
    window.handleNotificationAction = function (title, msg) {
        return new Promise(resolve => resolve(confirm(msg)));
    };
}
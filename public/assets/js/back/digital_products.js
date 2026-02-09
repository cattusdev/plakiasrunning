// Table & Prefix Options
let mainTable;
let tableName = "#products_table";
let editTitle = "eBook";
let genPrefix = "products";
let ajaxAction = "fetchDigitalProducts";

document.addEventListener("DOMContentLoaded", function () {
    // Table Columns Options
    var columnDefs = [
        {
            type: "num",
            data: "id",
            title: "ID",
            visible: false,
        },
        {
            // Στήλη Εικόνας
            data: "cover_image",
            title: "Εξώφυλλο",
            orderable: false,
            searchable: false,
            width: "60px",
            render: function (data) {
                if (data) {
                    // Υποθέτουμε ότι το path είναι σχετικό με το root, π.χ. uploads/ebooks/covers/...
                    return `<img src="/${data}" style="height: 50px; width: auto; border-radius: 4px;" alt="cover">`;
                }
                return '<i class="bi bi-image text-muted" style="font-size: 24px;"></i>';
            }
        },
        {
            type: "text",
            data: "title",
            title: "Τίτλος",
            responsivePriority: 1,
            render: function (data, type, row) {
                return `<strong>${data}</strong>` +
                    `<div class="edit-actions d-inline-block ms-3">
                        <span class="edit">
                            <a data-bs-toggle="modal" data-bs-target="#productsModal" class="text-primary hand"><i class="bi bi-pencil-square font-md"></i></a>
                        </span>
                        <span class="delete"> |
                            <a class="delProduct text-danger hand" aria-label="Delete"><i class="bi bi-trash font-md"></i></a>
                        </span>
                    </div>`;
            },
        },
        {
            type: "num",
            data: "price",
            title: "Τιμή",
            render: function (data) {
                return data ? `${parseFloat(data).toFixed(2)} €` : "Δωρεάν";
            },
        },
        {
            type: "text",
            data: "created_at",
            title: "Ημερομηνία",
            render: function (data) {
                return data ? new Date(data).toLocaleDateString('el-GR') : '-';
            }
        },
    ];

    // Main Table Init
    mainTable = $(tableName).DataTable({
        paging: true,
        responsive: { details: true },
        lengthChange: true,
        autoWidth: false,
        // dom: "Blrtip",
        language: {
            emptyTable: "Δεν βρέθηκαν eBooks.",
            search: "Αναζήτηση",
            paginate: { next: ">", previous: "<" },
        },
        columns: columnDefs,
        ajax: {
            type: "POST",
            url: "includes/admin/ajax.php",
            data: {
                action: ajaxAction,
                format: "json",
                csrf_token: document.getElementsByName("csrf_token")[0].getAttribute("content"),
            },
            dataSrc: "" // Expecting direct array
        }
    });

    // Custom Search Box
    $('#searchProducts').on('keyup click', function () {
        mainTable.search($('#searchProducts').val()).draw();
    });

    // --- MODAL ACTIONS ---

    // 1. Add New (Reset Form)
    $(document).on("click", "#addNewProduct", function () {
        document.getElementById(genPrefix + "Form").reset();

        // Hide "Existing file" messages
        $("#currentFileMsg").hide();
        $("#currentCoverMsg").hide();
        $("#coverPreview").attr('src', '');

        $("#" + genPrefix + "ActionBtn").attr("data-action", "addDigitalProduct");
        $("#" + genPrefix + "ActionBtn").text("Προσθήκη eBook");
        $("#" + genPrefix + "ModalLongTitle").text("Προσθήκη Νέου eBook");
    });

    // 2. Edit (Populate Form)
    $(document).on("click", "#products_table .edit", function () {
        let row = $(this).closest("tr");
        let rowData = mainTable.row(row).data();

        $("#productID").val(rowData.id);
        $("#title").val(rowData.title);
        $("#price").val(rowData.price);
        $("#description").val(rowData.description);

        // Reset inputs
        $("#ebook_file").val('');
        $("#cover_image").val('');

        // --- Διαχείριση Εμφάνισης Αρχείου PDF ---
        if (rowData.file_path) {
            $("#currentFileMsg").show();
            // Παίρνουμε μόνο το όνομα του αρχείου από το full path
            let fileName = rowData.file_path.split('/').pop();
            $("#currentFileName").text(fileName);
        } else {
            $("#currentFileMsg").hide();
            $("#currentFileName").text('');
        }

        // --- Διαχείριση Εμφάνισης Εικόνας ---
        if (rowData.cover_image) {
            $("#coverPreview").attr('src', '/' + rowData.cover_image);
            $("#currentCoverMsg").show();
        } else {
            $("#coverPreview").attr('src', '');
            $("#currentCoverMsg").hide();
        }

        $("#" + genPrefix + "ActionBtn").attr("data-action", "updDigitalProduct");
        $("#" + genPrefix + "ActionBtn").text("Ενημέρωση");
        $("#" + genPrefix + "ModalLongTitle").text("Επεξεργασία: " + rowData.title);
    });

    // 3. Save Changes (Create or Update)
    $("#" + genPrefix + "ActionBtn").on("click", function (e) {
        e.preventDefault();
        let btn = $(this);
        btn.prop("disabled", true);

        let action = btn.attr("data-action");
        let csrf_token = document.getElementsByName("csrf_token")[0].getAttribute("content");

        // --- Χρήση FormData για ανέβασμα αρχείων ---
        let formData = new FormData(document.getElementById(genPrefix + "Form"));
        formData.append("action", action);
        formData.append("csrf_token", csrf_token);

        $.ajax({
            type: "POST",
            url: "includes/admin/ajax.php",
            data: formData,
            processData: false, // Σημαντικό: Μην επεξεργαστείς τα δεδομένα
            contentType: false, // Σημαντικό: Μην ορίσεις content type (το κάνει ο browser αυτόματα)
            success: function (response) {
                btn.prop("disabled", false);
                if (response.success) {
                    $("#" + genPrefix + "Modal").modal("hide");
                    mainTable.ajax.reload(null, false);
                    // Χρήση της υπάρχουσας συνάρτησης ειδοποιήσεων
                    if (typeof setNotification === 'function') {
                        setNotification("Action", response.message, "success");
                    } else {
                        alert(response.message);
                    }
                } else {
                    let errorMessage = response.errors ? response.errors.join("<br>") : "Σφάλμα.";
                    if (typeof setNotification === 'function') {
                        setNotification("Warning", errorMessage, "warning");
                    } else {
                        alert(errorMessage);
                    }
                }
            },
            error: function () {
                btn.prop("disabled", false);
                alert("Σφάλμα επικοινωνίας με τον server.");
            },
        });
    });

    // 4. Delete Action
    $(document).on("click", ".delProduct", async function () {
        let row = $(this).closest("tr");
        let rowData = mainTable.row(row).data();
        let csrf_token = document.getElementsByName("csrf_token")[0].getAttribute("content");

        // Ακριβώς όπως το ζήτησες:
        const actionConfirmed = await handleNotificationAction(
            "Επιβεβαίωση Ενέργειας",
            "Διαγραφή; Αυτή η πράξη δε μπορεί να αναιρεθεί."
        );

        if (actionConfirmed) {
            $.ajax({
                type: "POST",
                url: "includes/admin/ajax.php",
                data: {
                    action: "delDigitalProduct",
                    csrf_token: csrf_token,
                    productID: rowData.id,
                },
                success: function (response) {
                    if (response.success) {
                        mainTable.ajax.reload(null, false);
                        // Εδώ χρησιμοποιούμε το setNotification για την επιτυχία
                        setNotification("Deleted", response.message, "success");
                    } else {
                        // Εδώ για το error
                        let errorMessage = response.errors ? response.errors.join("<br>") : "Σφάλμα κατά τη διαγραφή.";
                        setNotification("Error", errorMessage, "error");
                    }
                },
                error: function () {
                    setNotification("Error", "Αποτυχία διαγραφής του προϊόντος.", "error");
                },
            });
        }
    });

    // --- LIVE IMAGE PREVIEW ---
    $("#cover_image").change(function () {
        const file = this.files[0];
        if (file) {
            let reader = new FileReader();
            reader.onload = function (event) {
                $('#coverPreview').attr('src', event.target.result);
                $('#currentCoverMsg').show();
            }
            reader.readAsDataURL(file);
        }
    });
});
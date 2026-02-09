// Table & Prefix Options
let mainTable;
let tableName = "#packages_table";
let editTitle = "Πακέτου";
let dataTitle = "title";
let genPrefix = "packages";
let genSuffix = "EdietPackages";

// Ajax Options
let dataKey = "packages";
let ajaxAction = "fetchEdietPackages";

document.addEventListener("DOMContentLoaded", function () {
    // Table Columns Options
    var columnDefs = [
        {
            type: "num",
            data: "id",
            title: "ID",
            readonly: true,
            responsivePriority: 2,
            visible: false,
        },
        {
            type: "text",
            data: "title",
            title: "Τίτλος",
            responsivePriority: 1,
            render: function (data, type, row, meta) {
                return `<span>${data}</span>` +
                    `<div class="edit-actions d-inline-block ms-3">
                        <span class="edit">
                            <a data-bs-toggle="modal" data-bs-target="#packagesModal"><i class="bi bi-pencil-square font-md"></i></a>
                        </span>
                        <span class="delete"> |
                            <a class="del${genSuffix}" aria-label="Delete ${editTitle}"><i class="bi bi-trash font-md"></i></a>
                        </span>
                    </div>`;
            },
        },
        {
            type: "text",
            data: "price",
            title: "Τιμή (€)",
            render: function (data) {
                return data ? `${data} €` : "-";
            },
        },
        {
            type: "text",
            data: "created_at",
            title: "Δημιουργήθηκε",
        },
    ];

    // Main Table Options
    mainTable = $(tableName).DataTable({
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
            { title: editTitle, extend: "print", text: "print", className: "d-none" },
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
            type: "POST",
            url: "includes/admin/ajax.php",
            data: {
                action: ajaxAction,
                format: "json",
                csrf_token: document.getElementsByName("csrf_token")[0].getAttribute("content"),
            },
            dataSrc: function (responseText) {
                return responseText;
            },
        },
        initComplete: function (settings) {
            $("#total" + genSuffix).html($(tableName).DataTable().rows().count());
        },
    });

    $('#searchPackages').on('keyup click', function () {
        mainTable.search(
            $('#searchPackages').val()
        ).draw();
    });

    // Includes List Management
    const includesInput = document.getElementById("includesInput");
    const includesList = document.getElementById("includesList");
    const addIncludeBtn = document.getElementById("addIncludeBtn");
    let includesArray = [];

    // Add Include
    addIncludeBtn.addEventListener("click", function () {
        const includeText = includesInput.value.trim();
        if (includeText) {
            includesArray.push({ id: Date.now(), text: includeText }); // Add with correct structure
            renderIncludes();
            includesInput.value = ""; // Clear input after adding
        }
    });

    // Render Includes
    function renderIncludes() {
        includesList.innerHTML = ""; // Clear the list
        includesArray.forEach((include) => {
            const li = document.createElement("li");
            li.classList.add("list-group-item", "d-flex", "justify-content-between", "align-items-center");
            li.setAttribute("data-id", include.id); // Use ID for tracking

            li.innerHTML = `
            <span>${include.text}</span> <!-- Access the 'text' property -->
            <div class="d-flex">
                <button type="button" class="btn btn-sm btn-warning me-1 editIncludeBtn bi bi-pencil-square"></button>
                <button type="button" class="btn btn-sm btn-danger removeIncludeBtn bi bi-trash"></button>
            </div>
        `;

            includesList.appendChild(li);
        });
    }


    // Edit Include
    includesList.addEventListener("click", function (e) {
        if (e.target.classList.contains("editIncludeBtn")) {
            const li = e.target.closest("li");
            const includeId = li.getAttribute("data-id");
            const includeIndex = includesArray.findIndex((item) => item.id == includeId);

            if (includeIndex > -1) {
                // Populate input field with the text to edit
                includesInput.value = includesArray[includeIndex].text;

                // Remove the include temporarily only after confirmation
                includesArray.splice(includeIndex, 1);
                renderIncludes();
            }
        }
    });


    // Remove Include
    includesList.addEventListener("click", function (e) {
        if (e.target.classList.contains("removeIncludeBtn")) {
            const li = e.target.closest("li");
            const includeId = li.getAttribute("data-id");
            includesArray = includesArray.filter((item) => item.id != includeId);
            renderIncludes();
        }
    });

    // Add New on Modal
    $(document).on("click", "#addNewPackage", function () {
        document.getElementById(genPrefix + "Form").reset();
        includesArray = [];
        renderIncludes();
        $("#" + genPrefix + "ActionBtn").attr("data-key", dataKey);
        $("#" + genPrefix + "ActionBtn").attr("data-action", "addEdietPackage");
        $("#" + genPrefix + "ActionBtn").text("Προσθήκη");
        $("#" + genPrefix + "ModalLongTitle").text("Προσθήκη " + editTitle);
    });

    // Edit on Modal
    $(document).on("click", "#packages_table .edit-actions", function () {
        let row = $(this).parents("tr")[0];
        let rowData = mainTable.row(row).data();

        $("#title").val(rowData.title);
        $("#price").val(rowData.price);
        $("#description").val(rowData.description);
        $("#packageID").val(rowData.id);

        // Parse and ensure structure for includes
        includesArray = rowData.includes
            ? JSON.parse(rowData.includes).map((text, index) => ({ id: index + 1, text })) // Assign unique IDs
            : [];
        renderIncludes();

        $("#" + genPrefix + "ModalLongTitle").html(`Επεξεργασία Πακέτου: <strong>${rowData.title}</strong>`);
        $("#" + genPrefix + "ActionBtn").attr("data-key", dataKey);
        $("#" + genPrefix + "ActionBtn").attr("data-action", "updEdietPackage");
        $("#" + genPrefix + "ActionBtn").text("Ενημέρωση");
    });

    // Save Changes
    $("body").on("click", "#" + genPrefix + "ActionBtn", function (e) {
        e.preventDefault();
        $("#" + genPrefix + "ActionBtn").prop("disabled", true);

        let action = $(this).attr("data-action");
        let csrf_token = document.getElementsByName("csrf_token")[0].getAttribute("content");

        // Collect package data
        const packageData = {
            packageID: $("#packageID").val(),
            title: $("#title").val(),
            price: $("#price").val() || null,
            description: $("#description").val(),
            includes: JSON.stringify(includesArray.map((item) => item.text)),
        };

        $.ajax({
            type: "POST",
            url: "includes/admin/ajax.php",
            data: {
                action,
                csrf_token,
                ...packageData,
            },
            success: function (response) {
                $("#" + genPrefix + "ActionBtn").prop("disabled", false);
                if (response.success) {
                    $("#" + genPrefix + "Modal").modal("hide");
                    mainTable.ajax.reload(null, false);
                    setNotification("Action", response.message, "success");
                } else {
                    let errorMessage = response.errors ? response.errors.join("<br>") : "An error occurred.";
                    setNotification("Warning", errorMessage, "warning");
                }
            },
            error: function () {
                $("#" + genPrefix + "ActionBtn").prop("disabled", false);
                setNotification("Error", "Failed to perform the action.", "error");
            },
        });
    });

    // Delete Package
    $(document).on("click", ".del" + genSuffix, async function () {
        let row = $(this).closest("tr")[0];
        let rowData = mainTable.row(row).data();
        let csrf_token = document.getElementsByName("csrf_token")[0].getAttribute("content");

        const actionConfirmed = await handleNotificationAction(
            "Επιβεβαίωση Ενέργειας",
            "Διαγραφή; Αυτή η πράξη δε μπορεί να αναιρεθεί."
        );

        if (actionConfirmed) {
            $.ajax({
                type: "POST",
                url: "includes/admin/ajax.php",
                data: {
                    action: "delPackage",
                    csrf_token,
                    packageID: rowData.id,
                },
                success: function (response) {
                    if (response.success) {
                        mainTable.ajax.reload(null, false);
                        setNotification("Action", response.message, "success");
                    } else {
                        let errorMessage = response.errors ? response.errors.join("<br>") : "An error occurred.";
                        setNotification("Warning", errorMessage, "warning");
                    }
                },
                error: function () {
                    setNotification("Error", "Failed to delete the package.", "error");
                },
            });
        }
    });
});

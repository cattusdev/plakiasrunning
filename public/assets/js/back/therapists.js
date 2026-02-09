/**
 * therapists.js
 * Handles CRUD operations for Guides/Pacers using DataTables and AJAX.
 */

// Table & Prefix Options
let therapistTable;
let tableName = "#therapists_table";
let genPrefix = "therapists";
let ajaxAction = "fetchTherapistsData";

// Store available packages globally to populate dropdowns
let availablePackagesList = [];

document.addEventListener("DOMContentLoaded", function () {

    // ==========================================
    // 1. Table Columns Configuration
    // ==========================================
    var columnDefs = [
        {
            type: "num",
            data: "id",
            title: "ID",
            visible: false
        },
        {
            // Photo Column
            data: "avatar",
            title: "Photo",
            orderable: false,
            searchable: false,
            width: "60px",
            className: "text-center align-middle",
            render: function (data) {
                if (data) {
                    return `<img src="/${data}" style="width: 50px; height: 50px; object-fit: cover; border-radius: 50%; border: 2px solid #eee;" alt="avatar">`;
                }
                return `<div class="bg-secondary text-white rounded-circle d-flex align-items-center justify-content-center mx-auto" style="width: 50px; height: 50px;">
                            <i class="bi bi-person-fill fs-4"></i>
                        </div>`;
            }
        },
        {
            type: "text",
            data: null,
            title: "Guide Info",
            width: "30%",
            responsivePriority: 1,
            render: function (data, type, row) {
                return `
                    <div class="d-flex flex-column">
                        <span class="fw-bold text-dark fs-6">${row.first_name} ${row.last_name}</span>
                        <small class="text-primary">${row.title || 'Guide'}</small>
                    </div>
                `;
            }
        },
        {
            data: "languages",
            title: "Languages",
            width: "20%",
            render: function (data) {
                return data ? `<span class="small text-muted"><i class="bi bi-translate me-1"></i>${data}</span>` : '-';
            }
        },
        {
            data: "pace_range",
            title: "Pace Range",
            width: "15%",
            render: function (data) {
                return data ? `<span class="badge bg-light text-dark border"><i class="bi bi-speedometer2 me-1"></i>${data}</span>` : '-';
            }
        },
        {
            data: null,
            title: "Actions",
            className: "text-end",
            orderable: false,
            render: function (data, type, row) {
                return `
                    <a href="#" class="btn btn-sm btn-light text-primary border me-1 edit" title="Edit Profile & Schedule">
                        <i class="bi bi-pencil-square"></i>
                    </a>
                    <a href="#" class="delTherapist btn btn-sm btn-light text-danger border" title="Delete">
                        <i class="bi bi-trash"></i>
                    </a>
                `;
            }
        }
    ];

    // ==========================================
    // 2. Initialize DataTable
    // ==========================================
    therapistTable = $(tableName).DataTable({
        paging: true,
        responsive: { details: true },
        lengthChange: true,
        autoWidth: false,
        language: {
            emptyTable: "No guides found.",
            search: "Search:",
            lengthMenu: "Show _MENU_",
            info: "Showing _START_ to _END_ of _TOTAL_",
            paginate: { next: ">", previous: "<" }
        },
        columns: columnDefs,
        ajax: {
            type: "POST",
            url: "includes/admin/ajax.php",
            data: {
                action: ajaxAction,
                csrf_token: getCsrfToken()
            },
            dataSrc: ""
        }
    });

    $('#searchTherapists').on('keyup click', function () {
        therapistTable.search(this.value).draw();
    });

    // ==========================================
    // 3. Modal Actions (Add, Edit, Save)
    // ==========================================

    // Helper: Generate Rule Row HTML with Package Dropdown
    function getRuleRowHtml(rule = {}) {
        const wd = (rule.weekday ?? 1);
        const st = (rule.start_time ?? '09:00').slice(0, 5);
        const en = (rule.end_time ?? '17:00').slice(0, 5);
        const pid = rule.package_id || ''; // Selected Package ID

        const wdays = ['Κυριακή', 'Δευτέρα', 'Τρίτη', 'Τετάρτη', 'Πέμπτη', 'Παρασκευή', 'Σάββατο'];

        // Build Package Options
        let pkgOptions = `<option value="" ${pid == '' ? 'selected' : ''} class="fw-bold text-muted">-- General Availability --</option>`;
        availablePackagesList.forEach(p => {
            let sel = (p.id == pid) ? 'selected' : '';
            pkgOptions += `<option value="${p.id}" ${sel}>${p.title}</option>`;
        });

        return `<tr>
         <td>
            <select class="form-select form-select-sm rule-weekday border-0 bg-light">
                ${wdays.map((d, i) => `<option value="${i}" ${i == wd ? 'selected' : ''}>${d}</option>`).join('')}
            </select>
         </td>
         <td><input type="time" class="form-control form-control-sm rule-start border-0" value="${st}"></td>
         <td><input type="time" class="form-control form-control-sm rule-end border-0" value="${en}"></td>
         <td>
            <select class="form-select form-select-sm rule-package border-0" style="font-size:0.85rem">
                ${pkgOptions}
            </select>
         </td>
         <td class="text-end"><button type="button" class="btn btn-sm text-danger delModalRuleBtn"><i class="bi bi-x-lg"></i></button></td>
        </tr>`;
    }

    // A. Add New
    $(document).on("click", "#addNewTherapist", function () {
        document.getElementById(genPrefix + "Form").reset();
        $("#therapistID").val('');
        $("#currentAvatarMsg").hide();
        $("#avatarPreview").attr('src', '');

        // Reset Lists
        availablePackagesList = [];

        $("#nav-item-schedule").hide(); // Cannot add rules before saving profile
        $("#modalRulesTable tbody").empty();

        new bootstrap.Tab(document.querySelector('#tab-general-link')).show();

        $("#" + genPrefix + "ActionBtn").attr("data-action", "addTherapist").text("Δημιουργία");
        $("#" + genPrefix + "ModalLongTitle").text("Νέος Guide");
    });

    // B. Edit
    $(document).on("click", ".edit", function (e) {
        e.preventDefault();
        let row = $(this).closest("tr");
        let rowData = therapistTable.row(row).data();
        let tid = rowData.id;

        // Fill inputs
        $("#therapistID").val(tid);
        $("#first_name").val(rowData.first_name);
        $("#last_name").val(rowData.last_name);
        $("#title").val(rowData.title);
        $("#email").val(rowData.email);
        $("#phone").val(rowData.phone);
        $("#bio").val(rowData.bio);
        $("#languages").val(rowData.languages);   // New Field
        $("#pace_range").val(rowData.pace_range); // New Field
        $("#booking_window_days").val(rowData.booking_window_days || 60);
        $("#min_notice_hours").val(rowData.min_notice_hours !== null ? rowData.min_notice_hours : 12);

        $("#avatar").val('');
        if (rowData.avatar) {
            $("#avatarPreview").attr('src', '/' + rowData.avatar);
            $("#currentAvatarMsg").show();
        } else {
            $("#avatarPreview").attr('src', '');
            $("#currentAvatarMsg").hide();
        }

        // Show Schedule Tab
        $("#nav-item-schedule").show();
        const tbody = $("#modalRulesTable tbody");
        tbody.html('<tr><td colspan="5" class="text-center text-muted"><div class="spinner-border spinner-border-sm"></div> Loading Schedule...</td></tr>');

        // Fetch Rules AND Packages List
        $.post("includes/admin/ajax.php", {
            action: 'availabilityRules_get',
            therapist_id: tid,
            csrf_token: getCsrfToken()
        }, function (res) {
            tbody.empty();

            if (res.success) {
                // 1. Store packages list globally
                availablePackagesList = res.packages_list || [];

                // 2. Render Rules
                if (res.data && res.data.length > 0) {
                    res.data.forEach(r => tbody.append(getRuleRowHtml(r)));
                } else {
                    // Empty state or default row
                    tbody.append(getRuleRowHtml({ weekday: 1 }));
                }
            } else {
                tbody.html('<tr><td colspan="5" class="text-danger text-center">Error loading rules.</td></tr>');
            }
        }, 'json');

        new bootstrap.Tab(document.querySelector('#tab-general-link')).show();
        $("#" + genPrefix + "ActionBtn").attr("data-action", "updTherapist").text("Ενημέρωση");
        $("#" + genPrefix + "ModalLongTitle").text("Επεξεργασία Guide");
        $("#" + genPrefix + "Modal").modal('show');
    });

    // Add Rule Row Button
    $("#addModalRuleBtn").click(function () {
        $("#modalRulesTable tbody").append(getRuleRowHtml({}));
    });

    // Remove Rule Row Button
    $(document).on("click", ".delModalRuleBtn", function () {
        $(this).closest("tr").remove();
    });

    // C. Save Changes
    $("#" + genPrefix + "ActionBtn").on("click", async function (e) {
        e.preventDefault();
        let btn = $(this);
        let action = btn.attr("data-action");
        let csrf_token = getCsrfToken();
        let tid = $("#therapistID").val();

        btn.prop("disabled", true).html('<span class="spinner-border spinner-border-sm"></span> Αποθήκευση...');

        // 1. Profile Data
        let formData = new FormData(document.getElementById(genPrefix + "Form"));
        formData.append("action", action);
        formData.append("csrf_token", csrf_token);

        // 2. Rules Data (Only if Updating)
        let rulesJson = null;
        if (action === 'updTherapist' && tid) {
            let rules = [];
            $("#modalRulesTable tbody tr").each(function () {
                let row = $(this);
                rules.push({
                    weekday: parseInt(row.find(".rule-weekday").val()),
                    start_time: row.find(".rule-start").val() + ':00',
                    end_time: row.find(".rule-end").val() + ':00',
                    package_id: row.find(".rule-package").val() || null, // Capture Package ID
                    is_active: 1
                });
            });
            rulesJson = JSON.stringify(rules);
        }

        try {
            // Save Profile
            const profileReq = $.ajax({
                type: "POST",
                url: "includes/admin/ajax.php",
                data: formData,
                processData: false,
                contentType: false
            });

            const promises = [profileReq];

            // Save Rules (if applicable)
            if (rulesJson) {
                promises.push($.post("includes/admin/ajax.php", {
                    action: 'availabilityRules_saveBulk',
                    therapist_id: tid,
                    rules_json: rulesJson,
                    csrf_token: csrf_token
                }));
            }

            const results = await Promise.all(promises);

            // Handle Profile Result
            let profileRes = typeof results[0] === 'string' ? JSON.parse(results[0]) : results[0];

            if (profileRes.success) {
                $("#" + genPrefix + "Modal").modal("hide");
                therapistTable.ajax.reload(null, false);

                if (typeof setNotification === 'function') {
                    setNotification("Επιτυχία", "Ο Guide ενημερώθηκε.", "success");
                } else {
                    alert("Επιτυχία!");
                }
            } else {
                let msg = profileRes.errors ? profileRes.errors.join("\n") : "Error saving profile.";
                alert(msg);
            }

        } catch (err) {
            console.error(err);
            alert("Σφάλμα επικοινωνίας.");
        } finally {
            btn.prop("disabled", false).text("Αποθήκευση");
        }
    });

    // ==========================================
    // 4. Delete Action
    // ==========================================
    $(document).on("click", ".delTherapist", async function (e) {
        e.preventDefault();
        let row = $(this).closest("tr");
        let rowData = therapistTable.row(row).data();
        let csrf_token = getCsrfToken();

        const actionConfirmed = await handleNotificationAction(
            "Διαγραφή Guide",
            `Είστε σίγουροι ότι θέλετε να διαγράψετε τον/την <b>${rowData.first_name} ${rowData.last_name}</b>;`
        );

        if (actionConfirmed) {
            $.ajax({
                type: "POST",
                url: "includes/admin/ajax.php",
                data: {
                    action: "delTherapist",
                    therapistID: rowData.id,
                    csrf_token: csrf_token
                },
                success: function (res) {
                    if (res.success) {
                        therapistTable.ajax.reload(null, false);
                        setNotification("Deleted", "Ο Guide διαγράφηκε.", "success");
                    } else {
                        setNotification("Error", "Αποτυχία διαγραφής.", "error");
                    }
                }
            });
        }
    });

    // ==========================================
    // 5. Utilities
    // ==========================================
    $("#avatar").change(function () {
        const file = this.files[0];
        if (file) {
            let reader = new FileReader();
            reader.onload = function (event) {
                $('#avatarPreview').attr('src', event.target.result);
                $('#currentAvatarMsg').show();
            }
            reader.readAsDataURL(file);
        }
    });

    function getCsrfToken() {
        return document.getElementsByName("csrf_token")[0]?.getAttribute("content") || '';
    }

    if (typeof handleNotificationAction === 'undefined') {
        window.handleNotificationAction = function (title, msg) {
            return new Promise(resolve => resolve(confirm(title + "\n" + msg.replace(/<[^>]*>/g, ''))));
        };
    }
});
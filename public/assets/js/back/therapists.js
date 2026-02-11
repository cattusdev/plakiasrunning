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
    // 0. Helper: Time Calculation
    // ==========================================
    function addMinutesToTime(timeStr, minutes) {
        if (!timeStr) return '';
        let [h, m] = timeStr.split(':').map(Number);

        let date = new Date();
        date.setHours(h);
        date.setMinutes(m + parseInt(minutes));

        let newH = String(date.getHours()).padStart(2, '0');
        let newM = String(date.getMinutes()).padStart(2, '0');
        return `${newH}:${newM}`;
    }

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
            data: "avatar",
            title: "Φωτο",
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
            title: "Guide",
            width: "30%",
            responsivePriority: 1,
            render: function (data, type, row) {
                const role = row.title || 'Guide';
                return `
                    <div class="d-flex flex-column">
                        <span class="fw-bold text-dark fs-6">${row.first_name} ${row.last_name}</span>
                        <small class="text-muted">${role}</small>
                    </div>
                `;
            }
        },
        {
            data: "languages",
            title: "Γλώσσες",
            width: "20%",
            render: function (data) {
                return data ? `<span class="small text-muted"><i class="bi bi-translate me-1"></i>${data}</span>` : '-';
            }
        },
        {
            data: "pace_range",
            title: "Ρυθμός (Pace)",
            width: "15%",
            render: function (data) {
                return data ? `<span class="badge bg-light text-dark border"><i class="bi bi-speedometer2 me-1"></i>${data}</span>` : '-';
            }
        },
        {
            data: null,
            title: "Ενέργειες",
            className: "text-end",
            orderable: false,
            render: function () {
                return `
                    <a href="#" class="btn btn-sm btn-light text-primary border me-1 edit" title="Επεξεργασία">
                        <i class="bi bi-pencil-square"></i>
                    </a>
                    <a href="#" class="delTherapist btn btn-sm btn-light text-danger border" title="Διαγραφή">
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
            emptyTable: "Δεν βρέθηκαν Guides.",
            search: "Αναζήτηση:",
            lengthMenu: "Εμφάνιση _MENU_",
            info: "Εμφάνιση _START_ έως _END_ από _TOTAL_",
            infoEmpty: "Εμφάνιση 0 έως 0 από 0",
            zeroRecords: "Δεν βρέθηκαν αποτελέσματα.",
            paginate: { next: "›", previous: "‹" }
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
    // 3. Modal Actions
    // ==========================================

    function getRuleRowHtml(rule = {}) {
        const wd = (rule.weekday ?? 1);
        const st = (rule.start_time ?? '09:00').slice(0, 5);
        const en = (rule.end_time ?? '17:00').slice(0, 5);
        const pid = rule.package_id || '';

        const wdays = ['Κυριακή', 'Δευτέρα', 'Τρίτη', 'Τετάρτη', 'Πέμπτη', 'Παρασκευή', 'Σάββατο'];

        let pkgOptions = `<option value="" ${pid == '' ? 'selected' : ''} class="fw-bold text-muted">— Γενική Διαθεσιμότητα —</option>`;

        if (availablePackagesList && availablePackagesList.length > 0) {
            availablePackagesList.forEach(p => {
                let sel = (p.id == pid) ? 'selected' : '';
                pkgOptions += `<option value="${p.id}" ${sel}>${p.title}</option>`;
            });
        }

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
         <td class="text-end">
            <button type="button" class="btn btn-sm text-danger delModalRuleBtn" title="Αφαίρεση">
                <i class="bi bi-x-lg"></i>
            </button>
         </td>
        </tr>`;
    }

    // A. Add New
    $(document).on("click", "#addNewTherapist", function () {
        document.getElementById(genPrefix + "Form").reset();
        $("#therapistID").val('');
        $("#currentAvatarMsg").hide();
        $("#avatarPreview").attr('src', '');

        availablePackagesList = [];

        $("#nav-item-schedule").hide();
        $("#modalRulesTable tbody").empty();

        new bootstrap.Tab(document.querySelector('#tab-general-link')).show();

        $("#" + genPrefix + "ActionBtn").attr("data-action", "addTherapist").text("Δημιουργία");
        $("#" + genPrefix + "ModalLongTitle").text("Νέος Guide / Pacer");
    });

    // B. Edit
    $(document).on("click", ".edit", function (e) {
        e.preventDefault();
        let row = $(this).closest("tr");
        let rowData = therapistTable.row(row).data();
        let tid = rowData.id;

        $("#therapistID").val(tid);
        $("#first_name").val(rowData.first_name);
        $("#last_name").val(rowData.last_name);
        $("#title").val(rowData.title);
        $("#email").val(rowData.email);
        $("#phone").val(rowData.phone);
        $("#bio").val(rowData.bio);
        $("#languages").val(rowData.languages);
        $("#pace_range").val(rowData.pace_range);
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

        $("#nav-item-schedule").show();
        const tbody = $("#modalRulesTable tbody");
        tbody.html('<tr><td colspan="5" class="text-center text-muted"><div class="spinner-border spinner-border-sm"></div> Φόρτωση προγράμματος...</td></tr>');

        // 1) Packages
        $.post("includes/admin/ajax.php", {
            action: 'getTherapistPackages',
            therapist_id: tid,
            csrf_token: getCsrfToken()
        }, function (pkgRes) {

            availablePackagesList = pkgRes.data || [];

            // 2) Rules
            $.post("includes/admin/ajax.php", {
                action: 'availabilityRules_get',
                therapist_id: tid,
                csrf_token: getCsrfToken()
            }, function (res) {
                tbody.empty();

                if (res.success) {
                    if (res.data && res.data.length > 0) {
                        res.data.forEach(r => tbody.append(getRuleRowHtml(r)));
                    } else {
                        tbody.append(getRuleRowHtml({ weekday: 1 }));
                    }
                } else {
                    tbody.html('<tr><td colspan="5" class="text-danger text-center">Αποτυχία φόρτωσης κανόνων.</td></tr>');
                }
            }, 'json');

        }, 'json');

        new bootstrap.Tab(document.querySelector('#tab-general-link')).show();
        $("#" + genPrefix + "ActionBtn").attr("data-action", "updTherapist").text("Ενημέρωση");
        $("#" + genPrefix + "ModalLongTitle").text("Επεξεργασία Guide / Pacer");
        $("#" + genPrefix + "Modal").modal('show');
    });

    $("#addModalRuleBtn").click(function () {
        $("#modalRulesTable tbody").append(getRuleRowHtml({}));
    });

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

        let formData = new FormData(document.getElementById(genPrefix + "Form"));
        formData.append("action", action);
        formData.append("csrf_token", csrf_token);

        let rulesJson = null;
        if (action === 'updTherapist' && tid) {
            let rules = [];
            $("#modalRulesTable tbody tr").each(function () {
                let row = $(this);
                rules.push({
                    weekday: parseInt(row.find(".rule-weekday").val()),
                    start_time: row.find(".rule-start").val() + ':00',
                    end_time: row.find(".rule-end").val() + ':00',
                    package_id: row.find(".rule-package").val() || null,
                    is_active: 1
                });
            });
            rulesJson = JSON.stringify(rules);
        }

        try {
            const profileReq = $.ajax({
                type: "POST",
                url: "includes/admin/ajax.php",
                data: formData,
                processData: false,
                contentType: false
            });

            const promises = [profileReq];

            if (rulesJson) {
                promises.push($.post("includes/admin/ajax.php", {
                    action: 'availabilityRules_saveBulk',
                    therapist_id: tid,
                    rules_json: rulesJson,
                    csrf_token: csrf_token
                }));
            }

            const results = await Promise.all(promises);
            let profileRes = typeof results[0] === 'string' ? JSON.parse(results[0]) : results[0];

            if (profileRes.success) {
                $("#" + genPrefix + "Modal").modal("hide");
                therapistTable.ajax.reload(null, false);

                if (typeof setNotification === 'function') {
                    setNotification("Επιτυχία", "Ο Guide αποθηκεύτηκε.", "success");
                } else if (typeof uiAlert === 'function') {
                    uiAlert("Επιτυχία", "Ο Guide αποθηκεύτηκε.");
                }
            } else {
                let msg = profileRes.errors ? profileRes.errors.join("\n") : "Αποτυχία αποθήκευσης.";
                if (typeof uiAlert === 'function') uiAlert("Σφάλμα", msg);
            }

        } catch (err) {
            console.error(err);
            if (typeof uiAlert === 'function') uiAlert("Σφάλμα", "Σφάλμα επικοινωνίας με τον server.");
        } finally {
            btn.prop("disabled", false).text("Αποθήκευση");
        }
    });

    // ==========================================
    // 4. Delete Action (uiConfirm)
    // ==========================================
    $(document).on("click", ".delTherapist", async function (e) {
        e.preventDefault();
        let row = $(this).closest("tr");
        let rowData = therapistTable.row(row).data();
        let csrf_token = getCsrfToken();

        const ok = (typeof uiConfirm === 'function')
            ? await uiConfirm(
                "Διαγραφή Guide",
                `Είστε σίγουροι ότι θέλετε να διαγράψετε τον/την <b>${rowData.first_name} ${rowData.last_name}</b>;`
            )
            : false;

        if (!ok) return;

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
                    if (typeof setNotification === 'function') {
                        setNotification("Διαγράφηκε", "Ο Guide διαγράφηκε.", "success");
                    } else if (typeof uiAlert === 'function') {
                        uiAlert("Διαγράφηκε", "Ο Guide διαγράφηκε.");
                    }
                } else {
                    if (typeof setNotification === 'function') {
                        setNotification("Σφάλμα", "Αποτυχία διαγραφής.", "error");
                    } else if (typeof uiAlert === 'function') {
                        uiAlert("Σφάλμα", "Αποτυχία διαγραφής.");
                    }
                }
            }
        });
    });

    // ==========================================
    // 5. Utilities
    // ==========================================
    $("#avatar").change(function () {
        const file = this.files[0];
        if (file) {
            let letReader = new FileReader();
            letReader.onload = function (event) {
                $('#avatarPreview').attr('src', event.target.result);
                $('#currentAvatarMsg').show();
            }
            letReader.readAsDataURL(file);
        }
    });

    function getCsrfToken() {
        return document.getElementsByName("csrf_token")[0]?.getAttribute("content") || '';
    }

    // ==========================================
    // 6. SMART RULES: AUTO-CALCULATE END TIME
    // ==========================================
    $(document).on('change', '.rule-package', function () {
        const tr = $(this).closest('tr');
        const pkgId = $(this).val();
        const startVal = tr.find('.rule-start').val();

        if (pkgId && startVal && availablePackagesList.length > 0) {
            const pkg = availablePackagesList.find(p => p.id == pkgId);

            if (pkg && pkg.duration_minutes) {
                const newEnd = addMinutesToTime(startVal, pkg.duration_minutes);
                tr.find('.rule-end').val(newEnd);

                tr.find('.rule-end').addClass('bg-warning-subtle');
                setTimeout(() => tr.find('.rule-end').removeClass('bg-warning-subtle'), 500);
            }
        }
    });

    $(document).on('change', '.rule-start', function () {
        const tr = $(this).closest('tr');
        const pkgId = tr.find('.rule-package').val();
        const startVal = $(this).val();

        if (pkgId && startVal && availablePackagesList.length > 0) {
            const pkg = availablePackagesList.find(p => p.id == pkgId);

            if (pkg && pkg.duration_minutes) {
                const newEnd = addMinutesToTime(startVal, pkg.duration_minutes);
                tr.find('.rule-end').val(newEnd);
            }
        }
    });

});

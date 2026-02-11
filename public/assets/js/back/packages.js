/* ============================================================
   packages.js (clean + no duplicates + proper actions column)
   ============================================================ */

(function () {
    // Guard against double-init if script injected twice
    if (window.__packagesInit) return;
    window.__packagesInit = true;

    let mainTable;
    const tableName = "#packages_table";
    const genPrefix = "packages";
    const genSuffix = "Packages";
    const ajaxAction = "fetchPackages";

    // Maps
    const categoryMap = {
        "Road Running": { label: "Άσφαλτος", class: "bg-dark text-white" },
        "Trail Running": { label: "Βουνό / Trail", class: "bg-success text-white" },
        "Track Session": { label: "Στίβος", class: "bg-danger text-white" },
        "Fun Run": { label: "Fun Run", class: "bg-info text-dark" },
    };

    // Data Storage for Lists
    let listData = {
        includes: [],
        gearMandatory: [],
        gearOptional: [],
    };

    // ------------------------------------------------------------
    // Helpers
    // ------------------------------------------------------------
    function getCsrfToken() {
        return (
            document.querySelector('meta[name="csrf_token"]')?.getAttribute("content") ||
            document.getElementsByName("csrf_token")[0]?.getAttribute("content") ||
            ""
        );
    }

    function escapeHtml(text) {
        if (text === null || text === undefined) return "";
        return String(text)
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }

    function fmtEUR(v) {
        const n = parseFloat(v);
        if (isNaN(n) || n <= 0) return "Δωρεάν";
        return `${n.toFixed(0)}€`;
    }

    function fmtDateTimeEL(sql) {
        if (!sql) return "";
        const d = new Date(String(sql).replace(" ", "T"));
        if (isNaN(d.getTime())) return "";
        const date = d.toLocaleDateString("el-GR", {
            day: "2-digit",
            month: "2-digit",
            year: "numeric",
        });
        const time = d.toLocaleTimeString("el-GR", { hour: "2-digit", minute: "2-digit" });
        return `${date} · ${time}`;
    }

    function terrainBadge(v) {
        const map = {
            Road: { t: "Άσφαλτος", i: "bi-signpost-2", c: "text-dark bg-light border" },
            Trail: {
                t: "Trail",
                i: "bi-tree-fill",
                c: "text-success bg-success-subtle border border-success-subtle",
            },
            Mixed: {
                t: "Μικτό",
                i: "bi-shuffle",
                c: "text-primary bg-primary-subtle border border-primary-subtle",
            },
            Track: {
                t: "Στίβος",
                i: "bi-stopwatch-fill",
                c: "text-danger bg-danger-subtle border border-danger-subtle",
            },
        };
        const x = map[v] || { t: v || "-", i: "bi-info-circle", c: "text-muted bg-light border" };
        return `<span class="badge rounded-pill ${x.c}"><i class="bi ${x.i} me-1"></i>${x.t}</span>`;
    }

    function shouldShowTerrain(categoryName, terrain) {
        const c = String(categoryName || '').toLowerCase();
        const t = String(terrain || '').toLowerCase();

        // normalize common doubles
        if (c.includes('road') && t === 'road') return false;
        if (c.includes('trail') && t === 'trail') return false;
        if (c.includes('track') && t === 'track') return false;

        return !!terrain; // show if exists and not duplicate
    }


    function difficultyPill(v) {
        const map = {
            Easy: { t: "Εύκολο", c: "text-success bg-success-subtle border border-success-subtle" },
            Moderate: { t: "Μέτριο", c: "text-warning bg-warning-subtle border border-warning-subtle" },
            Hard: { t: "Δύσκολο", c: "text-danger bg-danger-subtle border border-danger-subtle" },
            Elite: { t: "Elite", c: "text-white bg-dark border" },
        };
        const x = map[v] || { t: v || "-", c: "text-muted bg-light border" };
        return `<span class="badge rounded-pill ${x.c}">${x.t}</span>`;
    }

    function categoryPill(name) {
        const cat = categoryMap[name] || { label: name || "Γενικό", class: "bg-secondary text-white" };
        return `<span class="badge rounded-pill ${cat.class}">${cat.label}</span>`;
    }

    function capBlock(row) {
        const isGroup = parseInt(row.is_group) === 1;

        // Standard / recurring
        if (!isGroup) {
            const max = parseInt(row.max_attendants || 1);
            const label = max === 1 ? "Ατομικό" : `Max ${max} / slot`;
            const icon = max === 1 ? "bi-person" : "bi-people";
            return `
        <div class="d-flex align-items-center justify-content-between gap-2">
          <span class="badge bg-white text-dark border">
            <i class="bi ${icon} me-1"></i>${label}
          </span>
          <span class="text-muted small"><i class="bi bi-repeat me-1"></i>Επαναλ.</span>
        </div>
      `;
        }

        // Group event
        const db = parseInt(row.db_bookings_count || 0);
        const manual = parseInt(row.manual_bookings || 0);
        const total = db + manual;
        const max = parseInt(row.max_attendants || 0);
        const pct = max > 0 ? Math.min(100, Math.round((total / max) * 100)) : 0;

        let bar = "bg-success";
        let status = "Διαθέσιμο";
        if (max > 0 && total >= max) {
            bar = "bg-danger";
            status = "Full";
        } else if (pct >= 75) {
            bar = "bg-warning";
            status = "Σχεδόν γεμάτο";
        }

        return `
      <div class="d-flex align-items-center justify-content-between mb-1">
        <span class="small fw-bold">${total}/${max}</span>
        <span class="small text-muted">${status}</span>
      </div>
      <div class="progress bg-light" style="height:8px;">
        <div class="progress-bar ${bar}" role="progressbar" style="width:${pct}%"></div>
      </div>
      <div class="small text-muted mt-1">
        <i class="bi bi-person-check me-1"></i>DB: ${db}
        <span class="mx-1">·</span>
        <i class="bi bi-person-plus me-1"></i>Manual: ${manual}
      </div>
    `;
    }

    function notifyOk(title, msg) {
        if (typeof setNotification === "function") return setNotification(title, msg, "success");
        alert(`${title}\n${msg}`);
    }

    function notifyWarn(title, msg) {
        if (typeof setNotification === "function") return setNotification(title, msg, "warning");
        alert(`${title}\n${msg}`);
    }

    // ------------------------------------------------------------
    // DataTable Init
    // ------------------------------------------------------------
    function initDataTable() {
        // If already initialized (e.g. hot reload), destroy safely
        if ($.fn.DataTable.isDataTable(tableName)) {
            $(tableName).DataTable().destroy();
            $(tableName + " tbody").empty();
        }

        const columnDefs = [
            { data: "id", title: "ID", visible: false },

            // Title “card”
            {
                data: "title",
                title: "Διαδρομή",
                width: "42%",
                responsivePriority: 1,
                render: function (data, type, row) {
                    const title = escapeHtml(data || "-");
                    const isGroup = parseInt(row.is_group) === 1;

                    const when = isGroup ? fmtDateTimeEL(row.start_datetime) : "";
                    const kindBadge = isGroup
                        ? `<span class="badge rounded-pill bg-warning-subtle text-dark border border-warning-subtle"><i class="bi bi-calendar-event me-1"></i>Event</span>`
                        : `<span class="badge rounded-pill bg-light text-dark border"><i class="bi bi-repeat me-1"></i>Recurring</span>`;

                    // ✅ Price appears ONLY here (no separate price column)
                    const price = `<span class="badge rounded-pill bg-white text-dark border"><i class="bi bi-cash-coin me-1"></i>${fmtEUR(row.price)}</span>`;

                    const mini = [];
                    mini.push(kindBadge);
                    mini.push(categoryPill(row.category_name));

                    // ✅ only if not duplicate
                    if (shouldShowTerrain(row.category_name, row.terrain_type)) {
                        mini.push(terrainBadge(row.terrain_type));
                    }

                    mini.push(difficultyPill(row.difficulty));
                    mini.push(price);

                    return `
            <div class="d-flex gap-3">
              <div class="pkg-mark"><i class="bi bi-geo-alt-fill"></i></div>

              <div class="flex-grow-1">
                <div class="fw-bold text-dark pkg-title">${title}</div>

                ${when
                            ? `<div class="small text-primary mt-1"><i class="bi bi-clock me-1"></i>${when}</div>`
                            : `<div class="small text-muted mt-1"><i class="bi bi-clock-history me-1"></i>Χωρίς συγκεκριμένη ημερομηνία</div>`
                        }

                <div class="d-flex flex-wrap gap-1 mt-2">
                  ${mini.join("")}
                </div>

                <div class="small text-muted mt-2 pkg-sub">
                  <span class="me-2"><i class="bi bi-signpost-split me-1"></i>${parseFloat(
                            row.distance_km || 0
                        ).toFixed(1)} km</span>
                  <span class="me-2"><i class="bi bi-arrow-up-right me-1"></i>${parseInt(
                            row.elevation_gain || 0
                        )} m</span>
                  <span><i class="bi bi-stopwatch me-1"></i>${parseInt(row.duration_minutes || 0)}'</span>
                  ${parseInt(row.buffer_minutes || 0)
                            ? `<span class="ms-2 text-muted"><i class="bi bi-hourglass-split me-1"></i>Buffer ${parseInt(
                                row.buffer_minutes
                            )}'</span>`
                            : ``
                        }
                </div>
              </div>
            </div>
          `;
                },
            },

            // Capacity
            {
                data: null,
                title: "Συμμετοχές",
                width: "22%",
                className: "pkg-cap",
                render: function (data, type, row) {
                    return `<div class="pkg-cap-box">${capBlock(row)}</div>`;
                },
            },

            // Info / map / description
            {
                data: null,
                title: "Πληροφορίες",
                width: "26%",
                responsivePriority: 2,
                render: function (data, type, row) {
                    const desc = escapeHtml((row.description || "").trim());
                    const short = desc.length > 100 ? desc.slice(0, 100) + "…" : desc || "—";

                    const mapLink = row.meeting_point_url
                        ? `<a class="btn btn-sm btn-outline-secondary border-0 px-0" href="${escapeHtml(
                            row.meeting_point_url
                        )}" target="_blank" rel="noopener">
                <i class="bi bi-map me-1"></i>Σημείο συνάντησης
              </a>`
                        : `<span class="text-muted small"><i class="bi bi-map me-1"></i>Χωρίς link</span>`;

                    return `
            <div class="small text-muted">${short}</div>
            <div class="mt-2">${mapLink}</div>
          `;
                },
            },

            // ✅ Actions column RIGHT (buttons moved here)
            {
                data: null,
                title: "",
                width: "10%",
                className: "text-end",
                orderable: false,
                responsivePriority: 1,
                render: function (data, type, row) {
                    const isGroup = parseInt(row.is_group) === 1;
                    return `
            <div class="d-inline-flex align-items-center gap-1">
              ${isGroup
                            ? `<button class="btn btn-sm btn-outline-dark border-0 btn-attendees" data-id="${row.id}" title="Λίστα Runners">
                      <i class="bi bi-people"></i>
                    </button>`
                            : ``
                        }
              <button class="btn btn-sm btn-light text-primary border edit-btn" data-id="${row.id}" title="Επεξεργασία">
                <i class="bi bi-pencil-square"></i>
              </button>
              <button class="btn btn-sm btn-light text-danger border del${genSuffix}" data-id="${row.id}" title="Διαγραφή">
                <i class="bi bi-trash"></i>
              </button>
            </div>
          `;
                },
            },
        ];

        mainTable = $(tableName).DataTable({
            paging: true,
            responsive: true,
            autoWidth: false,
            pageLength: 25,
            dom: "rtip",
            order: [[0, "desc"]],
            language: {
                emptyTable: "Δεν υπάρχουν διαδρομές.",
                zeroRecords: "Δεν βρέθηκαν αποτελέσματα.",
                info: "Εμφάνιση _START_ έως _END_ από _TOTAL_",
                infoEmpty: "Εμφάνιση 0 έως 0 από 0",
                lengthMenu: "Εμφάνιση _MENU_",
                paginate: { next: "›", previous: "‹" },
            },
            columns: columnDefs,
            ajax: {
                type: "POST",
                url: "includes/admin/ajax.php",
                data: {
                    action: ajaxAction,
                    format: "json",
                    csrf_token: getCsrfToken(),
                },
                dataSrc: (res) => res || [],
            },
            createdRow: function (row) {
                row.classList.add("pkg-row");
            },
            initComplete: function () {
                // Search binding (off/on to prevent double bind)
                $("#searchPackages")
                    .off("keyup.packages")
                    .on("keyup.packages", function () {
                        mainTable.search(this.value).draw();
                    });

                checkUrlForEdit();
            },
        });
    }

    // ------------------------------------------------------------
    // Filters (safe, single predicate)
    // ------------------------------------------------------------
    let currentFilter = "all";
    function dtFilterFn(settings, data, dataIndex) {
        if (!mainTable) return true;
        const row = mainTable.row(dataIndex).data();
        if (!row) return true;

        if (currentFilter === "group") return parseInt(row.is_group) === 1;
        if (currentFilter === "standard") return parseInt(row.is_group) === 0;
        return true;
    }

    // Expose to inline onclick in HTML
    window.filterTable = function (type, btn) {
        currentFilter = type || "all";

        if (btn) {
            $(btn).parent().find(".btn").removeClass("active");
            $(btn).addClass("active");
        }

        // ensure only once
        $.fn.dataTable.ext.search = $.fn.dataTable.ext.search.filter((f) => f !== dtFilterFn);
        $.fn.dataTable.ext.search.push(dtFilterFn);

        if (mainTable) mainTable.draw();
    };

    // ------------------------------------------------------------
    // Modal UI badges
    // ------------------------------------------------------------
    function refreshModalBadges() {
        const isGroup = $("#is_group").is(":checked");

        $("#uiBadgeMode")
            .text(isGroup ? "Event" : "Recurring")
            .removeClass()
            .addClass(
                isGroup
                    ? "badge bg-warning-subtle text-dark border border-warning-subtle"
                    : "badge bg-light text-dark border"
            );

        const catText = $("#category_id option:selected").text() || "Route";
        $("#uiBadgeType").text(catText);
    }

    function bindGroupToggle() {
        $("#is_group")
            .off("change.packages")
            .on("change.packages", function () {
                const settings = document.getElementById("groupSettings");
                const helpText = document.getElementById("capacityHelpText");

                if (this.checked) {
                    settings.classList.remove("d-none");
                    helpText.innerText = "Συνολικά άτομα για το Event";
                } else {
                    settings.classList.add("d-none");
                    $("#start_datetime").val("");
                    helpText.innerText = "Ανά ραντεβού (Slot)";
                }

                refreshModalBadges();
            });

        $("#category_id")
            .off("change.packages")
            .on("change.packages", refreshModalBadges);
    }

    // ------------------------------------------------------------
    // Therapists checkboxes
    // ------------------------------------------------------------
    function loadTherapistCheckboxes() {
        $.ajax({
            url: "includes/admin/ajax.php",
            type: "POST",
            dataType: "json",
            data: { action: "fetchTherapists", csrf_token: getCsrfToken() },
            success: function (res) {
                const container = $("#therapistsCheckboxes");
                container.empty();

                if (res && res.success && res.data) {
                    res.data.forEach((t) => {
                        const label = escapeHtml((t.first_name || "") + " " + (t.last_name || ""));
                        container.append(`
              <div class="form-check">
                <input class="form-check-input therapist-cb" type="checkbox" name="therapists[]" value="${t.id}" id="th_${t.id}">
                <label class="form-check-label" for="th_${t.id}">${label}</label>
              </div>
            `);
                    });
                } else {
                    container.html('<small class="text-danger">No Guides.</small>');
                }
            },
        });
    }

    // ------------------------------------------------------------
    // Modal: reset for NEW
    // ------------------------------------------------------------
    function resetNewPackageModal() {
        document.getElementById(genPrefix + "Form")?.reset(); // packagesForm
        $("#packageID").val("");

        // reset lists
        listData.includes = [];
        listData.gearMandatory = [];
        listData.gearOptional = [];
        renderAllLists();

        // reset toggles and helpers
        $("#is_group").prop("checked", false);
        $("#groupSettings").addClass("d-none");
        $("#start_datetime").val("");
        $("#manual_bookings").val(0);
        $("#max_attendants").val(1);

        $(".therapist-cb").prop("checked", false);

        $("#packagesActionBtn").attr("data-action", "addPackage").text("Δημιουργία");

        setTimeout(refreshModalBadges, 30);
    }

    // ------------------------------------------------------------
    // Save
    // ------------------------------------------------------------
    function savePackage() {
        const btn = $("#packagesActionBtn");
        btn.prop("disabled", true);

        const selectedTherapists = [];
        $(".therapist-cb:checked").each(function () {
            selectedTherapists.push($(this).val());
        });

        const payload = {
            packageID: $("#packageID").val(),
            title: $("#title").val(),
            therapists: selectedTherapists,
            category_id: $("#category_id").val(),
            distance_km: $("#distance_km").val(),
            elevation_gain: $("#elevation_gain").val(),
            difficulty: $("#difficulty").val(),
            terrain_type: $("#terrain_type").val(),
            meeting_point_url: $("#meeting_point_url").val(),
            price: $("#price").val(),
            description: $("#description").val(),
            duration_minutes: $("#duration_minutes").val(),
            buffer_minutes: $("#buffer_minutes").val(),
            is_group: $("#is_group").is(":checked") ? 1 : 0,
            max_attendants: $("#max_attendants").val(),
            manual_bookings: $("#manual_bookings").val(),
            start_datetime: $("#start_datetime").val(),
            includes: JSON.stringify(listData.includes),
            gear_mandatory: JSON.stringify(listData.gearMandatory),
            gear_optional: JSON.stringify(listData.gearOptional),

            action: btn.attr("data-action"),
            csrf_token: getCsrfToken(),
        };

        $.ajax({
            type: "POST",
            url: "includes/admin/ajax.php",
            data: payload,
            dataType: "json",
            success: function (res) {
                btn.prop("disabled", false);
                if (res && res.success) {
                    $("#packagesModal").modal("hide");
                    if (mainTable) mainTable.ajax.reload(null, false);
                    notifyOk("Επιτυχία", res.message || "Αποθηκεύτηκε.");
                } else {
                    const msg = Array.isArray(res?.errors) ? res.errors.join("<br>") : res?.errors || "Σφάλμα.";
                    notifyWarn("Σφάλμα", msg);
                }
            },
            error: function () {
                btn.prop("disabled", false);
                notifyWarn("Σφάλμα", "Σφάλμα επικοινωνίας με τον server.");
            },
        });
    }

    // ------------------------------------------------------------
    // Edit modal open
    // ------------------------------------------------------------
    function openEditPackageModal(id) {
        $.ajax({
            url: "includes/admin/ajax.php",
            type: "POST",
            dataType: "json",
            data: { action: "fetchPackage", id: id, csrf_token: getCsrfToken() },
            success: function (res) {
                if (!res || !res.success) {
                    console.error("Package not found");
                    return;
                }

                const d = res.data;

                $("#packageID").val(d.id);
                $("#title").val(d.title);
                $("#category_id").val(d.category_id);
                $("#distance_km").val(d.distance_km);
                $("#elevation_gain").val(d.elevation_gain);
                $("#difficulty").val(d.difficulty);
                $("#terrain_type").val(d.terrain_type);
                $("#meeting_point_url").val(d.meeting_point_url);
                $("#price").val(d.price);
                $("#description").val(d.description);
                $("#duration_minutes").val(d.duration_minutes);
                $("#buffer_minutes").val(d.buffer_minutes);
                $("#max_attendants").val(d.max_attendants);

                // Guides
                $(".therapist-cb").prop("checked", false);
                if (d.therapists && Array.isArray(d.therapists)) {
                    d.therapists.forEach((tId) => $("#th_" + tId).prop("checked", true));
                }

                // Group logic
                if (parseInt(d.is_group) === 1) {
                    $("#is_group").prop("checked", true);
                    $("#groupSettings").removeClass("d-none");
                    $("#manual_bookings").val(d.manual_bookings || 0);
                    if (d.start_datetime) $("#start_datetime").val(String(d.start_datetime).replace(" ", "T").substring(0, 16));
                } else {
                    $("#is_group").prop("checked", false);
                    $("#groupSettings").addClass("d-none");
                    $("#manual_bookings").val(0);
                    $("#start_datetime").val("");
                }

                // Lists
                try {
                    listData.includes = d.includes ? JSON.parse(d.includes) : [];
                } catch (e) {
                    listData.includes = [];
                }

                listData.gearMandatory = [];
                listData.gearOptional = [];
                try {
                    const gearObj = d.gear_requirements ? JSON.parse(d.gear_requirements) : {};
                    if (gearObj.mandatory) listData.gearMandatory = gearObj.mandatory;
                    if (gearObj.optional) listData.gearOptional = gearObj.optional;
                } catch (e) { }

                renderAllLists();

                $("#packagesActionBtn").attr("data-action", "updPackage").text("Ενημέρωση");

                setTimeout(refreshModalBadges, 30);
                $("#packagesModal").modal("show");
            },
            error: function (err) {
                console.error("Ajax Error", err);
            },
        });
    }

    // expose for URL open
    function checkUrlForEdit() {
        const urlParams = new URLSearchParams(window.location.search);
        const packageId = urlParams.get("id");
        if (packageId) openEditPackageModal(packageId);
    }

    // ------------------------------------------------------------
    // Delete
    // ------------------------------------------------------------
    async function deletePackage(id) {
        const rowData = mainTable
            ? mainTable.rows().data().toArray().find((x) => String(x.id) === String(id))
            : null;

        const title = rowData?.title ? escapeHtml(rowData.title) : "";

        const ok = typeof uiConfirm === "function"
            ? await uiConfirm("Διαγραφή Διαδρομής", `Είστε σίγουροι ότι θέλετε να διαγράψετε τη διαδρομή <b>${title}</b>;`)
            : confirm("Είστε σίγουροι για τη διαγραφή;");

        if (!ok) return;

        $.ajax({
            type: "POST",
            url: "includes/admin/ajax.php",
            dataType: "json",
            data: { action: "delPackage", packageID: id, csrf_token: getCsrfToken() },
            success: function (res) {
                if (res && res.success) {
                    if (mainTable) mainTable.ajax.reload(null, false);
                    notifyOk("Επιτυχία", "Η διαδρομή διαγράφηκε.");
                } else {
                    const msg = Array.isArray(res?.errors) ? res.errors.join("\n") : res?.errors || "Αποτυχία διαγραφής.";
                    notifyWarn("Σφάλμα", msg.replace(/\n/g, "<br>"));
                }
            },
            error: function () {
                notifyWarn("Σφάλμα", "Σφάλμα επικοινωνίας με τον server.");
            },
        });
    }

    // ------------------------------------------------------------
    // Attendees
    // ------------------------------------------------------------
    window.showAttendees = function (packageId) {
        const modal = new bootstrap.Modal(document.getElementById("attendeesModal"));
        const list = document.getElementById("attendeesList");
        const loader = document.getElementById("attendeesLoader");
        const noMsg = document.getElementById("noAttendeesMsg");

        list.innerHTML = "";
        loader.classList.remove("d-none");
        noMsg.classList.add("d-none");
        modal.show();

        $.post(
            "includes/admin/ajax.php",
            { action: "fetchGroupAttendees", package_id: packageId, csrf_token: getCsrfToken() },
            function (res) {
                loader.classList.add("d-none");

                if ((res?.data && res.data.length > 0) || (res?.manual_count && res.manual_count > 0)) {
                    if (res.data) {
                        res.data.forEach((row) => {
                            list.insertAdjacentHTML(
                                "beforeend",
                                `<tr>
                  <td class="fw-bold">${escapeHtml(row.first_name)} ${escapeHtml(row.last_name)}</td>
                  <td>${escapeHtml(row.phone || "-")}</td>
                  <td>${escapeHtml(row.payment_status || "-")}</td>
                </tr>`
                            );
                        });
                    }
                    if (res.manual_count > 0) {
                        list.insertAdjacentHTML(
                            "beforeend",
                            `<tr class="table-warning"><td colspan="3"><strong>${res.manual_count}</strong> Manual/Offline</td></tr>`
                        );
                    }
                } else {
                    noMsg.classList.remove("d-none");
                }
            },
            "json"
        );
    };

    // ------------------------------------------------------------
    // Lists management
    // ------------------------------------------------------------
    function addListItem(type) {
        const inputId = type + "Input";
        const input = document.getElementById(inputId);
        if (!input) return;

        const val = input.value.trim();
        if (!val) return;

        listData[type].push(val);
        renderList(type);
        input.value = "";
        input.focus();
    }

    window.addListItem = addListItem;

    function removeListItem(type, index) {
        listData[type].splice(index, 1);
        renderList(type);
    }

    window.removeListItem = removeListItem;

    function renderList(type) {
        const listId = type + "List";
        const el = document.getElementById(listId);
        if (!el) return;

        el.innerHTML = "";

        listData[type].forEach((item, index) => {
            let iconClass = "bi-check2";
            if (type === "gearMandatory") iconClass = "bi-exclamation-circle text-danger";
            if (type === "gearOptional") iconClass = "bi-check-circle text-success";

            el.innerHTML += `
        <li class="list-group-item d-flex justify-content-between align-items-center py-1 px-2 bg-transparent">
          <span class="small"><i class="bi ${iconClass} me-2"></i>${escapeHtml(item)}</span>
          <button type="button" class="btn btn-sm text-secondary border-0" onclick="removeListItem('${type}', ${index})">
            <i class="bi bi-x-lg"></i>
          </button>
        </li>
      `;
        });
    }

    function renderAllLists() {
        renderList("includes");
        renderList("gearMandatory");
        renderList("gearOptional");
    }

    function bindListInputs() {
        ["includesInput", "gearMandatoryInput", "gearOptionalInput"].forEach((id) => {
            const el = document.getElementById(id);
            if (!el) return;

            const type = id.replace("Input", "");

            // prevent duplicate
            el.onkeypress = null;
            el.addEventListener("keypress", function (e) {
                if (e.key === "Enter") {
                    e.preventDefault();
                    addListItem(type);
                }
            });
        });
    }

    // ------------------------------------------------------------
    // Bind UI events (single)
    // ------------------------------------------------------------
    function bindUIEvents() {
        // New
        $(document)
            .off("click.packages", "#addNewPackage")
            .on("click.packages", "#addNewPackage", function () {
                resetNewPackageModal();
            });

        // Save
        $(document)
            .off("click.packages", "#packagesActionBtn")
            .on("click.packages", "#packagesActionBtn", function (e) {
                e.preventDefault();
                savePackage();
            });

        // Edit
        $(document)
            .off("click.packages", ".edit-btn")
            .on("click.packages", ".edit-btn", function (e) {
                e.preventDefault();
                const id = $(this).data("id");
                if (id) openEditPackageModal(id);
            });

        // Delete (button includes data-id)
        $(document)
            .off("click.packages", ".del" + genSuffix)
            .on("click.packages", ".del" + genSuffix, async function (e) {
                e.preventDefault();
                const id = $(this).data("id");
                if (!id) return;
                await deletePackage(id);
            });

        // Attendees (actions column)
        $(document)
            .off("click.packages", ".btn-attendees")
            .on("click.packages", ".btn-attendees", function (e) {
                e.preventDefault();
                const id = $(this).data("id");
                if (id) window.showAttendees(id);
            });
    }

    // ------------------------------------------------------------
    // Boot
    // ------------------------------------------------------------
    document.addEventListener("DOMContentLoaded", function () {
        loadTherapistCheckboxes();
        bindGroupToggle();
        bindListInputs();
        bindUIEvents();
        initDataTable();

        // ensure filter function exists once
        $.fn.dataTable.ext.search = $.fn.dataTable.ext.search.filter((f) => f !== dtFilterFn);
        $.fn.dataTable.ext.search.push(dtFilterFn);
    });

})();

let mainTable;
let tableName = "#packages_table";
let genPrefix = "packages";
let genSuffix = "Packages";
let ajaxAction = "fetchPackages";

// ΕΛΛΗΝΙΚΑ LABELS
const categoryMap = {
    'Road Running': { label: 'Άσφαλτος', class: 'bg-dark text-white' },
    'Trail Running': { label: 'Βουνό / Trail', class: 'bg-success text-white' },
    'Track Session': { label: 'Στίβος', class: 'bg-danger text-white' },
    'Fun Run': { label: 'Fun Run', class: 'bg-info text-dark' }
};

const difficultyMap = {
    'Easy': '<span class="badge bg-success-subtle text-success border border-success-subtle">Εύκολο</span>',
    'Moderate': '<span class="badge bg-warning-subtle text-warning border border-warning-subtle">Μέτριο</span>',
    'Hard': '<span class="badge bg-danger-subtle text-danger border border-danger-subtle">Δύσκολο</span>',
    'Elite': '<span class="badge bg-dark text-white border">Elite</span>'
};

// Data Storage for Lists
let listData = {
    includes: [],
    gearMandatory: [],
    gearOptional: []
};

document.addEventListener("DOMContentLoaded", function () {
    loadTherapistCheckboxes();

    // --- DATATABLE ---
    var columnDefs = [
        { data: "id", title: "ID", visible: false },
        {
            data: "title", title: "Διαδρομή / Event", width: "25%",
            render: function (data, type, row) {
                let metaHtml = '';
                if (row.is_group == 1 && row.start_datetime) {
                    const dateObj = new Date(row.start_datetime);
                    if (!isNaN(dateObj.getTime())) {
                        const dateStr = dateObj.toLocaleDateString('el-GR', { day: '2-digit', month: '2-digit' });
                        const timeStr = dateObj.toLocaleTimeString('el-GR', { hour: '2-digit', minute: '2-digit' });
                        metaHtml = `<div class="mt-1 small text-primary"><i class="bi bi-calendar-event me-1"></i> ${dateStr} @ ${timeStr}</div>`;
                    }
                } else {
                    metaHtml = `<div class="mt-1 small text-muted"><i class="bi bi-repeat me-1"></i> Επαναλαμβανόμενο</div>`;
                }
                return `<div><span class="fw-bold text-dark h6">${data}</span>${metaHtml}</div>`;
            }
        },
        {
            data: "category_name", title: "Τύπος", width: "10%",
            render: function (data) {
                const cat = categoryMap[data] || { label: data || 'General', class: 'bg-secondary' };
                return `<span class="badge ${cat.class} fw-normal shadow-sm">${cat.label}</span>`;
            }
        },
        {
            data: null, title: "Στοιχεία", width: "20%",
            render: function (data, type, row) {
                let dist = row.distance_km ? `<strong>${parseFloat(row.distance_km).toFixed(1)} km</strong>` : '-';
                let elev = row.elevation_gain ? `<span class="text-muted ms-2"><i class="bi bi-arrow-up-right small"></i> ${row.elevation_gain}m</span>` : '';
                let diff = difficultyMap[row.difficulty] || '';
                return `
                    <div class="d-flex align-items-center mb-1">${dist} ${elev}</div>
                    <div>${diff} <span class="badge bg-light text-dark border ms-1">${row.terrain_type || '-'}</span></div>
                `;
            }
        },
        // COLUMN: ΣΥΜΜΕΤΟΧΕΣ / CAPACITY
        {
            data: null, title: "Συμμετοχές", width: "15%",
            render: function (data, type, row) {
                // 1. STANDARD RUN (Recurring)
                if (row.is_group == 0) {
                    let max = parseInt(row.max_attendants);
                    // Αν είναι 1 = Private/Personal, αλλιώς Small Group
                    if (max === 1) {
                        return '<span class="badge bg-white text-dark border"><i class="bi bi-person me-1"></i>Personal (1)</span>';
                    } else {
                        return `<span class="badge bg-white text-dark border"><i class="bi bi-people me-1"></i>Max ${max} / slot</span>`;
                    }
                }

                // 2. GROUP EVENT
                let totalBooked = (parseInt(row.db_bookings_count) || 0) + (parseInt(row.manual_bookings) || 0);
                let max = parseInt(row.max_attendants);
                let percent = max > 0 ? (totalBooked / max) * 100 : 0;
                let colorClass = totalBooked >= max ? 'danger' : 'success';

                return `
                    <div class="d-flex align-items-center">
                        <div class="progress flex-grow-1 bg-white border" style="height: 6px;">
                            <div class="progress-bar bg-${colorClass}" role="progressbar" style="width: ${percent}%"></div>
                        </div>
                        <span class="ms-2 small fw-bold">${totalBooked}/${max}</span>
                    </div>
                    <div class="small text-muted" style="font-size: 0.75em;">Event Capacity</div>
                `;
            }
        },
        {
            data: "price", title: "Κόστος", width: "10%", className: "text-end fw-bold",
            render: function (data) { return data ? `${parseFloat(data).toFixed(0)}€` : 'Δωρεάν'; }
        },
        {
            data: null, title: "", className: "text-end", width: "15%", orderable: false,
            render: function (data, type, row) {
                let attendBtn = '';
                if (row.is_group == 1) {
                    attendBtn = `<a class="btn btn-sm btn-outline-dark border-0 me-1" title="Λίστα Runners" onclick="showAttendees(${row.id})"><i class="bi bi-people"></i></a>`;
                }
                return `
                    ${attendBtn}
                    <a class="btn btn-sm btn-light text-primary border me-1 edit-btn" title="Επεξεργασία"><i class="bi bi-pencil-square"></i></a>
                    <a class="del${genSuffix} btn btn-sm btn-light text-danger border" title="Διαγραφή"><i class="bi bi-trash"></i></a>
                `;
            }
        }
    ];

    mainTable = $(tableName).DataTable({
        paging: true, responsive: true, autoWidth: false, pageLength: 25,
        dom: "rtip",
        language: { url: "//cdn.datatables.net/plug-ins/1.13.4/i18n/el.json" },
        columns: columnDefs,
        ajax: {
            type: "POST", url: "includes/admin/ajax.php",
            data: { action: ajaxAction, format: "json", csrf_token: document.getElementsByName("csrf_token")[0].getAttribute("content") },
            dataSrc: (res) => res || []
        },
        initComplete: function () {
            $('#searchPackages').on('keyup', function () { mainTable.search(this.value).draw(); });
        }
    });

    // --- MODAL ACTIONS ---
    $(document).on("click", "#addNewPackage", function () {
        document.getElementById(genPrefix + "Form").reset();

        // Reset Lists
        listData.includes = [];
        listData.gearMandatory = [];
        listData.gearOptional = [];
        renderAllLists();

        $("#is_group").prop('checked', false);
        // Trigger manually to update Help Text
        document.getElementById('is_group').dispatchEvent(new Event('change'));

        $('.therapist-cb').prop('checked', false);
        // Default values
        $("#max_attendants").val(1);
        $("#" + genPrefix + "ActionBtn").attr("data-action", "addPackage").text("Δημιουργία");
    });

    $(document).on("click", ".edit-btn", function (e) {
        e.preventDefault();
        let tr = $(this).closest("tr");
        let rowData = mainTable.row(tr).data();
        let id = rowData.id;
        let csrf_token = document.getElementsByName("csrf_token")[0].getAttribute("content");

        $.ajax({
            url: "includes/admin/ajax.php", type: "POST",
            data: { action: "fetchPackage", id: id, csrf_token: csrf_token },
            success: function (res) {
                if (res.success) {
                    let d = res.data;
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

                    // Capacity
                    $("#max_attendants").val(d.max_attendants);

                    // Guides
                    $('.therapist-cb').prop('checked', false);
                    if (d.therapists && Array.isArray(d.therapists)) {
                        d.therapists.forEach(tId => { $('#th_' + tId).prop('checked', true); });
                    }

                    // Group Logic
                    if (d.is_group == 1) {
                        $("#is_group").prop('checked', true);
                        $("#groupSettings").removeClass('d-none');
                        $("#manual_bookings").val(d.manual_bookings);
                        if (d.start_datetime) $("#start_datetime").val(d.start_datetime.replace(' ', 'T').substring(0, 16));
                    } else {
                        $("#is_group").prop('checked', false);
                        $("#groupSettings").addClass('d-none');
                    }

                    // Update Help Text based on state
                    document.getElementById('is_group').dispatchEvent(new Event('change'));

                    // Lists
                    try { listData.includes = d.includes ? JSON.parse(d.includes) : []; } catch (e) { listData.includes = []; }
                    listData.gearMandatory = [];
                    listData.gearOptional = [];
                    try {
                        let gearObj = d.gear_requirements ? JSON.parse(d.gear_requirements) : {};
                        if (gearObj.mandatory) listData.gearMandatory = gearObj.mandatory;
                        if (gearObj.optional) listData.gearOptional = gearObj.optional;
                    } catch (e) { }
                    renderAllLists();

                    $("#" + genPrefix + "ActionBtn").attr("data-action", "updPackage").text("Ενημέρωση");
                    $("#packagesModal").modal("show");
                }
            }
        });
    });

    // --- SAVE ---
    $("body").on("click", "#" + genPrefix + "ActionBtn", function (e) {
        e.preventDefault();
        const btn = $(this);
        btn.prop("disabled", true);

        let selectedTherapists = [];
        $('.therapist-cb:checked').each(function () { selectedTherapists.push($(this).val()); });

        let data = {
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
            is_group: $("#is_group").is(':checked') ? 1 : 0,
            max_attendants: $("#max_attendants").val(),
            manual_bookings: $("#manual_bookings").val(),
            start_datetime: $("#start_datetime").val(),
            includes: JSON.stringify(listData.includes),
            gear_mandatory: JSON.stringify(listData.gearMandatory),
            gear_optional: JSON.stringify(listData.gearOptional),

            action: btn.attr("data-action"),
            csrf_token: document.getElementsByName("csrf_token")[0].getAttribute("content")
        };

        $.ajax({
            type: "POST", url: "includes/admin/ajax.php", data: data, dataType: 'json',
            success: function (res) {
                btn.prop("disabled", false);
                if (res.success) {
                    $("#packagesModal").modal("hide");
                    mainTable.ajax.reload(null, false);
                    setNotification("Επιτυχία", res.message, "success");
                } else {
                    setNotification("Σφάλμα", Array.isArray(res.errors) ? res.errors.join("<br>") : res.errors, "warning");
                }
            }
        });
    });

    // Delete
    $(document).on("click", ".del" + genSuffix, async function () {
        let tr = $(this).closest("tr");
        let rowData = mainTable.row(tr).data();
        if (confirm("Είστε σίγουροι για τη διαγραφή;")) {
            $.ajax({
                type: "POST", url: "includes/admin/ajax.php",
                data: { action: "delPackage", packageID: rowData.id, csrf_token: document.getElementsByName("csrf_token")[0].getAttribute("content") },
                success: function (res) { if (res.success) mainTable.ajax.reload(null, false); }
            });
        }
    });

    // Group Toggle Logic
    document.getElementById('is_group').addEventListener('change', function () {
        const settings = document.getElementById('groupSettings');
        const helpText = document.getElementById('capacityHelpText');

        if (this.checked) {
            settings.classList.remove('d-none');
            helpText.innerText = "Συνολικά άτομα για το Event";
        } else {
            settings.classList.add('d-none');
            $('#start_datetime').val('');
            helpText.innerText = "Ανά ραντεβού (Slot)";
        }
    });
});

// --- HELPER FUNCTIONS ---
function loadTherapistCheckboxes() {
    let csrf_token = document.getElementsByName("csrf_token")[0].getAttribute("content");
    $.ajax({
        url: "includes/admin/ajax.php", type: "POST",
        data: { action: "fetchTherapists", csrf_token: csrf_token },
        success: function (res) {
            const container = $('#therapistsCheckboxes');
            container.empty();
            if (res.success && res.data) {
                res.data.forEach(t => {
                    let label = t.first_name + ' ' + t.last_name;
                    container.append(`<div class="form-check"><input class="form-check-input therapist-cb" type="checkbox" name="therapists[]" value="${t.id}" id="th_${t.id}"><label class="form-check-label" for="th_${t.id}">${label}</label></div>`);
                });
            } else { container.html('<small class="text-danger">No Guides.</small>'); }
        }
    });
}

function filterTable(type, btn) {
    if (btn) { $(btn).parent().find('.btn').removeClass('active'); $(btn).addClass('active'); }
    $.fn.dataTable.ext.search.pop();
    if (type === 'group') $.fn.dataTable.ext.search.push((s, d, i) => mainTable.row(i).data().is_group == 1);
    if (type === 'standard') $.fn.dataTable.ext.search.push((s, d, i) => mainTable.row(i).data().is_group == 0);
    mainTable.draw();
}

function showAttendees(packageId) {
    const modal = new bootstrap.Modal(document.getElementById('attendeesModal'));
    const list = document.getElementById('attendeesList');
    const loader = document.getElementById('attendeesLoader');
    const noMsg = document.getElementById('noAttendeesMsg');

    list.innerHTML = ''; loader.classList.remove('d-none'); noMsg.classList.add('d-none');
    modal.show();

    $.post('includes/admin/ajax.php', {
        action: 'fetchGroupAttendees', package_id: packageId,
        csrf_token: document.querySelector('meta[name="csrf_token"]')?.getAttribute('content')
    }, function (res) {
        loader.classList.add('d-none');
        if ((res.data && res.data.length > 0) || res.manual_count > 0) {
            if (res.data) res.data.forEach(row => {
                list.insertAdjacentHTML('beforeend', `<tr><td class="fw-bold">${row.first_name} ${row.last_name}</td><td>${row.phone}</td><td>${row.payment_status}</td></tr>`);
            });
            if (res.manual_count > 0) list.insertAdjacentHTML('beforeend', `<tr class="table-warning"><td colspan="3"><strong>${res.manual_count}</strong> Manual/Offline</td></tr>`);
        } else {
            noMsg.classList.remove('d-none');
        }
    }, 'json');
}

// --- NEW LIST MANAGEMENT LOGIC ---
function addListItem(type) {
    let inputId = type + "Input";
    let input = document.getElementById(inputId);
    let val = input.value.trim();
    if (val) {
        listData[type].push(val);
        renderList(type);
        input.value = "";
        input.focus();
    }
}

function removeListItem(type, index) {
    listData[type].splice(index, 1);
    renderList(type);
}

function renderList(type) {
    let listId = type + "List";
    let el = document.getElementById(listId);
    el.innerHTML = "";

    listData[type].forEach((item, index) => {
        let iconClass = "bi-check2";
        if (type === 'gearMandatory') iconClass = "bi-exclamation-circle text-danger";
        if (type === 'gearOptional') iconClass = "bi-check-circle text-success";

        el.innerHTML += `
            <li class="list-group-item d-flex justify-content-between align-items-center py-1 px-2 bg-transparent">
                <span class="small"><i class="bi ${iconClass} me-2"></i>${item}</span>
                <button type="button" class="btn btn-sm text-secondary border-0" onclick="removeListItem('${type}', ${index})"><i class="bi bi-x-lg"></i></button>
            </li>`;
    });
}

function renderAllLists() {
    renderList('includes');
    renderList('gearMandatory');
    renderList('gearOptional');
}

['includesInput', 'gearMandatoryInput', 'gearOptionalInput'].forEach(id => {
    let type = id.replace('Input', '');
    document.getElementById(id).addEventListener("keypress", function (e) {
        if (e.key === "Enter") { e.preventDefault(); addListItem(type); }
    });
});
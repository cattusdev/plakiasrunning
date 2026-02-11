<?php
if (!isset($GLOBAL_INCLUDE_CHECK)) die("Access Denied");
?>

<div class="container-fluid mt-5">
    <div class="bg-primary d-flex align-items-center justify-content-between p-3 rounded shadow-sm mb-4">
        <h3 class="fw-bold text-white m-0">
            <i class="bi bi-geo-alt-fill me-2"></i> Διαδρομές & Events
        </h3>

        <div class="btn-group shadow-sm bg-white rounded" role="group">
            <button type="button" class="btn btn-outline-primary active border-0" onclick="filterTable('all', this)">Όλα</button>
            <button type="button" class="btn btn-outline-primary border-0" onclick="filterTable('group', this)">Events / Αγώνες</button>
            <button type="button" class="btn btn-outline-primary border-0" onclick="filterTable('standard', this)">Καθημερινά Runs</button>
        </div>
    </div>

    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body p-3">
            <div class="d-flex justify-content-between align-items-center">
                <div class="position-relative" style="width: 300px;">
                    <span class="position-absolute top-50 start-0 translate-middle-y ms-3 text-muted"><i class="bi bi-search"></i></span>
                    <input type="text" class="form-control ps-5 rounded-pill bg-light border-0" placeholder="Αναζήτηση..." id="searchPackages">
                </div>
                <button class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm" type="button" data-bs-toggle="modal" data-bs-target="#packagesModal" id="addNewPackage">
                    <i class="bi bi-plus-lg me-2"></i> Νέα Διαδρομή
                </button>
            </div>
        </div>
    </div>

    <table class="table table-hover table-bordered rounded shadow-sm align-middle" id="packages_table">
        <thead class="table-light">
        </thead>
        <tbody id="packages_body">
        </tbody>
    </table>
</div>

<div class="modal fade" id="packagesModal" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg">

            <div class="modal-header bg-white border-bottom-0 pb-0">
                <h5 class="modal-title fw-bold text-primary" id="packagesModalLongTitle">Στοιχεία Διαδρομής</h5>
                <div class="d-flex gap-2 mt-2">
                    <span class="badge bg-light text-dark border" id="uiBadgeMode">Recurring</span>
                    <span class="badge bg-light text-dark border" id="uiBadgeType">Route</span>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body pt-4">
                <form id="packagesForm">
                    <input type="hidden" id="packageID" />
                    <input type="hidden" id="type" name="type" value="inPerson">

                    <h6 class="text-uppercase text-muted fw-bold small mb-3"><i class="bi bi-card-heading me-1"></i> Γενικες Πληροφοριες</h6>
                    <div class="row g-3 mb-4">
                        <div class="col-md-8">
                            <label class="form-label fw-semibold">Τίτλος <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="title" name="title" required placeholder="π.χ. Ορεινό τρέξιμο Υμηττός" />

                            <div class="card bg-light border-0 mt-3">
                                <div class="card-body py-2">
                                    <label class="form-label fw-bold small text-uppercase text-muted mb-2">Αναθεση σε Guides / Pacers</label>
                                    <div id="therapistsCheckboxes" class="d-flex flex-wrap gap-3">
                                        <span class="text-muted small"><span class="spinner-border spinner-border-sm"></span> Φόρτωση...</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Κατηγορία <span class="text-danger">*</span></label>
                            <select class="form-select" id="category_id" name="category_id" required>
                                <option value="" selected disabled>Επιλέξτε...</option>
                                <option value="1">Road Running</option>
                                <option value="2">Trail Running</option>
                                <option value="3">Track Session</option>
                                <option value="4">Fun Run</option>
                            </select>
                        </div>
                    </div>

                    <h6 class="text-uppercase text-muted fw-bold small mb-3 border-top pt-3"><i class="bi bi-activity me-1"></i> Χαρακτηριστικα</h6>
                    <div class="row g-3 mb-3">
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Απόσταση (km) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="number" step="0.1" class="form-control" id="distance_km" name="distance_km" placeholder="0.0">
                                <span class="input-group-text text-muted">km</span>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Θετική Υψομετρική</label>
                            <div class="input-group">
                                <input type="number" class="form-control" id="elevation_gain" name="elevation_gain" placeholder="0">
                                <span class="input-group-text text-muted">m</span>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Δυσκολία</label>
                            <select class="form-select" id="difficulty" name="difficulty">
                                <option value="Easy">Easy (Εύκολο)</option>
                                <option value="Moderate" selected>Moderate (Μέτριο)</option>
                                <option value="Hard">Hard (Δύσκολο)</option>
                                <option value="Elite">Elite (Μόνο έμπειροι)</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Τερέν</label>
                            <select class="form-select" id="terrain_type" name="terrain_type">
                                <option value="Road">Road (Άσφαλτος)</option>
                                <option value="Trail">Trail (Μονοπάτι)</option>
                                <option value="Mixed">Mixed (Μικτό)</option>
                                <option value="Track">Track (Στίβος)</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Google Maps Link (Σημείο Συνάντησης)</label>
                        <input type="url" class="form-control" id="meeting_point_url" name="meeting_point_url" placeholder="https://maps.google.com/...">
                    </div>

                    <h6 class="text-uppercase text-muted fw-bold small mb-3 border-top pt-3"><i class="bi bi-clock-history me-1"></i> Χρονος, Κοστος & Χωρητικοτητα</h6>
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Διάρκεια (λεπτά) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="duration_minutes" name="duration_minutes" value="60" step="5" />
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold text-muted" title="Χρόνος για προετοιμασία/χαλάρωμα">Buffer (λεπτά)</label>
                            <input type="number" class="form-control bg-light" id="buffer_minutes" name="buffer_minutes" value="0" />
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Τιμή (€)</label>
                            <input type="number" step="0.01" class="form-control" id="price" name="price" />
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold text-primary">Max Runners <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="number" class="form-control border-primary" id="max_attendants" name="max_attendants" value="1" min="1">
                                <span class="input-group-text bg-light text-primary"><i class="bi bi-people-fill"></i></span>
                            </div>
                            <div class="form-text small text-end" id="capacityHelpText">Ανά ραντεβού (Slot)</div>
                        </div>
                    </div>

                    <hr class="my-4 opacity-25">
                    <div class="p-3 bg-light rounded-3 border border-light">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <label class="form-check-label fw-bold text-dark mb-1" for="is_group">
                                    <i class="bi bi-calendar-event me-2 text-warning"></i>Scheduled Event / Αγώνας
                                </label>
                                <div class="small text-muted">Ενεργοποιήστε αν έχει <strong>συγκεκριμένη ημερομηνία</strong> έναρξης.</div>
                            </div>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="is_group" name="is_group" style="width: 3em; height: 1.5em;">
                            </div>
                        </div>

                        <div id="groupSettings" class="mt-3 pt-3 border-top d-none">
                            <div class="row g-3">
                                <div class="col-md-8">
                                    <label class="form-label fw-semibold text-primary">Ημερομηνία & Ώρα Εκκίνησης</label>
                                    <input type="datetime-local" class="form-control" id="start_datetime" name="start_datetime">
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label fw-semibold text-danger">Manual / Offline</label>
                                    <input type="number" class="form-control text-center border-danger" id="manual_bookings" name="manual_bookings" value="0" min="0">
                                </div>
                            </div>
                        </div>
                    </div>

                    <h6 class="text-uppercase text-muted fw-bold small mb-3 border-top pt-3"><i class="bi bi-info-circle me-1"></i> Λεπτομερειες</h6>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Περιγραφή Διαδρομής</label>
                        <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-danger"><i class="bi bi-exclamation-circle me-1"></i> Απαραίτητος Εξοπλισμός</label>
                            <div class="input-group mb-2">
                                <input type="text" class="form-control form-control-sm" id="gearMandatoryInput" placeholder="π.χ. Παπούτσια Trail, Νερό 1L" />
                                <button type="button" class="btn btn-outline-danger btn-sm" onclick="addListItem('gearMandatory')"><i class="bi bi-plus-lg"></i></button>
                            </div>
                            <ul id="gearMandatoryList" class="list-group list-group-flush border rounded-bottom bg-light"></ul>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-success"><i class="bi bi-check-circle me-1"></i> Προαιρετικός (Bring with you)</label>
                            <div class="input-group mb-2">
                                <input type="text" class="form-control form-control-sm" id="gearOptionalInput" placeholder="π.χ. Καπέλο, Γυαλιά" />
                                <button type="button" class="btn btn-outline-success btn-sm" onclick="addListItem('gearOptional')"><i class="bi bi-plus-lg"></i></button>
                            </div>
                            <ul id="gearOptionalList" class="list-group list-group-flush border rounded-bottom bg-light"></ul>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Παροχές Διοργάνωσης (Includes)</label>
                        <div class="input-group mb-2">
                            <input type="text" class="form-control" id="includesInput" placeholder="π.χ. Σταθμός Τροφοδοσίας, Μετάλλιο..." />
                            <button type="button" class="btn btn-dark" onclick="addListItem('includes')"><i class="bi bi-plus-lg"></i></button>
                        </div>
                        <ul id="includesList" class="list-group list-group-flush rounded border-0"></ul>
                    </div>

                </form>
            </div>

            <div class="modal-footer border-top-0 bg-light rounded-bottom">
                <button type="button" class="btn btn-light text-muted fw-bold" data-bs-dismiss="modal">Άκυρο</button>
                <button type="button" class="btn btn-primary px-4 fw-bold shadow-sm" id="packagesActionBtn" data-action="addPackage">Αποθήκευση</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="attendeesModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title fw-bold"><i class="bi bi-people-fill me-2"></i>Λίστα Runners</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Runner</th>
                                <th>Τηλέφωνο</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody id="attendeesList"></tbody>
                    </table>
                </div>
                <div id="attendeesLoader" class="text-center py-3 d-none">
                    <div class="spinner-border text-warning" role="status"></div>
                </div>
                <div id="noAttendeesMsg" class="text-center py-3 text-muted d-none">
                    Κανένας συμμετέχων ακόμα.
                </div>
            </div>
        </div>
    </div>
</div>


<style>
    /* -----------------------------
   Packages Modal - clean polish
------------------------------ */

    #packagesModal .modal-dialog {
        max-width: 980px;
    }

    #packagesModal .modal-content {
        border-radius: 18px;
        overflow: hidden;
    }

    #packagesModal .modal-header {
        padding: 18px 18px 10px 18px;
    }

    #packagesModal .modal-title {
        letter-spacing: .2px;
    }

    #packagesModal .modal-body {
        padding: 18px;
        background: #fff;
    }

    #packagesModal .modal-footer {
        padding: 14px 18px;
        background: #fff;
        border-top: 1px solid rgba(0, 0, 0, .06);
    }

    /* Section headings */
    #packagesModal h6 {
        font-size: .78rem;
        letter-spacing: .08em;
        margin-bottom: .75rem;
    }

    /* Inputs consistent */
    #packagesModal .form-control,
    #packagesModal .form-select {
        border-radius: 12px;
    }

    /* Softer group blocks */
    #packagesModal .card.bg-light {
        border-radius: 14px;
    }

    #packagesModal .card.bg-light .card-body {
        padding: 12px 12px;
    }

    /* Event toggle block */
    #packagesModal .p-3.bg-light.rounded-3 {
        border-radius: 16px !important;
        border: 1px solid rgba(0, 0, 0, .06) !important;
    }

    /* List groups look cleaner */
    #packagesModal .list-group {
        border-radius: 14px;
        overflow: hidden;
    }

    #packagesModal .list-group-item {
        border-color: rgba(0, 0, 0, .06);
        padding: 8px 10px;
    }

    /* Small inputs in lists keep same rounding */
    #packagesModal .form-control-sm {
        border-radius: 12px;
    }

    /* Make footer sticky (optional, nice) */
    #packagesModal .modal-footer {
        position: sticky;
        bottom: 0;
        z-index: 3;
    }

    /* Guides checkboxes spacing */
    #therapistsCheckboxes .form-check {
        margin: 0;
    }

    #therapistsCheckboxes .form-check-input {
        cursor: pointer;
    }
</style>
<?php
function hook_end_scripts()
{
?>
    <link rel="stylesheet" type="text/css" href="/assets/vendor/plugins/datatables/datatables.css">
    <link rel="stylesheet" type="text/css" href="/assets/css/back/table.css">
    <script src="/assets/vendor/plugins/datatables/datatables.js"></script>
    <script src="/assets/js/back/packages.js"></script>
<?php
}
?>
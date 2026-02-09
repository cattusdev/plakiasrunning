<?php
if (!isset($GLOBAL_INCLUDE_CHECK)) die("Access Denied");
?>

<div class="container-fluid mt-5">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3><i class="bi bi-person-lines-fill"></i> Πελατολόγιο</h3>
    </div>

    <div class="bg-white border d-flex align-items-center justify-content-between p-2 rounded mb-3 shadow-sm">
        <div class="d-flex align-items-center gap-2">
            <div class="input-group">
                <span class="input-group-text bg-white border-end-0"><i class="bi bi-search"></i></span>
                <input type="text" class="form-control border-start-0 ps-0" placeholder="Αναζήτηση..." id="searchClients">
            </div>
            <button class="btn btn-primary text-nowrap" type="button" title="Προσθήκη Πελάτη" id="addNewClient">
                <i class="bi bi-person-plus-fill me-1"></i> Νέος Πελάτης
            </button>
        </div>

        <div class="d-flex align-items-center gap-2">
            <select class="form-select form-select-sm" id="exportOptions" style="width: auto;">
                <option value="" selected disabled>Εξαγωγή...</option>
                <option value="excel">Excel</option>
                <option value="csv">CSV</option>
                <option value="pdf">PDF</option>
                <option value="print">Εκτύπωση</option>
            </select>
        </div>
    </div>

    <table class="table table-hover table-bordered rounded shadow-sm w-100" id="clients_table">
        <thead class="table-light">
        </thead>
        <tbody></tbody>
    </table>
</div>

<div class="modal fade" id="clientsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-light">
                <h5 class="modal-title" id="clientsModalLongTitle">Διαχείριση Πελάτη</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body p-0">
                <ul class="nav nav-tabs nav-fill bg-light" id="clientTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="details-tab" data-bs-toggle="tab" data-bs-target="#pane-details" type="button" role="tab">
                            <i class="bi bi-person-vcard me-2"></i>Στοιχεία
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="history-tab" data-bs-toggle="tab" data-bs-target="#pane-history" type="button" role="tab">
                            <i class="bi bi-clock-history me-2"></i>Ιστορικό
                        </button>
                    </li>
                </ul>

                <div class="tab-content p-3">
                    <div class="tab-pane fade show active" id="pane-details" role="tabpanel">
                        <form id="clientsForm">
                            <input type="hidden" id="clientID">

                            <div class="row g-3 mb-3">
                                <div class="col-md-6">
                                    <label for="fname" class="form-label fw-bold small text-muted">ΟΝΟΜΑ <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="fname" id="fname" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="lname" class="form-label fw-bold small text-muted">ΕΠΩΝΥΜΟ <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="lname" id="lname" required>
                                </div>
                            </div>

                            <div class="row g-3 mb-3">
                                <div class="col-md-6">
                                    <label for="phone" class="form-label fw-bold small text-muted">ΤΗΛΕΦΩΝΟ <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-white"><i class="bi bi-telephone"></i></span>
                                        <input type="tel" class="form-control" name="phone" id="phone" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label for="email" class="form-label fw-bold small text-muted">EMAIL</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-white"><i class="bi bi-envelope"></i></span>
                                        <input type="email" class="form-control" name="email" id="email">
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="clientNote" class="form-label fw-bold small text-muted">ΣΗΜΕΙΩΣΕΙΣ</label>
                                <textarea class="form-control bg-light" rows="3" name="clientNote" id="clientNote"></textarea>
                            </div>
                        </form>

                        <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
                            <small class="text-muted" id="clientUpdated"></small>
                            <div>
                                <button type="button" class="btn btn-light border me-1" data-bs-dismiss="modal">Άκυρο</button>
                                <button type="button" class="btn btn-primary" id="clientsActionBtn">Αποθήκευση</button>
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="pane-history" role="tabpanel">
                        <div class="text-center py-5 text-muted">
                            <i class="bi bi-calendar-x fs-1 mb-3 d-block"></i>
                            <p>Η λειτουργία ιστορικού θα ενεργοποιηθεί μόλις ολοκληρώσουμε τις Κρατήσεις.</p>
                        </div>
                    </div>
                </div>
            </div>

            
        </div>
    </div>
</div>



<?php
function hook_end_scripts()
{
?>
    <link rel="stylesheet" type="text/css" href="/assets/vendor/plugins/datatables/datatables.css">
    <link rel="stylesheet" type="text/css" href="/assets/css/back/table.css">
    <script src="/assets/vendor/plugins/datatables/datatables.js"></script>

    <!-- <script src="js/genFunctions.js"></script> -->

    <script src="/assets/js/back/clients.js"></script>
    <!-- <script src="/assets/js/back/client_bookings.js"></script> -->
<?php
}
?>
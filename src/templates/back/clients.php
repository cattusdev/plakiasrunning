<?php
if (!isset($GLOBAL_INCLUDE_CHECK)) die("Access Denied");
?>

<style>
    .client-initials {
        width: 42px;
        height: 42px;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        border: 1px solid rgba(0, 0, 0, .08);
        background: rgba(13, 110, 253, .08);
        color: #0d6efd;
        flex: 0 0 auto;
    }

    #clients_table tbody tr.client-row td {
        vertical-align: top;
        padding-top: 14px;
        padding-bottom: 14px;
    }

    #clients_table .client-meta {
        font-size: .85rem;
        color: #6c757d;
    }

    #clients_table .client-actions .btn {
        border-radius: 10px;
    }
</style>

<div class="container-fluid mt-5">

    <!-- Header -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-3">
        <div class="d-flex align-items-center gap-3">
            <div class="rounded-3 d-flex align-items-center justify-content-center border bg-white" style="width:42px;height:42px;">
                <i class="bi bi-person-lines-fill fs-5 text-primary"></i>
            </div>
            <div>
                <h3 class="mb-0 fw-bold">Πελατολόγιο</h3>
                <div class="small text-muted"><span id="clientsCount">—</span></div>
            </div>
        </div>

        <div class="d-flex flex-wrap align-items-center justify-content-end gap-2">
            <div class="input-group" style="min-width:260px;max-width:360px;">
                <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
                <input type="text" class="form-control" placeholder="Αναζήτηση..." id="searchClients">
                <button class="btn btn-outline-secondary" type="button" id="clearSearchClients" title="Καθαρισμός">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>

            <button class="btn btn-primary text-nowrap" type="button" id="addNewClient">
                <i class="bi bi-person-plus-fill me-1"></i> Νέος Πελάτης
            </button>

            <select class="form-select form-select-sm" id="exportOptions" style="width:auto;">
                <option value="" selected>Εξαγωγή...</option>
                <option value="excel">Excel</option>
                <option value="csv">CSV</option>
                <option value="pdf">PDF</option>
                <option value="print">Εκτύπωση</option>
            </select>
        </div>
    </div>

    <!-- Table wrapper -->
    <div class="bg-white border rounded-3 p-2 p-md-3">
        <table class="table table-hover table-bordered rounded shadow-sm w-100" id="clients_table">
            <thead class="table-light"></thead>
            <tbody></tbody>
        </table>
    </div>

</div>

<!-- Clients Modal -->
<div class="modal fade" id="clientsModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg rounded-4">

            <div class="modal-header bg-white border-0">
                <div>
                    <h5 class="modal-title fw-bold mb-1" id="clientsModalLongTitle">Διαχείριση Πελάτη</h5>
                    <small class="text-muted" id="clientUpdated"></small>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body pt-0">

                <ul class="nav nav-tabs" id="clientTabs" role="tablist">
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

                <div class="tab-content pt-3">
                    <!-- Details -->
                    <div class="tab-pane fade show active" id="pane-details" role="tabpanel">
                        <form id="clientsForm">
                            <input type="hidden" id="clientID">

                            <div class="p-3 border rounded-3 bg-white">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label for="fname" class="form-label fw-bold small text-muted">ΟΝΟΜΑ <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="fname" id="fname" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="lname" class="form-label fw-bold small text-muted">ΕΠΩΝΥΜΟ <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="lname" id="lname" required>
                                    </div>

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

                                    <div class="col-12">
                                        <label for="clientNote" class="form-label fw-bold small text-muted">ΣΗΜΕΙΩΣΕΙΣ</label>
                                        <textarea class="form-control bg-light" rows="3" name="clientNote" id="clientNote"></textarea>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- History -->
                    <div class="tab-pane fade" id="pane-history" role="tabpanel">
                        <div class="table-responsive">
                            <table class="table table-sm table-hover align-middle mb-0" id="clientHistoryTable">
                                <thead class="table-light">
                                    <tr class="text-muted small text-uppercase">
                                        <th>Ημ/νία</th>
                                        <th>Υπηρεσία</th>
                                        <th>Θεραπευτής</th>
                                        <th>Status</th>
                                        <th class="text-end">Ποσό</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>

                        <div id="historyLoader" class="text-center py-4 d-none">
                            <div class="spinner-border spinner-border-sm text-secondary"></div>
                        </div>

                        <div id="historyEmpty" class="text-center py-5 text-muted d-none">
                            <i class="bi bi-calendar-x fs-1 mb-2 d-block opacity-50"></i>
                            <small>Δεν βρέθηκε ιστορικό ραντεβού.</small>
                        </div>
                    </div>
                </div>

            </div>

            <div class="modal-footer bg-white border-top sticky-bottom">
                <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Άκυρο</button>
                <button type="button" class="btn btn-primary px-4" id="clientsActionBtn" data-action="addClient">Αποθήκευση</button>
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

    <script src="/assets/js/back/clients.js"></script>
<?php
}
?>
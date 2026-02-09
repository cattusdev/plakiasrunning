<?php
if (!isset($GLOBAL_INCLUDE_CHECK)) die("Access Denied");
?>


<div class="container-fluid mt-5">
    <h3><i class="bi bi-box-seam"></i>E-diet Πακέτα</h3>

    <div class="bg-primary d-flex align-items-center justify-content-between p-2 rounded mb-3">
        <div class="d-flex align-items-center gap-1">
            <input type="text" class="form-control mr-sm-2" placeholder="Αναζήτηση.." id="searchPackages">
            <button class="btn btn-primary" type="button" title="Add new package" data-bs-toggle="modal" data-bs-target="#packagesModal" id="addNewPackage">
                <i class="bi bi-plus font-md hand"></i>
            </button>
        </div>
    </div>

    <table class="table table-hover table-bordered rounded shadow-sm" id="packages_table"></table>
</div>

<div class="modal fade" id="packagesModal" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content bg-primary">
            <div class="modal-header border-0 bg-secondary">
                <h5 class="modal-title" id="packagesModalLongTitle">Πακέτα</h5>
                <button type="button" class="btn-close text-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="packagesForm">
                    <input type="hidden" id="packageID" />
                    <div class="mb-3">
                        <label for="title" class="form-label">Τίτλος</label>
                        <input type="text" class="form-control" id="title" name="title" required />
                    </div>
                    <div class="mb-3">
                        <label for="price" class="form-label">Τιμή (€)</label>
                        <input type="currency" class="form-control" id="price" name="price" />
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Περιγραφή</label>
                        <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="includesInput" class="form-label">Τι περιλαμβάνει</label>
                        <div class="d-flex">
                            <input type="text" class="form-control me-2" id="includesInput" placeholder="Προσθέστε ένα στοιχείο..." />
                            <button type="button" class="btn btn-primary" id="addIncludeBtn">Προσθήκη</button>
                        </div>
                        <ul id="includesList" class="list-group mt-2"></ul>
                    </div>
                </form>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Κλείσιμο</button>
                <button type="button" class="btn btn-primary" id="packagesActionBtn" data-action="addPackage" data-key="packages">Αποθήκευση</button>
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

    <script src="/assets/js/back/ediet-packages.js"></script>
<?php
}
?>
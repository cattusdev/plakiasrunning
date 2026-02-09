<?php
if (!isset($GLOBAL_INCLUDE_CHECK)) die("Access Denied");
?>

<div class="container-fluid mt-5">
    <h3><i class="bi bi-book"></i> Digital Products (eBooks)</h3>

    <div class="bg-primary d-flex align-items-center justify-content-between p-2 rounded mb-3">
        <div class="d-flex align-items-center gap-1">
            <input type="text" class="form-control mr-sm-2" placeholder="Αναζήτηση..." id="searchProducts">
            <button class="btn btn-primary" type="button" title="Προσθήκη eBook" data-bs-toggle="modal" data-bs-target="#productsModal" id="addNewProduct">
                <i class="bi bi-plus font-md hand"></i>
            </button>
        </div>
    </div>

    <table class="table table-hover table-bordered rounded shadow-sm" id="products_table"></table>
</div>

<div class="modal fade" id="productsModal" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content bg-primary">
            <div class="modal-header border-0 bg-secondary">
                <h5 class="modal-title" id="productsModalLongTitle">eBook</h5>
                <button type="button" class="btn-close text-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="productsForm" enctype="multipart/form-data">
                    <input type="hidden" id="productID" name="productID" />

                    <div class="mb-3">
                        <label for="title" class="form-label">Τίτλος</label>
                        <input type="text" class="form-control" id="title" name="title" required />
                    </div>

                    <div class="mb-3">
                        <label for="price" class="form-label">Τιμή (€)</label>
                        <input type="currency" class="form-control" id="price" name="price" required />
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Περιγραφή</label>
                        <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="ebook_file" class="form-label">Αρχείο eBook (PDF)</label>
                        <input type="file" class="form-control" id="ebook_file" name="ebook_file" accept=".pdf" />

                        <div id="currentFileMsg" class="alert alert-info mt-2 p-2" style="display:none; font-size: 0.9em;">
                            <i class="bi bi-file-earmark-pdf-fill"></i> Τρέχον αρχείο:
                            <strong id="currentFileName"></strong>
                            <div class="text-muted small mt-1">Ανεβάστε νέο αρχείο μόνο αν θέλετε να αντικαταστήσετε το τρέχον.</div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="cover_image" class="form-label">Εικόνα Εξωφύλλου</label>
                        <input type="file" class="form-control" id="cover_image" name="cover_image" accept="image/*" />

                        <div id="currentCoverMsg" class="mt-2 text-center p-2 border rounded bg-light" style="display:none;">
                            <label class="d-block text-muted small mb-1">Preview:</label>
                            <img id="coverPreview" src="" alt="Cover Preview" style="max-height: 150px; border-radius: 4px; border: 1px solid #ddd;">
                        </div>
                    </div>

                </form>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Κλείσιμο</button>
                <button type="button" class="btn btn-primary" id="productsActionBtn" data-action="addDigitalProduct">Αποθήκευση</button>
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

    <script src="/assets/js/back/digital_products.js"></script>
<?php
}
?>
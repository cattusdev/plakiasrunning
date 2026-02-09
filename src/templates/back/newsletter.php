<?php
if (!isset($GLOBAL_INCLUDE_CHECK)) die("Access Denied");
?>


<div class="container-fluid mt-5">

    <h3><i class="bi bi-building"></i> Newsletter</h3>

    <div class="bg-primary d-flex align-items-center justify-content-between p-2 rounded mb-3 gap-4">

        <div class="d-flex align-items-center gap-1">
            <input type="text" class="form-control mr-sm-2" placeholder="Αναζήτηση.." name="searchNewsletter" id="searchNewsletter">
            <button class="m-0 btn btn-primary" type="button" title="Add new operator" data-bs-toggle="modal" data-bs-target="#newsletterModal" id="addNewItem">
                <i class="bi bi-plus-lg font-md hand"></i>
            </button>
        </div>

        <div class="d-flex align-items-center">


            <i class="bi bi-printer mx-1 hand optionsPrint"></i>
            <i class="bi bi-download mx-1 hand optionsExport"></i>
            <select class="form-select form-select-sm border-0 w-auto mx-1" id="exportOptions">
                <option value="excel">Excel</option>
                <option value="csv">CSV</option>
                <option value="pdf">PDF</option>
            </select>
        </div>

    </div>

    <table class="table table-hover table-bordered rounded shadow-sm" id="newsletter_table">

        <tbody id="newsletter_body">

        </tbody>

    </table>
</div>


<div class="modal fade" id="newsletterModal" aria-labelledby="newsletterModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content bg-primary">
            <div class="modal-header border-0 bg-secondary">
                <h1 class="modal-title fs-5" id="newsletterModalLongTitle">Newsletter

                </h1>
                <button type="button" class="btn-close text-white m-1" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>


            <div class="modal-body">
                <div class="d-flex justify-content-start align-items-center"> <small id="newsletterCreated" class="pull-right"></small></div>

                <form action="" id="newsletterForm">

                    <div class="row">
                        <div class="col-md-12">
                            <label for="email">Email</label>
                            <input type="email" class="form-control mb-2 mr-sm-2" placeholder="Email" name="email" id="email" required>
                        </div>
                    </div>
                    <div class="d-flex justify-content-start align-items-center"> <small id="newsletterUpdated" class="pull-right"></small></div>
                    <input type="hidden" id="mainID">
                </form>
            </div>

            <div class="modal-footer border-0">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Κλείσιμο</button>
                <button type="button" class="btn btn-primary" value="Add" id="newsletterActionBtn" data-action="addItem" data-key="newsletter">Αποθήκευση</button>
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

    <script src="/assets/js/back/newsletter.js"></script>
<?php
}
?>
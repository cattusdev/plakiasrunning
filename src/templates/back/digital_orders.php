<?php
if (!isset($GLOBAL_INCLUDE_CHECK)) die("Access Denied");
?>

<div class="container-fluid mt-5">
    <h3><i class="bi bi-cart-check"></i> Παραγγελίες eBooks</h3>

    <div class="bg-primary d-flex align-items-center justify-content-between p-2 rounded mb-3">
        <div class="d-flex align-items-center gap-1">
            <input type="text" class="form-control mr-sm-2" placeholder="Αναζήτηση..." id="searchOrders">
        </div>
        <div class="text-white pe-3">
            Σύνολο: <span id="totalOrders" class="fw-bold">0</span>
        </div>
    </div>

    <table class="table table-hover table-bordered rounded shadow-sm" id="orders_table"></table>
</div>

<div class="modal fade" id="editOrderModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Διαχείριση Παραγγελίας #<span id="modalOrderId"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Επιλέξτε ενέργεια για τον πελάτη:</p>

                <div class="d-grid gap-2">
                    <button class="btn btn-outline-primary reset-action" data-type="downloads">
                        <i class="bi bi-arrow-counterclockwise"></i> Μηδενισμός Λήψεων (Reset Count)
                    </button>
                    <small class="text-muted mb-3">Χρήσιμο αν ο πελάτης ξεπέρασε το όριο των 5 λήψεων.</small>

                    <button class="btn btn-outline-success reset-action" data-type="expiration">
                        <i class="bi bi-calendar-check"></i> Ανανέωση Χρόνου (Renew Link)
                    </button>
                    <small class="text-muted">Ανανεώνει το link για άλλες 7 ημέρες από σήμερα.</small>
                </div>

                <input type="hidden" id="selectedOrderId">
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

    <script src="/assets/js/back/digital_orders.js"></script>
<?php
}
?>
<?php
if (!isset($GLOBAL_INCLUDE_CHECK)) die("Access Denied");
?>



<div class="container-fluid mt-5">
    <h3><i class="bi bi-credit-card"></i> Πληρωμές</h3>

    <div class=" bg-primary d-flex align-items-center justify-content-between p-2 rounded mb-3">
        <div class="d-flex align-items-center gap-1">
            <input type="text" class="form-control mr-sm-2" placeholder="Αναζήτηση.." id="searchPayments">

        </div>
    </div>

    <table class="table table-hover table-bordered rounded shadow-sm" id="payments_table"></table>
    <small class="text-danger float-left" id="paymentsTableRet">
    </small>
</div>


<div class="modal fade" id="paymentsModal" tabindex="-1" role="dialog" aria-labelledby="paymentsModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content border-0">
            <div class="modal-header bg-deepB align-items-baseline">
                <i class="bi bi-credit-card pr-2"></i>
                <h5 class="modal-title" id="paymentsModalLongTitle">Payment #</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true" class="text-white">&times;</span>
                </button>
            </div>
            <div class="modal-body pb-0">
                <div class="container">
                    <div class="row">
                        <div class="col-12">
                            <form action="" id="paymentsForm" class="text-dark">
                                <div class="card shadow-sm">
                                    <div class="card-header bg-primary text-white">
                                        <h5 class="mb-0"><i class="bi bi-receipt-cutoff"></i> Payment Details</h5>
                                    </div>
                                    <div class="card-body p-3">
                                        <ul class="list-group list-group-flush" id="bookingDetails">
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                <strong>Client Name</strong> <span id="clientName"></span>
                                            </li>
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                <strong>Reservation ID</strong> <span id="reservationID"></span>
                                            </li>
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                <strong>Payer Email</strong> <span id="payerEmail"></span>
                                            </li>
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                <strong>Transaction ID</strong> <span id="transactionID"></span>
                                            </li>
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                <strong>Payment Reference</strong> <span id="paymentRef"></span>
                                            </li>
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                <strong>Payment Method</strong> <span id="paymentMethod"></span>
                                            </li>
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                <strong>Amount Paid</strong> <span id="amount_paid" class="text-success fw-bold"></span>
                                            </li>
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                <strong>Total Amount</strong> <span id="total_amount"></span>
                                            </li>
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                <strong>Amount Due</strong> <span id="amount_due" class="text-danger fw-bold"></span>
                                            </li>
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                <strong>Billing Country</strong> <span id="clientCountry"></span>
                                            </li>
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                <strong>Billing City</strong> <span id="billingCity"></span>
                                            </li>
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                <strong>Billing Zip</strong> <span id="billingZip"></span>
                                            </li>
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                <strong>Billing Address</strong> <span id="billingAddress"></span>
                                            </li>
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                <strong>Status</strong> <span id="paymentStatus" class="badge bg-info text-dark"></span>
                                            </li>
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                <strong>Created At</strong> <span id="createdAt"></span>
                                            </li>
                                        </ul>
                                    </div>
                                    <div class="card-footer bg-light">
                                        <label for="paymentNote" class="form-label fw-bold"><i class="bi bi-journal-text"></i> Add Note</label>
                                        <textarea class="form-control" rows="3" placeholder="Write your note here..." name="paymentNote" id="paymentNote"></textarea>
                                    </div>
                                </div>

                                <input type="hidden" id="paymentID">
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <input type="button" class="btn btn-info mt-1 pull-right" value="Add" id="paymentsActionBtn" data-action="updPayment" data-key="payments">
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

    <script src="/assets/js/back/payments.js"></script>
<?php
}
?>
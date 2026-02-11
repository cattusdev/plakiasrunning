<?php
if (!isset($GLOBAL_INCLUDE_CHECK)) die("Access Denied");
?>

<div class="container-fluid mt-5">

    <!-- Header -->
    <div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-3 mb-3">
        <div>
            <h3 class="mb-1 d-flex align-items-center gap-2">
                <i class="bi bi-credit-card"></i>
                Πληρωμές
            </h3>
            <div class="text-muted small">
                Διαχείριση πληρωμών, στοιχεία κάρτας/τιμολόγησης, κατάσταση και σημειώσεις.
            </div>
        </div>

        <div class="d-flex align-items-center gap-2">
            <div class="input-group" style="max-width: 360px;">
                <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
                <input type="text" class="form-control" placeholder="Αναζήτηση σε πληρωμές..." id="searchPayments">
            </div>

            <!-- Optional: export hooks (αν ήδη τα χρησιμοποιείς σε αλλού) -->
            <div class="dropdown">
                <button class="btn btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown" type="button">
                    <i class="bi bi-download me-1"></i> Export
                </button>
                <div class="dropdown-menu dropdown-menu-end p-2" style="min-width: 220px;">
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-sm btn-outline-secondary w-100 optionsExport">Export</button>
                        <select class="form-select form-select-sm w-100">
                            <option value="excel">Excel</option>
                            <option value="csv">CSV</option>
                            <option value="pdf">PDF</option>
                        </select>
                    </div>
                    <button type="button" class="btn btn-sm btn-outline-secondary w-100 mt-2 optionsPrint">
                        <i class="bi bi-printer me-1"></i> Print
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Table wrapper -->
    <div class="bg-white border rounded-3 p-2 p-md-3">
        <table class="table table-hover align-middle mb-0" id="payments_table"></table>
        <small class="text-danger" id="paymentsTableRet"></small>
    </div>

</div>

<!-- Payments Modal -->
<div class="modal fade" id="paymentsModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg rounded-4">

            <div class="modal-header bg-white border-0">
                <div class="w-100 d-flex align-items-start justify-content-between gap-3">
                    <div class="min-w-0">
                        <h5 class="modal-title fw-bold mb-1" id="paymentsModalLongTitle">Payment #</h5>
                        <div class="d-flex flex-wrap gap-2">
                            <span class="badge bg-light text-dark border" id="uiPayStatus">—</span>
                            <span class="badge bg-light text-dark border" id="uiPayAmount">—</span>
                            <span class="badge bg-light text-dark border" id="uiPayMethod">—</span>
                        </div>
                    </div>
                    <button type="button" class="btn-close mt-1" data-bs-dismiss="modal"></button>
                </div>
            </div>

            <div class="modal-body pt-2">
                <form action="" id="paymentsForm" class="text-dark">
                    <div class="row g-3">

                        <!-- Left: summary -->
                        <div class="col-lg-6">
                            <div class="p-3 border rounded-3 bg-white">
                                <div class="fw-bold mb-2"><i class="bi bi-receipt-cutoff me-2"></i>Payment Summary</div>

                                <div class="d-flex justify-content-between py-2 border-bottom">
                                    <span class="text-muted">Client</span>
                                    <span class="fw-semibold text-end" id="clientName"></span>
                                </div>

                                <div class="d-flex justify-content-between py-2 border-bottom">
                                    <span class="text-muted">Reservation</span>
                                    <span class="fw-semibold" id="reservationID"></span>
                                </div>

                                <div class="d-flex justify-content-between py-2 border-bottom">
                                    <span class="text-muted">Payer Email</span>
                                    <span class="fw-semibold text-end" id="payerEmail"></span>
                                </div>

                                <div class="d-flex justify-content-between py-2 border-bottom">
                                    <span class="text-muted">Status</span>
                                    <span class="fw-semibold" id="paymentStatus"></span>
                                </div>

                                <div class="d-flex justify-content-between py-2 border-bottom">
                                    <span class="text-muted">Created</span>
                                    <span class="fw-semibold" id="createdAt"></span>
                                </div>
                            </div>

                            <div class="p-3 border rounded-3 bg-white mt-3">
                                <div class="fw-bold mb-2"><i class="bi bi-cash-coin me-2"></i>Amounts</div>

                                <div class="d-flex justify-content-between py-2 border-bottom">
                                    <span class="text-muted">Paid</span>
                                    <span class="fw-bold text-success" id="amount_paid"></span>
                                </div>

                                <div class="d-flex justify-content-between py-2 border-bottom">
                                    <span class="text-muted">Total</span>
                                    <span class="fw-semibold" id="total_amount"></span>
                                </div>

                                <div class="d-flex justify-content-between pt-2">
                                    <span class="text-muted">Due</span>
                                    <span class="fw-bold text-danger" id="amount_due"></span>
                                </div>
                            </div>
                        </div>

                        <!-- Right: transaction + billing + note -->
                        <div class="col-lg-6">
                            <div class="p-3 border rounded-3 bg-white">
                                <div class="fw-bold mb-2"><i class="bi bi-shield-lock me-2"></i>Transaction</div>

                                <div class="d-flex flex-column justify-content-start py-2 border-bottom">
                                    <span class="text-muted">Transaction ID</span>
                                    <span class="fw-semibold text-end" id="transactionID"></span>
                                </div>

                                <div class="d-flex justify-content-between py-2">
                                    <span class="text-muted">Payment Ref</span>
                                    <span class="fw-semibold text-end" id="paymentRef"></span>
                                </div>

                                <div class="d-flex justify-content-between pt-2">
                                    <span class="text-muted">Method</span>
                                    <span class="fw-semibold" id="paymentMethod"></span>
                                </div>
                            </div>

                            <div class="p-3 border rounded-3 bg-white mt-3">
                                <div class="fw-bold mb-2"><i class="bi bi-geo-alt me-2"></i>Billing</div>

                                <div class="d-flex justify-content-between py-2 border-bottom">
                                    <span class="text-muted">Country</span>
                                    <span class="fw-semibold" id="clientCountry"></span>
                                </div>

                                <div class="d-flex justify-content-between py-2 border-bottom">
                                    <span class="text-muted">City</span>
                                    <span class="fw-semibold" id="billingCity"></span>
                                </div>

                                <div class="d-flex justify-content-between py-2 border-bottom">
                                    <span class="text-muted">Zip</span>
                                    <span class="fw-semibold" id="billingZip"></span>
                                </div>

                                <div class="d-flex justify-content-between pt-2">
                                    <span class="text-muted">Address</span>
                                    <span class="fw-semibold text-end" id="billingAddress"></span>
                                </div>
                            </div>

                            <div class="p-3 border rounded-3 bg-light mt-3">
                                <div class="fw-bold mb-2"><i class="bi bi-journal-text me-2"></i>Note</div>
                                <textarea class="form-control" rows="4" placeholder="Write your note here..." name="paymentNote" id="paymentNote"></textarea>
                            </div>

                            <input type="hidden" id="paymentID">
                        </div>

                    </div>
                </form>
            </div>

            <div class="modal-footer bg-white border-top sticky-bottom">
                <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Κλείσιμο</button>
                <input type="button" class="btn btn-primary px-4" value="Αποθήκευση" id="paymentsActionBtn" data-action="updPayment" data-key="payments">
            </div>

        </div>
    </div>
</div>

<style>
    /* Payments page UI polish (safe) */
    #payments_table tbody td {
        vertical-align: top;
    }

    #payments_table .pay-pill {
        display: inline-flex;
        align-items: center;
        gap: .35rem;
        padding: .25rem .55rem;
        border-radius: 999px;
        border: 1px solid rgba(0, 0, 0, .08);
        background: #fff;
        font-size: .78rem;
        font-weight: 600;
        white-space: nowrap;
    }

    #payments_table .pay-actions .btn {
        border-radius: 10px;
    }

    #payments_table .pay-client {
        font-weight: 700;
        line-height: 1.2;
    }

    #payments_table .pay-sub {
        font-size: .82rem;
        color: #6c757d;
    }

    #paymentsModal .modal-content {
        border-radius: 18px;
    }

    #paymentsModal .modal-footer.sticky-bottom {
        position: sticky;
        bottom: 0;
        z-index: 3;
    }

    .img-flag {
        width: 18px;
        height: 12px;
        object-fit: cover;
        border-radius: 2px;
        box-shadow: 0 0 0 1px rgba(0, 0, 0, .08);
    }
</style>

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
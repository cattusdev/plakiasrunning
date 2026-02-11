<?php
if (!isset($GLOBAL_INCLUDE_CHECK)) die("Access Denied");
?>

<div class="container-fluid mt-5">

    <!-- Header -->
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-3">
        <div>
            <h3 class="mb-1 d-flex align-items-center gap-2">
                <i class="bi bi-calendar-event"></i>
                Διαχείριση Κρατήσεων
            </h3>
            <div class="text-muted small">
                Καταχώριση & επεξεργασία κρατήσεων για running events / routes (slots, ομαδικά & ατομικά).
            </div>
        </div>
        <div class="d-flex align-items-center gap-2">
            <button class="btn btn-primary text-nowrap" type="button" id="addNewBooking">
                <i class="bi bi-plus-lg me-1"></i> Νέα Κράτηση
            </button>
        </div>
    </div>

    <!-- Toolbar -->
    <div class="bg-white border rounded-3 p-3 mb-3">
        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-2">
            <div class="d-flex align-items-center gap-2">
                <div class="input-group" style="max-width: 380px;">
                    <span class="input-group-text bg-white">
                        <i class="bi bi-search"></i>
                    </span>
                    <input type="text" class="form-control" placeholder="Αναζήτηση σε κρατήσεις..." id="searchBookings">
                </div>

                <select class="form-select form-select-sm" id="exportOptions" style="max-width: 160px;">
                    <option value="" disabled selected>Εξαγωγή</option>
                    <option value="excel">Excel</option>
                    <option value="print">Εκτύπωση</option>
                </select>
            </div>

           
        </div>
    </div>

    <!-- TABLE (as-is, not wrapped) -->
    <table class="table table-hover table-bordered w-100 shadow-sm" id="bookings_table">
        <thead class="table-light">
            <tr>
                <th>ID</th>
                <th>Πελάτης</th>
                <th>Guide</th>
                <th>Έναρξη</th>
                <th>Λήξη</th>
                <th>Διαδρομή</th>
                <th>Status</th>
                <th>Ενέργειες</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>
</div>

<!-- Booking Modal -->
<div class="modal fade" id="bookingModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-sm rounded-4">
            <div class="modal-header border-0">
                <div>
                    <h5 class="modal-title mb-0" id="bookingModalTitle">
                        Διαχείριση Κράτησης
                        <span id="modalStatusBadge" class="badge bg-light text-dark ms-2 fs-6"></span>
                    </h5>
                    <div class="small text-muted">Στοιχεία κράτησης, slots/διαθεσιμότητα και πληρωμές.</div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body p-0">
                <ul class="nav nav-tabs nav-fill bg-light" id="bookingTabs" role="tablist">
                    <li class="nav-item">
                        <button class="nav-link active" id="tab-details" data-bs-toggle="tab" data-bs-target="#pane-details" type="button">
                            <i class="bi bi-calendar-check me-2"></i>Στοιχεία
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link" id="tab-payments" data-bs-toggle="tab" data-bs-target="#pane-payments" type="button" disabled>
                            <i class="bi bi-currency-euro me-2"></i>Πληρωμές
                        </button>
                    </li>
                </ul>

                <div class="tab-content p-3">

                    <!-- Details -->
                    <div class="tab-pane fade show active" id="pane-details">
                        <form id="bookingForm">
                            <input type="hidden" id="bookingId">

                            <div class="row g-3 mb-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Πελάτης</label>
                                    <div class="input-group">
                                        <select class="form-select" id="client_id" style="width:85%" required></select>
                                        <button class="btn btn-outline-primary" type="button" id="btnQuickAddClient" know-hint="quick-client">
                                            <i class="bi bi-person-plus-fill"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Guide</label>
                                    <select class="form-select" id="therapist_id" required></select>
                                </div>
                            </div>

                            <div class="row g-3 mb-3">
                                <div class="col-md-9">
                                    <label class="form-label fw-bold">Διαδρομή / Route</label>
                                    <select class="form-select" id="package_id" disabled></select>

                                    <input type="hidden" id="package_duration">
                                    <input type="hidden" id="booking_price">
                                    <input type="hidden" id="appointment_type" value="inPerson">
                                </div>

                                <div class="col-md-3">
                                    <label class="form-label fw-bold">Άτομα (Pax)</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-white"><i class="bi bi-people-fill"></i></span>
                                        <input type="number" class="form-control text-center fw-bold" id="attendees_count" value="1" min="1" required>
                                    </div>
                                </div>
                            </div>

                            <div id="group_info_panel" class="alert alert-info d-none">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="alert-heading fw-bold mb-1"><i class="bi bi-people-fill me-2"></i>Ομαδικό Event</h6>
                                        <small id="group_time_display">...</small>
                                    </div>
                                    <div class="text-end">
                                        <span class="badge bg-light text-dark border fs-6" id="group_capacity_badge">0 / 10</span>
                                        <div class="mt-1">
                                            <button type="button" class="btn btn-xs btn-link text-decoration-none p-0" id="btnViewAttendees">
                                                Προβολή λίστας
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div id="slot_picker_panel" class="mb-4">
                                <label class="form-label fw-bold">Επιλογή Ημερομηνίας</label>
                                <input type="date" class="form-control mb-3" id="slot_date_picker">

                                <div id="slots_loader" class="text-center d-none text-muted my-3">
                                    <div class="spinner-border spinner-border-sm"></div> Αναζήτηση διαθεσιμότητας...
                                </div>

                                <label class="form-label small text-muted">Διαθέσιμες Ώρες</label>
                                <div id="slots_container" class="d-flex flex-wrap gap-2 p-2 border rounded bg-light" style="min-height: 60px;">
                                    <span class="text-muted small align-self-center mx-auto">Επιλέξτε Guide & Διαδρομή</span>
                                </div>
                            </div>

                            <div class="row g-2 mb-3 bg-white border p-2 rounded align-items-end">
                                <div class="col-5">
                                    <label class="small text-muted">Έναρξη</label>
                                    <input type="datetime-local" class="form-control form-control-sm" id="start_datetime" readonly>
                                </div>
                                <div class="col-2 text-center pb-1">
                                    <button type="button" class="btn btn-sm btn-outline-secondary w-100" id="btnSlotAttendees" title="Προβολή λίστας για αυτή την ώρα" disabled>
                                        <i class="bi bi-people-fill"></i>
                                    </button>
                                </div>
                                <div class="col-5">
                                    <label class="small text-muted">Λήξη</label>
                                    <input type="datetime-local" class="form-control form-control-sm" id="end_datetime">
                                </div>
                            </div>

                            <div class="mb-3">
                                <div class="row g-2">
                                    <div class="col-md-8">
                                        <label class="form-label">Σημειώσεις</label>
                                        <textarea class="form-control" id="booking_notes" rows="1"></textarea>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Κατάσταση Κράτησης</label>
                                        <select class="form-select" id="booking_status">
                                            <option value="booked">Κρατημένο</option>
                                            <option value="canceled">Ακυρωμένο</option>
                                            <option value="completed">Ολοκληρωμένο</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-end pt-2 border-top">
                                <button type="submit" class="btn btn-success" id="saveBookingBtn">Αποθήκευση</button>
                            </div>
                        </form>
                    </div>

                    <!-- Payments (unchanged structure) -->
                    <div class="tab-pane fade" id="pane-payments">
                        ...
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="quickClientModal" tabindex="-1" aria-hidden="true" style="z-index: 1060;">
    <div class="modal-dialog modal-md modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-light py-2">
                <h6 class="modal-title fw-bold"><i class="bi bi-person-plus-fill me-2"></i>Γρήγορη Προσθήκη Πελάτη</h6>
                <button type="button" class="btn-close" onclick="$('#quickClientModal').modal('hide')"></button>
            </div>
            <div class="modal-body">
                <form id="quickClientForm">
                    <div class="row g-2 mb-2">
                        <div class="col-6">
                            <label class="form-label small fw-bold text-muted">Όνομα <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="qc_fname" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-bold text-muted">Επώνυμο <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="qc_lname" required>
                        </div>
                    </div>

                    <div class="row g-2 mb-2">
                        <div class="col-6">
                            <label class="form-label small fw-bold text-muted">Τηλέφωνο <span class="text-danger">*</span></label>
                            <input type="tel" class="form-control" id="qc_phone" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-bold text-muted">Email</label>
                            <input type="email" class="form-control" id="qc_email">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">Σημείωση</label>
                        <textarea class="form-control" id="qc_note" rows="2" placeholder="π.χ. Σύσταση από..."></textarea>
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary" id="btnSaveQuickClient">Δημιουργία & Επιλογή</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="attendeesModal" tabindex="-1" aria-hidden="true" style="z-index: 1070;">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title fw-bold"><i class="bi bi-people-fill me-2"></i>Λίστα Συμμετεχόντων</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Ονοματεπώνυμο</th>
                                <th>Τηλέφωνο</th>
                                <th>Κατάσταση</th>
                            </tr>
                        </thead>
                        <tbody id="attendeesList">
                        </tbody>
                    </table>
                </div>
                <div id="attendeesLoader" class="text-center py-3 d-none">
                    <div class="spinner-border text-warning" role="status"></div>
                </div>
                <div id="noAttendeesMsg" class="text-center py-3 text-muted d-none">
                    Κανένας συμμετέχων ακόμα.
                </div>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Κλείσιμο</button>
            </div>
        </div>
    </div>
</div>

<style>
    .input-group>.select2-container--bootstrap-5 {
        width: auto;
        flex: 1 1 auto;
    }
</style>

<style>
    .input-group>.select2-container--bootstrap-5 {
        width: auto;
        flex: 1 1 auto;
    }

    /* Small polish */
    #slots_container .time-slot-btn {
        min-width: 78px;
    }

    .modal-content {
        background: #fff;
    }

    .nav-tabs .nav-link {
        border-radius: .75rem .75rem 0 0;
    }
</style>

<?php
function hook_end_scripts()
{
?>
    <link rel="stylesheet" href="/assets/vendor/plugins/datatables/datatables.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />

    <link rel="stylesheet" type="text/css" href="/assets/vendor/plugins/datatables/datatables.css">
    <link rel="stylesheet" type="text/css" href="/assets/css/back/table.css">
    <script src="/assets/vendor/plugins/datatables/datatables.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="/assets/js/back/bookings.js"></script>
<?php
}
?>
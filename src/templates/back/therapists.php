<?php
if (!isset($GLOBAL_INCLUDE_CHECK)) die("Access Denied");
?>

<div class="container-fluid mt-5">
    <h3><i class="bi bi-person-badge-fill"></i> Διαχείριση Guides / Pacers</h3>

    <div class="bg-white d-flex align-items-center justify-content-between p-3 rounded shadow-sm mb-3">
        <div class="d-flex align-items-center gap-2">
            <input type="text" class="form-control" placeholder="Αναζήτηση..." id="searchTherapists" style="max-width: 300px;">
            <button class="btn btn-primary" id="addNewTherapist" data-bs-toggle="modal" data-bs-target="#therapistsModal">
                <i class="bi bi-plus-lg me-1"></i> Προσθήκη
            </button>
        </div>
    </div>

    <table class="table table-hover table-bordered rounded bg-white" id="therapists_table" style="width:100%"></table>
</div>

<div class="modal fade" id="therapistsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-light">
                <h5 class="modal-title" id="therapistsModalLongTitle">Προσθήκη Guide</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <form id="therapistsForm">
                    <input type="hidden" id="therapistID" name="therapistID">

                    <ul class="nav nav-tabs nav-fill mb-3" id="therapistTabs" role="tablist">
                        <li class="nav-item">
                            <button class="nav-link active" id="tab-general-link" data-bs-toggle="tab" data-bs-target="#tab-general" type="button">
                                <i class="bi bi-person-lines-fill me-1"></i> Στοιχεία
                            </button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link" id="tab-policies-link" data-bs-toggle="tab" data-bs-target="#tab-policies" type="button">
                                <i class="bi bi-sliders me-1"></i> Πολιτικές
                            </button>
                        </li>
                        <li class="nav-item" id="nav-item-schedule">
                            <button class="nav-link" id="tab-schedule-link" data-bs-toggle="tab" data-bs-target="#tab-schedule" type="button">
                                <i class="bi bi-clock me-1"></i> Πρόγραμμα
                            </button>
                        </li>
                    </ul>

                    <div class="tab-content">

                        <div class="tab-pane fade show active" id="tab-general">
                            <div class="row g-3 mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Όνομα *</label>
                                    <input type="text" class="form-control" name="first_name" id="first_name" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Επώνυμο *</label>
                                    <input type="text" class="form-control" name="last_name" id="last_name" required>
                                </div>
                            </div>

                            <div class="row g-3 mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Τίτλος / Ειδικότητα</label>
                                    <input type="text" class="form-control" name="title" id="title" placeholder="π.χ. Lead Guide">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Pace Range (Ρυθμός)</label>
                                    <input type="text" class="form-control" name="pace_range" id="pace_range" placeholder="π.χ. 4:30 - 7:00 min/km">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Γλώσσες (Languages)</label>
                                <input type="text" class="form-control" name="languages" id="languages" placeholder="π.χ. English, Greek, German">
                            </div>

                            <div class="row g-3 mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Email</label>
                                    <input type="email" class="form-control" name="email" id="email">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Τηλέφωνο</label>
                                    <input type="text" class="form-control" name="phone" id="phone">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Βιογραφικό (Bio)</label>
                                <textarea class="form-control" name="bio" id="bio" rows="3"></textarea>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Φωτογραφία</label>
                                <input type="file" class="form-control" name="avatar" id="avatar" accept="image/*">
                                <div class="mt-2" id="currentAvatarMsg" style="display:none;">
                                    <img src="" id="avatarPreview" style="height: 60px; width: 60px; object-fit: cover; border-radius: 50%; border: 2px solid #ddd;">
                                </div>
                            </div>
                        </div>

                        <div class="tab-pane fade" id="tab-policies">
                            <div class="p-2">
                                <div class="alert alert-light border small mb-3">
                                    <i class="bi bi-info-circle me-1"></i> Κανόνες κρατήσεων για τον Guide.
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-bold small text-uppercase text-muted">Booking Window</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-white"><i class="bi bi-calendar-range"></i></span>
                                        <input type="number" class="form-control" name="booking_window_days" id="booking_window_days" min="1" placeholder="60">
                                        <span class="input-group-text">ημέρες</span>
                                    </div>
                                    <div class="form-text small">Πόσες μέρες μπροστά ανοίγει το πρόγραμμα.</div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-bold small text-uppercase text-muted">Min Notice</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-white"><i class="bi bi-hourglass-bottom"></i></span>
                                        <input type="number" class="form-control" name="min_notice_hours" id="min_notice_hours" min="0" placeholder="12">
                                        <span class="input-group-text">ώρες</span>
                                    </div>
                                    <div class="form-text small">Ελάχιστη προειδοποίηση πριν την κράτηση.</div>
                                </div>
                            </div>
                        </div>

                        <div class="tab-pane fade" id="tab-schedule">
                            <div class="d-flex justify-content-between align-items-center mb-2 px-1">
                                <span class="small text-muted">Εβδομαδιαίο πρόγραμμα</span>
                                <button class="btn btn-sm btn-outline-primary py-0" id="addModalRuleBtn" type="button">
                                    <i class="bi bi-plus-lg"></i> Προσθήκη
                                </button>
                            </div>

                            <div class="table-responsive border rounded" style="max-height: 300px; overflow-y: auto;">
                                <table class="table table-sm align-middle mb-0" id="modalRulesTable">
                                    <thead class="table-light sticky-top">
                                        <tr class="small text-muted text-center">
                                            <th style="width: 20%;">Ημέρα</th>
                                            <th style="width: 15%;">Από</th>
                                            <th style="width: 15%;">Έως</th>
                                            <th style="width: 35%;">Ανάθεση (Route)</th>
                                            <th style="width: 15%;"></th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white"></tbody>
                                </table>
                            </div>
                            <div class="form-text small mt-2 text-muted px-1">
                                * "General Availability": Ο Guide είναι διαθέσιμος για όλα τα πακέτα.
                                <br>* "Specific Route": Η ώρα δεσμεύεται αποκλειστικά για το συγκεκριμένο πακέτο.
                            </div>
                        </div>

                    </div>
                </form>
            </div>

            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Ακύρωση</button>
                <button type="button" class="btn btn-primary" id="therapistsActionBtn">Αποθήκευση</button>
            </div>
        </div>
    </div>
</div>

<?php function hook_end_scripts()
{ ?>
    <link rel="stylesheet" type="text/css" href="/assets/vendor/plugins/datatables/datatables.css">
    <link rel="stylesheet" type="text/css" href="/assets/css/back/table.css">
    <script src="/assets/vendor/plugins/datatables/datatables.js"></script>

    <script src="/assets/js/back/therapists.js"></script>
<?php } ?>
<?php
if (!isset($GLOBAL_INCLUDE_CHECK)) die("Access Denied");
?>

<div class="container-fluid mt-5">

    <!-- Page Header -->
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-3">
        <div>
            <h3 class="mb-1 d-flex align-items-center gap-2">
                <i class="bi bi-person-badge-fill"></i>
                Διαχείριση Guides / Pacers
            </h3>
            <div class="text-muted small">
                Καταχώριση και διαχείριση συνοδών/pace leaders για τα running events (διαθεσιμότητα & ανάθεση σε routes).
            </div>
        </div>

        <div class="d-flex align-items-center gap-2">
            <button class="btn btn-primary" id="addNewTherapist" data-bs-toggle="modal" data-bs-target="#therapistsModal">
                <i class="bi bi-plus-lg me-1"></i> Νέος Guide
            </button>
        </div>
    </div>

    <!-- Toolbar -->
    <div class="bg-white border rounded-3 p-3 mb-3">
        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-2">
            <div class="d-flex align-items-center gap-2">
                <div class="input-group" style="max-width: 360px;">
                    <span class="input-group-text bg-white">
                        <i class="bi bi-search"></i>
                    </span>
                    <input type="text" class="form-control" placeholder="Αναζήτηση σε Guides..." id="searchTherapists">
                </div>
            </div>

            <div class="small text-muted">
                Tip: Κάνε κλικ στο ✏️ για επεξεργασία στοιχείων & προγράμματος.
            </div>
        </div>
    </div>

    <!-- TABLE (as-is, no wrapping) -->
    <table class="table table-hover table-bordered rounded bg-white" id="therapists_table" style="width:100%"></table>
</div>

<!-- Modal -->
<div class="modal fade" id="therapistsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-sm rounded-4">
            <div class="modal-header border-0">
                <div>
                    <h5 class="modal-title mb-0" id="therapistsModalLongTitle">Προσθήκη Guide</h5>
                    <div class="small text-muted">Συμπλήρωσε στοιχεία, πολιτικές και εβδομαδιαία διαθεσιμότητα.</div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body pt-0">
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

                        <!-- TAB: General -->
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
                                    <label class="form-label">Ρόλος / Τίτλος</label>
                                    <input type="text" class="form-control" name="title" id="title" placeholder="π.χ. Lead Pacer">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Εύρος Ρυθμού (Pace)</label>
                                    <input type="text" class="form-control" name="pace_range" id="pace_range" placeholder="π.χ. 4:30 - 7:00 min/km">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Γλώσσες</label>
                                <input type="text" class="form-control" name="languages" id="languages" placeholder="π.χ. Ελληνικά, Αγγλικά, Γερμανικά">
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
                                <label class="form-label">Σύντομο Βιογραφικό</label>
                                <textarea class="form-control" name="bio" id="bio" rows="3" placeholder="Λίγα λόγια για εμπειρία, events, routes κτλ."></textarea>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Φωτογραφία</label>
                                <input type="file" class="form-control" name="avatar" id="avatar" accept="image/*">
                                <div class="mt-2 d-flex align-items-center gap-2" id="currentAvatarMsg" style="display:none;">
                                    <img src="" id="avatarPreview" style="height: 60px; width: 60px; object-fit: cover; border-radius: 50%; border: 2px solid #eee;" alt="avatar">
                                    <div class="small text-muted">Προεπισκόπηση</div>
                                </div>
                            </div>
                        </div>

                        <!-- TAB: Policies -->
                        <div class="tab-pane fade" id="tab-policies">
                            <div class="p-2">
                                <div class="alert alert-light border small mb-3">
                                    <i class="bi bi-info-circle me-1"></i>
                                    Ρυθμίσεις κρατήσεων/διαθεσιμότητας για τον συγκεκριμένο Guide.
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-bold small text-uppercase text-muted">Παράθυρο Κρατήσεων</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-white"><i class="bi bi-calendar-range"></i></span>
                                        <input type="number" class="form-control" name="booking_window_days" id="booking_window_days" min="1" placeholder="60">
                                        <span class="input-group-text">ημέρες</span>
                                    </div>
                                    <div class="form-text small">Πόσες μέρες μπροστά ανοίγει η διαθεσιμότητα.</div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-bold small text-uppercase text-muted">Ελάχιστη Προειδοποίηση</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-white"><i class="bi bi-hourglass-bottom"></i></span>
                                        <input type="number" class="form-control" name="min_notice_hours" id="min_notice_hours" min="0" placeholder="12">
                                        <span class="input-group-text">ώρες</span>
                                    </div>
                                    <div class="form-text small">Ελάχιστος χρόνος πριν επιτραπεί κράτηση.</div>
                                </div>
                            </div>
                        </div>

                        <!-- TAB: Schedule -->
                        <div class="tab-pane fade" id="tab-schedule">
                            <div class="d-flex justify-content-between align-items-center mb-2 px-1">
                                <span class="small text-muted">Εβδομαδιαίο πρόγραμμα διαθεσιμότητας</span>
                                <button class="btn btn-sm btn-outline-primary" id="addModalRuleBtn" type="button">
                                    <i class="bi bi-plus-lg"></i> Προσθήκη
                                </button>
                            </div>

                            <div class="table-responsive border rounded">
                                <table class="table table-sm align-middle mb-0" id="modalRulesTable">
                                    <thead class="table-light sticky-top">
                                        <tr class="small text-muted text-center">
                                            <th style="width: 20%;">Ημέρα</th>
                                            <th style="width: 15%;">Από</th>
                                            <th style="width: 15%;">Έως</th>
                                            <th style="width: 35%;">Ανάθεση Route</th>
                                            <th style="width: 15%;"></th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white"></tbody>
                                </table>
                            </div>

                            <div class="form-text small mt-2 text-muted px-1">
                                * <b>Γενική Διαθεσιμότητα</b>: διαθέσιμος για όλα τα routes/packages.<br>
                                * <b>Συγκεκριμένο Route</b>: το slot δεσμεύεται αποκλειστικά για το επιλεγμένο route/package.
                            </div>
                        </div>

                    </div>
                </form>
            </div>

            <div class="modal-footer border-0">
                <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Ακύρωση</button>
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

    <!-- (Optional) tiny polish, no IDs touched -->
    <style>
        .modal-content {
            background: #fff;
        }

        .nav-tabs .nav-link {
            border-radius: .75rem .75rem 0 0;
        }

        .input-group-text {
            border-right: 0;
        }

        #searchTherapists {
            border-left: 0;
        }
    </style>

    <script src="/assets/js/back/therapists.js"></script>
<?php } ?>
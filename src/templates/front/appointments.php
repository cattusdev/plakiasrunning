<?php
if (!isset($GLOBAL_INCLUDE_CHECK)) die(header('location:  /'));
?>


<!-- Section: Introduction -->
<section class="py-5 bgb mbase">
    <div class="container text-center">
        <h1 class="mb-4 mstitle">Συνεδρίες</h1>
        <p class="mainp">
            Εξασφαλίστε έναν ποιοτικότερο τρόπο ζωής επιλέγοντας τον τύπο συνεδρίας που σας ταιριάζει.
        </p>
    </div>
</section>

<!-- Section: Tabs for Session Type Selection -->
<section class="mbase">
    <div class="container text-center">
        <button type="button" class="btn-main btn-lg" id="inPersonTab">Δια Ζώσης</button>
        <button type="button" class="btn-main btn-sec btn-lg me-2" id="onlineTab">Online</button>
    </div>
</section>

<!-- Online Sessions Content -->
<div id="onlineContent" class="content-section" style="display: none;">
    <!-- Section: Introduction -->
    <section class="py-5 bgs mbase">
        <div class="container text-center">
            <i class="fa fa-video fa-3x"></i>
            <h1 class="mb-4 stitle">Online</h1>
            <p class="mainp">
                Εξασφαλίστε έναν ποιοτικότερο τρόπο διατροφής από την άνεση του σπιτιού σας.
            </p>
        </div>
    </section>

    <!-- Section: How It Works -->
    <section class="mbase">
        <div class="container">
            <h2 class="mb-4 sstitle text-center">Πώς λειτουργεί</h2>
            <div class="row justify-content-center">
                <div class="col-md-4 text-center mb-4">
                    <div class="p-3">
                        <div class="cardIcon"> <i class="fas fa-list-alt fa-3x mb-3"></i>
                        </div>
                        <h4 class="stitle">1. Επιλέξτε Τύπο</h4>
                        <p class="mainp">
                            Διαλέξτε αν θέλετε πρώτη συνεδρία ή επαναληπτική συνεδρία.
                        </p>
                    </div>
                </div>
                <div class="col-md-4 text-center mb-4">
                    <div class="p-3">
                        <div class="cardIcon"> <i class="fas fa-calendar-alt fa-3x mb-3"></i>
                        </div>
                        <h4 class="stitle">2. Κλείστε Ραντεβού</h4>
                        <p class="mainp">
                            Επιλέξτε την ημέρα και ώρα που σας εξυπηρετεί μέσω της πλατφόρμας μας.
                        </p>
                    </div>
                </div>
                <div class="col-lg-3 text-center mb-4">
                    <div class="p-3">

                        <div class="cardIcon"><i class="fas fa-credit-card fa-3x mb-3"></i></div>
                        <h4 class="stitle">3. Πληρωμή</h4>
                        <p class="mainp">
                            Πληρώστε εύκολα και με ασφάλεια με την κάρτα σας μέσω της πλατφόρμας μας.
                        </p>
                    </div>
                </div>
                <div class="col-md-4 text-center mb-4">
                    <div class="p-3">
                        <div class="cardIcon"> <i class="fas fa-video fa-3x mb-3"></i>
                        </div>
                        <h4 class="stitle">4. Συνδεθείτε Online</h4>
                        <p class="mainp">
                            Λάβετε τον σύνδεσμο για τη συνεδρία σας και συνδεθείτε μέσω βιντεοκλήσης.
                        </p>
                    </div>
                </div>
                <div class="col-md-4 text-center mb-4">
                    <div class="p-3">
                        <div class="cardIcon"> <i class="fas fa-heart fa-3x mb-3"></i>
                        </div>
                        <h4 class="stitle">5. Ξεκινήστε τη Συνεδρία</h4>
                        <p class="mainp">
                            Απολαύστε προσωποποιημένες συμβουλές και καθοδήγηση από τον ειδικό.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Section: Benefits -->
    <section class="py-5 bg-light mbase">
        <div class="container">
            <div class="row">
                <div class="col-12 col-lg-4">
                    <h2 class="mstitle">Γιατί να επιλέξετε Online Συνεδρίες;</h2>
                </div>
                <div class="col-12 col-lg-6">
                    <div class="p-4 border-bottom">
                        <h4 class="stitle"><i class="fa fa-universal-access cs me-2"></i>Ευκολία</h4>
                        <p class="mainp">
                            Κάντε τη συνεδρία σας από οπουδήποτε, χωρίς μετακινήσεις.
                        </p>
                    </div>
                    <div class="p-4 border-bottom">
                        <h4 class="stitle"><i class="fa fa-user-shield cs me-2"></i>Ασφάλεια</h4>
                        <p class="mainp">
                            Όλες οι συνεδρίες πραγματοποιούνται μέσω ασφαλών και ιδιωτικών καναλιών.
                        </p>
                    </div>
                    <div class="p-4 border-bottom">
                        <h4 class="stitle"><i class="fa fa-calendar-check cs me-2"></i>Ευελιξία</h4>
                        <p class="mainp">
                            Διαλέξτε την ώρα που σας βολεύει.
                        </p>
                    </div>
                    <div class="p-4 border-bottom">
                        <h4 class="stitle"><i class="fa fa-list-check cs me-2"></i>Εξατομίκευση</h4>
                        <p class="mainp">
                            Προσαρμοσμένες λύσεις και καθοδήγηση, ειδικά για εσάς.
                        </p>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <style>
        .Subtitle {
            color: #181818;
        }
    </style>

    <!-- Section: Get Started -->
    <section class="py-5 text-center mbase">
        <div class="container">
            <h2 class="mb-4 mstitle">Ξεκινήστε Τώρα</h2>
            <p class="mb-4 mainp">
                Κλείστε την online συνεδρία σας εύκολα και γρήγορα και κάντε το πρώτο βήμα προς την ευεξία.
            </p>
            <a href="#" class="btn-main btn-lg" data-bs-toggle="modal" data-bs-target="#sessionModal">Κλείστε Συνεδρία</a>
        </div>
    </section>

</div>

<!-- In-Person Sessions Content -->
<div id="inPersonContent" class="content-section" style="display: none;">

    <!-- Section: Introduction -->
    <section class="py-5 bgt mbase">
        <div class="container text-center">
            <i class="fa fa-handshake fa-3x"></i>
            <h1 class="mb-4 stitle">Δια Ζώσης</h1>
            <p class="mainp">
                Ζήστε την εμπειρία της προσωπικής επικοινωνίας και της άμεσης καθοδήγησης με τις δια ζώσης συνεδρίες μας.
            </p>
        </div>
    </section>


    <!-- Section: How It Works -->
    <section class="mbase">
        <div class="container">
            <h2 class="mb-4 sstitle text-center">Πώς λειτουργεί</h2>
            <div class="row justify-content-center">
                <div class="col-md-4 text-center mb-4">
                    <div class="p-3">
                        <div class="cardIcon">
                            <i class="fas fa-list-alt fa-3x mb-3"></i>
                        </div>
                        <h4 class="stitle">1. Επιλέξτε Τύπο</h4>
                        <p class="mainp">
                            Διαλέξτε αν θέλετε πρώτη συνεδρία ή επαναληπτική συνεδρία.
                        </p>
                    </div>
                </div>
                <div class="col-md-4 text-center mb-4">
                    <div class="p-3">
                        <div class="cardIcon"><i class="fas fa-calendar-alt fa-3x mb-3"></i></div>
                        <h4 class="stitle">2. Κλείστε Ραντεβού</h4>
                        <p class="mainp">
                            Επιλέξτε την ημέρα και ώρα που σας εξυπηρετεί μέσω της πλατφόρμας μας.
                        </p>
                    </div>
                </div>
                <div class="col-md-4 text-center mb-4">
                    <div class="p-3">
                        <div class="cardIcon"><i class="fas fa-credit-card fa-3x mb-3"></i></div>
                        <h4 class="stitle">3. Ολοκληρώστε την Πληρωμή</h4>
                        <p class="mainp">
                            Πληρώστε εύκολα και με ασφάλεια με την κάρτα σας μέσω της πλατφόρμας μας.
                        </p>
                    </div>
                </div>
                <div class="col-md-4 text-center mb-4">
                    <div class="p-3">
                        <div class="cardIcon">
                            <i class="fas fa-map-marker-alt fa-3x mb-3"></i>
                        </div>
                        <h4 class="stitle">4. Επισκεφθείτε το Χώρο</h4>
                        <p class="mainp">
                            Ελάτε στο χώρο μας την προκαθορισμένη ώρα για την προσωπική σας συνεδρία.
                        </p>
                    </div>
                </div>
            </div>
        </div>

    </section>



    <!-- Section: Get Started -->
    <section class="py-5 text-center mbase">
        <div class="container">
            <h2 class="mb-4 mstitle">Ξεκινήστε Τώρα</h2>
            <p class="mb-4 mainp">
                Κλείστε την δια ζώσης συνεδρία σας εύκολα και γρήγορα.
            </p>
            <a href="#" class="btn-main btn-lg" data-bs-toggle="modal" data-bs-target="#sessionModal">Κλείστε Ραντεβού</a>
        </div>
    </section>

    <!-- Section: Benefits -->
    <section class="py-5 bg-light mbase">
        <div class="container">
            <div class="row">
                <div class="col-12 col-lg-4">
                    <h2 class="mstitle">Γιατί να επιλέξετε Δια Ζώσης Συνεδρίες;</h2>
                </div>
                <div class="col-12 col-lg-6">
                    <div class="p-4 border-bottom">
                        <h4 class="stitle"><i class="fa fa-handshake cs me-2"></i>Προσωπική Επαφή</h4>
                        <p class="mainp">
                            Απολαύστε την εμπειρία της άμεσης αλληλεπίδρασης σε έναν άνετο χώρο.
                        </p>
                    </div>
                    <div class="p-4 border-bottom">
                        <h4 class="stitle"><i class="fa fa-building cs me-2"></i>Επαγγελματικός Χώρος</h4>
                        <p class="mainp">
                            Όλες οι συνεδρίες πραγματοποιούνται σε έναν άρτια εξοπλισμένο και φιλόξενο χώρο.
                        </p>
                    </div>
                    <div class="p-4 border-bottom">
                        <h4 class="stitle"><i class="fa fa-calendar-check cs me-2"></i>Ευελιξία</h4>
                        <p class="mainp">
                            Επιλέξτε την ώρα που σας βολεύει και προγραμματίστε τη συνεδρία σας.
                        </p>
                    </div>
                    <div class="p-4 border-bottom">
                        <h4 class="stitle"><i class="fa fa-user-check cs me-2"></i>Άμεση Υποστήριξη</h4>
                        <p class="mainp">
                            Λάβετε εξατομικευμένες συμβουλές με έμφαση στις ανάγκες σας.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<style>
    .herobg {
        background-image: url(/assets/images/edietbg.svg) !important;
    }


    #sessionsCards .packageCard {
        display: flex;
        justify-content: center;
        align-items: start;
        flex-direction: column;
        border: 1px solid gainsboro;
        border-radius: 5px;
        padding: 5px 10px;
        box-shadow: 0 .125rem .25rem rgba(0, 0, 0, .075) !important;
        transition: transform ease .3s;
        backface-visibility: hidden;
        transform: translateZ(0);
        -webkit-font-smoothing: subpixel-antialiased;

    }

    #sessionsCards .packageCard:hover {
        transform: scale(1.01);
        transform-origin: bottom;
        backface-visibility: hidden;
        -webkit-font-smoothing: subpixel-antialiased;
    }

    #sessionsCards .packageHeader {
        padding: 10px 15px 5px 15px;
        width: 100%;
        text-align: start;
        position: relative;
    }

    #sessionsCards .packageBody {
        display: flex;
        gap: 4px;
        justify-content: space-between;
        align-items: start;
        padding: 0px 15px 10px 15px;
        width: 100%;
        flex-wrap: wrap;
    }

    #sessionsCards small {
        color: #6c757d;
    }

    #sessionsCards span.packagePrice {
        font-weight: 500;
        font-size: 14px;
        border: 1px solid var(--bg-secondary);
        border-radius: 10px;
        /* height: 45px; */
        /* width: 45px; */
        position: absolute;
        display: inline-flex;
        justify-content: center;
        align-items: center;
        margin: 5px;
        background-color: var(--bg-secondary);
        position: relative;
        /* right: -20px; */
        /* top: -20px; */
        padding: 0px 5px;
    }

    #sessionsCards .packageImg {}

    #sessionsCards .packageText {
        text-align: start;
        overflow: hidden;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        /* Limit to 3 lines */
        -webkit-box-orient: vertical;
    }

    #sessionsCards .show-more {
        color: #020202;
        cursor: pointer;
        display: inline-block;
        font-size: 13px;
        text-decoration: none;
        font-weight: 500;
        text-align: start;
        position: relative;
        padding-right: 20px;
        font-weight: 500;
        /* Space for the chevron */
    }

    #sessionsCards .show-more::after {
        content: '\F282';
        font-family: 'bootstrap-icons';
        /* Chevron character */
        position: absolute;
        right: 0;
        top: 50%;
        transform: translateY(-50%) rotate(0deg);
        /* Initial position */
        transition: transform 0.3s ease;
        /* Smooth animation */
    }

    #sessionsCards .show-more.expanded::after {
        transform: translateY(-50%) rotate(180deg);
        /* Upside down */
    }

    #sessionsCards .packageText.expanded {
        -webkit-line-clamp: unset;
        /* Remove line clamp */
        overflow: visible;
    }

    .btn-group-sm>.btn,
    .btn-sm {
        padding: .25rem .5rem !important;
        font-size: .875rem !important;
        border-radius: .2rem !important;
    }

    #sessionsCards .packageCta {}

    #sessionsCards .twrap {
        text-align: left;
    }


    /* #sessionModal .modal-body {
        max-height: 490px;
        overflow: auto;
    } */

    #sessionModal .modal-body {
        max-height: 58vh;
        /* Set a maximum height for the modal body */
        overflow-y: auto;
        /* Enable vertical scrolling if content overflows */
        overflow-x: hidden;
        /* Prevent horizontal scrolling */
    }

    #sessionModal .modal-body::-webkit-scrollbar {
        width: 6px;
    }

    #sessionModal .modal-body::-webkit-scrollbar-track {
        background: #f1f1f1;
    }

    #sessionModal .modal-body::-webkit-scrollbar-thumb {
        background: #6c757d;
        border-top-left-radius: 0px;
        border-top-right-radius: 0px;
        border-bottom-left-radius: 3px;
        border-bottom-right-radius: 3px;
    }

    #sessionModal .modal-body::-webkit-scrollbar-thumb:hover {
        background: #555;
        border-top-left-radius: 0px;
        border-top-right-radius: 0px;
        border-bottom-left-radius: 3px;
        border-bottom-right-radius: 3px;
    }

    /* Modal Styles */
    /* #sessionModal .modal-body {
        max-height: 490px;
        overflow: auto;
        position: relative;
    } */

    #sessionModal .modal-body .step {
        display: none;
        width: 100%;
        height: 100%;
    }

    #sessionModal .modal-body .step.active {
        display: block;
        animation: fadeIn 0.3s;
    }

    #sessionModal .custom-control-input:checked~.custom-control-label::before {
        color: #fff;
        border-color: #7B1FA2;
        background-color: red;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
        }

        to {
            opacity: 1;
        }
    }

    /* Progress Bar Styles */
    .progressbar {
        display: flex;
        margin-bottom: 40px;
        margin-top: 40px;
        counter-reset: step;
        justify-content: space-evenly;
        align-items: center;
        gap: 3px;
        padding: 0px;
    }


    .progressbar li {
        list-style-type: none;
        /* width: 25%; */
        position: relative;
        text-align: center;
        font-size: 14px;
        font-weight: 500;
        color: #6c757d;
    }

    .progressbar li:before {
        content: counter(step);
        counter-increment: step;
        width: 30px;
        height: 30px;
        line-height: 30px;
        border: 2px solid #6c757d;
        display: flex;
        text-align: center;
        margin: 0 auto 10px auto;
        border-radius: 50%;
        background-color: #fff;
        justify-content: center;
        align-items: center;
    }

    .progressbar li:after {
        /* content: ''; */
        position: absolute;
        width: 100%;
        height: 2px;
        background-color: #6c757d;
        top: 15px;
        left: -50%;
        z-index: -1;
    }

    .progressbar li:first-child:after {
        content: none;
    }

    .progressbar li.active {
        color: var(--text-color);
        transform: scale(1.2);
        transition: all ease .6s;
    }

    .progressbar li.active:before {
        border-color: #fee7dd;
    }

    .progressbar li.active+li:after {
        background-color: #007bff;
    }


    .progressbar li:not(.active) {
        filter: blur(1px);
    }


    /* Button Styles */
    .modal-footer .btn-main {
        min-width: 100px;
    }

    /* Validation Styles */
    .error {
        color: red;
        font-size: 13px;
        margin-top: 5px;
    }

    /* Style for the selected package */
    input[name="package"]:checked+label {
        background-color: var(--bg-color-success);
        border-color: var(--bg-color-success);
        /* box-shadow: 0 0 10px rgb(0 0 0 / 50%); */
        transition: background-color 0.3s ease, box-shadow 0.3s ease;
    }

    /* Adjusting the entire card when the radio input is checked */
    input[name="package"]:checked+label #edietPackages .packageCard {
        background-color: var(--bg-color-success) !important;
        border-color: var(--bg-color-success);
        box-shadow: 0 0 10px rgba(0, 123, 255, 0.5);
        transition: all 0.3s ease-in-out;
    }

    #questionareList .stitle {
        text-transform: capitalize;
    }

    #questionareList label.form-label {
        font-weight: 500;
    }
</style>


<!-- Modal -->
<div class="modal fade" id="sessionModal" tabindex="-1" aria-labelledby="sessionModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-xl modal-fullscreen-lg-down">
        <div class="modal-content" style="overflow:auto;">
            <form id="eSessions" novalidate>
                <div class="modal-header">
                    <h5 class="modal-title" id="sessionModalLabel">Ραντεβού</h5>
                    <button type="button" class="btn-close close-modal" data-bs-dismiss="modal" aria-label="Κλείσιμο"></button>
                </div>
                <div class="bodyhead sticky-top">
                    <!-- Progress Bar -->
                    <ul class="progressbar">
                        <li class="active">Τύπος</li>
                        <li>Στοιχεία</li>
                        <li>Ημερομηνία</li>
                        <li>Ολοκλήρωση</li>
                    </ul>

                    <!-- General Error Message -->
                    <div class="alert alert-danger d-none" id="generalError">Παρακαλώ συμπληρώστε όλα τα απαιτούμενα πεδία.</div>
                </div>
                <div class="modal-body">

                    <!-- Step 1: Package Selection -->
                    <div class="step active" data-step="1">
                        <div class="row" id="sessionsCards">
                            <!-- Online Συνεδρίες -->

                            <!-- Add more cards as needed following this pattern -->
                        </div>
                    </div>

                    <!-- Step 2: Personal Information -->
                    <div class="step" data-step="2">
                        <!-- Form fields with required attributes -->
                        <div class="mb-3">
                            <label for="firstName" class="form-label">Όνομα *</label>
                            <input type="text" class="form-control" id="firstName" name="firstName" required>
                        </div>
                        <div class="mb-3">
                            <label for="lastName" class="form-label">Επώνυμο *</label>
                            <input type="text" class="form-control" id="lastName" name="lastName" required>
                        </div>
                        <label for="phoneNumber" class="form-label">Τηλέφωνο *</label>
                        <div class="mb-3 d-flex gap-1">
                            <input type="text" class="form-control" id="phoneNumber" name="phoneNumber" required>
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Email *</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                        <!-- ... Other fields ... -->
                    </div>

                    <div class="step" data-step="3">
                        <div class="container border rounded">
                            <!-- Navigation Bar with Month/Dates and Prev/Next Buttons -->
                            <div class="navbar navbar-expand-lg">
                                <div class="container-fluid justify-content-center align-items-center p-0 gap-1">
                                    <button type="button" class="btn btn-outline order-2 order-lg-1" id="prevWeekBtn">
                                        <i class="fas fa-chevron-left"></i>
                                    </button>
                                    <div class="d-flex gap-1 mb-2 order-1 order-lg-2" id="dateNav">
                                        <!-- Dates will be injected here -->
                                    </div>

                                    <button type="button" class="btn btn-outline order-3 order-lg-3" id="nextWeekBtn">
                                        <i class="fas fa-chevron-right"></i>
                                    </button>
                                    <button type="button" class="btn btn-secondary btn-sm order-2 order-lg-4" id="todayBtn">Σήμερα</button><button type="button" class="btn btn-secondary btn-sm ms-auto order-4" id="clearBtn">Καθαρισμός</button>

                                </div>

                            </div>

                            <!-- "Today", "Clear Selection", and Selected Slot Label -->
                            <div class="container mt-2">

                                <div id="selectedSlotLabelContainer" style="display: none;">
                                    <span class="text-dark font-xs" id="selectedSlotLabel" style="cursor: pointer;">
                                        <!-- Selected slot info will be displayed here -->
                                    </span>
                                </div>
                            </div>

                            <!-- Time Slots Containers -->
                            <div class="container mt-4">
                                <h4 class="sstitle">Πρωί</h4>
                                <div class="row" id="morningSlots">
                                    <!-- Morning slots will be injected here -->
                                </div>

                                <h4 class="mt-5 sstitle">Απόγευμα</h4>
                                <div class="row" id="afternoonSlots">
                                    <!-- Afternoon slots will be injected here -->
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Step 4: Completion -->
                    <div class="step" data-step="4">
                        <div id="confirmInfo" class="px-2 py-4 px-md-4 py-md-4 bg-light rounded shadow-sm">
                            <h2 class="mb-4 text-center text-success">Επιβεβαίωση</h2>
                            <p class="text-center text-muted">Παρακαλώ επιβεβαιώστε τα στοιχεία σας πριν την υποβολή.</p>

                            <div class="card border-0">
                                <div class="card-body">
                                    <ul class="list-group list-group-flush">
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            <strong>Πακέτο:</strong> <span id="confirmPackage" class="text-end"></span>
                                        </li>
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            <strong>Ημερομηνία:</strong> <span id="confirmDate" class="text-end"></span>
                                        </li>
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            <strong>Ονοματεπώνυμο:</strong> <span id="confirmName" class="text-end"></span>
                                        </li>
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            <strong>Email:</strong> <span id="confirmEmail" class="text-end"></span>
                                        </li>
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            <strong>Τηλέφωνο:</strong> <span id="confirmPhone" class="text-end"></span>
                                        </li>
                                    </ul>

                                    <hr class="my-3">
                                    <div id="pay-form" class="mx-auto w-100" disabled></div>

                                    <p class="fw-bold text-end mb-1"><i class="bi bi-wallet2"></i> Ποσό Πληρωμής</p>
                                    <p id="confirmPrice" class="text-end fs-4 text-primary fw-bold"></p>
                                    <div class="form-check mt-3">
                                        <input class="form-check-input" type="checkbox" value="" id="terms">
                                        <label class="form-check-label" for="terms">
                                            Συμφωνώ με τους <a href="#" id="openTerms" data-bs-toggle="modal" data-bs-target="#termsModal">όρους και προϋποθέσεις</a>.
                                        </label>
                                    </div>
                                </div>
                            </div>

                        </div>





                        <style>
                            #completion {
                                width: 250px;
                                /* height: 280px; */
                                margin: auto;
                                display: block;
                            }

                            @keyframes hideshow {
                                0% {
                                    opacity: 0.2;
                                }

                                10% {
                                    opacity: 0.2;
                                }

                                15% {
                                    opacity: 0.2;
                                }

                                100% {
                                    opacity: 1;
                                }
                            }

                            #cirkel {
                                animation: hideshow 0.4s ease;
                            }

                            #check {
                                animation: hideshow 0.4s ease;
                            }

                            #stars {
                                animation: hideshow 1.0s ease;
                                opacity: 0.9;
                            }


                            @keyframes hideshow {
                                0% {
                                    transform: scale(0.2);
                                    transform-origin: initial;

                                }

                                100% {
                                    transform: scale(1.0);
                                    transform-origin: initial;
                                }
                            }

                            @keyframes draaien {
                                0% {
                                    transform: rotate(40deg);
                                    transform-origin: initial;

                                }

                                100% {
                                    transform: scale(0deg);
                                    transform-origin: initial;
                                }
                            }

                            #check {
                                animation: draaien 0.8s ease;
                            }


                            @keyframes transparant {
                                0% {
                                    opacity: 0;

                                }

                                100% {
                                    opacity: 1;
                                }
                            }

                            #check {
                                animation: transparant 2s;
                            }
                        </style>
                        <div class="p-2 px-md-5 my-md-2 d-none" id="submitSuccess">



                            <div class="alert p-4 text-center" role="alert" id="successMessage">
                                <h4 class="alert-heading">Η Κράτησή Σας Ολοκληρώθηκε!</h4>
                                <p class="mt-3">Σας ευχαριστούμε για την κράτησή σας. Η πληρωμή σας επιβεβαιώθηκε με επιτυχία.</p>
                                <p>Σας έχουμε αποστείλει ένα email με όλες τις λεπτομέρειες της κράτησής σας και περαιτέρω οδηγίες.</p>

                                <svg id="completion" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 96 101">
                                    <style>
                                        .st0 {
                                            fill: #51BBA0;
                                            fill-opacity: 0.4;
                                        }

                                        .st1 {
                                            fill: #51BBA0;
                                            fill-opacity: 0.1;
                                        }

                                        .st2 {
                                            fill: #51BBA0;
                                        }
                                    </style>
                                    <title>
                                        C1DBEBC0-CF7A-42D3-B615-1AB5DE73C3E9
                                    </title>
                                    <g id="configurator">
                                        <g id="configurator_completion">
                                            <g id="stars">
                                                <circle id="Oval" class="st0" cx="14" cy="18" r="1" />
                                                <circle id="Oval-Copy-4" class="st0" cx="27" cy="20" r="1" />
                                                <circle id="Oval-Copy-10" class="st0" cx="76" cy="20" r="1" />
                                                <circle id="Oval-Copy-2" class="st0" cx="61.5" cy="12.5" r="1.5" />
                                                <circle id="Oval-Copy-9" class="st0" cx="94" cy="53" r="1" />
                                                <circle id="Oval-Copy-6" class="st0" cx="88" cy="14" r="1" />
                                                <circle id="Oval-Copy-7" class="st0" cx="59" cy="1" r="1" />
                                                <circle id="Oval_1_" class="st0" cx="43" cy="9" r="2" />
                                                <path id="ster-01" class="st0" d="M28.5 3.8L26 6l2.2-2.5L26 1l2.5 2.2L31 1l-2.2 2.5L31 6z" />
                                                <path id="ster-01" class="st0" d="M3.5 50.9l-2.1 2.4 1.7-2.7-2.9-1.2 3.1.8.2-3.2.2 3.2 3.1-.8-2.9 1.2 1.7 2.7z" />
                                                <path id="ster-01" class="st0" d="M93.5 27.8L91 30l2.2-2.5L91 25l2.5 2.2L96 25l-2.2 2.5L96 30z" />
                                                <circle id="Oval-Copy-5" class="st0" cx="91" cy="40" r="2" />
                                                <circle id="Oval-Copy-3" class="st0" cx="7" cy="36" r="2" />
                                                <circle id="Oval-Copy-8" class="st0" cx="7.5" cy="5.5" r=".5" />
                                            </g>
                                        </g>
                                    </g>
                                    <g id="cirkel">
                                        <g id="Mask">
                                            <path id="path-1_1_" class="st1" d="M49 21c22.1 0 40 17.9 40 40s-17.9 40-40 40S9 83.1 9 61s17.9-40 40-40z" />
                                        </g>
                                    </g>
                                    <path id="check" class="st2" d="M31.3 64.3c-1.2-1.5-3.4-1.9-4.9-.7-1.5 1.2-1.9 3.4-.7 4.9l7.8 10.4c1.3 1.7 3.8 1.9 5.3.4L71.1 47c1.4-1.4 1.4-3.6 0-5s-3.6-1.4-5 0L36.7 71.5l-5.4-7.2z" />
                                </svg>

                                <hr class="mt-3">

                                <p class="mt-4 mb-0">Για οποιαδήποτε απορία ή διευκρίνιση, μη διστάσετε να επικοινωνήσετε μαζί μας.</p>
                                <p class="fw-bold">Σας περιμένουμε!</p>

                                <a href="/" class="mx-auto text-center">
                                    <button type="button" class="btn btn-main mt-3"><i class="bi bi-house-door-fill"></i> Επιστροφή στην Αρχική</button>
                                </a>

                            </div>

                        </div>

                        <div class="p-5 d-none" id="submitFailed">
                            <div class="alert alert-danger shadow-sm p-4 rounded text-center" role="alert">
                                <h4 class="alert-heading"><i class="bi bi-exclamation-triangle-fill"></i> Κάτι Πήγε Στραβά!</h4>
                                <p class="mt-3" id="failedMessage"></p>
                                <p class="mt-3">Δυστυχώς, η κράτησή σας δεν ολοκληρώθηκε λόγω σφάλματος.</p>
                                <p>Παρακαλώ δοκιμάστε ξανά. Αν το πρόβλημα συνεχιστεί, επικοινωνήστε μαζί μας για βοήθεια.</p>

                                <hr>

                                <div class="row justify-content-center">
                                    <div class="col-md-4 text-center">
                                        <p><strong>Email Υποστήριξης:</strong> <a href="mailto:info@almapsychology.gr" class="text-decoration-none">info@almapsychology.gr</a></p>
                                        <p><strong>Τηλέφωνο:</strong> <a href="tel: (+30) 2695 027036" class="text-decoration-none"> (+30) 2695 027036</a></p>
                                    </div>
                                </div>

                                <a href="/appointments" class="mx-auto text-center">
                                    <button type="button" class="btn btn-optional bg-danger mt-3"><i class="bi bi-arrow-clockwise"></i> Προσπαθήστε Ξανά</button>
                                </a>

                            </div>
                        </div>

                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary prev-btn" style="display: none;"><i class="bi bi-arrow-left-short"></i> Προηγούμενο</button>
                    <button type="button" class="btn btn-main btn-sec next-btn">Επόμενο <i class="bi bi-arrow-right-short"></i></button>
                    <!-- <button type="submit" class="btn btn-main submit-btn" style="display: none;">Υποβολή</button> -->
                    <button type="button" id="payNowBtn" style="display: none;" class="btn-main mt-3 submit-btn">
                        Πληρωμή
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>




<div class="modal fade nested-modal" id="termsModal" tabindex="-1" role="dialog" aria-labelledby="termsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="termsModalLabel">Όροι και Προϋποθέσεις</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Κλείσιμο"></button>
            </div>
            <div class="modal-body">
                <div class="container-lg p-2">
                    <?= TRS('privacy_policy_text') ?>

                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Κλείσιμο</button>
            </div>
        </div>
    </div>
</div>

<style>
    /* Higher z-index for nested modal */
    .modal.nested-modal {
        z-index: 1065;
    }

    .modal-backdrop.nested-backdrop {
        z-index: 1064;
    }

    .Subtitle {
        color: #181818;
    }
</style>



<?php
function hook_end_scripts()
{
?>

    <script src="https://cdn.jsdelivr.net/npm/google-libphonenumber@3.2.13/dist/libphonenumber.js"></script>

    <script src="<?php echo $GLOBALS['config']['base_url']; ?>assets/js/pages/apn2d.js"></script>

<?php
}
?>
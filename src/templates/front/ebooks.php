<?php
if (!isset($GLOBAL_INCLUDE_CHECK)) die(header('location:  /'));
?>

<section class="hero-section py-5">
    <div class="container-xl">
        <div class="row align-items-center g-5">
            <div class="col-12 col-lg-6 order-2 order-lg-1">
                <h1 class="display-4 mb-4 mstitle">Ανακαλύψτε τα <br><span style="color: var(--primary-color);">e-Books & Guides</span></h1>
                <p class="mainp mb-4">
                    Ξεκλειδώστε τα μυστικά της ισορροπημένης διατροφής.
                    Κατεβάστε εξειδικευμένους οδηγούς και eBooks άμεσα στον υπολογιστή ή το κινητό σας.
                </p>
                <a href="#ebooksSection" class="btn btn-outline-dark rounded-pill px-4">Δείτε τη συλλογή <i class="bi bi-arrow-down"></i></a>
            </div>
            <div class="col-12 col-lg-6 order-1 order-lg-2 text-center">
                <div class="herobg" style="background-image: url('/assets/images/ebook_desing.svg') !important;"></div>
            </div>
        </div>
    </div>
</section>

<section class="mbase py-5" id="ebooksSection" style="background-color: #fff;">
    <div class="container">
        <div class="text-center mb-5">
            <h6 class="text-uppercase text-muted fw-bold mb-2" style="letter-spacing: 2px; font-size: 0.8rem;">Η ΒΙΒΛΙΟΘΗΚΗ ΜΑΣ</h6>
            <h2 class="sstitle fw-bold">Διαθέσιμα eBooks</h2>
            <div style="width: 60px; height: 3px; background: var(--primary-color); margin: 15px auto;"></div>
        </div>

        <div class="row justify-content-center g-4" id="ebooksList">
            <div class="col-12 text-center text-muted py-5">
                <div class="spinner-border text-primary" role="status"></div>
                <div class="mt-2">Φόρτωση βιβλίων...</div>
            </div>
        </div>
    </div>
</section>

<style>
    :root {
        --primary-color: #51BBA0;
        --text-dark: #2c3e50;
        --text-muted: #6c757d;
        --bg-light: #f8f9fa;
    }

    .hero-section {
        background: linear-gradient(135deg, #fdfbfb 0%, #ebedee 100%);
        min-height: 400px;
        display: flex;
        align-items: center;
        position: relative;
        overflow: hidden;
    }

    .herobg {
        width: 100%;
        height: 350px;
        background-repeat: no-repeat;
        background-position: center;
        background-size: contain;
        animation: float 6s ease-in-out infinite;
    }

    @keyframes float {
        0% {
            transform: translateY(0px);
        }

        50% {
            transform: translateY(-10px);
        }

        100% {
            transform: translateY(0px);
        }
    }

    .mstitle {
        font-weight: 800;
        color: var(--text-dark);
        letter-spacing: -0.5px;
    }

    .mainp {
        font-size: 1.1rem;
        color: var(--text-muted);
        line-height: 1.6;
    }

    /* Κάρτα Προϊόντος */
    .ebook-card {
        background: var(--card-bg);
        border: none;
        border-radius: 20px;
        overflow: hidden;
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.05);
        height: 100%;
        display: flex;
        flex-direction: column;
    }

    .ebook-card:hover {
        transform: translateY(var(--hover-lift));
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.12);
    }

    .card-img-wrapper {
        background: #f8f9fa;
        height: 260px;
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
        padding: 30px;
    }

    .card-img-wrapper::before {
        content: '';
        position: absolute;
        width: 160px;
        height: 160px;
        background: rgba(81, 187, 160, 0.1);
        border-radius: 50%;
        z-index: 0;
        transition: transform 0.4s ease;
    }

    .ebook-card:hover .card-img-wrapper::before {
        transform: scale(1.2);
    }

    .card-img-top {
        max-height: 100%;
        width: auto;
        object-fit: contain;
        position: relative;
        z-index: 1;
        filter: drop-shadow(5px 5px 10px rgba(0, 0, 0, 0.2));
        transition: transform 0.4s ease;
    }

    .ebook-card:hover .card-img-top {
        transform: scale(1.08) rotate(2deg);
    }

    .card-body-custom {
        padding: 25px;
        display: flex;
        flex-direction: column;
        flex-grow: 1;
        background: #fff;
    }

    .card-title {
        font-weight: 700;
        color: #2c3e50;
        font-size: 1.15rem;
        margin-bottom: 10px;
        line-height: 1.4;
    }

    .price-tag {
        font-size: 1.25rem;
        font-weight: 800;
        color: var(--primary-color);
    }

    .btn-card-action {
        width: auto;
        height: 42px;
        padding: 0 20px;
        border-radius: 50px;
        background-color: #fff;
        color: var(--primary-color);
        border: 1px solid var(--primary-color);
        display: flex;
        align-items: center;
        gap: 8px;
        font-weight: 600;
        font-size: 0.95rem;
        transition: all 0.3s ease;
        cursor: pointer;
    }

    .ebook-card:hover .btn-card-action {
        background-color: var(--primary-color);
        color: white;
        transform: translateY(-3px);
        box-shadow: 0 5px 15px rgba(81, 187, 160, 0.3);
    }

    .btn-text-action {
        font-size: 0.9rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #888;
        transition: 0.3s;
    }

    .ebook-card:hover .btn-text-action {
        color: var(--primary-color);
    }
</style>

<style>
    #ebookModal .modal-body {
        max-height: 58vh;
        overflow-y: auto;
        overflow-x: hidden;
    }

    #ebookModal .modal-body::-webkit-scrollbar {
        width: 6px;
    }

    #ebookModal .modal-body::-webkit-scrollbar-track {
        background: #f1f1f1;
    }

    #ebookModal .modal-body::-webkit-scrollbar-thumb {
        background: #6c757d;
        border-radius: 3px;
    }

    #ebookModal .modal-body::-webkit-scrollbar-thumb:hover {
        background: #555;
    }

    #ebookModal .modal-body .step {
        display: none;
        width: 100%;
        height: 100%;
    }

    #ebookModal .modal-body .step.active {
        display: block;
        animation: fadeIn 0.3s;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
        }

        to {
            opacity: 1;
        }
    }

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
        position: relative;
        text-align: center;
        font-size: 14px;
        font-weight: 500;
        color: #6c757d;
        width: 50%;
        /* Adjusted for 2 visible steps */
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

    .modal-footer .btn-main {
        min-width: 100px;
    }

    .error {
        color: red;
        font-size: 13px;
        margin-top: 5px;
    }

    /* Success Animation */
    #completion {
        width: 250px;
        margin: auto;
        display: block;
    }

    @keyframes hideshow {

        0%,
        10%,
        15% {
            opacity: 0.2;
        }

        100% {
            opacity: 1;
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

    @keyframes transparant {
        0% {
            opacity: 0;
        }

        100% {
            opacity: 1;
        }
    }

    #cirkel,
    #check {
        animation: hideshow 0.4s ease;
    }

    #stars {
        animation: hideshow 1.0s ease;
        opacity: 0.9;
    }

    #check {
        animation: draaien 0.8s ease;
        animation: transparant 2s;
    }
</style>

<div class="modal fade" id="ebookModal" tabindex="-1" aria-labelledby="ebookModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-xl modal-fullscreen-lg-down">
        <div class="modal-content" style="overflow:auto;">
            <form id="ebookForm" novalidate>
                <div class="modal-header">
                    <h5 class="modal-title" id="ebookModalLabel">Αγορά eBook</h5>
                    <button type="button" class="btn-close close-modal" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="bodyhead sticky-top bg-white">
                    <ul class="progressbar">
                        <li class="active">Στοιχεία</li>
                        <li>Πληρωμή</li>
                    </ul>
                    <div class="alert alert-danger d-none" id="generalError">Παρακαλώ διορθώστε τα σφάλματα πριν προχωρήσετε.</div>
                </div>

                <div class="modal-body">

                    <div class="step active" data-step="1">
                        <div class="alert alert-light border shadow-sm mb-4 d-flex justify-content-between align-items-center">
                            <div>
                                <small class="text-muted d-block">Επιλεγμένο eBook:</small>
                                <strong id="selectedTitle" class="text-primary fs-5"></strong>
                            </div>
                            <div class="text-end">
                                <span id="selectedPrice" class="badge bg-info fs-6"></span>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="firstName" class="form-label">Όνομα *</label>
                            <input type="text" class="form-control" id="firstName" name="firstName" required>
                        </div>
                        <div class="mb-3">
                            <label for="lastName" class="form-label">Επώνυμο *</label>
                            <input type="text" class="form-control" id="lastName" name="lastName" required>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email (για αποστολή αρχείου) *</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                        <label for="phoneNumber" class="form-label">Τηλέφωνο *</label>
                        <div class="mb-3 d-flex gap-1">
                            <input type="text" class="form-control" id="phoneNumber" name="phoneNumber" required>
                        </div>
                    </div>

                    <div class="step" data-step="2">
                        <div id="confirmInfo" class="px-2 py-4 px-md-4 py-md-4 bg-light rounded shadow-sm">
                            <h2 class="mb-4 text-center text-success">Επιβεβαίωση & Πληρωμή</h2>
                            <p class="text-center text-muted">Η πληρωμή γίνεται με ασφάλεια μέσω EveryPay.</p>

                            <div class="card border-0">
                                <div class="card-body">

                                    <div id="pay-form" class="mx-auto w-100"></div>

                                    <div class="form-check mt-3">
                                        <input class="form-check-input" type="checkbox" value="" id="terms">
                                        <label class="form-check-label" for="terms">
                                            Συμφωνώ με τους <a href="#" id="openTerms" data-bs-toggle="modal" data-bs-target="#termsModal">όρους και προϋποθέσεις</a>.
                                        </label>
                                    </div>
                                    <div class="text-end">
                                        <span id="selectedPrice2" class="badge bg-info fs-6"></span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="p-2 px-md-5 my-md-2 d-none" id="submitSuccess">
                            <div class="alert p-4 text-center" role="alert" id="successMessage">
                                <h4 class="alert-heading">Η Αγορά Ολοκληρώθηκε!</h4>
                                <p class="mt-3">Η πληρωμή σας επιβεβαιώθηκε. Ελέγξτε το email σας για το αρχείο.</p>

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
                                    <g id="configurator">
                                        <g id="configurator_completion">
                                            <g id="stars">
                                                <circle class="st0" cx="14" cy="18" r="1" />
                                                <circle class="st0" cx="27" cy="20" r="1" />
                                                <circle class="st0" cx="76" cy="20" r="1" />
                                                <circle class="st0" cx="94" cy="53" r="1" />
                                                <path class="st0" d="M28.5 3.8L26 6l2.2-2.5L26 1l2.5 2.2L31 1l-2.2 2.5L31 6z" />
                                            </g>
                                        </g>
                                    </g>
                                    <path id="check" class="st2" d="M31.3 64.3c-1.2-1.5-3.4-1.9-4.9-.7-1.5 1.2-1.9 3.4-.7 4.9l7.8 10.4c1.3 1.7 3.8 1.9 5.3.4L71.1 47c1.4-1.4 1.4-3.6 0-5s-3.6-1.4-5 0L36.7 71.5l-5.4-7.2z" />
                                </svg>

                                <button type="button" class="btn btn-main mt-3" onclick="window.location.reload()">Κλείσιμο</button>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary prev-btn" style="display: none;"><i class="bi bi-arrow-left-short"></i> Προηγούμενο</button>
                    <button type="button" class="btn btn-main btn-sec next-btn">Επόμενο <i class="bi bi-arrow-right-short"></i></button>
                    <button type="button" id="payNowBtn" style="display: none;" class="btn-main mt-3 submit-btn">
                        Πληρωμή
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php
function hook_end_scripts()
{
?>
    <script src="https://cdn.jsdelivr.net/npm/google-libphonenumber@3.2.13/dist/libphonenumber.js"></script>
    <script src="/assets/js/pages/ebooks_front.js"></script>
<?php
}
?>
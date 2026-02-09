<section class="contact-premium-section">
    <div class="container">
        <div class="row g-0 shadow-sm rounded-5 overflow-hidden border-soft">

            <div class="col-lg-5 contact-info-side p-5 d-flex flex-column justify-content-between position-relative">

                <div class="deco-blob"></div>

                <div class="position-relative z-2">
                    <span class="mini-tag text-orange mb-2 d-block">ΕΠΙΚΟΙΝΩΝΙΑ</span>
                    <h2 class="display-5 font-serif mb-4 text-dark">Ας μιλήσουμε.</h2>
                    <p class="text-muted lead">
                        Είμαστε εδώ για να λύσουμε κάθε απορία σας ή να προγραμματίσουμε την πρώτη μας συνεδρία.
                    </p>
                </div>

                <div class="booking-card mt-5 mb-5 position-relative z-2">
                    <h4 class="font-serif mb-2 text-dark">Νέα αρχή;</h4>
                    <p class="text-muted text-sm mb-3">
                        Δείτε τη διαθεσιμότητα και κλείστε το ραντεβού σας άμεσα online.
                    </p>
                    <a href="/services" class="btn-alma-dark w-100 justify-content-between">
                        <span>Κλείστε Ραντεβού</span>
                        <i class="bi bi-arrow-right"></i>
                    </a>
                </div>

                <div class="contact-details position-relative z-2 mt-auto">
                    <div class="d-flex align-items-start mb-4">
                        <div class="icon-box me-3"><i class="bi bi-geo-alt"></i></div>
                        <div>
                            <h6 class="text-uppercase text-muted text-xs fw-bold ls-1">ΔΙΕΥΘΥΝΣΗ</h6>
                            <p class="mb-0 text-dark fw-medium"><?= $mainSettings->companySettings->physicalAddress ?></p>
                            <a href="<?= $mainSettings->companySettings->mapUrl ?>" target="_blank" class="text-orange text-sm text-decoration-underline">Προβολή στο χάρτη</a>
                        </div>
                    </div>

                    <div class="d-flex align-items-start mb-4">
                        <div class="icon-box me-3"><i class="bi bi-envelope"></i></div>
                        <div>
                            <h6 class="text-uppercase text-muted text-xs fw-bold ls-1">EMAIL</h6>
                            <a href="mailto:<?= $mainSettings->companySettings->contactEmail ?>" class="text-dark text-decoration-none big-link">
                                <?= $mainSettings->companySettings->contactEmail ?>
                            </a>
                        </div>
                    </div>

                    <div class="d-flex align-items-start">
                        <div class="icon-box me-3"><i class="bi bi-telephone"></i></div>
                        <div>
                            <h6 class="text-uppercase text-muted text-xs fw-bold ls-1">ΤΗΛΕΦΩΝΟ</h6>
                            <a href="tel:<?= $mainSettings->companySettings->contactPhoneNumber ?>" class="text-dark text-decoration-none big-link">
                                <?= $mainSettings->companySettings->contactPhoneNumber ?>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-7 bg-white p-5">
                <div class="form-header mb-5">
                    <h3 class="font-serif text-dark">Στείλτε μας μήνυμα</h3>
                    <div class="divider-line"></div>
                </div>

                <form id="submitMessage" autocomplete="on" novalidate class="alma-premium-form">

                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <div class="form-group-material">
                                <label for="fullName" class="form-label">Ονοματεπώνυμο *</label>
                                <input type="text" class="form-control form-control-line" id="fullName" name="fullName" placeholder=" " required>
                            </div>
                        </div>
                        <div class="col-md-6 mb-4">
                            <div class="form-group-material">
                                <label for="email" class="form-label">Email *</label>
                                <input type="email" class="form-control form-control-line" id="email" name="email" placeholder=" " required>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <div class="form-group-material">
                                <label for="phone" class="form-label">Τηλέφωνο *</label>
                                <input type="tel" class="form-control form-control-line" id="phone" name="phone" placeholder=" " required>
                            </div>
                        </div>
                        <div class="col-md-6 mb-4">
                            <div class="form-group-material">
                                <label for="mailSubject" class="form-label">Θέμα *</label>
                                <input type="text" class="form-control form-control-line" id="mailSubject" name="mailSubject" placeholder=" " required>
                            </div>
                        </div>
                    </div>

                    <div class="mb-5">
                        <div class="form-group-material">
                            <label for="message" class="form-label">Μήνυμα *</label>
                            <textarea class="form-control form-control-line" id="message" name="message" rows="4" placeholder=" " required></textarea>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                        <p class="text-muted text-xs mb-0">* Υποχρεωτικά πεδία</p>
                        <button type="submit" class="btn-alma-solid btn-lg px-5">
                            Αποστολή <i class="bi bi-send ms-2"></i>
                        </button>
                    </div>

                    <div id="contactMessage" class="mt-4"></div>
                </form>
            </div>

        </div>
    </div>
</section>

<style>
    /* ========================
   PREMIUM CONTACT SECTION (WARM)
   ======================== */
    .contact-premium-section {
        padding: 160px 0;
        background-color: #fff;
    }

    .border-soft {
        border: 1px solid rgba(0, 0, 0, 0.05);
    }

    /* --- LEFT SIDE (WARM BEIGE) --- */
    .contact-info-side {
        background-color: #f9f7f2;
        /* Το "Editorial Beige" που ταιριάζει με το site */
        position: relative;
        overflow: hidden;
    }

    /* Subtle background decoration */
    .deco-blob {
        position: absolute;
        top: -50px;
        left: -50px;
        width: 300px;
        height: 300px;
        background: radial-gradient(circle, #fcece4 0%, rgba(255, 255, 255, 0) 70%);
        /* Πολύ απαλό πορτοκαλί tint */
        opacity: 0.6;
        border-radius: 50%;
        filter: blur(40px);
    }

    .font-serif {
        font-family: 'Playfair Display', serif;
    }

    .text-xs {
        font-size: 0.75rem;
    }

    .ls-1 {
        letter-spacing: 1px;
    }

    .text-orange {
        color: var(--alma-orange);
    }

    /* Booking Card (White on Beige) */
    .booking-card {
        background: #ffffff;
        padding: 30px;
        border-radius: 20px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.03);
        /* Soft shadow */
        border: 1px solid rgba(0, 0, 0, 0.02);
    }

    /* Button inside Booking Card */
    .btn-alma-dark {
        display: flex;
        align-items: center;
        background: #333;
        /* Dark Grey */
        color: #fff;
        padding: 12px 25px;
        border-radius: 50px;
        text-decoration: none;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .btn-alma-dark:hover {
        background: var(--alma-orange);
        color: #fff;
        transform: translateY(-2px);
    }

    /* Contact Links */
    .big-link {
        font-size: 1.1rem;
        font-weight: 600;
        transition: color 0.3s;
    }

    .big-link:hover {
        color: var(--alma-orange);
    }

    .icon-box {
        width: 40px;
        height: 40px;
        background: #fff;
        /* White icon circle */
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--alma-orange);
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.03);
    }

    /* --- RIGHT SIDE (FORM) --- */
    .divider-line {
        width: 60px;
        height: 3px;
        background-color: var(--alma-orange);
        margin-top: 15px;
    }

    /* Material Form Styling */
    .form-group-material {
        position: relative;
        padding-top: 10px;
    }

    .form-label {
        font-size: 0.8rem;
        font-weight: 700;
        color: #999;
        margin-bottom: 5px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    /* Input με γραμμή από κάτω */
    .form-control-line {
        border: none;
        border-bottom: 2px solid #eee;
        border-radius: 0;
        padding: 10px 0;
        background: transparent;
        font-size: 1.1rem;
        color: #333;
        transition: all 0.3s ease;
    }

    .form-control-line:focus {
        box-shadow: none;
        border-bottom-color: var(--alma-orange);
        background: transparent;
    }

    .form-control-line::placeholder {
        color: transparent;
    }

    /* Submit Button */
    .btn-alma-solid {
        background-color: var(--alma-orange);
        color: #fff;
        border: none;
        border-radius: 50px;
        padding: 12px 35px;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .btn-alma-solid:hover {
        background-color: #333;
        /* Hover to dark */
        transform: translateY(-2px);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
    }

    /* Responsive */
    @media (max-width: 991px) {
        .contact-premium-section {
            padding: 140px 0;
            background-color: #fff;
        }

        .contact-info-side {
            padding: 40px !important;
        }

        .col-lg-7 {
            padding: 30px !important;
        }
    }
</style>
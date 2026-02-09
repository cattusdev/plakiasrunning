<?php
if (!isset($GLOBAL_INCLUDE_CHECK)) die(header('location:  /'));
?>

<header class="hero-puzzle-section" id="home">
    <div class="container h-100">
        <div class="row h-100 align-items-center">

            <div class="col-lg-5 hero-text-area">

                <div class="label-wrapper fade-item">
                    <span class="line-dash d-none d-md-blocl"></span>
                    <span class="mini-tag">ΚΕΝΤΡΟ ΨΥΧΟΛΟΓΙΑΣ & ΑΝΑΠΤΥΞΗΣ</span>
                </div>

                <h1 class="hero-header fade-item">
                    Συνθέτουμε μαζί<br>
                    τα κομμάτια του<br>
                    <span class="highlight-stitch">Εαυτού σας.</span>
                </h1>

                <div class="hero-desc-wrapper fade-item">
                    <p class="hero-paragraph">
                        Όπως το "Άλμα" ενώνει το σώμα με το πνεύμα, έτσι κι εμείς βοηθάμε να ενώσετε σκέψεις και συναισθήματα. Μια διαδικασία επούλωσης, αποδοχής και επανασύνδεσης.
                    </p>
                </div>

                <div class="hero-action-btns fade-item">
                    <a href="/services" class="btn-alma-solid">
                        <span>Κλείστε Συνεδρία</span>
                    </a>
                    <a href="#about" class="btn-alma-outline">
                        <span>Η Ομάδα</span>
                        <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
            </div>

            <div class="col-lg-6 offset-lg-1 hero-visual-area">

                <canvas id="connectionCanvas"></canvas>

                <div class="assembly-composition">

                    <div class="shard-img-wrapper" id="shard1">
                        <img src="/assets/images/icons/1_.png" alt="Skevi" class="floating-img">
                        <span class="img-label">Σκέψη</span>
                    </div>

                    <div class="shard-img-wrapper" id="shard2">
                        <img src="/assets/images/icons/expression1.png" alt="Synaisthima" class="floating-img">
                        <span class="img-label">Συναίσθημα</span>
                    </div>

                    <div class="shard-img-wrapper" id="shard3">
                        <img src="/assets/images/icons/feelings.png" alt="Ekfrasi" class="floating-img">
                        <span class="img-label">Έκφραση</span>
                    </div>

                </div>
            </div>

        </div>
    </div>
</header>

<style>
    /* ========================
   HERO SECTION: COMPLETE & RESPONSIVE
   ======================== */

    .hero-puzzle-section {
        position: relative;
        width: 100%;
        min-height: 100vh;
        padding-top: 140px;
        overflow: hidden;
        display: flex;
        align-items: center;
    }

    /* --- LEFT COLUMN: EDITORIAL STYLE --- */
    .hero-text-area {
        position: relative;
        z-index: 5;
        padding-right: 40px;
        /* Λίγο αέρα δεξιά στο desktop */

        /* Αρχική κατάσταση για το JS animation */
        opacity: 0;
        transform: translateY(30px);
    }

    /* Label with Dash */
    .label-wrapper {
        display: flex;
        align-items: center;
        gap: 15px;
        margin-bottom: 25px;
    }

    .line-dash {
        width: 40px;
        height: 1px;
        background-color: var(--alma-orange);
    }

    .mini-tag {
        font-size: 0.85rem;
        color: var(--alma-nav-text);
        font-weight: 700;
        letter-spacing: 2px;
        text-transform: uppercase;
        margin: 0;
    }

    /* Heading */
    .hero-header {
        font-family: 'Playfair Display', serif;
        font-size: 3.6rem;
        line-height: 1.15;
        color: var(--alma-text);
        font-weight: 400;
        margin-bottom: 35px;
    }

    .highlight-stitch {
        color: var(--alma-nav-text);
        position: relative;
        white-space: nowrap;
        font-style: italic;
        padding-right: 5px;
    }

    .highlight-stitch::after {
        content: '';
        position: absolute;
        bottom: 8px;
        left: 0;
        width: 100%;
        height: 2px;
        background: repeating-linear-gradient(to right, var(--alma-orange) 0, var(--alma-orange) 5px, transparent 5px, transparent 10px);
    }

    /* Paragraph */
    .hero-desc-wrapper {
        border-left: 3px solid rgba(51, 76, 71, 0.1);
        padding-left: 25px;
        margin-bottom: 45px;
    }

    .hero-paragraph {
        font-size: 1.15rem;
        color: #555;
        line-height: 1.7;
        max-width: 480px;
        margin: 0;
        font-weight: 400;
    }

    /* Buttons */
    .hero-action-btns {
        display: flex;
        align-items: center;
        gap: 20px;
        flex-wrap: wrap;
    }

    .btn-alma-solid {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background-color: var(--alma-bg-button-main);
        color: #fff;
        padding: 16px 38px;
        border-radius: 50px;
        font-weight: 600;
        letter-spacing: 0.5px;
        transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
        box-shadow: 0 10px 25px rgba(51, 76, 71, 0.2);
    }

    .btn-alma-solid:hover {
        background-color: var(--alma-orange);
        transform: translateY(-3px);
        box-shadow: 0 15px 30px rgba(245, 134, 52, 0.25);
        color: #fff;
    }

    .btn-alma-outline {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        color: var(--alma-nav-text);
        font-weight: 600;
        font-size: 1rem;
        padding: 10px 20px;
        border-radius: 50px;
        transition: all 0.3s ease;
    }

    .btn-alma-outline i {
        font-size: 1.2rem;
        transition: transform 0.3s ease;
    }

    .btn-alma-outline:hover {
        color: var(--alma-orange);
        background-color: rgba(255, 255, 255, 0.5);
    }

    .btn-alma-outline:hover i {
        transform: translateX(5px);
    }

    /* --- RIGHT COLUMN (Visual) --- */
    .hero-visual-area {
        position: relative;
        height: 600px;
        display: flex;
        justify-content: center;
        align-items: center;
    }

    #connectionCanvas {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        z-index: 1;
        pointer-events: none;
    }

    .assembly-composition {
        position: relative;
        width: 100%;
        height: 100%;
        z-index: 2;
        pointer-events: none;
    }

    .shard-img-wrapper {
        position: absolute;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        z-index: 10;
        opacity: 0;
        transform: scale(0);
    }

    .floating-img {
        width: 190px;
        height: auto;
        filter: drop-shadow(0 15px 25px rgba(51, 76, 71, 0.15));
        transition: transform 0.3s ease;
    }

    .img-label {
        margin-top: 15px;
        font-family: 'Manrope', sans-serif;
        font-size: 0.85rem;
        font-weight: 700;
        color: var(--alma-nav-text);
        letter-spacing: 1px;
        text-transform: uppercase;
        background: rgba(255, 255, 255, 0.9);
        padding: 6px 16px;
        border-radius: 20px;
        backdrop-filter: blur(5px);
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
    }

    /* Positions Desktop */
    #shard1 {
        top: 60px;
        left: 20px;
    }

    #shard2 {
        bottom: 80px;
        left: 50%;
        margin-left: -65px;
    }

    #shard3 {
        top: 90px;
        right: 20px;
    }

    /* --- RESPONSIVE & MOBILE --- */
    @media (max-width: 991px) {
        .hero-puzzle-section {
            display: flex;
            flex-direction: column;
            /* Κάθετη στοίβαξη */
            height: auto;
            padding-top: 150px;
            padding-bottom: 60px;
            text-align: center;
            /* Κεντράρισμα */
        }

        /* Text Area Mobile Fixes */
        .hero-text-area {
            padding-right: 0;
            padding-left: 0;
            margin-bottom: 50px;
            width: 100%;
            max-width: 600px;
            /* Περιορισμός πλάτους */
            margin-left: auto;
            margin-right: auto;
            padding: 0 20px;
            /* Padding ασφαλείας */

            /* Reset transform for mobile initial state handled by JS */
            /* transform: translateY(20px); */
        }

        .label-wrapper {
            justify-content: center;
            /* Κέντρο label */
            margin-bottom: 20px;
        }

        .hero-header {
            font-size: 2.8rem;
            /* Μικρότερη γραμματοσειρά */
            margin-bottom: 25px;
        }

        /* Αφαίρεση της αριστερής γραμμής στο mobile */
        .hero-desc-wrapper {
            border-left: none;
            padding-left: 0;
            margin: 0 auto 35px auto;
        }

        .hero-paragraph {
            font-size: 1.05rem;
        }

        .hero-action-btns {
            justify-content: center;
            gap: 15px;
        }

        /* Visual Area Mobile Fixes */
        .hero-visual-area {
            height: 420px;
            /* Μικρότερο ύψος */
            width: 100%;
            margin-top: 0;
        }

        .floating-img {
            width: 100px;
        }

        /* Mobile Positions for Icons */
        #shard1 {
            top: 20px;
            left: 10px;
        }

        #shard2 {
            bottom: 40px;
            margin-left: -50px;
        }

        #shard3 {
            top: 40px;
            right: 10px;
        }
    }
</style>




<section class="services-stack-section" id="services">

    <div class="container text-center mb-5" style="position: relative; z-index: 10;">
        <span class="mini-tag">ΘΕΡΑΠΕΥΤΙΚΑ ΜΟΝΟΠΑΤΙΑ</span>
        <h2 class="section-title">
            Ένα ταξίδι προς την <span class="serif-italic">εσωτερική σύνδεση.</span>
        </h2>
    </div>

    <div class="stack-wrapper">

        <div class="service-card-gsap card-1">
            <div class="card-inner-content">
                <div class="row h-100 align-items-center">
                    <div class="col-lg-6 mb-4 mb-lg-0">
                        <div class="service-img-wrapper">
                            <img src="/assets/images/icons/isession.png" alt="Individual Therapy" class="service-img">
                        </div>
                    </div>
                    <div class="col-lg-5 offset-lg-1">
                        <div class="service-meta">
                            <span class="meta-line"></span>
                            <span class="meta-number">01</span>
                        </div>
                        <h3 class="service-title">Ατομική Ψυχοθεραπεία</h3>
                        <p class="service-desc">
                            Ένα ασφαλές περιβάλλον εμπιστοσύνης για την κατανόηση και διαχείριση συναισθημάτων. Στόχος η ανακούφιση από άγχος, φοβίες και η προσωπική αυτοβελτίωση.
                        </p>
                        <div class="service-tags">
                            <span class="tag-pill">Αυτογνωσία</span>
                            <span class="tag-pill">Διαχείριση Άγχους</span>
                            <span class="tag-pill">Προσωπική Ανάπτυξη</span>
                        </div>
                        <a href="/services#individual" class="btn-circle-action">
                            <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="service-card-gsap card-2">
            <div class="card-inner-content">
                <div class="row h-100 align-items-center flex-row-reverse">
                    <div class="col-lg-6 mb-4 mb-lg-0">
                        <div class="service-img-wrapper">
                            <img src="/assets/images/icons/gsession.png" alt="Kids & Teens" class="service-img">
                        </div>
                    </div>
                    <div class="col-lg-5 offset-lg-1">
                        <div class="service-meta">
                            <span class="meta-line"></span>
                            <span class="meta-number">02</span>
                        </div>
                        <h3 class="service-title">Παιδιά & Έφηβοι</h3>
                        <p class="service-desc">
                            Υποστήριξη στη διαχείριση συμπεριφορικών δυσκολιών και Ομάδες Δεξιοτήτων. Ενίσχυση της συνεργασίας, της επικοινωνίας και της αυτοπεποίθησης μέσω βιωματικών ασκήσεων.
                        </p>
                        <div class="service-tags">
                            <span class="tag-pill">Διαχείριση Συμπεριφοράς</span>
                            <span class="tag-pill">Συναισθηματική Νοημοσύνη</span>
                            <span class="tag-pill">Ομάδες Παιδιών</span>
                        </div>
                        <a href="/services#kids" class="btn-circle-action">
                            <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="service-card-gsap card-3">
            <div class="card-inner-content">
                <div class="row h-100 align-items-center">
                    <div class="col-lg-6 mb-4 mb-lg-0">
                        <div class="service-img-wrapper">
                            <img src="/assets/images/icons/couple2.png" alt="Parents Counseling" class="service-img">
                        </div>
                    </div>
                    <div class="col-lg-5 offset-lg-1">
                        <div class="service-meta">
                            <span class="meta-line"></span>
                            <span class="meta-number">03</span>
                        </div>
                        <h3 class="service-title">Συμβουλευτική Γονέων</h3>
                        <p class="service-desc">
                            Καθοδήγηση για την ενδυνάμωση του γονεϊκού ρόλου. Ατομική υποστήριξη και Ομάδες Γονέων για τη διαχείριση ορίων, την εφηβεία και τη βελτίωση της επικοινωνίας.
                        </p>
                        <div class="service-tags">
                            <span class="tag-pill">Ενδυνάμωση Ρόλου</span>
                            <span class="tag-pill">Όρια & Κανόνες</span>
                            <span class="tag-pill">Ομάδες Υποστήριξης</span>
                        </div>
                        <a href="/services#parents" class="btn-circle-action">
                            <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="service-card-gsap card-4">
            <div class="card-inner-content">
                <div class="row h-100 align-items-center flex-row-reverse">
                    <div class="col-lg-6 mb-4 mb-lg-0">
                        <div class="service-img-wrapper">
                            <img src="/assets/images/icons/shealth1.png" alt="Psychosexual Therapy" class="service-img">
                        </div>
                    </div>
                    <div class="col-lg-5 offset-lg-1">
                        <div class="service-meta">
                            <span class="meta-line bg-white"></span>
                            <span class="meta-number">04</span>
                        </div>
                        <h3 class="service-title">Ψυχοσεξουαλική Υγεία</h3>
                        <p class="service-desc text-muted">
                            Εξειδικευμένη θεραπεία (ατομική ή ζεύγους) για ζητήματα σεξουαλικής υγείας και δυσλειτουργιών. Κατανόηση των αιτιών και ενίσχυση της οικειότητας.
                        </p>
                        <div class="service-tags">
                            <span class="tag-pill dark-mode-pill">Σεξουαλική Υγεία</span>
                            <span class="tag-pill dark-mode-pill">Λειτουργικότητα</span>
                            <span class="tag-pill dark-mode-pill">Ενίσχυση Οικειότητας</span>
                        </div>
                        <a href="/services#psychosexual" class="btn-circle-action btn-circle-white">
                            <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>

    </div>
</section>

<style>
    /* ========================
   SERVICES STACK SECTION
   ======================== */

    .services-stack-section {
        padding-top: 100px;
        padding-bottom: 100px;
        overflow: hidden;
    }

    /* Typography */
    .section-title {
        font-family: 'Playfair Display', serif;
        font-size: 3rem;
        color: var(--alma-text);
    }

    .serif-italic {
        font-family: 'Playfair Display', serif;
        font-style: italic;
        color: var(--alma-nav-text);
    }

    .mini-tag {
        font-size: 0.85rem;
        color: var(--alma-nav-text);
        font-weight: 700;
        letter-spacing: 2px;
        text-transform: uppercase;
    }

    /* Stacking Wrapper */
    .stack-wrapper {
        position: relative;
        width: 100%;
        padding-bottom: 50px;
        z-index: 1;
    }

    /* Card Base */
    .service-card-gsap {
        width: 100%;
        height: 550px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 40px;
        box-shadow: 0 -10px 40px rgba(0, 0, 0, 0.05);
        overflow: hidden;
        position: relative;
        margin-top: 20px;
        background-color: #fff;
    }

    .card-inner-content {
        width: 100%;
        padding: 0 50px;
    }

    /* Colors */
    .card-1 {
        background-color: #fffcf6;
        z-index: 1;
    }

    .card-2 {
        background-color: #f4f1ea;
        z-index: 2;
    }

    .card-3 {
        background-color: #faf7eb;
        z-index: 3;
    }

    .card-4 {
        background-color: #f7efe9;
        z-index: 4;
    }

    /* New Editorial Elements */
    .service-meta {
        display: flex;
        align-items: center;
        gap: 15px;
        margin-bottom: 20px;
    }

    .meta-line {
        width: 30px;
        height: 1px;
        background-color: var(--alma-nav-text);
        opacity: 0.5;
    }

    .meta-number {
        font-family: 'Manrope', sans-serif;
        font-weight: 700;
        font-size: 0.9rem;
        color: var(--alma-nav-text);
    }

    .service-title {
        font-family: 'Playfair Display', serif;
        font-size: 2.5rem;
        margin-bottom: 20px;
    }

    .service-desc {
        font-size: 1.1rem;
        line-height: 1.7;
        color: #666;
        margin-bottom: 30px;
    }

    /* Pills Tags */
    .service-tags {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin-bottom: 40px;
    }

    .tag-pill {
        padding: 8px 16px;
        background-color: rgba(0, 0, 0, 0.05);
        border-radius: 50px;
        font-size: 0.85rem;
        font-weight: 600;
        color: var(--alma-nav-text);
        transition: all 0.3s ease;
        cursor: default;
    }

    .tag-pill:hover {
        background-color: var(--alma-nav-text);
        color: #fff;
    }

    /* Circle Button */
    .btn-circle-action {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 60px;
        height: 60px;
        border-radius: 50%;
        background-color: var(--alma-bg-button-main);
        color: #fff;
        font-size: 1.5rem;
        transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
        text-decoration: none;
    }

    .btn-circle-action:hover {
        background-color: var(--alma-orange);
        transform: scale(1.1);
        color: #fff;
    }

    /* Dark Mode Utils (Card 4) */
    .text-white {
        color: #fff !important;
    }

    .text-white-50 {
        color: rgba(255, 255, 255, 0.7) !important;
    }

    .bg-white {
        background-color: #fff !important;
    }

    .dark-mode-pill {
        background-color: rgb(149 145 122);
        color: #fff;
    }

    .dark-mode-pill:hover {
        background-color: #fff;
        color: var(--alma-nav-text);
    }

    .btn-circle-white {
        background-color: #fff;
        color: var(--alma-nav-text);
    }

    .btn-circle-white:hover {
        background-color: var(--alma-orange);
        color: #fff;
    }

    /* Image */
    .service-img-wrapper {
        height: 400px;
        width: 100%;
        border-radius: 20px;
        overflow: hidden;
    }

    .service-img {
        width: 100%;
        height: 100%;
        object-fit: contain;
        /* Change to contain if icons are PNGs with transparency */
        transition: transform 0.6s ease;
    }

    .service-card-gsap:hover .service-img {
        transform: scale(1.05);
    }

    /* Footer Z-Index Safety */
    footer,
    .next-section,
    #footer {
        position: relative;
        z-index: 100;
        background-color: transparent;
    }

    /* --- RESPONSIVE (MOBILE) --- */
    @media (max-width: 991px) {
        .services-stack-section {
            padding-top: 80px;
        }

        .stack-wrapper {
            padding-bottom: 0;
        }

        .service-card-gsap {
            height: 75vh;
            min-height: 500px;
            width: 100%;
            flex-direction: column;
            justify-content: flex-start;
            padding: 0;
            border-radius: 25px 25px 0 0;
            box-shadow: 0 -10px 30px rgba(0, 0, 0, 0.08);
        }

        .card-inner-content {
            padding: 30px 20px;
            height: 100%;
            display: flex;
            flex-direction: column;
        }

        .row.h-100 {
            height: auto !important;
            display: block;
        }

        .service-img-wrapper {
            width: 100%;
            height: 220px;
            margin-bottom: 25px;
            border-radius: 15px;
        }

        .service-title {
            font-size: 1.8rem;
            margin-bottom: 15px;
        }

        .service-desc {
            font-size: 1rem;
            margin-bottom: 20px;
        }

        /* Force stack order on mobile */
        .flex-row-reverse {
            flex-direction: column !important;
        }

        .btn-circle-action {
            width: 50px;
            height: 50px;
            font-size: 1.2rem;
            /* Push to bottom of content if needed */
            margin-top: auto;
        }

        /* Ensure footer covers cards */
        footer,
        .next-section {
            z-index: 999 !important;
            position: relative;
        }
    }
</style>

<section class="team-section" id="about">
    <div class="container">

        <div class="row mb-5">
            <div class="col-lg-6">
                <span class="mini-tag">Η ΟΜΑΔΑ ΜΑΣ</span>
                <h2 class="section-title">
                    Άνθρωποι που <br>σας <span class="serif-italic">καταλαβαίνουν.</span>
                </h2>
            </div>
            <div class="col-lg-5 offset-lg-1 d-flex align-items-end">
                <p class="section-desc mb-0">
                    Μια διεπιστημονική ομάδα ψυχολόγων και συμβούλων, αφοσιωμένη στη δημιουργία ενός ασφαλούς πλαισίου για τη δική σας εξέλιξη.
                </p>
            </div>
        </div>

        <div class="team-accordion-wrapper">

            <div class="team-img-cursor" id="teamCursor">
                <div class="cursor-inner">
                    <img src="/assets/images/used/opti_anastasia2.jpg" class="team-img img-1" alt="Αναστασία Μαυρία">
                    <img src="/assets/images/used/opti_lefteris.jpg" class="team-img img-2" alt="Ελευθέριος Λιώνας">
                </div>
            </div>

            <div class="team-item" data-img="img-1">
                <div class="team-header">
                    <div class="member-info">
                        <div class="member-avatar mobile-only">
                            <img src="/assets/images/used/opti_anastasia2.jpg" alt="Αναστασία Μαυρία">
                        </div>

                        <div class="info-text">
                            <span class="member-role">Κλινική Ψυχολόγος, MSc</span>
                            <h3 class="member-name">Αναστασία Μαυρία</h3>
                        </div>
                    </div>

                    <div class="accordion-btn">
                        <i class="bi bi-plus-lg"></i>
                    </div>
                </div>

                <div class="team-body">
                    <div class="body-inner">
                        <p class="member-bio">
                            Απόφοιτη Ψυχολογίας (ΕΚΠΑ) με μεταπτυχιακό στην Κλινική Ψυχολογία (Leiden University) και εκπαίδευση στη Γνωσιακή Συμπεριφορική Θεραπεία (CBT).
                            Η προσέγγισή της εστιάζει στον σεβασμό της μοναδικότητας, βοηθώντας το άτομο να κατανοήσει τον εαυτό του και να χτίσει ισορροπημένες σχέσεις μέσω της αποδοχής και της συνεργασίας.
                        </p>
                        <div class="specialties-grid">
                            <span class="spec-dot">Κλινική Ψυχολογία</span>
                            <span class="spec-dot">CBT</span>
                            <span class="spec-dot">Αυτογνωσία</span>
                            <span class="spec-dot">Διαπροσωπικές Σχέσεις</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="team-item" data-img="img-2">
                <div class="team-header">
                    <div class="member-info">
                        <div class="member-avatar mobile-only">
                            <img src="/assets/images/used/opti_lefteris.jpg" alt="Λιώνας Ελευθέριος">
                        </div>
                        <div class="info-text">
                            <span class="member-role">Ψυχολόγος, MSc Ψυχοσεξουαλικής Θεραπείας</span>
                            <h3 class="member-name">Ελευθέριος Λιώνας</h3>
                        </div>
                    </div>

                    <div class="accordion-btn">
                        <i class="bi bi-plus-lg"></i>
                    </div>
                </div>

                <div class="team-body">
                    <div class="body-inner">
                        <p class="member-bio">
                            Απόφοιτος Ψυχολογίας (ΕΚΠΑ) με εξειδίκευση στη Ψυχοσεξουαλική Θεραπεία (University of Lancashire).
                            Με ενσυναίσθηση και σεβασμό στη διαφορετικότητα, παρέχει ολιστική υποστήριξη σε άτομα και ζευγάρια, εστιάζοντας στη σεξουαλική υγεία, την επικοινωνία και τη συναισθηματική σύνδεση.
                        </p>
                        <div class="specialties-grid">
                            <span class="spec-dot">Ψυχοσεξουαλική Υγεία</span>
                            <span class="spec-dot">Θεραπεία Ζεύγους</span>
                            <span class="spec-dot">Σεξουαλικές Δυσκολίες</span>
                            <span class="spec-dot">Συναισθηματική Σύνδεση</span>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

<style>
    /* ========================
   TEAM SECTION (ACCORDION + HOVER REVEAL)
   ======================== */

    .team-section {
        padding: 120px 0;
        background-color: #fff;
        position: relative;
        z-index: 10;
    }

    /* Typography */
    .section-title {
        font-family: 'Playfair Display', serif;
        font-size: 3rem;
        color: var(--alma-text);
    }

    .serif-italic {
        font-family: 'Playfair Display', serif;
        font-style: italic;
        color: var(--alma-nav-text);
    }

    .section-desc {
        font-size: 1.1rem;
        color: #666;
        line-height: 1.6;
    }

    .team-accordion-wrapper {
        margin-top: 60px;
        position: relative;
    }

    /* --- COMMON ITEM STYLES --- */
    .team-item {
        border-bottom: 1px solid rgba(0, 0, 0, 0.1);
        position: relative;
        overflow: hidden;
        /* Important for accordion height anim */
    }

    .team-item:first-child {
        border-top: 1px solid rgba(0, 0, 0, 0.1);
    }

    /* Header (The Trigger) */
    .team-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        cursor: pointer;
        transition: background-color 0.3s ease;
        /* Padding varies by device */
    }

    .member-info {
        display: flex;
        align-items: center;
        gap: 20px;
    }

    .member-role {
        display: block;
        font-size: 0.8rem;
        text-transform: uppercase;
        letter-spacing: 1px;
        color: var(--alma-orange);
        margin-bottom: 5px;
        font-weight: 600;
    }

    .member-name {
        font-family: 'Playfair Display', serif;
        color: var(--alma-text);
        margin: 0;
        transition: color 0.3s ease, transform 0.3s ease;
    }

    /* Accordion Button (Plus) */
    .accordion-btn {
        width: 45px;
        height: 45px;
        border-radius: 50%;
        border: 1px solid rgba(0, 0, 0, 0.1);
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--alma-text);
        transition: all 0.4s ease;
        flex-shrink: 0;
        /* Μην ζουλιέται */
    }

    .accordion-btn i {
        font-size: 1.2rem;
        transition: transform 0.4s ease;
    }

    /* Accordion Body */
    .team-body {
        height: 0;
        overflow: hidden;
        transition: height 0.5s cubic-bezier(0.25, 0.8, 0.25, 1);
    }

    .member-bio {
        font-size: 1.05rem;
        color: #555;
        line-height: 1.7;
        margin-bottom: 20px;
    }

    /* Specialties Chips */
    .specialties-grid {
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
    }

    .spec-dot {
        display: inline-flex;
        align-items: center;
        font-size: 0.9rem;
        font-weight: 600;
        color: var(--alma-nav-text);
        background-color: #f4f1ea;
        padding: 8px 16px;
        border-radius: 50px;
    }

    .spec-dot::before {
        content: '';
        display: inline-block;
        width: 6px;
        height: 6px;
        background-color: var(--alma-orange);
        border-radius: 50%;
        margin-right: 8px;
    }

    /* Active State (Open Accordion) */
    .team-item.active .accordion-btn {
        background-color: var(--alma-nav-text);
        color: #fff;
        border-color: var(--alma-nav-text);
    }

    .team-item.active .accordion-btn i {
        transform: rotate(45deg);
    }

    /* Turns to X */
    .team-item.active .member-name {
        color: var(--alma-nav-text);
    }


    /* ============================
   DESKTOP STYLES (Hover Image + No Avatar)
   ============================ */
    @media (min-width: 992px) {

        /* Hide Static Avatar on Desktop */
        .mobile-only {
            display: none !important;
        }

        .team-header {
            padding: 40px 0;
        }

        .member-name {
            font-size: 2.2rem;
        }

        /* Indent body to align with text */
        .body-inner {
            padding: 0 100px 40px 0;
            max-width: 800px;
        }

        /* Hover Interaction */
        .team-header:hover .member-name {
            transform: translateX(15px);
            color: var(--alma-nav-text);
        }

        .team-header:hover .accordion-btn {
            background-color: var(--alma-nav-text);
            color: #fff;
            transform: rotate(90deg);
            /* Little spin */
        }

        /* --- FLOATING CURSOR STYLES --- */
        .team-img-cursor {
            position: fixed;
            top: 0;
            left: 0;
            width: 300px;
            height: 400px;
            /* Λίγο πιο compact */
            z-index: 20;
            pointer-events: none;
            /* Το ποντίκι περνάει από μέσα */
            opacity: 0;
            transform: scale(0.8);
            transition: opacity 0.3s ease, transform 0.3s ease;
        }

        .cursor-inner {
            width: 100%;
            height: 100%;
            position: relative;
            overflow: hidden;
            border-radius: 10px;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.15);
        }

        .team-img {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            opacity: 0;
            transition: opacity 0.3s ease, transform 0.5s ease;
            transform: scale(1.1);
        }

        .team-img.active {
            opacity: 1;
            transform: scale(1);
        }

        .team-img-cursor.active {
            opacity: 1;
            transform: scale(1);
        }
    }

    /* ============================
   MOBILE STYLES (Avatar + Accordion)
   ============================ */
    @media (max-width: 991px) {

        /* Hide Desktop Cursor */
        .team-img-cursor {
            display: none !important;
        }

        .team-header {
            padding: 25px 0;
        }

        .member-name {
            font-size: 1.4rem;
        }

        /* Avatar Styling */
        .member-avatar {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            overflow: hidden;
            flex-shrink: 0;
        }

        .member-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        /* Body Padding (No indent, full width) */
        .body-inner {
            padding: 0 0 30px 0;
        }

        .accordion-btn {
            width: 40px;
            height: 40px;
        }
    }
</style>




<section class="process-section" id="process">
    <div class="container">

        <div class="row justify-content-center text-center mb-5 fade-in-up">
            <div class="col-lg-8">
                <span class="mini-tag">Η ΔΙΑΔΙΚΑΣΙΑ</span>
                <h2 class="section-title">
                    Ξεκινήστε το ταξίδι σας <br>σε <span class="serif-italic">3 απλά βήματα.</span>
                </h2>
            </div>
        </div>

        <div class="process-steps-wrapper">

            <div class="process-line">
                <div class="line-fill"></div>
            </div>

            <div class="row">

                <div class="col-lg-4 col-md-4 mb-5 mb-md-0">
                    <div class="step-item fade-in-up">
                        <div class="step-number">01</div>
                        <h3 class="step-title">Επιλογή</h3>
                        <p class="step-desc">
                            Εξερευνήστε τις υπηρεσίες μας και βρείτε το κατάλληλο πλαίσιο υποστήριξης για εσάς.
                        </p>
                    </div>
                </div>

                <div class="col-lg-4 col-md-4 mb-5 mb-md-0">
                    <div class="step-item fade-in-up" data-delay="0.2">
                        <div class="step-number">02</div>
                        <h3 class="step-title">Κράτηση</h3>
                        <p class="step-desc">
                            Δείτε τη διαθεσιμότητα, επιλέξτε αν επιθυμείτε συνεδρία <strong>στο γραφείο μας</strong> ή <strong>online</strong> και ολοκληρώστε την κράτηση.
                        </p>
                    </div>
                </div>

                <div class="col-lg-4 col-md-4">
                    <div class="step-item fade-in-up" data-delay="0.4">
                        <div class="step-number">03</div>
                        <h3 class="step-title">Συνεδρία</h3>
                        <p class="step-desc">
                            Σας υποδεχόμαστε στον χώρο μας ή συνδεόμαστε διαδικτυακά μέσω της πλατφόρμας που προτιμάτε.
                        </p>
                    </div>
                </div>

            </div>
        </div>

        <div class="row mt-5 text-center">
            <div class="col-12">
                <a href="/services" class="btn-alma-solid">
                    <span>Κλείστε Ραντεβού</span>
                </a>
            </div>
        </div>

    </div>
</section>

<style>
    /* ========================
   PROCESS SECTION (TIMELINE)
   ======================== */
    .process-section {
        padding: 100px 0;
        background-color: var(--alma-bg);
        /* Επιστροφή στο απαλό χρώμα για contrast με το λευκό team */
        position: relative;
        z-index: 5;
        overflow: hidden;
    }

    .process-steps-wrapper {
        position: relative;
        padding-top: 40px;
        /* Χώρος για τους αριθμούς */
        margin-top: 60px;
    }

    /* --- THE ANIMATED LINE --- */
    .process-line {
        position: absolute;
        top: 55px;
        left: 15%;
        width: 70%;
        height: 1px;
        background-color: rgba(0, 0, 0, 0.08);
        /* Το αχνό γκρι background της γραμμής */
        z-index: 0;
        overflow: hidden;
        /* Για να κόβει το fill */
    }

    .line-fill {
        width: 0%;
        /* Ξεκινάει άδεια */
        height: 100%;
        background-color: var(--alma-orange);
        /* Το χρώμα που γεμίζει */
        position: absolute;
        top: 0;
        left: 0;
    }


    /* Προαιρετικά: Αν θες να γεμίζει η γραμμή με animation, 
   θα το κάνουμε με JS, αλλιώς άστο απλό background */

    /* --- STEP ITEM --- */
    .step-item {
        position: relative;
        z-index: 1;
        text-align: center;
        padding: 0 20px;
    }

    .step-number {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 110px;
        height: 110px;
        background-color: var(--alma-bg);
        /* Κρύβει τη γραμμή από πίσω */
        border: 1px solid rgba(0, 0, 0, 0.05);
        /* Πολύ απαλό border */
        border-radius: 50%;

        font-family: 'Playfair Display', serif;
        font-size: 2.5rem;
        color: var(--alma-nav-text);
        font-style: italic;

        margin-bottom: 25px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.03);
        /* Ελαφριά σκιά για βάθος */
        transition: transform 0.3s ease;
    }

    .step-item:hover .step-number {
        transform: translateY(-10px);
        border-color: var(--alma-orange);
        color: var(--alma-orange);
    }

    .step-title {
        font-family: 'Manrope', sans-serif;
        /* Sans serif για καθαρότητα */
        font-weight: 700;
        font-size: 1.25rem;
        margin-bottom: 15px;
        color: var(--alma-text);
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    .step-desc {
        font-size: 1rem;
        color: #666;
        line-height: 1.6;
        max-width: 300px;
        margin: 0 auto;
    }

    /* --- RESPONSIVE --- */
    @media (max-width: 991px) {
        .process-line {
            /* Στο mobile η γραμμή είναι κάθετη */
            width: 1px;
            height: 80%;
            top: 0;
            left: 50%;
            transform: translateX(-50%);
        }

        .line-fill {
            width: 100%;
            height: 0%;
            /* Στο mobile γεμίζει προς τα κάτω */
        }

        .process-steps-wrapper {
            padding-top: 0;
        }

        .step-item {
            margin-bottom: 50px;
            /* Μεγαλύτερο κενό κάθετα */
            background-color: var(--alma-bg);
            /* Για να καλύπτει τη γραμμή */
            padding: 20px 0;
            /* Λίγο padding πάνω κάτω */
        }

        /* Τελευταίο βήμα χωρίς margin */
        .col-lg-4:last-child .step-item {
            margin-bottom: 0;
        }

        .step-number {
            width: 90px;
            height: 90px;
            font-size: 2rem;
            margin-bottom: 20px;
        }
    }
</style>

<section class="faq-focus-section" id="faq">
    <div class="container">

        <div class="row mb-5">
            <div class="col-12 text-center">
                <span class="mini-tag">ΑΠΟΡΙΕΣ</span>
                <h2 class="section-title">Συχνές ερωτήσεις</h2>
            </div>
        </div>

        <div class="row">

            <div class="col-lg-5 d-none d-lg-block">
                <div class="faq-nav-list">

                    <div class="faq-nav-item active" data-index="0">
                        <h3 class="nav-text">Διάρκεια Συνεδρίας</h3>
                    </div>

                    <div class="faq-nav-item" data-index="1">
                        <h3 class="nav-text">Πολιτική Ακυρώσεων</h3>
                    </div>

                    <div class="faq-nav-item" data-index="2">
                        <h3 class="nav-text">Online Θεραπεία</h3>
                    </div>

                    <div class="faq-nav-item" data-index="3">
                        <h3 class="nav-text">Απόρρητο & Δεοντολογία</h3>
                    </div>

                </div>
            </div>

            <div class="col-lg-6 offset-lg-1 d-none d-lg-block position-relative">
                <div class="focus-glow-blob"></div>

                <div class="faq-display-card">
                    <div class="faq-content-stack">

                        <div class="faq-content-item active">
                            <i class="bi bi-clock display-icon"></i>
                            <h4 class="display-title">Χρόνος για εσάς.</h4>
                            <p class="display-desc">
                                Η ατομική συνεδρία διαρκεί συνήθως <strong>50 λεπτά</strong>.
                                <br><br>
                                Αυτός ο χρόνος είναι αποκλειστικά δικός σας. Για ζεύγη, ο χρόνος ενδέχεται να είναι 60-90 λεπτά.
                            </p>
                        </div>

                        <div class="faq-content-item">
                            <i class="bi bi-calendar-x display-icon"></i>
                            <h4 class="display-title">Ευελιξία & Σεβασμός.</h4>
                            <p class="display-desc">
                                Παρακαλούμε για ενημέρωση τουλάχιστον <strong>24 ώρες πριν</strong>, ώστε να αποφευχθεί η χρέωση και να δοθεί η ευκαιρία σε κάποιον άλλο να αξιοποιήσει τον χρόνο.
                            </p>
                        </div>

                        <div class="faq-content-item">
                            <i class="bi bi-wifi display-icon"></i>
                            <h4 class="display-title">Σύνδεση από παντού.</h4>
                            <p class="display-desc">
                                Οι online συνεδρίες πραγματοποιούνται μέσω ασφαλών πλατφορμών. Έρευνες επιβεβαιώνουν ότι η θεραπευτική συμμαχία χτίζεται εξίσου δυνατά και διαδικτυακά.
                            </p>
                        </div>

                        <div class="faq-content-item">
                            <i class="bi bi-shield-check display-icon"></i>
                            <h4 class="display-title">Απόλυτη εχεμύθεια.</h4>
                            <p class="display-desc">
                                Τηρούμε αυστηρά τον Κώδικα Δεοντολογίας των Ψυχολόγων. Όσα λέγονται εδώ, μένουν εδώ (με εξαίρεση τις νομικές προβλέψεις για κίνδυνο ζωής).
                            </p>
                        </div>

                    </div>
                </div>
            </div>

            <div class="col-12 d-lg-none">

                <div class="mobile-swipe-container">

                    <button class="mob-nav-btn prev-btn hidden" id="mobPrev">
                        <i class="bi bi-chevron-left"></i>
                    </button>

                    <div class="mobile-swipe-wrapper" id="mobSwipeWrapper">

                        <div class="mob-swipe-card">
                            <div class="mob-card-header">
                                <h3 class="mob-title">Διάρκεια</h3>
                                <div class="mob-line"></div>
                            </div>
                            <p class="mob-desc">
                                Η ατομική συνεδρία διαρκεί συνήθως <strong>50 λεπτά</strong>. Για ζεύγη, ο χρόνος ενδέχεται να είναι 60-90 λεπτά.
                            </p>
                            <i class="bi bi-clock mob-icon-bg"></i>
                        </div>

                        <div class="mob-swipe-card">
                            <div class="mob-card-header">
                                <h3 class="mob-title">Ακύρωση</h3>
                                <div class="mob-line"></div>
                            </div>
                            <p class="mob-desc">
                                Παρακαλούμε για ενημέρωση τουλάχιστον <strong>24 ώρες πριν</strong> το ραντεβού σας, ώστε να αποφευχθεί η χρέωση.
                            </p>
                            <i class="bi bi-calendar-x mob-icon-bg"></i>
                        </div>

                        <div class="mob-swipe-card">
                            <div class="mob-card-header">
                                <h3 class="mob-title">Online</h3>
                                <div class="mob-line"></div>
                            </div>
                            <p class="mob-desc">
                                Ναι, είναι εξίσου αποτελεσματική. Πραγματοποιείται μέσω ασφαλών πλατφορμών (Zoom/Skype).
                            </p>
                            <i class="bi bi-laptop mob-icon-bg"></i>
                        </div>

                        <div class="mob-swipe-card">
                            <div class="mob-card-header">
                                <h3 class="mob-title">Απόρρητο</h3>
                                <div class="mob-line"></div>
                            </div>
                            <p class="mob-desc">
                                Τηρούμε αυστηρά το ιατρικό απόρρητο και τον Κώδικα Δεοντολογίας. Όλα είναι αυστηρά εμπιστευτικά.
                            </p>
                            <i class="bi bi-shield-lock mob-icon-bg"></i>
                        </div>

                    </div>

                    <button class="mob-nav-btn next-btn" id="mobNext">
                        <i class="bi bi-chevron-right"></i>
                    </button>

                </div>

            </div>

        </div>
    </div>
</section>

<style>
    /* ========================
   FAQ FOCUS SECTION (FINAL)
   ======================== */
    .faq-focus-section {
        padding: 120px 0;
        background-color: #fcfcfc;
        position: relative;
        overflow: hidden;
    }

    /* --- DESKTOP LEFT (NAVIGATION) --- */
    .faq-nav-list {
        display: flex;
        flex-direction: column;
        gap: 25px;
        padding-top: 40px;
    }

    .faq-nav-item {
        display: flex;
        align-items: center;
        padding: 15px 0;
        cursor: pointer;
        opacity: 0.5;
        transition: all 0.3s ease;
        border-left: 3px solid transparent;
        padding-left: 0;
    }

    .faq-nav-item.active {
        opacity: 1;
        border-left-color: var(--alma-orange);
        padding-left: 25px;
        /* Shift right effect */
    }

    .faq-nav-item:hover {
        opacity: 1;
        padding-left: 10px;
        /* Small shift on hover */
    }

    .nav-text {
        font-family: 'Playfair Display', serif;
        font-size: 2.2rem;
        color: var(--alma-text);
        margin: 0;
        line-height: 1.1;
    }

    /* --- DESKTOP RIGHT (GLASS CARD) --- */
    .faq-display-card {
        background: #fff;
        border: 1px solid rgba(0, 0, 0, 0.05);
        border-radius: 30px;
        padding: 60px;
        box-shadow: 0 30px 60px rgba(0, 0, 0, 0.05);
        height: 450px;
        width: 100%;
        position: relative;
        z-index: 2;
        overflow: hidden;
    }

    /* The Glow Blob behind the card */
    .focus-glow-blob {
        position: absolute;
        top: 50%;
        left: 50%;
        width: 300px;
        height: 300px;
        background: radial-gradient(circle, var(--alma-orange) 0%, rgba(255, 255, 255, 0) 70%);
        opacity: 0.15;
        border-radius: 50%;
        transform: translate(-50%, -50%);
        filter: blur(60px);
        z-index: 1;
        transition: all 1s ease;
    }

    /* Content Stacking */
    .faq-content-item {
        position: absolute;
        top: 60px;
        left: 60px;
        right: 60px;
        opacity: 0;
        visibility: hidden;
        transform: translateY(20px);
    }

    /* Active State for Content */
    .faq-content-item.active {
        opacity: 1;
        visibility: visible;
        transform: translateY(0);
    }

    /* Typography inside card */
    .display-icon {
        font-size: 2.5rem;
        color: var(--alma-orange);
        margin-bottom: 25px;
        display: block;
    }

    .display-title {
        font-family: 'Manrope', sans-serif;
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--alma-text);
        margin-bottom: 20px;
    }

    .display-desc {
        font-size: 1.1rem;
        color: #666;
        line-height: 1.7;
    }


    /* =================================
   MOBILE STYLES (SWIPE + ARROWS)
   ================================= */
    @media (max-width: 991px) {
        .faq-focus-section {
            padding: 60px 0;
        }

        /* Container for positioning arrows */
        .mobile-swipe-container {
            position: relative;
            width: 100%;
        }

        /* Scrollable Wrapper */
        .mobile-swipe-wrapper {
            display: flex;
            gap: 15px;
            overflow-x: auto;
            padding-bottom: 30px;
            /* Space for shadow */
            scroll-snap-type: x mandatory;
            -webkit-overflow-scrolling: touch;
            padding-left: 5vw;
            padding-right: 5vw;
            scroll-behavior: smooth;
        }

        /* Hide Scrollbar */
        .mobile-swipe-wrapper::-webkit-scrollbar {
            display: none;
        }

        .mobile-swipe-wrapper {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }

        /* The Card */
        .mob-swipe-card {
            min-width: 85vw;
            /* Almost full width */
            background: #fff;
            border-radius: 20px;
            padding: 30px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            scroll-snap-align: center;
            position: relative;
            overflow: hidden;
            border: 1px solid rgba(0, 0, 0, 0.03);
        }

        /* Floating Nav Buttons */
        .mob-nav-btn {
            position: absolute;
            top: 45%;
            /* Center vertically relative to card height */
            transform: translateY(-50%);
            width: 45px;
            height: 45px;
            border-radius: 50%;
            background-color: #fff;
            border: 1px solid rgba(0, 0, 0, 0.05);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            color: var(--alma-orange);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            z-index: 10;
            cursor: pointer;
            transition: all 0.3s ease;
            opacity: 1;
            visibility: visible;
        }

        .prev-btn {
            left: 5px;
        }

        .next-btn {
            right: 5px;
        }

        /* Hidden State for Buttons */
        .mob-nav-btn.hidden {
            opacity: 0;
            visibility: hidden;
            pointer-events: none;
        }

        /* Mobile Typography */
        .mob-card-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 20px;
        }

        .mob-title {
            font-size: 1.6rem;
            color: var(--alma-text);
            margin: 0;
            font-family: 'Playfair Display', serif;
        }

        .mob-line {
            height: 2px;
            width: 40px;
            background-color: var(--alma-orange);
            opacity: 0.5;
        }

        .mob-desc {
            font-size: 1.05rem;
            color: #666;
            line-height: 1.6;
            position: relative;
            z-index: 2;
        }

        /* Background Decor Icon */
        .mob-icon-bg {
            position: absolute;
            bottom: -20px;
            right: -20px;
            font-size: 6rem;
            color: var(--alma-orange);
            opacity: 0.05;
            z-index: 1;
            transform: rotate(-15deg);
        }
    }
</style>

<?php
function hook_end_scripts()
{
?>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/ScrollTrigger.min.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {

            // --- ELEMENTS ---
            const navItems = document.querySelectorAll('.faq-nav-item');
            const contentItems = document.querySelectorAll('.faq-content-item');
            const glowBlob = document.querySelector('.focus-glow-blob');

            const mobWrapper = document.getElementById('mobSwipeWrapper');
            const mobPrev = document.getElementById('mobPrev');
            const mobNext = document.getElementById('mobNext');

            let activeIndex = 0;

            // --- 1. DESKTOP LOGIC FUNCTION ---
            function initDesktopInteractions() {
                navItems.forEach((item, index) => {
                    // Καθαρίζουμε τυχόν παλιούς listeners για να μην διπλασιαστούν
                    item.onmouseenter = null;

                    item.onmouseenter = () => {
                        if (window.innerWidth <= 991) return; // Stop if on mobile

                        if (activeIndex === index) return;
                        activeIndex = index;

                        // Update Visuals
                        navItems.forEach(n => n.classList.remove('active'));
                        item.classList.add('active');

                        // Kill old animations
                        gsap.killTweensOf(contentItems);

                        // Hide others
                        contentItems.forEach((content, i) => {
                            if (i !== index) {
                                gsap.to(content, {
                                    opacity: 0,
                                    y: -15,
                                    duration: 0.2,
                                    onComplete: () => content.classList.remove('active')
                                });
                            }
                        });

                        // Show New
                        const newContent = contentItems[index];
                        newContent.classList.add('active');

                        gsap.fromTo(newContent, {
                            opacity: 0,
                            y: 15
                        }, {
                            opacity: 1,
                            y: 0,
                            duration: 0.4,
                            ease: "power2.out",
                            delay: 0.1
                        });

                        // Glow Blob
                        if (glowBlob) {
                            const randomX = (Math.random() - 0.5) * 80;
                            const randomY = (Math.random() - 0.5) * 80;
                            gsap.to(glowBlob, {
                                x: randomX,
                                y: randomY,
                                duration: 1,
                                ease: "power2.out"
                            });
                        }
                    };
                });
            }

            // --- 2. MOBILE LOGIC FUNCTION ---
            function updateMobileArrows() {
                if (!mobWrapper || !mobPrev || !mobNext) return;

                const scrollLeft = mobWrapper.scrollLeft;
                const scrollWidth = mobWrapper.scrollWidth;
                const clientWidth = mobWrapper.clientWidth;

                // Hide Prev if at start
                if (scrollLeft <= 10) {
                    mobPrev.classList.add('hidden');
                } else {
                    mobPrev.classList.remove('hidden');
                }

                // Hide Next if at end
                if (Math.ceil(scrollLeft + clientWidth) >= scrollWidth - 10) {
                    mobNext.classList.add('hidden');
                } else {
                    mobNext.classList.remove('hidden');
                }
            }

            function initMobileInteractions() {
                if (!mobWrapper) return;

                // Αφαιρούμε παλιούς listeners
                mobWrapper.onscroll = null;
                if (mobNext) mobNext.onclick = null;
                if (mobPrev) mobPrev.onclick = null;

                // Προσθήκη νέων
                mobWrapper.onscroll = updateMobileArrows;

                if (mobNext) {
                    mobNext.onclick = () => {
                        mobWrapper.scrollBy({
                            left: mobWrapper.clientWidth * 0.85,
                            behavior: 'smooth'
                        });
                    };
                }

                if (mobPrev) {
                    mobPrev.onclick = () => {
                        mobWrapper.scrollBy({
                            left: -(mobWrapper.clientWidth * 0.85),
                            behavior: 'smooth'
                        });
                    };
                }

                // Initial check
                updateMobileArrows();
            }

            // --- 3. MASTER RESIZE HANDLER ---
            function handleResize() {
                const width = window.innerWidth;

                if (width > 991) {
                    initDesktopInteractions();
                    // Reset mobile styles/listeners if needed
                } else {
                    initMobileInteractions();
                    // Reset desktop styles if needed
                }
            }

            // --- INITIALIZATION ---
            // Τρέξε μία φορά στην αρχή
            handleResize();

            // Τρέξε κάθε φορά που αλλάζει το μέγεθος (με μικρό debounce για performance)
            let resizeTimer;
            window.addEventListener('resize', () => {
                clearTimeout(resizeTimer);
                resizeTimer = setTimeout(handleResize, 100);
            });
        });


        const tagsSwiper = new Swiper('.tagsSlider', {
            // centeredSlides: false,
            grabCursor: true,
            effect: "creative",
            creativeEffect: {
                prev: {
                    shadow: true,
                    translate: ["-120%", 0, -500],
                },
                next: {
                    shadow: true,
                    translate: ["120%", 0, -500],
                },
            },
            navigation: {
                nextEl: '.navNext',
                prevEl: '.navPrev',
            },
            slidesPerView: 1,
            updateOnWindowResize: true,
            loop: true,
            // autoplay: {
            //     delay: 2000, // Time between slides in milliseconds
            //     disableOnInteraction: false, // Keep autoplay active on interaction
            // },
            speed: 2500,
            grabCursor: true,
            centeredSlides: true,
            centeredSlidesBounds: true,
        });

        document.addEventListener("DOMContentLoaded", function() {

            const canvas = document.getElementById('connectionCanvas');
            if (!canvas) return;

            const ctx = canvas.getContext('2d');
            const visualArea = document.querySelector('.hero-visual-area');

            // --- CONFIGURATION ---
            const config = {
                lineColor: '#334c47',
                lineWidth: 1.5,
                gravity: 50,
                stiffness: 0.05,
                damping: 0.9,
                mouseForce: 40,
                mouseRadius: 150
            };

            let width, height;
            let mouse = {
                x: -1000,
                y: -1000
            };

            // --- CLASS: ELASTIC LINE ---
            class ElasticLine {
                constructor(startElId, endElId) {
                    this.startEl = document.getElementById(startElId);
                    this.endEl = document.getElementById(endElId);

                    this.cp = {
                        x: 0,
                        y: 0
                    };
                    this.vel = {
                        x: 0,
                        y: 0
                    };
                    this.progress = 0;
                }

                getCenter(el) {
                    const rect = el.getBoundingClientRect();
                    const areaRect = visualArea.getBoundingClientRect();
                    return {
                        x: (rect.left - areaRect.left) + (rect.width / 2),
                        y: (rect.top - areaRect.top) + (rect.height / 2)
                    };
                }

                update() {
                    if (!this.startEl || !this.endEl) return;

                    const p1 = this.getCenter(this.startEl);
                    const p2 = this.getCenter(this.endEl);

                    // Calculate current endpoint based on progress
                    const currentP2 = {
                        x: p1.x + (p2.x - p1.x) * this.progress,
                        y: p1.y + (p2.y - p1.y) * this.progress
                    };

                    // Physics Target
                    const targetX = (p1.x + currentP2.x) / 2;
                    const targetY = ((p1.y + currentP2.y) / 2) + config.gravity;

                    // Mouse Interaction
                    const dx = mouse.x - targetX;
                    const dy = mouse.y - targetY;
                    const dist = Math.sqrt(dx * dx + dy * dy);

                    let forceX = 0;
                    let forceY = 0;

                    if (dist < config.mouseRadius) {
                        const force = (config.mouseRadius - dist) / config.mouseRadius;
                        const angle = Math.atan2(dy, dx);
                        forceX = -Math.cos(angle) * force * config.mouseForce;
                        forceY = -Math.sin(angle) * force * config.mouseForce;
                    }

                    // Spring Physics
                    const ax = (targetX + forceX - this.cp.x) * config.stiffness;
                    const ay = (targetY + forceY - this.cp.y) * config.stiffness;

                    this.vel.x += ax;
                    this.vel.y += ay;
                    this.vel.x *= config.damping;
                    this.vel.y *= config.damping;

                    if (this.progress < 0.05) {
                        this.cp.x = targetX;
                        this.cp.y = targetY;
                    } else {
                        this.cp.x += this.vel.x;
                        this.cp.y += this.vel.y;
                    }

                    // Render
                    if (this.progress > 0) {
                        ctx.beginPath();
                        ctx.strokeStyle = config.lineColor;
                        ctx.lineWidth = config.lineWidth;
                        ctx.lineCap = "round";
                        ctx.moveTo(p1.x, p1.y);
                        ctx.quadraticCurveTo(this.cp.x, this.cp.y, currentP2.x, currentP2.y);
                        ctx.stroke();
                    }
                }
            }

            // Initialize Lines
            const line1 = new ElasticLine('shard1', 'shard2');
            const line2 = new ElasticLine('shard2', 'shard3');

            // --- SETUP ---
            function resize() {
                width = visualArea.clientWidth;
                height = visualArea.clientHeight;
                const dpr = window.devicePixelRatio || 1;
                canvas.width = width * dpr;
                canvas.height = height * dpr;
                ctx.scale(dpr, dpr);
            }
            resize();
            window.addEventListener('resize', resize);

            // Mouse
            visualArea.addEventListener('mousemove', (e) => {
                const rect = visualArea.getBoundingClientRect();
                mouse.x = e.clientX - rect.left;
                mouse.y = e.clientY - rect.top;
            });

            visualArea.addEventListener('mouseleave', () => {
                mouse.x = -1000;
                mouse.y = -1000;
            });

            // Render Loop
            function render() {
                ctx.clearRect(0, 0, width, height);
                line1.update();
                line2.update();
                requestAnimationFrame(render);
            }
            render();

            // --- ANIMATION SEQUENCE (GSAP - CALM FLOW) ---
            function runSequence() {
                const tl = gsap.timeline({
                    delay: 0.2
                });

                // 1. Text Reveal (Παραμένει απαλό)
                tl.to(".hero-text-area", {
                    opacity: 1,
                    y: 0,
                    duration: 1.6,
                    ease: "power3.out"
                });

                tl.from(".fade-item", {
                    y: 20,
                    opacity: 0,
                    duration: 1.2,
                    stagger: 0.15,
                    ease: "power3.out"
                }, "<");

                // 2. Poof Image 1 (Logic)
                // Λίγο πιο αργό poof για να μην είναι απότομο
                tl.to("#shard1", {
                    opacity: 1,
                    scale: 1,
                    duration: 1.0,
                    ease: "back.out(1.2)" // Λιγότερο "πεταχτό" bounce
                }, "-=1.0");

                // 3. Draw Line 1 -> CALM TRAVEL
                // Αυξήσαμε το duration από 1.4 σε 2.0
                tl.to(line1, {
                    progress: 1,
                    duration: 2.0,
                    ease: "power1.inOut" // Πιο γραμμική/ήρεμη κίνηση
                }, "-=0.6"); // Ξεκινάει αφού η εικόνα 1 έχει σχεδόν εμφανιστεί

                // 4. Poof Image 2 (Emotion)
                tl.to("#shard2", {
                    opacity: 1,
                    scale: 1,
                    duration: 1.0,
                    ease: "back.out(1.2)"
                }, "-=0.8"); // Εμφανίζεται απαλά καθώς πλησιάζει η γραμμή

                // 5. Draw Line 2 -> CALM TRAVEL
                tl.to(line2, {
                    progress: 1,
                    duration: 2.0,
                    ease: "power1.inOut"
                }, "-=0.6");

                // 6. Poof Image 3 (Expression)
                tl.to("#shard3", {
                    opacity: 1,
                    scale: 1,
                    duration: 1.0,
                    ease: "back.out(1.2)"
                }, "-=0.8");

                // 7. Float
                tl.to(".assembly-composition", {
                    y: -10,
                    duration: 6, // Πιο αργή αιώρηση
                    repeat: -1,
                    yoyo: true,
                    ease: "sine.inOut"
                }, "-=0.2");
            }

            runSequence();
        });

        document.addEventListener("DOMContentLoaded", function() {

            gsap.registerPlugin(ScrollTrigger);

            // --- PROCESS LINE ANIMATION ---
            // Ελέγχουμε αν υπάρχει το στοιχείο
            if (document.querySelector('.line-fill')) {

                // Desktop Animation (Horizontal)
                if (window.innerWidth > 991) {
                    gsap.to(".line-fill", {
                        width: "100%", // Γεμίζει οριζόντια
                        duration: 2,
                        ease: "power2.inOut",
                        scrollTrigger: {
                            trigger: ".process-steps-wrapper",
                            start: "top 75%", // Ξεκινάει όταν το section είναι ορατό
                        }
                    });
                }
                // Mobile Animation (Vertical)
                else {
                    gsap.to(".line-fill", {
                        height: "100%", // Γεμίζει κάθετα
                        duration: 2,
                        ease: "power2.inOut",
                        scrollTrigger: {
                            trigger: ".process-steps-wrapper",
                            start: "top 60%",
                        }
                    });
                }
            }

            // Animation για τα βήματα (Fade Up)
            gsap.from(".step-item", {
                y: 40,
                opacity: 0,
                duration: 1,
                stagger: 0.3, // Εμφανίζονται διαδοχικά
                ease: "power2.out",
                scrollTrigger: {
                    trigger: ".process-steps-wrapper",
                    start: "top 75%"
                }
            });


            // --- SERVICES STACKING LOGIC ---

            const cards = gsap.utils.toArray(".service-card-gsap");

            // Calculate offset based on device width (Mobile needs smaller offset)
            const topOffset = window.innerWidth < 991 ? 90 : 140;

            cards.forEach((card, index) => {

                const isLastCard = index === cards.length - 1;

                // Apply pinning to all cards EXCEPT the last one
                if (!isLastCard) {

                    ScrollTrigger.create({
                        trigger: card,
                        start: `top top+=${topOffset}`,

                        // End when the whole section ends
                        endTrigger: ".services-stack-section",
                        end: "bottom bottom",

                        pin: true,
                        pinSpacing: false,
                        id: `pin-${index+1}`,
                    });

                    // Animation: Scale Down & Fade Out
                    // This prevents background bleeding on mobile
                    const scaleAmount = window.innerWidth < 991 ? 0.98 : 0.95;

                    gsap.to(card, {
                        scale: scaleAmount,
                        opacity: 0, // Complete fade out
                        force3D: true, // Hardware acceleration

                        scrollTrigger: {
                            trigger: card,
                            start: `top top+=${topOffset}`,
                            // Fade out completes after scrolling 500px past start
                            end: "+=500",
                            scrub: true,
                        }
                    });
                }
            });

            // Re-calculate positions
            ScrollTrigger.refresh();
        });

        document.addEventListener("DOMContentLoaded", function() {

            const items = document.querySelectorAll('.team-item');
            const cursor = document.getElementById('teamCursor');

            // 1. UNIVERSAL ACCORDION LOGIC (Works everywhere)
            items.forEach(item => {
                const header = item.querySelector('.team-header');

                header.addEventListener('click', () => {
                    const body = item.querySelector('.team-body');
                    const inner = item.querySelector('.body-inner');
                    const isActive = item.classList.contains('active');

                    // Optional: Close others (Auto-collapse)
                    items.forEach(otherItem => {
                        if (otherItem !== item && otherItem.classList.contains('active')) {
                            otherItem.classList.remove('active');
                            otherItem.querySelector('.team-body').style.height = 0;
                        }
                    });

                    // Toggle Current
                    if (isActive) {
                        item.classList.remove('active');
                        body.style.height = 0;
                    } else {
                        item.classList.add('active');
                        body.style.height = inner.clientHeight + 'px';
                    }
                });
            });

            // 2. DESKTOP HOVER IMAGE LOGIC
            if (window.innerWidth > 991) {

                let mouseX = 0;
                let mouseY = 0;

                // Move the cursor container
                document.addEventListener('mousemove', (e) => {
                    mouseX = e.clientX;
                    mouseY = e.clientY;

                    if (cursor) {
                        gsap.to(cursor, {
                            x: mouseX - 150, // Half width
                            y: mouseY - 200, // Half height
                            duration: 0.5,
                            ease: "power2.out"
                        });
                    }
                });

                // Show image on Header Hover ONLY
                items.forEach(item => {
                    const header = item.querySelector('.team-header');

                    header.addEventListener('mouseenter', () => {
                        if (cursor) {
                            cursor.classList.add('active');
                            const imgClass = item.getAttribute('data-img');
                            const targetImg = cursor.querySelector(`.${imgClass}`);

                            // Reset others
                            cursor.querySelectorAll('.team-img').forEach(img => img.classList.remove('active'));

                            // Activate target
                            if (targetImg) targetImg.classList.add('active');
                        }
                    });

                    header.addEventListener('mouseleave', () => {
                        // Hide when leaving header (so text is readable in body)
                        if (cursor) cursor.classList.remove('active');
                    });
                });
            }
        });
    </script>
<?php
}
?>
<?php
if (!isset($GLOBAL_INCLUDE_CHECK)) die(header('location:  /'));
?>
<!-- 29c1c2 -->

<style>
    :root {
        --text-color: #ffffff;
        --accent-color: #29c1c2;
        /* Mint Green */
        --dark-bg: #0f172a;
    }

    /* HERO WRAPPER */
    .hero-wrapper {
        position: relative;
        height: 100vh;
        width: 100%;
        overflow: hidden;
        background-color: var(--dark-bg);
        display: flex;
        align-items: center;
        justify-content: center;
        padding-top: 80px;
    }

    /* BACKGROUND IMAGE (Bright Beach) */
    .hero-bg-image {
        position: absolute;
        top: -10%;
        left: -10%;
        width: 120%;
        height: 120%;
        background-image: url('/assets/images/used/hero.jpg');
        background-size: cover;
        background-position: center;
        /* Κρατάμε λίγο brightness drop για να ξεχωρίζουν τα λευκά γράμματα */
        filter: brightness(0.7) contrast(1.1);
        z-index: 1;
        will-change: transform;
    }

    /* GRAIN */
    .grain-overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 200 200' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='noiseFilter'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.65' numOctaves='3' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23noiseFilter)' opacity='0.05'/%3E%3C/svg%3E");
        pointer-events: none;
        z-index: 2;
        mix-blend-mode: overlay;
    }

    /* GRID LINES */
    .grid-lines {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        z-index: 5;
        pointer-events: none;
        display: flex;
        justify-content: space-between;
        padding: 0 15%;
    }

    .line {
        width: 1px;
        height: 100%;
        background: rgba(255, 255, 255, 0.08);
        /* Λίγο πιο έντονο για να φαίνεται στον ήλιο */
    }

    /* UI LAYER */
    .hero-ui-layer {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        z-index: 15;
        pointer-events: none;
        padding: 40px;
    }

    .ui-data {
        font-family: 'Courier New', monospace;
        font-size: 0.75rem;
        color: rgba(255, 255, 255, 0.8);
        /* Πιο λευκό για contrast */
        text-transform: uppercase;
        letter-spacing: 2px;
        position: absolute;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .ui-top-left {
        top: 120px;
        left: 40px;
    }

    .ui-bottom-left {
        bottom: 40px;
        left: 40px;
    }

    .status-dot {
        width: 8px;
        height: 8px;
        background-color: var(--accent-color);
        border-radius: 50%;
        box-shadow: 0 0 10px var(--accent-color);
        animation: pulse 2s infinite;
    }

    @keyframes pulse {
        0% {
            opacity: 1;
            transform: scale(1);
        }

        50% {
            opacity: 0.5;
            transform: scale(0.8);
        }

        100% {
            opacity: 1;
            transform: scale(1);
        }
    }

    /* CONTAINER */
    .hero-container {
        position: relative;
        z-index: 10;
        width: 100%;
        max-width: 1600px;
        padding: 0 20px;
        display: flex;
        flex-direction: column;
        justify-content: end;
        align-items: start;
        text-align: start;
        padding-top: 15vh;
    }

    .text-reveal-wrapper {
        margin-bottom: -1vh;
        overflow: hidden;
    }

    .hero-badge-pill {
        display: inline-flex;
        align-items: center;
        padding: 6px 16px;
        margin-bottom: 20px;
        border: 1px solid rgba(255, 255, 255, 0.3);
        border-radius: 50px;
        font-size: 0.8rem;
        color: white;
        background: rgba(0, 0, 0, 0.3);
        backdrop-filter: blur(5px);
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    .hero-badge-pill i {
        color: var(--accent-color);
        margin-right: 8px;
    }

    .hero-big-text {
        font-size: clamp(3.5rem, 10vw, 7rem);
        font-weight: 800;
        line-height: 1;
        text-transform: uppercase;
        color: white;
        letter-spacing: -0.03em;
        transform: translateY(120%);
        display: block;
        white-space: nowrap;
    }

    .hero-big-text span {
        color: transparent;
        -webkit-text-stroke: 2px rgba(255, 255, 255, 0.6);
    }

    .hero-big-text.highlight {
        color: var(--accent-color);
        -webkit-text-stroke: 0;
    }

    .hero-sub {
        font-size: clamp(1rem, 1.2vw, 1.25rem);
        color: rgba(255, 255, 255, 0.9);
        max-width: 550px;
        margin-top: 30px;
        line-height: 1.6;
        opacity: 0;
        transform: translateY(20px);
    }

    /* BUTTON */
    .magnetic-wrap {
        display: inline-block;
        position: relative;
        margin-top: 50px;
        z-index: 20;
    }

    .btn-magnetic {
        position: relative;
        display: flex;
        align-items: center;
        justify-content: center;
        width: 160px;
        height: 160px;
        border: 1px solid rgba(255, 255, 255, 0.4);
        border-radius: 50%;
        color: white;
        font-weight: 700;
        text-transform: uppercase;
        font-size: 1rem;
        background: rgba(0, 0, 0, 0.2);
        backdrop-filter: blur(5px);
        transition: all 0.3s ease;
        text-decoration: none;
        overflow: hidden;
    }

    .btn-fill {
        position: absolute;
        top: 50%;
        left: 50%;
        width: 0%;
        height: 0%;
        background: var(--accent-color);
        border-radius: 50%;
        transform: translate(-50%, -50%);
        z-index: -1;
        transition: width 0.6s ease, height 0.6s ease;
    }

    .btn-magnetic:hover {
        border-color: var(--accent-color);
        transform: scale(1.05);
    }

    .btn-magnetic:hover .btn-fill {
        width: 150%;
        height: 150%;
    }

    .btn-text {
        position: relative;
        z-index: 2;
    }

    /* ========================
       DARK GLASS CARD (FIXED VISIBILITY)
       ======================== */
    .hero-featured-card {
        position: absolute;
        bottom: 40px;
        right: 40px;
        background: rgb(127 144 145 / 4%);
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        border-top: 1px solid rgba(255, 255, 255, 0.4);
        padding: 20px 25px;
        border-radius: 8px;
        min-width: 260px;
        z-index: 100;
        cursor: pointer;
        text-align: left;
        pointer-events: auto;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.4);
        opacity: 0;
    }

    .hero-featured-card:hover {
        background: rgb(15 23 42 / 8%);
        transform: translateY(-5px);
        border-color: var(--accent-color);
    }

    .feat-content-wrap {
        opacity: 1;
        transition: opacity 0.4s ease;
    }

    .feat-content-wrap.fade-out {
        opacity: 0;
    }

    .feat-label {
        font-size: 0.7rem;
        color: var(--accent-color);
        text-transform: uppercase;
        letter-spacing: 1.5px;
        margin-bottom: 8px;
        font-weight: 700;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .feat-title {
        font-size: 1.1rem;
        font-weight: 700;
        color: white;
        margin: 0;
        line-height: 1.3;
    }

    .feat-meta {
        font-size: 0.8rem;
        color: rgba(255, 255, 255, 0.6);
        margin-top: 5px;
    }

    .scroll-line {
        position: absolute;
        bottom: 0;
        right: 50%;
        width: 1px;
        height: 80px;
        background: rgba(255, 255, 255, 0.2);
        overflow: hidden;
        z-index: 3;
    }

    .scroll-line::after {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: var(--accent-color);
        transform: translateY(-100%);
        animation: scrollDrop 2.5s cubic-bezier(0.77, 0, 0.175, 1) infinite;
    }

    @keyframes scrollDrop {
        0% {
            transform: translateY(-100%);
        }

        50% {
            transform: translateY(0%);
        }

        100% {
            transform: translateY(100%);
        }
    }

    @media (max-width: 768px) {

        .grid-lines,
        .ui-top-left {
            display: none;
        }

        .hero-featured-card {
            bottom: 20px;
            right: 20px;
            left: 20px;
            min-width: auto;
        }

        .ui-bottom-left {
            top: 100px;
            bottom: auto;
            left: 20px;
        }

        .btn-magnetic {
            width: 120px;
            height: 120px;
            font-size: 0.9rem;
        }
    }
</style>

<section class="hero-wrapper" id="heroSection">
    <div class="grain-overlay"></div>
    <div class="hero-bg-image" id="heroBg"></div>

    <div class="grid-lines">
        <div class="line"></div>
        <div class="line"></div>
        <div class="line"></div>
    </div>

    <div class="hero-ui-layer">
        <div class="ui-data ui-top-left">
            <i class="bi bi-geo-alt"></i> 35.19° N, 24.39° E
        </div>
        <div class="ui-data ui-bottom-left">
            <div class="status-dot"></div>
            <span>LIVE: SUNDAY RUN OPEN</span>
        </div>
    </div>

    <div class="hero-container">
        <div class="text-reveal-wrapper">
            <div class="hero-badge-pill">
                <i class="bi bi-trophy-fill"></i> Crete's Finest Trails
            </div>
        </div>
        <div class="text-reveal-wrapper">
            <h1 class="hero-big-text">DISCOVER</h1>
        </div>
        <div class="text-reveal-wrapper">
            <h1 class="hero-big-text"><span>PLAKIAS</span></h1>
        </div>
        <div class="text-reveal-wrapper">
            <h1 class="hero-big-text highlight">RUNNING.</h1>
        </div>
        <p class="hero-sub">
            Join the movement. Exclusive trails, small groups, and the raw beauty of Crete.
        </p>
        <div class="magnetic-wrap" id="magneticBtnWrap">
            <a href="/book" class="btn-magnetic" id="magneticBtn">
                <span class="btn-text">Book Run</span>
                <div class="btn-fill"></div>
            </a>
        </div>
    </div>

    <a href="/routes" class="hero-featured-card" id="heroCard">
        <div class="feat-content-wrap" id="cardContent">
            <div class="feat-label">
                <span id="cardLabel">TRENDING</span> <i class="bi bi-arrow-right-short"></i>
            </div>
            <h3 class="feat-title" id="cardTitle">Sunset Gorge Run</h3>
            <div class="feat-meta" id="cardMeta">ONLY 3 SPOTS LEFT</div>
        </div>
    </a>

    <div class="scroll-line"></div>
</section>



<style>
    /* ========================
   SECTION 2: MARQUEE (The Energy Bar)
   ======================== */
    .marquee-section {
        position: relative;
        background: var(--accent-color);
        padding: 20px 0;
        overflow: hidden;
        z-index: 5;
        transform: skewY(-2deg);
        margin-top: -36px;
        /* Καβαλάει λίγο το Hero */
        border-top: 1px solid rgba(255, 255, 255, 0.2);
        border-bottom: 1px solid rgba(255, 255, 255, 0.2);
    }

    .marquee-content {
        display: flex;
        width: fit-content;
        animation: scrollText 20s linear infinite;
    }

    .marquee-item {
        font-size: 2.5rem;
        font-weight: 800;
        color: #0f172a;
        /* Dark text on Green */
        text-transform: uppercase;
        white-space: nowrap;
        padding: 0 40px;
        display: flex;
        align-items: center;
        letter-spacing: -1px;
    }

    .marquee-item i {
        font-size: 1rem;
        margin: 0 20px;
        opacity: 0.6;
    }

    @keyframes scrollText {
        0% {
            transform: translateX(0);
        }

        100% {
            transform: translateX(-50%);
        }
    }

    /* ========================
   SECTION 3: THE MANIFESTO (Intro)
   ======================== */
    .intro-section {
        position: relative;
        background-color: #f8fafc;
        /* Light Clean Background */
        color: #0f172a;
        padding: 150px 0 100px 0;
        z-index: 4;
    }

    .intro-container {
        max-width: 1400px;
        margin: 0 auto;
        padding: 0 20px;
        display: grid;
        grid-template-columns: 1.2fr 0.8fr;
        /* Asymmetrical Layout */
        gap: 80px;
        align-items: center;
    }

    /* Left: Typography */
    .intro-text-wrap h2 {
        font-size: clamp(2.5rem, 5vw, 5rem);
        line-height: 1;
        font-weight: 800;
        text-transform: uppercase;
        margin-bottom: 30px;
        letter-spacing: -0.03em;
    }

    .intro-text-wrap h2 span {
        display: block;
        color: transparent;
        -webkit-text-stroke: 1px #0f172a;
        /* Outline effect */
    }

    .intro-desc {
        font-size: 1.1rem;
        line-height: 1.7;
        color: #64748b;
        max-width: 500px;
        margin-bottom: 40px;
    }

    /* Feature List (Icons) */
    .intro-features {
        display: flex;
        gap: 30px;
        border-top: 1px solid #e2e8f0;
        padding-top: 30px;
    }

    .feat-box h4 {
        font-size: 1.5rem;
        font-weight: 800;
        margin: 0 0 5px 0;
    }

    .feat-box p {
        font-size: 0.85rem;
        color: #64748b;
        margin: 0;
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    /* Right: Image Reveal */
    .intro-img-wrap {
        position: relative;
        height: 600px;
        width: 100%;
        overflow: hidden;
        border-radius: 12px;
    }

    .intro-img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transform: scale(1.2);
        /* Zoomed in αρχικά */
    }

    /* The Curtain (Mask) */
    .img-curtain {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: var(--accent-color);
        z-index: 2;
        transform-origin: bottom;
    }

    /* Mobile */
    @media (max-width: 992px) {
        .intro-container {
            grid-template-columns: 1fr;
            gap: 50px;
        }

        .intro-img-wrap {
            height: 400px;
        }

        .marquee-section {
            transform: skewY(0deg);
            margin-top: 0;
        }
    }
</style>
<div class="marquee-section">
    <div class="marquee-content">
        <div class="marquee-item">EXPLORE THE UNSEEN <i class="bi bi-circle-fill"></i></div>
        <div class="marquee-item">RUN WITH SAFETY <i class="bi bi-circle-fill"></i></div>
        <div class="marquee-item">SMALL GROUPS <i class="bi bi-circle-fill"></i></div>
        <div class="marquee-item">EXPLORE THE UNSEEN <i class="bi bi-circle-fill"></i></div>
        <div class="marquee-item">RUN WITH SAFETY <i class="bi bi-circle-fill"></i></div>
        <div class="marquee-item">SMALL GROUPS <i class="bi bi-circle-fill"></i></div>
    </div>
</div>

<section class="intro-section">
    <div class="intro-container">

        <div class="intro-text-wrap">
            <h2 class="reveal-text">
                More Than <br>
                <span>Just Running.</span>
            </h2>
            <p class="intro-desc reveal-opacity">
                We believe running is the best way to see the world.
                Escape the treadmill and discover Plakias through hidden trails,
                coastal paths, and local history.
            </p>

            <div class="intro-features reveal-opacity">
                <div class="feat-box">
                    <h4 style="color: var(--accent-color);">MAX 10</h4>
                    <p>Runners per Group</p>
                </div>
                <div class="feat-box">
                    <h4>100%</h4>
                    <p>Safety & Fun</p>
                </div>
            </div>

            <a href="/about" class="btn-magnetic mt-5 reveal-opacity" style="border-color: #0f172a; color: #0f172a; width: 140px; height: 140px;">
                <span class="btn-text">Our Story</span>
                <div class="btn-fill"></div>
            </a>
        </div>

        <div class="intro-img-wrap">
            <div class="img-curtain"></div> <img src="/assets/images/used/use1.png" alt="Trail Running" class="intro-img">
        </div>

    </div>
</section>


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


    <script>
        document.addEventListener("DOMContentLoaded", () => {

            // 1. Text Animations
            gsap.to(".hero-big-text", {
                y: 0,
                duration: 1.4,
                stagger: 0.15,
                ease: "power4.out",
                delay: 0.2
            });

            gsap.to(".hero-sub", {
                opacity: 1,
                y: 0,
                duration: 1.4,
                delay: 1,
                ease: "power2.out"
            });

            gsap.from(".btn-magnetic", {
                scale: 0,
                opacity: 0,
                duration: 1,
                delay: 1.2,
                ease: "elastic.out(1, 0.5)"
            });

            // 2. UI Elements Animation (ΞΕΧΩΡΙΣΤΑ ΓΙΑ ΤΗΝ ΚΑΡΤΑ)
            gsap.from(".ui-data, .grid-lines, .hero-badge-pill", {
                opacity: 0,
                duration: 2,
                delay: 1.5,
                ease: "power2.out"
            });

            // *** FORCE CARD VISIBILITY ***
            gsap.to(".hero-featured-card", {
                opacity: 1,
                y: 0,
                duration: 1,
                delay: 1.8,
                ease: "power2.out",
                startAt: {
                    y: 20,
                    opacity: 0
                } // Ορίζουμε ρητά το αρχικό state
            });

            // 3. Parallax
            const heroBg = document.getElementById('heroBg');
            document.addEventListener('mousemove', (e) => {
                const x = (e.clientX / window.innerWidth - 0.5) * 15;
                const y = (e.clientY / window.innerHeight - 0.5) * 15;
                gsap.to(heroBg, {
                    x: -x,
                    y: -y,
                    duration: 1,
                    ease: "power2.out"
                });
            });

            // 4. Magnetic Button
            const btnWrap = document.getElementById('magneticBtnWrap');
            const btn = document.getElementById('magneticBtn');
            if (btnWrap && btn) {
                btnWrap.addEventListener('mousemove', (e) => {
                    const rect = btnWrap.getBoundingClientRect();
                    const x = e.clientX - rect.left - rect.width / 2;
                    const y = e.clientY - rect.top - rect.height / 2;
                    gsap.to(btn, {
                        x: x * 0.4,
                        y: y * 0.4,
                        duration: 0.3
                    });
                });
                btnWrap.addEventListener('mouseleave', () => {
                    gsap.to(btn, {
                        x: 0,
                        y: 0,
                        duration: 0.5,
                        ease: "elastic.out(1, 0.3)"
                    });
                });
            }

            // 5. Card Rotator
            const cardData = [{
                    label: "TRENDING",
                    title: "Sunset Gorge Run",
                    meta: "ONLY 3 SPOTS LEFT"
                },
                {
                    label: "POPULAR",
                    title: "Coastal Morning",
                    meta: "BEGINNER FRIENDLY"
                },
                {
                    label: "ADVENTURE",
                    title: "Mountain Trail",
                    meta: "SUNDAY 08:00 AM"
                }
            ];
            let currentIndex = 0;
            const labelEl = document.getElementById('cardLabel');
            const titleEl = document.getElementById('cardTitle');
            const metaEl = document.getElementById('cardMeta');
            const contentWrap = document.getElementById('cardContent');

            if (contentWrap) {
                setInterval(() => {
                    contentWrap.classList.add('fade-out');
                    setTimeout(() => {
                        currentIndex = (currentIndex + 1) % cardData.length;
                        const item = cardData[currentIndex];
                        labelEl.textContent = item.label;
                        titleEl.textContent = item.title;
                        metaEl.textContent = item.meta;
                        contentWrap.classList.remove('fade-out');
                    }, 400);
                }, 5000);
            }
        });

        document.addEventListener("DOMContentLoaded", () => {
            gsap.registerPlugin(ScrollTrigger);

            // 1. Reveal Heading (Lines coming up)
            gsap.from(".reveal-text", {
                scrollTrigger: {
                    trigger: ".intro-section",
                    start: "top 80%", // Όταν το section φτάσει στο 80% της οθόνης
                },
                y: 100,
                opacity: 0,
                duration: 1,
                ease: "power4.out"
            });

            // 2. Reveal Paragraphs & Stats
            gsap.from(".reveal-opacity", {
                scrollTrigger: {
                    trigger: ".intro-section",
                    start: "top 70%",
                },
                y: 30,
                opacity: 0,
                duration: 1,
                stagger: 0.2,
                delay: 0.3
            });

            // 3. IMAGE REVEAL (The Curtain Effect)
            // Πρώτα φεύγει η κουρτίνα (curtain) προς τα πάνω
            gsap.to(".img-curtain", {
                scrollTrigger: {
                    trigger: ".intro-img-wrap",
                    start: "top 75%",
                },
                height: "0%", // Μαζεύει προς τα πάνω
                duration: 1.2,
                ease: "power3.inOut"
            });

            // Ταυτόχρονα η εικόνα κάνει ελαφρύ zoom out (scale down) για βάθος
            gsap.to(".intro-img", {
                scrollTrigger: {
                    trigger: ".intro-img-wrap",
                    start: "top 75%",
                    scrub: 1, // Συνδέεται με το scroll!
                },
                scale: 1, // Από 1.2 πάει στο 1
                ease: "none"
            });
        });
    </script>


<?php
}
?>
<?php
if (!isset($GLOBAL_INCLUDE_CHECK)) die(header('location:  /'));
?>
<!-- 29c1c2 -->

<style>
    :root {
        --text-color: #ffffff;
        /* --accent-color: #29c1c2; */
        --accent-color: #34cdce;
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
        padding-top: 120px;
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
        border-left: 3px solid rgb(230 105 38 / 69%);
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
        color: #fff8dc;
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


<style>
    /* ========================
   SECTION: ROUTES SHOWCASE (CLEAN)
   ======================== */
    .routes-section {
        padding: 120px 0;
        background-color: #f8fafc;
        /* Απαλό, καθαρό γκρι */
        position: relative;
        overflow: hidden;
    }

    .section-header {
        max-width: 1600px;
        margin: 0 auto 60px auto;
        padding: 0 40px;
        display: flex;
        justify-content: space-between;
        align-items: flex-end;
    }

    .section-title {
        font-size: clamp(2.5rem, 5vw, 4.5rem);
        font-weight: 800;
        line-height: 1;
        text-transform: uppercase;
        color: var(--run-dark, #0f172a);
        margin: 0;
        letter-spacing: -0.03em;
    }

    .section-title span {
        color: transparent;
        -webkit-text-stroke: 1px var(--run-dark, #0f172a);
    }

    .section-subtitle {
        font-size: 1.1rem;
        color: #64748b;
        max-width: 400px;
        margin: 0;
        text-align: right;
    }

    /* ========================
   SWIPER SLIDER ADAPTATION
   ======================== */
    .swiper-container {
        width: 100%;
        padding: 0 40px 60px 40px;
        /* Χώρος για σκιές και margin */
        overflow: visible;
        /* ΣΗΜΑΝΤΙΚΟ: Για να φαίνονται οι κάρτες που βγαίνουν έξω από την οθόνη */
    }

    /* Fix για να μην κόβονται οι σκιές αριστερά-δεξιά */
    .routes-section {
        overflow: hidden;
        /* Αυτό παραμένει στο section */
    }

    .swiper-wrapper {
        align-items: center;
        /* Κεντράρει τις κάρτες κάθετα αν χρειαστεί */
    }

    .swiper-slide {
        width: auto;
        /* Επιτρέπει στην κάρτα να πάρει το πλάτος της (.route-card) */
        height: auto;
    }

    /* Η Κάρτα σου (Παραμένει Ίδια, απλά αφαιρέσαμε το flex: 0 0 auto) */
    .route-card {
        width: 380px;
        height: 520px;
        border-radius: 24px;
        position: relative;
        overflow: hidden;
        background: #e2e8f0;
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.06);
        user-select: none;
        /* transition για ομαλό scale αν θέλουμε effect στο ενεργό slide */
        transition: transform 0.4s ease;
    }

    /* CLEAN ROUTE CARD */
    .route-card {
        flex: 0 0 auto;
        width: 380px;
        height: 520px;
        border-radius: 24px;
        position: relative;
        overflow: hidden;
        scroll-snap-align: start;
        background: #e2e8f0;
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.06);
        /* Disable text selection while dragging */
        user-select: none;
    }

    .route-img {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.6s ease;
    }

    /* White Info Panel at the bottom */
    .route-info-panel {
        position: absolute;
        bottom: -60px;
        left: 0;
        width: 100%;
        background: #3737379e;
        padding: 30px;
        border-radius: 20px 20px 0 0;
        transform: translateY(145px);
        transition: transform 0.4s cubic-bezier(0.19, 1, 0.22, 1);
    }

    .route-badge {
        background: var(--accent-color, #29c285);
        color: #fff;
        padding: 4px 10px;
        border-radius: 4px;
        font-size: 0.65rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 1px;
        display: inline-block;
        margin-bottom: 12px;
    }

    .route-name {
        color: var(--run-light, #efefef);
        font-size: 1.8rem;
        font-weight: 800;
        margin: 0;
        line-height: 1.1;
    }

    /* Specs (Hidden by default) */
    .route-specs {
        margin-top: 25px;
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    .spec-item {
        display: flex;
        justify-content: space-between;
        border-bottom: 1px solid #f1f5f9;
        padding: 10px 0;
        color: #dedede;
        font-size: 0.85rem;
        text-transform: uppercase;
        font-weight: 600;
    }

    .spec-item span {
        font-weight: 800;
        color: var(--run-gray);
    }

    .route-book-btn {
        display: block;
        width: 100%;
        text-align: center;
        background: var(--run-blue-dark);
        color: white;
        padding: 14px 0;
        border-radius: 8px;
        font-weight: 700;
        text-transform: uppercase;
        margin-top: 20px;
        text-decoration: none;
        transition: background 0.3s;
    }

    .route-book-btn:hover {
        background: var(--run-blue);
        color: #fff;
    }

    /* --- HOVER EFFECTS --- */
    .route-card:hover .route-img {
        transform: scale(1.05);
    }

    .route-card:hover .route-info-panel {
        transform: translateY(-60px);
    }

    .route-card:hover .route-specs {
        opacity: 1;
        transition-delay: 0.1s;
    }


    /* ========================
   SECTION: VIP PRIVATE SESSION (LIGHT & CLEAN)
   ======================== */
    .vip-section {
        background-color: #ffffff;
        /* Ολόλευκο, καθαρό */
        padding: 120px 40px;
        color: var(--run-dark, #0f172a);
        position: relative;
        border-top: 1px solid #f1f5f9;
    }

    .vip-container {
        max-width: 1400px;
        margin: 0 auto;
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 80px;
        align-items: center;
    }

    .vip-img-wrap {
        position: relative;
        border-radius: 24px;
        overflow: hidden;
        height: 650px;
        box-shadow: 0 20px 50px rgba(0, 0, 0, 0.08);
        /* Απαλή σκιά */
    }

    .vip-img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    /* Ένα διακριτικό πράσινο στοιχείο διακόσμησης */
    .vip-accent-shape {
        position: absolute;
        bottom: -20px;
        left: -20px;
        width: 200px;
        height: 200px;
        background: var(--accent-color, #29c285);
        border-radius: 50%;
        z-index: -1;
        opacity: 0.1;
    }

    .vip-tag {
        font-family: 'Courier New', monospace;
        color: var(--accent-color, #29c285);
        letter-spacing: 2px;
        text-transform: uppercase;
        margin-bottom: 20px;
        display: block;
        font-weight: 600;
    }

    .vip-title {
        font-size: clamp(2.5rem, 4vw, 4rem);
        font-weight: 800;
        line-height: 1.1;
        margin-bottom: 30px;
        letter-spacing: -0.02em;
    }

    .vip-desc {
        font-size: 1.1rem;
        line-height: 1.7;
        color: #64748b;
        margin-bottom: 40px;
        max-width: 500px;
    }

    .vip-features {
        list-style: none;
        padding: 0;
        margin: 0 0 50px 0;
    }

    .vip-features li {
        display: flex;
        align-items: center;
        gap: 15px;
        margin-bottom: 15px;
        font-size: 1rem;
        font-weight: 700;
        color: var(--run-dark, #0f172a);
    }

    .vip-features i {
        color: var(--accent-color, #29c285);
        font-size: 1.4rem;
    }

    /* Light Theme Magnetic Button */
    .btn-magnetic-light {
        position: relative;
        display: flex;
        align-items: center;
        justify-content: center;
        width: 150px;
        height: 150px;
        border: 1px solid #cbd5e1;
        border-radius: 50%;
        color: var(--run-dark, #0f172a);
        font-weight: 700;
        text-transform: uppercase;
        background: transparent;
        transition: all 0.3s ease;
        overflow: hidden;
        text-decoration: none;
    }

    .btn-fill-light {
        position: absolute;
        top: 50%;
        left: 50%;
        width: 0%;
        height: 0%;
        background: var(--run-dark, #0f172a);
        border-radius: 50%;
        transform: translate(-50%, -50%);
        transition: width 0.5s ease, height 0.5s ease;
        z-index: -1;
    }

    .btn-magnetic-light:hover {
        color: white;
        border-color: var(--run-dark, #0f172a);
    }

    .btn-magnetic-light:hover .btn-fill-light {
        width: 150%;
        height: 150%;
    }

    /* Responsive Adjustments */
    @media (max-width: 992px) {
        .section-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 15px;
            padding: 0 20px;
        }

        .section-subtitle {
            text-align: left;
        }

        .routes-slider {
            padding: 0 20px 40px 20px;
            gap: 15px;
        }

        .route-card {
            width: 320px;
            height: 480px;
        }

        .vip-container {
            grid-template-columns: 1fr;
            gap: 50px;
        }

        .vip-section {
            padding: 80px 20px;
        }

        .vip-img-wrap {
            height: 400px;
            order: -1;
        }
    }

    /* ========================
   SWIPER PAGINATION & HINTS
   ======================== */

    /* Το animated hint (κρύβεται στο desktop) */
    .mobile-swipe-hint {
        display: none;
        text-align: center;
        font-size: 0.75rem;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 2px;
        margin-bottom: 20px;
        font-weight: 600;
    }

    .mobile-swipe-hint i {
        display: inline-block;
        margin-right: 5px;
        animation: swipeAnim 1.5s infinite ease-in-out;
    }

    @keyframes swipeAnim {

        0%,
        100% {
            transform: translateX(0);
        }

        50% {
            transform: translateX(-5px);
        }
    }

    /* Custom Premium Pagination Dots */
    .swiper-pagination {
        position: relative !important;
        margin-top: 30px;
        bottom: 0 !important;
    }

    .swiper-pagination-bullet {
        background: #cbd5e1;
        opacity: 0.6;
        width: 8px;
        height: 8px;
        transition: all 0.3s ease;
    }

    .swiper-pagination-bullet-active {
        background: var(--accent-color, #29c285);
        opacity: 1;
        width: 24px;
        /* Το ενεργό dot γίνεται μακρόστενο (Premium effect) */
        border-radius: 4px;
    }

    /* ========================
   MOBILE FIXES FOR SLIDER
   ======================== */
    @media (max-width: 992px) {
        .mobile-swipe-hint {
            display: block;
            /* Εμφανίζεται μόνο στο κινητό */
        }

        .routes-slider {
            padding: 0 0 20px 0;
            /* Αφαιρούμε το padding γιατί το κεντράρει το Swiper */
        }

        .route-card {
            width: 80vw;
            /* Η κάρτα πιάνει το 80% της οθόνης, ώστε να φαίνονται οι δίπλα */
            height: 480px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
        }
    }

    /* ========================
   SECTION: ROUTES SHOWCASE (ELEVATED)
   ======================== */
    .routes-section {
        padding: 140px 0 120px 0;
        /* Λίγο περισσότερος χώρος πάνω */
        background-color: #f8fafc;
        position: relative;
        overflow: hidden;
        /* Κρατάμε τα πάντα μέσα */
    }

    /* 1. TOPO BACKGROUND & OVERSIZED TEXT */
    .bg-oversized-text {
        position: absolute;
        top: 15%;
        left: -5%;
        font-size: 28vw;
        /* ΤΕΡΑΣΤΙΑ ΓΡΑΜΜΑΤΑ */
        font-weight: 900;
        line-height: 1;
        color: #0f172a;
        opacity: 0.02;
        /* Σχεδόν αόρατο, μοιάζει με watermark */
        white-space: nowrap;
        pointer-events: none;
        z-index: 0;
        user-select: none;
        will-change: transform;
    }

    .topo-pattern {
        position: absolute;
        top: 0;
        right: 0;
        width: 60%;
        height: 100%;
        /* Ένα minimal inline SVG pattern που μοιάζει με υψομετρικές καμπύλες / Grid */
        background-image: radial-gradient(circle at center, transparent 0%, #f8fafc 70%), repeating-radial-gradient(circle at 100% 0%, transparent 0, transparent 40px, rgba(15, 23, 42, 0.03) 40px, rgba(15, 23, 42, 0.03) 41px);
        z-index: 1;
        pointer-events: none;
    }

    /* 2. TECHNICAL HEADER */
    .section-header {
        position: relative;
        z-index: 5;
        /* Πάνω από το background */
        max-width: 1600px;
        margin: 0 auto 60px auto;
        padding: 0 40px;
        display: flex;
        justify-content: space-between;
        align-items: flex-end;
    }

    /* Tech Badges */
    .tech-badge {
        font-family: 'Courier New', monospace;
        font-size: 0.75rem;
        font-weight: 700;
        color: var(--accent-color, #29c285);
        letter-spacing: 2px;
        margin-bottom: 15px;
        display: block;
    }

    .tech-divider {
        width: 60px;
        height: 3px;
        background: var(--run-dark, #0f172a);
        margin: 20px 0;
    }

    .tech-counter {
        font-family: 'Courier New', monospace;
        font-size: 0.8rem;
        color: #64748b;
        letter-spacing: 1px;
        text-transform: uppercase;
        text-align: right;
        padding-bottom: 10px;
        border-bottom: 1px solid #cbd5e1;
    }

    /* 3. CUSTOM DRAG CURSOR */
    .routes-slider {
        cursor: none !important;
        /* Κρύβουμε τον κλασικό κέρσορα όταν είσαι πάνω στο slider */
        position: relative;
        z-index: 10;
    }

    .custom-drag-cursor {
        position: fixed;
        /* Ακολουθεί την οθόνη */
        top: 0;
        left: 0;
        width: 80px;
        height: 80px;
        background-color: var(--accent-color, #29c285);
        color: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.75rem;
        font-weight: 800;
        letter-spacing: 1px;
        pointer-events: none;
        /* Δεν μπλοκάρει τα κλικ */
        z-index: 9999;
        opacity: 0;
        transform: scale(0);
        /* Κρυμμένο αρχικά */
        transition: transform 0.2s ease, background 0.2s ease;
        /* Blend mode για να κάνει ωραίο effect πάνω από τις φώτος */
        mix-blend-mode: hard-light;
    }

    .custom-drag-cursor.active {
        transform: scale(0.8) !important;
        background-color: white;
        color: black;
    }

    /* (Τα υπόλοιπα styles των καρτών παραμένουν ίδια) */
    @media (max-width: 992px) {
        .bg-oversized-text {
            font-size: 40vw;
            top: 5%;
        }

        .tech-counter,
        .custom-drag-cursor {
            display: none;
        }

        /* Κρύβουμε τον cursor στα κινητά */
        .routes-slider {
            cursor: grab !important;
        }

        /* Επαναφέρουμε το native swipe hint */
    }
</style>
<section class="routes-section" id="routes">
    <div class="topo-pattern"></div>
    <div class="bg-oversized-text" id="bgTextParallax">EXPLORE</div>

    <div class="section-header">
        <div>
            <span class="tech-badge gs-reveal">[ 02 // THE STAGES ]</span>
            <h2 class="section-title gs-reveal">CHOOSE YOUR <br><span>PATH.</span></h2>
            <div class="tech-divider gs-reveal"></div>
            <p class="section-subtitle gs-reveal">
                From easy coastal breezes to demanding mountain technical trails.
                All groups are limited to 10 runners.
            </p>
        </div>

        <div class="tech-counter gs-reveal">
            AVAILABLE ROUTES: 04 <br>
            <span style="font-size: 0.65rem; opacity: 0.5;">SCROLL TO DISCOVER</span>
        </div>
    </div>

    <div class="swiper swiper-container gs-reveal">
        <div class="mobile-swipe-hint">
            <i class="bi bi-arrow-left-right"></i> Swipe to explore
        </div>

        <div class="swiper-wrapper">

            <div class="swiper-slide">
                <div class="route-card">
                    <img src="https://images.unsplash.com/photo-1502904550040-7534597429ae?q=80&w=800&auto=format&fit=crop" alt="Coastal Run" class="route-img" draggable="false">
                    <div class="route-info-panel">
                        <span class="route-badge">BEGINNER</span>
                        <h3 class="route-name">Coastal Escape</h3>

                        <div class="route-specs">
                            <div class="spec-item">Terrain <span>Asphalt & Path</span></div>
                            <div class="spec-item">Distance <span>5km / 10km</span></div>
                            <div class="spec-item">Elevation <span>Flat</span></div>
                            <a href="/book?route=coastal" class="route-book-btn">Book Run</a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="swiper-slide">
                <div class="route-card">
                    <img src="https://images.unsplash.com/photo-1476480862126-209bfaa8edc8?q=80&w=800&auto=format&fit=crop" alt="Mountain Trail" class="route-img" draggable="false">
                    <div class="route-info-panel">
                        <span class="route-badge" style="background: #e63946;">ADVANCED</span>
                        <h3 class="route-name">Mountain Trail</h3>

                        <div class="route-specs">
                            <div class="spec-item">Terrain <span>Rocky / Technical</span></div>
                            <div class="spec-item">Distance <span>8km</span></div>
                            <div class="spec-item">Elevation <span>High Gain</span></div>
                            <a href="/book?route=mountain" class="route-book-btn">Book Run</a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="swiper-slide">
                <div class="route-card">
                    <img src="https://plus.unsplash.com/premium_photo-1698513454953-dd944ad9f1a9?q=80&w=774&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D" alt="Sunset Gorge" class="route-img" draggable="false">
                    <div class="route-info-panel">
                        <span class="route-badge" style="background: #ffb703; color: #000;">TRENDING</span>
                        <h3 class="route-name">Sunset Gorge</h3>

                        <div class="route-specs">
                            <div class="spec-item">Terrain <span>Mixed</span></div>
                            <div class="spec-item">Distance <span>6km</span></div>
                            <div class="spec-item">Vibe <span>Nature & Views</span></div>
                            <a href="/book?route=sunset" class="route-book-btn">Book Run</a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="swiper-slide">
                <div class="route-card">
                    <img src="https://images.unsplash.com/photo-1534067783941-51c9c23ecefd?q=80&w=800&auto=format&fit=crop" alt="History Run" class="route-img" draggable="false">
                    <div class="route-info-panel">
                        <span class="route-badge">CULTURE</span>
                        <h3 class="route-name">History Run</h3>

                        <div class="route-specs">
                            <div class="spec-item">Terrain <span>Village Streets</span></div>
                            <div class="spec-item">Highlights <span>Monasteries</span></div>
                            <div class="spec-item">Pace <span>Easy & Chatty</span></div>
                            <a href="/book?route=history" class="route-book-btn">Book Run</a>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <div class="swiper-pagination"></div>
    </div>
</section>

<div class="custom-drag-cursor" id="dragCursor">DRAG</div>

<section class="vip-section" id="private-coaching">
    <div class="vip-accent-shape"></div>

    <div class="vip-container">
        <div class="vip-text">
            <span class="vip-tag">// 1-ON-1 COACHING</span>
            <h2 class="vip-title gs-reveal">MORE THAN A RUN.<br>A PRIVATE EXPERIENCE.</h2>
            <p class="vip-desc gs-reveal">
                Want to run at your own pace? Need marathon advice? Or simply prefer a private guide to show you the hidden gems of Plakias? Book a session tailored exactly to your needs and fitness level.
            </p>

            <ul class="vip-features gs-reveal">
                <li><i class="bi bi-check-circle-fill"></i> Flexible Timing & Hotel Pickup</li>
                <li><i class="bi bi-check-circle-fill"></i> Tailor-made Pace & Distance</li>
                <li><i class="bi bi-check-circle-fill"></i> Pro Tips from experienced Runners</li>
            </ul>

            <div class="magnetic-wrap gs-reveal" id="magBtnWrapLight">
                <a href="mailto:info@plakiasrunning.com" class="btn-magnetic-light" id="magBtnLight">
                    <span class="btn-text">Request <br>Session</span>
                    <div class="btn-fill-light"></div>
                </a>
            </div>
        </div>

        <div class="vip-img-wrap gs-reveal">
            <img src="https://images.unsplash.com/photo-1530143311094-34d807799e8f?q=80&w=800&auto=format&fit=crop" alt="Private Running Coach" class="vip-img" id="vipParallaxImg">
        </div>
    </div>
</section>



<style>
    /* ========================
   SECTION: THE BENTO BOX (EXPERIENCE)
   ======================== */
    .bento-section {
        padding: 120px 0;
        background-color: var(--run-dark, #0f172a);
        /* Γυρνάμε σε dark για contrast με το προηγούμενο */
        color: white;
        position: relative;
        overflow: hidden;
    }

    .bento-header {
        max-width: 1400px;
        margin: 0 auto 60px auto;
        padding: 0 40px;
    }

    .bento-badge {
        font-family: 'Courier New', monospace;
        font-size: 0.75rem;
        font-weight: 700;
        color: var(--accent-color, #29c285);
        letter-spacing: 2px;
        margin-bottom: 15px;
        display: block;
    }

    .bento-title {
        font-size: clamp(2.5rem, 5vw, 4rem);
        font-weight: 800;
        line-height: 1.1;
        margin: 0;
        text-transform: uppercase;
        letter-spacing: -0.02em;
    }

    .bento-subtitle {
        font-size: 1.1rem;
        color: #94a3b8;
        max-width: 500px;
        margin: 20px 0 0 0;
    }

    /* --- THE GRID SYSTEM --- */
    .bento-grid {
        max-width: 1400px;
        margin: 0 auto;
        padding: 0 40px;
        display: grid;
        /* 4 στήλες. Κάθε κουτί θα πιάνει όσες του πούμε */
        grid-template-columns: repeat(4, 1fr);
        grid-template-rows: repeat(2, 320px);
        /* 2 σειρές των 320px */
        gap: 25px;
    }

    /* Το κάθε κουτί */
    .bento-box {
        border-radius: 24px;
        position: relative;
        overflow: hidden;
        padding: 40px;
        display: flex;
        flex-direction: column;
        justify-content: flex-end;
        /* Κείμενο στο κάτω μέρος */
        transition: transform 0.4s ease, box-shadow 0.4s ease;
        cursor: default;
    }

    .bento-box:hover {
        transform: translateY(-5px);
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
    }

    .bento-bg-img {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.7s ease;
        z-index: 1;
    }

    .bento-overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: linear-gradient(to top, rgba(15, 23, 42, 0.95) 0%, rgba(15, 23, 42, 0.2) 60%, transparent 100%);
        z-index: 2;
    }

    .bento-content {
        position: relative;
        z-index: 3;
    }

    .bento-box:hover .bento-bg-img {
        transform: scale(1.05);
    }

    /* Typography μέσα στα κουτιά */
    .bento-box h3 {
        font-size: 1.8rem;
        font-weight: 800;
        margin: 0 0 10px 0;
        line-height: 1.2;
        text-transform: uppercase;
        color: var(--run-gray);
    }

    .bento-box p {
        font-size: 0.95rem;
        color: rgba(255, 255, 255, 0.8);
        margin: 0;
        line-height: 1.5;
    }

    .bento-icon {
        position: absolute;
        top: 30px;
        right: 30px;
        font-size: 1.8rem;
        color: var(--run-gray);
        z-index: 3;
    }

    /* --- SPECIFIC BOX SIZING --- */
    /* Box 1: Ο Οδηγός (Μεγάλο κάθετο) */
    .box-guide {
        grid-column: span 2;
        grid-row: span 2;
    }

    .box-guide h3 {
        font-size: 2.5rem;
    }

    /* Box 2: Φωτογραφίες (Οριζόντιο) */
    .box-photos {
        grid-column: span 2;
        grid-row: span 1;
    }

    /* Box 3: Νερό/Σνακ (Τετράγωνο) */
    .box-fuel {
        grid-column: span 1;
        grid-row: span 1;
        background-color: var(--accent-color, #29c285);
        color: #0f172a;
    }

    .box-fuel .bento-overlay {
        display: none;
    }

    /* Όχι μαύρο gradient εδώ */
    .box-fuel h3,
    .box-fuel p,
    .box-fuel .bento-icon {
        color: #0f172a;
    }

    /* Box 4: Ασφάλεια (Τετράγωνο) */
    .box-safety {
        grid-column: span 1;
        grid-row: span 1;
        background-color: #1e293b;
        /* Ανοιχτό σκούρο γκρι */
        border: 1px solid rgba(255, 255, 255, 0.1);
    }

    .box-safety .bento-overlay {
        display: none;
    }

    /* --- RESPONSIVE BENTO --- */
    @media (max-width: 992px) {
        .bento-grid {
            grid-template-columns: repeat(2, 1fr);
            /* 2 στήλες στα tablet */
            grid-template-rows: auto;
        }

        .box-guide,
        .box-photos {
            grid-column: span 2;
            grid-row: auto;
            min-height: 400px;
        }

        .box-fuel,
        .box-safety {
            grid-column: span 1;
            grid-row: auto;
            min-height: 300px;
        }
    }

    @media (max-width: 768px) {
        .bento-header {
            padding: 0 20px;
        }

        .bento-grid {
            grid-template-columns: 1fr;
            /* 1 στήλη στα κινητά */
            padding: 0 20px;
        }

        .box-guide,
        .box-photos,
        .box-fuel,
        .box-safety {
            grid-column: span 1;
            min-height: 350px;
        }
    }
</style>


<section class="bento-section" id="experience">

    <div class="bento-header">
        <span class="bento-badge gs-reveal">[ 03 // THE EXPERIENCE ]</span>
        <h2 class="bento-title gs-reveal">BEYOND THE <br>MILES.</h2>
        <p class="bento-subtitle gs-reveal">
            We take care of the details, so you can focus on the run.
            Here is what's included in every single session.
        </p>
    </div>

    <div class="bento-grid">

        <div class="bento-box box-guide gs-bento">
            <img src="https://images.unsplash.com/photo-1562571373-e12c2f3a53ec?q=80&w=1740&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D" alt="The Guide" class="bento-bg-img">
            <div class="bento-overlay"></div>
            <i class="bi bi-person-badge bento-icon"></i>
            <div class="bento-content">
                <h3>Meet Your <br>Guide</h3>
                <p>Ultra Marathoner & Local Expert. Running the trails of Crete for over a decade. I don't just guide you; I show you Plakias through a runner's eyes.</p>
            </div>
        </div>

        <div class="bento-box box-photos gs-bento">
            <img src="https://images.unsplash.com/photo-1635963422426-f802fb6d7240?q=80&w=1742&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D" alt="Running Group" class="bento-bg-img">
            <div class="bento-overlay"></div>
            <i class="bi bi-camera bento-icon"></i>
            <div class="bento-content">
                <h3>Leave the phone. <br>We capture the moment.</h3>
                <p>Professional photos and videos are taken during the run and sent directly to you, totally free. Run hands-free.</p>
            </div>
        </div>

        <div class="bento-box box-fuel gs-bento">
            <i class="bi bi-droplet-fill bento-icon"></i>
            <div class="bento-content">
                <h3>Local <br>Fuel</h3>
                <p>Cold water, electrolytes, and authentic Cretan energy snacks provided before and after we hit the trail.</p>
            </div>
        </div>

        <div class="bento-box box-safety gs-bento">
            <i class="bi bi-shield-check bento-icon"></i>
            <div class="bento-content">
                <h3>100% <br>Safe</h3>
                <p>First aid certified guide. Mandatory waivers signed beforehand. Max 10 runners per group to ensure nobody is left behind.</p>
            </div>
        </div>

    </div>
</section>


<style>
    /* ========================
   SECTION: STRAVA COMMUNITY (LIGHT)
   ======================== */
    .strava-section {
        background-color: #ffffff;
        padding: 100px 0;
        position: relative;
        border-bottom: 1px solid #f1f5f9;
    }

    .strava-container {
        max-width: 1400px;
        margin: 0 auto;
        padding: 0 40px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 60px;
    }

    .strava-left {
        flex: 1;
    }

    .strava-badge {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: rgba(252, 76, 2, 0.1);
        color: #fc4c02;
        /* Strava Orange */
        padding: 6px 14px;
        border-radius: 50px;
        font-size: 0.75rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 1px;
        margin-bottom: 20px;
    }

    .strava-title {
        font-size: clamp(2rem, 3.5vw, 3.5rem);
        font-weight: 800;
        color: var(--run-dark, #0f172a);
        line-height: 1.1;
        margin: 0 0 20px 0;
        letter-spacing: -0.02em;
    }

    .strava-desc {
        font-size: 1.1rem;
        color: #64748b;
        line-height: 1.6;
        max-width: 450px;
        margin-bottom: 30px;
    }

    .btn-strava {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        background-color: #fc4c02;
        /* Strava Brand Color */
        color: white;
        padding: 14px 28px;
        border-radius: 8px;
        font-weight: 700;
        text-transform: uppercase;
        text-decoration: none;
        transition: all 0.3s ease;
        box-shadow: 0 10px 20px rgba(252, 76, 2, 0.2);
    }

    .btn-strava:hover {
        transform: translateY(-3px);
        box-shadow: 0 15px 25px rgba(252, 76, 2, 0.4);
        color: white;
    }

    /* Strava Stats Box (Right Side) */
    .strava-right {
        flex: 1;
        display: flex;
        justify-content: flex-end;
    }

    .strava-stats-box {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        padding: 40px;
        border-radius: 24px;
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 40px;
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.03);
    }

    .stat-item h4 {
        font-size: 3rem;
        font-weight: 800;
        color: var(--run-dark, #0f172a);
        margin: 0 0 5px 0;
        line-height: 1;
    }

    .stat-item p {
        font-size: 0.8rem;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 1px;
        margin: 0;
        font-weight: 600;
    }

    /* ========================
   SECTION: TERMINAL CTA (PITCH BLACK)
   ======================== */
    .terminal-section {
        background-color: #050505;
        /* Πολύ βαθύ μαύρο, σχεδόν OLED */
        height: 80vh;
        /* Δεν πιάνει όλη την οθόνη, αφήνει χώρο για footer */
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        text-align: center;
        position: relative;
        overflow: hidden;
    }

    /* Απαλό glow πίσω από το κείμενο */
    .terminal-glow {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        width: 50vw;
        height: 50vw;
        background: radial-gradient(circle, rgba(41, 194, 133, 0.08) 0%, transparent 60%);
        pointer-events: none;
    }

    .terminal-title {
        font-size: clamp(4rem, 12vw, 12rem);
        /* ΤΕΡΑΣΤΙΑ Fluid Typography */
        font-weight: 900;
        line-height: 0.9;
        text-transform: uppercase;
        margin: 0;
        color: white;
        letter-spacing: -0.04em;
        position: relative;
        z-index: 2;
    }

    .terminal-title span {
        color: transparent;
        -webkit-text-stroke: 2px rgba(255, 255, 255, 0.2);
        display: block;
    }

    .terminal-btn-wrap {
        margin-top: 60px;
        position: relative;
        z-index: 10;
    }

    .btn-finale {
        position: relative;
        display: inline-flex;
        align-items: center;
        justify-content: space-between;
        padding: 12px 12px 12px 40px;
        /* Χώρος αριστερά για το κείμενο, σφιχτό δεξιά για τον κύκλο */
        background: rgba(255, 255, 255, 0.03);
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 100px;
        /* Pill shape */
        text-decoration: none;
        overflow: hidden;
        transition: all 0.5s cubic-bezier(0.19, 1, 0.22, 1);
        cursor: pointer;
    }

    .btn-finale-text {
        color: #ffffff;
        font-size: 1.1rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 2px;
        margin-right: 40px;
        position: relative;
        z-index: 2;
        /* Πάνω από το background fill */
        transition: color 0.4s ease;
    }

    /* Ο κύκλος με το βελάκι */
    .btn-finale-icon {
        width: 60px;
        height: 60px;
        background: var(--accent-color, #29c285);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
        z-index: 2;
        transition: transform 0.5s cubic-bezier(0.19, 1, 0.22, 1);
    }

    .btn-finale-icon i {
        color: #050505;
        font-size: 1.8rem;
        transition: transform 0.4s ease;
    }

    /* Το "Υγρό" που θα γεμίσει το κουμπί - Κρυμμένο πίσω από το icon αρχικά */
    .btn-finale::before {
        content: '';
        position: absolute;
        top: 50%;
        right: 12px;
        width: 60px;
        height: 60px;
        background: var(--accent-color, #29c285);
        border-radius: 50%;
        transform: translateY(-50%) scale(1);
        transition: transform 0.6s cubic-bezier(0.19, 1, 0.22, 1);
        z-index: 1;
        /* Κάτω από το κείμενο */
    }

    /* --- HOVER EFFECTS --- */
    .btn-finale:hover {
        border-color: var(--accent-color, #29c285);
        box-shadow: 0 0 40px rgba(41, 194, 133, 0.3);
        /* Απαλό Glow */
    }

    /* Ο κύκλος μεγαλώνει δραματικά και γεμίζει το pill */
    .btn-finale:hover::before {
        transform: translateY(-50%) scale(15);
    }

    /* Το κείμενο γίνεται μαύρο για να κάνει αντίθεση με το πράσινο background */
    .btn-finale:hover .btn-finale-text {
        color: #050505;
    }

    /* Το βελάκι φεύγει ελαφρώς δεξιά (έξτρα δυναμισμός) */
    .btn-finale:hover .btn-finale-icon i {
        transform: translateX(5px);
    }

    /* Mobile Adjustments */
    @media (max-width: 992px) {
        .strava-container {
            flex-direction: column;
            text-align: center;
            gap: 40px;
        }

        .strava-right {
            justify-content: center;
            width: 100%;
        }

        .strava-stats-box {
            width: 100%;
            padding: 30px;
            gap: 20px;
        }

        .stat-item h4 {
            font-size: 2.2rem;
        }

        .terminal-section {
            height: 60vh;
        }

        .terminal-title span {
            -webkit-text-stroke: 1px rgba(255, 255, 255, 0.3);
        }
    }
</style>


<section class="strava-section">
    <div class="strava-container">

        <div class="strava-left gs-reveal">
            <div class="strava-badge">
                <i class="bi bi-activity"></i> Strava Club
            </div>
            <h2 class="strava-title">JOIN THE <br>COMMUNITY.</h2>
            <p class="strava-desc">
                Running is better together. Join the official Plakias Running Club on Strava. Share your routes, track your segments, and connect with runners from all over the world.
            </p>
            <a href="#" class="btn-strava" target="_blank">
                Join the Club <i class="bi bi-arrow-right"></i>
            </a>
        </div>

        <div class="strava-right gs-reveal">
            <div class="strava-stats-box">
                <div class="stat-item">
                    <h4 class="counter" data-target="142">0</h4>
                    <p>Club Members</p>
                </div>
                <div class="stat-item">
                    <h4 class="counter" data-target="850">0</h4>
                    <p>Weekly KM</p>
                </div>
                <div class="stat-item">
                    <h4 class="counter" data-target="12">0</h4>
                    <p>Local Segments</p>
                </div>
                <div class="stat-item">
                    <h4>∞</h4>
                    <p>Good Vibes</p>
                </div>
            </div>
        </div>

    </div>
</section>

<section class="terminal-section">
    <div class="terminal-glow"></div>

    <h1 class="terminal-title gs-reveal">
        READY TO <span>RUN?</span>
    </h1>

    <div class="terminal-btn-wrap gs-reveal">
        <a href="/routes" class="btn-finale">
            <span class="btn-finale-text">VIEW ALL PACKAGES</span>
            <div class="btn-finale-icon">
                <i class="bi bi-arrow-right-short"></i>
            </div>
        </a>
    </div>
</section>






<?php
function hook_end_scripts()
{
?>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/ScrollTrigger.min.js"></script>
   

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


            // --- LIGHT MAGNETIC BUTTON ---
            const btnWrapLight = document.getElementById('magBtnWrapLight');
            const btnLight = document.getElementById('magBtnLight');

            if (btnWrapLight && btnLight) {
                btnWrapLight.addEventListener('mousemove', (e) => {
                    const rect = btnWrapLight.getBoundingClientRect();
                    const x = e.clientX - rect.left - rect.width / 2;
                    const y = e.clientY - rect.top - rect.height / 2;
                    gsap.to(btnLight, {
                        x: x * 0.4,
                        y: y * 0.4,
                        duration: 0.3
                    });
                });
                btnWrapLight.addEventListener('mouseleave', () => {
                    gsap.to(btnLight, {
                        x: 0,
                        y: 0,
                        duration: 0.5,
                        ease: "elastic.out(1, 0.3)"
                    });
                });
            }
        });

        document.addEventListener("DOMContentLoaded", () => {

            // --- 1. INITIALIZE SWIPER ---
            const swiper = new Swiper('.swiper-container', {
                slidesPerView: 'auto',
                grabCursor: true,

                // Ενεργοποίηση των dots
                pagination: {
                    el: '.swiper-pagination',
                    clickable: true,
                },

                // Το Breakpoints είναι το κλειδί για το τέλειο Responsive
                breakpoints: {
                    // Mobile & Tablets (320px και πάνω)
                    320: {
                        spaceBetween: 20,
                        centeredSlides: true, // ΚΕΝΤΡΑΡΕΙ ΤΗΝ ΕΝΕΡΓΗ ΚΑΡΤΑ
                        slidesOffsetBefore: 0,
                        slidesOffsetAfter: 0,
                        freeMode: false, // Κάνει "Snap" αυστηρά στην επόμενη κάρτα (καλύτερο για κινητά)
                    },
                    // Desktop (992px και πάνω)
                    992: {
                        spaceBetween: 30,
                        centeredSlides: false, // Στοιχισμένα αριστερά στο desktop
                        slidesOffsetBefore: 40, // Για να ευθυγραμμίζεται με τον τίτλο
                        slidesOffsetAfter: 40,
                        freeMode: true, // Ελεύθερο drag στο ποντίκι
                    }
                }
            });

            // --- 2. GSAP ANIMATIONS (Αν τα χρησιμοποιείς) ---
            if (typeof ScrollTrigger !== "undefined") {
                gsap.registerPlugin(ScrollTrigger);

                // Text Reveals
                gsap.utils.toArray('.gs-reveal').forEach(function(elem) {
                    gsap.from(elem, {
                        scrollTrigger: {
                            trigger: elem,
                            start: "top 85%"
                        },
                        y: 40,
                        opacity: 0,
                        duration: 1,
                        ease: "power3.out"
                    });
                });
            }


            // --- NEW: PARALLAX BACKGROUND TEXT ---
            if (typeof ScrollTrigger !== "undefined") {
                gsap.to("#bgTextParallax", {
                    scrollTrigger: {
                        trigger: ".routes-section",
                        start: "top bottom",
                        end: "bottom top",
                        scrub: 1
                    },
                    x: -300, // Το κείμενο "EXPLORE" κινείται προς τα αριστερά καθώς σκρολάρεις
                    ease: "none"
                });
            }

            // --- NEW: CUSTOM DRAG CURSOR LOGIC ---
            const dragCursor = document.getElementById('dragCursor');
            const sliderWrap = document.querySelector('.swiper-container');

            if (dragCursor && sliderWrap && window.innerWidth > 992) {
                // Κάνει τον κέρσορα να ακολουθεί το ποντίκι μέσα στο slider
                sliderWrap.addEventListener('mousemove', (e) => {
                    gsap.to(dragCursor, {
                        x: e.clientX - 40, // -40 για να κεντραριστεί ο κύκλος (width/2)
                        y: e.clientY - 40,
                        duration: 0.1, // Super fast tracking
                        ease: "power2.out"
                    });
                });

                // Εμφάνιση όταν μπαίνεις στο slider
                sliderWrap.addEventListener('mouseenter', () => {
                    gsap.to(dragCursor, {
                        scale: 1,
                        opacity: 1,
                        duration: 0.3
                    });
                });

                // Απόκρυψη όταν βγαίνεις
                sliderWrap.addEventListener('mouseleave', () => {
                    gsap.to(dragCursor, {
                        scale: 0,
                        opacity: 0,
                        duration: 0.3
                    });
                });

                // "Πιάσιμο" (mousedown/mouseup)
                sliderWrap.addEventListener('mousedown', () => {
                    dragCursor.classList.add('active');
                    dragCursor.innerText = "SWIPE"; // Αλλάζει κείμενο
                });

                sliderWrap.addEventListener('mouseup', () => {
                    dragCursor.classList.remove('active');
                    dragCursor.innerText = "DRAG";
                });
            }

            // --- BENTO BOX ANIMATION ---
            if (typeof ScrollTrigger !== "undefined") {
                gsap.from(".gs-bento", {
                    scrollTrigger: {
                        trigger: ".bento-grid",
                        start: "top 80%", // Ξεκινάει όταν το grid μπει στο 80% της οθόνης
                    },
                    y: 60, // Έρχονται από κάτω
                    opacity: 0,
                    duration: 1,
                    stagger: 0.15, // Εμφανίζονται με 0.15s διαφορά το ένα από το άλλο
                    ease: "power3.out"
                });
            }


            // --- STRAVA COUNTER ANIMATION ---
            const counters = document.querySelectorAll('.counter');
            let hasCounted = false; // Για να μην ξαναμετρήσει αν ανέβουμε πάνω

            if (typeof ScrollTrigger !== "undefined" && counters.length > 0) {
                ScrollTrigger.create({
                    trigger: ".strava-stats-box",
                    start: "top 85%",
                    onEnter: () => {
                        if (!hasCounted) {
                            counters.forEach(counter => {
                                const target = +counter.getAttribute('data-target');

                                // Χρήση GSAP για ομαλή αύξηση των αριθμών
                                gsap.to(counter, {
                                    innerHTML: target,
                                    duration: 2,
                                    snap: {
                                        innerHTML: 1
                                    }, // Στρογγυλοποίηση
                                    ease: "power2.out"
                                });
                            });
                            hasCounted = true;
                        }
                    }
                });
            }

            // --- FINAL MAGNETIC BUTTON ---
            const btnWrapFinal = document.getElementById('magBtnWrapFinal');
            const btnFinal = document.getElementById('magBtnFinal');

            if (btnWrapFinal && btnFinal) {
                btnWrapFinal.addEventListener('mousemove', (e) => {
                    const rect = btnWrapFinal.getBoundingClientRect();
                    const x = e.clientX - rect.left - rect.width / 2;
                    const y = e.clientY - rect.top - rect.height / 2;
                    gsap.to(btnFinal, {
                        x: x * 0.4,
                        y: y * 0.4,
                        duration: 0.3
                    });
                });
                btnWrapFinal.addEventListener('mouseleave', () => {
                    gsap.to(btnFinal, {
                        x: 0,
                        y: 0,
                        duration: 0.5,
                        ease: "elastic.out(1, 0.3)"
                    });
                });
            }
        });
    </script>


<?php
}
?>
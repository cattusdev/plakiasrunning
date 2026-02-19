<section class="video-mask-hero">
    <div class="video-bg-container">
        <video autoplay muted loop playsinline class="hero-video">
            <source src="https://assets.mixkit.co/videos/44358/44358-720.mp4" type="video/mp4">
        </video>
    </div>
    <div class="text-mask-layer">
        <h1 class="massive-mask-text">ROUTES</h1>
    </div>
    <div class="hero-content-overlay">
        <p class="page-subtitle gs-reveal">
            From easy coastal breezes to demanding mountain technical trails.
            Find your pace and discover the unseen Plakias.
        </p>
        <div class="filter-wrapper gs-reveal">
            <div class="filter-bar" id="filterBar">
                <div class="filter-indicator" id="filterIndicator"></div>
                <button class="filter-btn active" data-filter="all">All Routes</button>
                <button class="filter-btn" data-filter="coastal">Coastal</button>
                <button class="filter-btn" data-filter="mountain">Mountain</button>
                <button class="filter-btn" data-filter="culture">Culture</button>
            </div>
        </div>
    </div>
</section>

<section class="routes-list-section">
    <div class="bg-editorial-text left-text gs-parallax" data-speed="0.8">ELEVATION</div>
    <div class="bg-editorial-text right-text gs-parallax" data-speed="1.2">TERRAIN</div>

    <div class="routes-list-container" id="routesContainer">

        <article class="route-card-editorial gs-reveal" data-category="coastal">
            <div class="route-visual">
                <div class="route-number">01</div>
                <img src="https://images.unsplash.com/photo-1502904550040-7534597429ae?q=80&w=1000&auto=format&fit=crop" alt="Coastal Run" class="route-img">
            </div>
            <div class="route-content">
                <div class="route-badge">COASTAL ESCAPE</div>
                <h2 class="route-title">The Blue <br>Horizon.</h2>
                <p class="route-desc">
                    Run by the waves. A smooth, fast route combining asphalt and dirt trails with constant views of the Libyan Sea. Perfect for a relaxed or recovery run.
                </p>
                <div class="route-specs-grid">
                    <div class="spec-item"><span class="spec-label">Distance</span><span class="spec-value">10 KM</span></div>
                    <div class="spec-item"><span class="spec-label">Elevation</span><span class="spec-value">+ 50 M</span></div>
                    <div class="spec-item"><span class="spec-label">Terrain</span><span class="spec-value">Mixed</span></div>
                    <div class="spec-item"><span class="spec-label">Level</span><span class="spec-value">Beginner</span></div>
                </div>
                <a href="/book?route=coastal" class="btn-solid-booking">Book This Run <i class="bi bi-arrow-right"></i></a>
            </div>
        </article>

        <article class="route-card-editorial gs-reveal" data-category="mountain">
            <div class="route-visual">
                <div class="route-number">02</div>
                <img src="https://images.unsplash.com/photo-1476480862126-209bfaa8edc8?q=80&w=1000&auto=format&fit=crop" alt="Mountain Trail" class="route-img">
            </div>
            <div class="route-content">
                <div class="route-badge" style="color: #e63946; background: rgba(230, 57, 70, 0.1);">PEAK CHALLENGE</div>
                <h2 class="route-title">Kouroupa <br>Summit.</h2>
                <p class="route-desc">
                    For those seeking a challenge. A steep, technical trail leading to the summit. Rocks, high elevation gain, and finally, panoramic views of southern Crete.
                </p>
                <div class="route-specs-grid">
                    <div class="spec-item"><span class="spec-label">Distance</span><span class="spec-value">14 KM</span></div>
                    <div class="spec-item"><span class="spec-label">Elevation</span><span class="spec-value">+ 850 M</span></div>
                    <div class="spec-item"><span class="spec-label">Terrain</span><span class="spec-value">Tech Trail</span></div>
                    <div class="spec-item"><span class="spec-label">Level</span><span class="spec-value" style="color: #e63946;">Advanced</span></div>
                </div>
                <a href="/book?route=mountain" class="btn-solid-booking">Book This Run <i class="bi bi-arrow-right"></i></a>
            </div>
        </article>

        <article class="route-card-editorial gs-reveal" data-category="culture">
            <div class="route-visual">
                <div class="route-number">03</div>
                <img src="https://images.unsplash.com/photo-1534067783941-51c9c23ecefd?q=80&w=1000&auto=format&fit=crop" alt="Culture Run" class="route-img">
            </div>
            <div class="route-content">
                <div class="route-badge" style="color: #ffb703; background: rgba(255, 183, 3, 0.1);">HISTORIC TRAIL</div>
                <h2 class="route-title">The Old <br>Villages.</h2>
                <p class="route-desc">
                    Run through time. A route passing through abandoned villages, old monasteries, and ancient olive groves. A cultural experience at a running pace.
                </p>
                <div class="route-specs-grid">
                    <div class="spec-item"><span class="spec-label">Distance</span><span class="spec-value">8 KM</span></div>
                    <div class="spec-item"><span class="spec-label">Elevation</span><span class="spec-value">+ 200 M</span></div>
                    <div class="spec-item"><span class="spec-label">Terrain</span><span class="spec-value">Dirt / Paved</span></div>
                    <div class="spec-item"><span class="spec-label">Level</span><span class="spec-value">Intermediate</span></div>
                </div>
                <a href="/book?route=culture" class="btn-solid-booking">Book This Run <i class="bi bi-arrow-right"></i></a>
            </div>
        </article>

    </div>
</section>








<section class="custom-run-banner gs-reveal">
    <div class="cr-container">
        <div class="cr-content">
            <span class="cr-badge">TAILOR-MADE EXPERIENCES</span>
            <h2 class="cr-title">YOUR PACE.<br>YOUR RULES.</h2>
            <p class="cr-desc">
                Not seeing your ideal route? From a 30km sunrise mountain peak to a gentle coastal recovery jog, we craft the exact running experience you're looking for.
            </p>
            <a onclick="openCustomModal()" class="btn-primary-custom">
                Request Custom Run <i class="bi bi-arrow-right"></i>
            </a>
        </div>

        <div class="cr-visual-path">
            <svg viewBox="0 0 500 200" class="path-svg">
                <defs>
                    <linearGradient id="lineGradient" x1="0%" y1="0%" x2="100%" y2="0%">
                        <stop offset="0%" stop-color="transparent" />
                        <stop offset="50%" stop-color="#34cdce" />
                        <stop offset="100%" stop-color="transparent" />
                    </linearGradient>
                </defs>
                <path class="path-line" d="M0,150 Q50,140 80,100 T150,80 T220,120 T300,40 T380,90 T450,70 T500,100" />

                <circle class="path-runner-dot" r="5" fill="#34cdce">
                    <animateMotion dur="5s" repeatCount="indefinite" path="M0,150 Q50,140 80,100 T150,80 T220,120 T300,40 T380,90 T450,70 T500,100" />
                </circle>
            </svg>

            <div class="path-stats">
                <div class="p-stat"><span>ELV GAIN</span><strong>+1.240m</strong></div>
                <div class="p-stat"><span>EST TIME</span><strong>02:45:00</strong></div>
            </div>
        </div>
    </div>
</section>


<style>
    .custom-run-banner {
        background-color: #0c120e;
        /* Πολύ βαθύ δασικό πράσινο/μαύρο */
        color: #ffffff;
        padding: 120px 0;
        position: relative;
        overflow: hidden;
    }

    .cr-container {
        max-width: 1400px;
        margin: 0 auto;
        padding: 0 40px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 80px;
    }

    .cr-badge {
        display: inline-block;
        font-size: 0.85rem;
        font-weight: 700;
        color: var(--accent-color);
        letter-spacing: 3px;
        margin-bottom: 20px;
        text-transform: uppercase;
    }

    .cr-title {
        font-size: clamp(3rem, 5vw, 4.5rem);
        font-weight: 900;
        line-height: 1;
        margin-bottom: 30px;
        letter-spacing: -0.03em;
    }

    .cr-desc {
        font-size: 1.1rem;
        color: #a0aec0;
        line-height: 1.8;
        margin-bottom: 40px;
        max-width: 500px;
    }

    .btn-primary-custom {
        display: inline-flex;
        align-items: center;
        gap: 12px;
        background: var(--accent-color);
        color: #0c120e;
        padding: 18px 40px;
        border-radius: 100px;
        font-weight: 800;
        text-decoration: none;
        text-transform: uppercase;
        transition: transform 0.3s ease, background 0.3s ease;
    }

    .btn-primary-custom:hover {
        transform: translateY(-3px);
        background: #ffffff;
    }

    .cr-visual-path {
        flex: 1;
        position: relative;
        display: flex;
        flex-direction: column;
        align-items: center;
        padding-left: 40px;
    }

    .path-svg {
        width: 100%;
        height: auto;
        overflow: visible;
    }

    .path-line {
        fill: none;
        stroke: url(#lineGradient);
        stroke-width: 3;
        stroke-linecap: round;
        /* Εφέ σχεδίασης της γραμμής */
        stroke-dasharray: 1000;
        stroke-dashoffset: 1000;
        animation: drawPath 4s ease-out forwards;
    }

    .path-runner-dot {
        filter: drop-shadow(0 0 8px #34cdce);
    }

    .path-stats {
        display: flex;
        gap: 40px;
        margin-top: 30px;
        border-top: 1px solid rgba(255, 255, 255, 0.1);
        padding-top: 20px;
        width: 100%;
    }

    .p-stat {
        display: flex;
        flex-direction: column;
        gap: 5px;
    }

    .p-stat span {
        font-family: 'Courier New', monospace;
        font-size: 0.7rem;
        color: #64748b;
        letter-spacing: 1px;
    }

    .p-stat strong {
        font-size: 1.1rem;
        color: #ffffff;
        font-weight: 800;
    }

    @keyframes drawPath {
        to {
            stroke-dashoffset: 0;
        }
    }

    @media (max-width: 992px) {
        .cr-visual-path {
            padding-left: 0;
            margin-top: 50px;
            width: 100%;
        }

        .path-stats {
            justify-content: center;
        }
    }

    .trail-tag {
        position: absolute;
        background: #ffffff;
        color: #0c120e;
        padding: 10px 20px;
        border-radius: 100px;
        display: flex;
        align-items: center;
        gap: 10px;
        font-weight: 800;
        font-size: 0.85rem;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        z-index: 3;
    }

    .tag-1 {
        top: 15%;
        left: -10%;
    }

    .tag-2 {
        bottom: 20%;
        right: -5%;
    }

    .tag-icon {
        width: 30px;
        height: 30px;
        background: var(--accent-color);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 0.9rem;
    }

    @media (max-width: 992px) {
        .cr-container {
            flex-direction: column;
            text-align: center;
        }

        .cr-visual-organic {
            margin-top: 60px;
        }

        .trail-tag {
            position: relative;
            left: 0;
            right: 0;
            margin: 10px auto;
            width: fit-content;
        }
    }
</style>





<button class="mobile-filter-fab" id="mobileFilterFab">
    <i class="bi bi-sliders"></i> Filters
    <span class="fab-badge" id="fabBadge" style="display:none;">0</span>
</button>

<div class="sheet-overlay" id="sheetOverlay"></div>

<div class="mobile-filter-sheet" id="mobileFilterSheet">
    <div class="sheet-header">
        <span>Select Terrain</span>
        <div class="sheet-actions">
            <button class="mobile-clear-btn" id="mobileClearBtn" style="display: none;">Clear</button>
            <button class="close-sheet-btn" id="closeSheetBtn"><i class="bi bi-x-lg"></i></button>
        </div>
    </div>
    <div class="sheet-body">
        <button class="sheet-btn" data-filter="coastal">Coastal Escape</button>
        <button class="sheet-btn" data-filter="mountain">Mountain Trail</button>
        <button class="sheet-btn" data-filter="culture">Historic Run</button>
    </div>
</div>

<div class="desktop-floating-filter" id="desktopFloatingFilter">
    <span class="dff-label">Viewing:</span>
    <span class="dff-value" id="dffValue">All Routes</span>
    <i class="bi bi-arrow-up-short"></i>
</div>

<style>
    .modern-nav {
        background: var(--run-dark, #0f172a);
    }

    .video-mask-hero {
        position: relative;
        height: 75vh;
        background: #ffffff;
        overflow: hidden;
        display: flex;
        align-items: flex-end;
        justify-content: center;
        padding-bottom: 35px;
        border-bottom: 1px solid #e2e8f0;
    }

    .video-bg-container {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        z-index: 1;
        pointer-events: none;
    }

    .hero-video {
        width: 100%;
        height: 100%;
        object-fit: cover;
        filter: saturate(1.2) contrast(1.1);
    }

    .text-mask-layer {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: #353535;
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 2;
        mix-blend-mode: screen;
        pointer-events: none;
    }

    .massive-mask-text {
        font-size: 24vw;
        font-weight: 900;
        margin: 0;
        color: #000000;
        text-transform: uppercase;
        letter-spacing: -0.05em;
        line-height: 0.8;
        transform: translateY(-10%);
    }

    .hero-content-overlay {
        position: relative;
        z-index: 3;
        text-align: center;
        max-width: 900px;
        padding: 0 20px;
        width: 100%;
    }

    .page-subtitle {
        font-size: 1.2rem;
        color: var(--run-gray);
        margin: 0 auto 40px auto;
        line-height: 1.6;
        max-width: 600px;
    }

    .filter-wrapper {
        width: 100%;
        display: flex;
        justify-content: center;
    }

    .filter-bar {
        position: relative;
        display: inline-flex;
        background: #e9e9e9;
        padding: 6px;
        border-radius: 100px;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.08);
        gap: 5px;
        max-width: 100%;
        overflow-x: auto;
        -ms-overflow-style: none;
        scrollbar-width: none;
    }

    .filter-bar::-webkit-scrollbar {
        display: none;
    }

    .filter-indicator {
        position: absolute;
        top: 6px;
        left: 6px;
        height: calc(100% - 12px);
        background: var(--run-dark, #0f172a);
        border-radius: 100px;
        transition: all 0.4s cubic-bezier(0.25, 1, 0.5, 1);
        z-index: 1;
    }

    .filter-btn {
        position: relative;
        z-index: 2;
        background: transparent;
        border: none;
        padding: 12px 28px;
        border-radius: 100px;
        font-size: 0.9rem;
        font-weight: 700;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 1px;
        cursor: pointer;
        transition: color 0.3s ease;
        white-space: nowrap;
    }

    .filter-btn:hover {
        color: var(--run-dark, #0f172a);
    }

    .filter-btn.active {
        color: #ffffff;
    }

    .routes-list-section {
        position: relative;
        background-color: #ffffff;
        padding: 100px 0;
        overflow: hidden;
    }

    .bg-editorial-text {
        position: absolute;
        font-size: 18vw;
        font-weight: 900;
        line-height: 0.8;
        color: var(--run-dark, #0f172a);
        opacity: 0.02;
        text-transform: uppercase;
        white-space: nowrap;
        z-index: 0;
        pointer-events: none;
    }

    .left-text {
        top: 10%;
        left: -5%;
        transform: rotate(-90deg);
        transform-origin: left top;
    }

    .right-text {
        top: 40%;
        right: -20%;
    }

    .routes-list-container {
        max-width: 1400px;
        margin: 0 auto;
        padding: 0 40px;
        display: flex;
        flex-direction: column;
        gap: 120px;
        position: relative;
        z-index: 2;
    }

    .route-card-editorial {
        display: flex;
        align-items: center;
        gap: 80px;
        transition: opacity 0.5s ease, transform 0.5s ease;
    }

    .route-card-editorial:nth-child(even) {
        flex-direction: row-reverse;
    }

    .route-visual {
        flex: 1;
        position: relative;
        border-radius: 24px;
        overflow: hidden;
        aspect-ratio: 1 / 1;
        max-height: 600px;
        box-shadow: 0 30px 60px rgba(0, 0, 0, 0.08);
    }

    .route-img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.7s ease;
    }

    .route-card-editorial:hover .route-img {
        transform: scale(1.05);
    }

    .route-number {
        position: absolute;
        top: 30px;
        left: 30px;
        font-size: 5rem;
        font-weight: 900;
        line-height: 0.8;
        color: transparent;
        -webkit-text-stroke: 2px rgba(255, 255, 255, 0.6);
        z-index: 2;
    }

    .route-content {
        flex: 1.2;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }

    .route-badge {
        display: inline-block;
        align-self: flex-start;
        padding: 6px 14px;
        background: rgba(41, 194, 133, 0.1);
        color: var(--accent-color);
        font-family: 'Courier New', monospace;
        font-size: 0.75rem;
        font-weight: 800;
        letter-spacing: 2px;
        border-radius: 50px;
        margin-bottom: 20px;
    }

    .route-title {
        font-size: clamp(2.5rem, 4vw, 4rem);
        font-weight: 900;
        line-height: 1.1;
        color: var(--run-dark, #0f172a);
        margin: 0 0 20px 0;
        letter-spacing: -0.02em;
    }

    .route-desc {
        font-size: 1.1rem;
        color: #64748b;
        line-height: 1.7;
        margin-bottom: 40px;
    }

    .route-specs-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 30px;
        margin-bottom: 50px;
        padding-bottom: 40px;
        border-bottom: 1px solid #e2e8f0;
    }

    .spec-item {
        display: flex;
        flex-direction: column;
        gap: 5px;
    }

    .spec-label {
        font-family: 'Courier New', monospace;
        font-size: 0.75rem;
        color: #94a3b8;
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    .spec-value {
        font-size: 1.3rem;
        font-weight: 800;
        color: var(--run-dark, #0f172a);
    }

    .btn-solid-booking {
        align-self: flex-start;
        display: inline-flex;
        align-items: center;
        gap: 12px;
        background-color: var(--run-dark, #0f172a);
        color: #ffffff;
        padding: 18px 36px;
        border-radius: 100px;
        font-size: 1rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 1px;
        text-decoration: none;
        transition: all 0.3s cubic-bezier(0.19, 1, 0.22, 1);
        box-shadow: 0 10px 20px rgba(15, 23, 42, 0.15);
    }

    .btn-solid-booking i {
        font-size: 1.2rem;
        transition: transform 0.3s ease;
    }

    .btn-solid-booking:hover {
        background-color: var(--accent-color);
        transform: translateY(-3px);
        box-shadow: 0 15px 30px rgba(41, 194, 133, 0.3);
        color: #0f172a;
    }

    .btn-solid-booking:hover i {
        transform: translateX(5px);
    }

    .desktop-floating-filter {
        position: fixed;
        bottom: 40px;
        right: 40px;
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        padding: 12px 24px;
        border-radius: 100px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        display: flex;
        align-items: center;
        gap: 8px;
        z-index: 90;
        opacity: 0;
        transform: translateY(20px);
        pointer-events: none;
        transition: all 0.4s cubic-bezier(0.19, 1, 0.22, 1);
        border: 1px solid #e2e8f0;
        cursor: pointer;
    }

    .desktop-floating-filter.show {
        opacity: 1;
        transform: translateY(0);
        pointer-events: auto;
    }

    .desktop-floating-filter:hover {
        transform: translateY(-3px);
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.15);
    }

    .dff-label {
        font-size: 0.8rem;
        color: #64748b;
        font-weight: 600;
        text-transform: uppercase;
    }

    .dff-value {
        font-size: 0.9rem;
        color: var(--run-dark, #0f172a);
        font-weight: 800;
        text-transform: uppercase;
    }

    .desktop-floating-filter i {
        color: var(--accent-color);
        font-size: 1.2rem;
        margin-left: 5px;
    }

    .mobile-filter-fab,
    .mobile-filter-sheet,
    .sheet-overlay {
        display: none;
    }

    @media (max-width: 992px) {
        .video-mask-hero {
            height: 60vh;
            padding-bottom: 60px;
        }

        .massive-mask-text {
            font-size: 28vw;
        }

        .routes-list-container {
            gap: 80px;
            padding: 0 20px;
        }

        .route-card-editorial,
        .route-card-editorial:nth-child(even) {
            flex-direction: column;
            gap: 30px;
        }

        .route-visual {
            width: 100%;
            aspect-ratio: 1/1;
        }

        .route-specs-grid {
            gap: 20px;
            margin-bottom: 30px;
            padding-bottom: 30px;
        }

        .btn-solid-booking {
            width: 100%;
            justify-content: center;
        }

        .desktop-floating-filter {
            display: none !important;
        }

        .mobile-filter-fab {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            position: fixed;
            bottom: 30px;
            left: 50%;
            transform: translateX(-50%) translateY(100px);
            background: var(--run-dark, #0f172a);
            color: white;
            border: none;
            padding: 14px 30px;
            border-radius: 100px;
            font-size: 1rem;
            font-weight: 700;
            box-shadow: 0 10px 30px rgba(15, 23, 42, 0.3);
            z-index: 90;
            opacity: 0;
            transition: transform 0.4s cubic-bezier(0.19, 1, 0.22, 1), opacity 0.4s ease;
        }

        .mobile-filter-fab.show {
            transform: translateX(-50%) translateY(0);
            opacity: 1;
        }

        .fab-badge {
            background: var(--accent-color);
            color: #0f172a;
            border-radius: 50%;
            width: 22px;
            height: 22px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 0.75rem;
            font-weight: 800;
            margin-left: 4px;
        }

        .sheet-actions {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .mobile-clear-btn {
            background: transparent;
            border: none;
            color: #ef4444;
            font-weight: 700;
            font-size: 0.9rem;
            text-transform: uppercase;
            cursor: pointer;
            padding: 0;
        }

        .sheet-overlay {
            display: block;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(4px);
            z-index: 99;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.3s ease;
        }

        .sheet-overlay.active {
            opacity: 1;
            pointer-events: auto;
        }

        .mobile-filter-sheet {
            display: block;
            position: fixed;
            bottom: 0;
            left: 0;
            width: 100%;
            background: #ffffff;
            border-radius: 24px 24px 0 0;
            z-index: 100;
            transform: translateY(100%);
            transition: transform 0.4s cubic-bezier(0.19, 1, 0.22, 1);
            padding: 24px;
            box-shadow: 0 -10px 40px rgba(0, 0, 0, 0.1);
        }

        .mobile-filter-sheet.active {
            transform: translateY(0);
        }

        .sheet-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            font-weight: 800;
            font-size: 1.2rem;
            color: var(--run-dark, #0f172a);
        }

        .close-sheet-btn {
            background: #f1f5f9;
            border: none;
            width: 36px;
            height: 36px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #64748b;
            font-size: 1.2rem;
        }

        .sheet-body {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .sheet-btn {
            background: transparent;
            border: 1px solid #e2e8f0;
            padding: 16px;
            border-radius: 16px;
            font-size: 1rem;
            font-weight: 600;
            color: #64748b;
            text-align: left;
            transition: all 0.2s ease;
        }

        .sheet-btn.active {
            background: var(--run-dark, #0f172a);
            color: white;
            border-color: var(--run-dark, #0f172a);
        }
    }

    @media (max-width: 768px) {
        .video-mask-hero {
            height: 55vh;
            padding-bottom: 40px;
            align-items: center;
        }

        .hero-content-overlay {
            position: absolute;
            bottom: 30px;
        }

        .massive-mask-text {
            font-size: 24vw;
            transform: translateY(-30%);
        }

        .page-subtitle {
            font-size: 1rem;
            margin-bottom: 30px;
        }

        .filter-btn {
            padding: 10px 20px;
            font-size: 0.8rem;
        }

        .filter-bar {
            justify-content: flex-start;
            padding: 5px;
        }
    }
</style>




<section class="faq-section">
    <div class="faq-container">
        <div class="faq-header gs-reveal">
            <span class="faq-badge">KNOW BEFORE YOU GO</span>
            <h2 class="faq-main-title">Runner's Intel.</h2>
        </div>

        <div class="faq-list gs-reveal">
            <div class="faq-item">
                <button class="faq-question">
                    <span>Do I need specific trail shoes for all routes?</span>
                    <i class="bi bi-plus"></i>
                </button>
                <div class="faq-answer">
                    <p>For Coastal routes, standard running shoes are fine. However, for Mountain and Technical trails, we strongly recommend trail-specific footwear with good grip to ensure your safety on rocky terrain.</p>
                </div>
            </div>

            <div class="faq-item">
                <button class="faq-question">
                    <span>What happens if my pace is slower than expected?</span>
                    <i class="bi bi-plus"></i>
                </button>
                <div class="faq-answer">
                    <p>Our "No-Drop Policy" means no runner is left behind. Our guides adjust the pace to yours. These are experiences, not races. We are here to enjoy the landscape, not just the stopwatch.</p>
                </div>
            </div>

            <div class="faq-item">
                <button class="faq-question">
                    <span>Is hotel pickup and drop-off included?</span>
                    <i class="bi bi-plus"></i>
                </button>
                <div class="faq-answer">
                    <p>Yes, for all private and scheduled runs within the Plakias wider area, we provide complimentary transfer to and from the starting point of the route.</p>
                </div>
            </div>

            <div class="faq-item">
                <button class="faq-question">
                    <span>What is included in the price?</span>
                    <i class="bi bi-plus"></i>
                </button>
                <div class="faq-answer">
                    <p>Every run includes a professional local guide, insurance, high-quality photography/video of your run, hydration (water/electrolytes), and a light local snack at the end.</p>
                </div>
            </div>
        </div>
    </div>
</section>


<style>
    .faq-section {
        padding: 120px 0;
        background: #ffffff;
    }

    .faq-container {
        max-width: 900px;
        margin: 0 auto;
        padding: 0 20px;
    }

    .faq-header {
        text-align: center;
        margin-bottom: 60px;
    }

    .faq-badge {
        font-family: 'Courier New', monospace;
        font-size: 0.8rem;
        font-weight: 700;
        color: var(--accent-color);
        letter-spacing: 2px;
    }

    .faq-main-title {
        font-size: 3rem;
        font-weight: 900;
        color: #0f172a;
        margin-top: 10px;
    }

    .faq-list {
        border-top: 1px solid #e2e8f0;
    }

    .faq-item {
        border-bottom: 1px solid #e2e8f0;
    }

    .faq-question {
        width: 100%;
        padding: 30px 0;
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: none;
        border: none;
        cursor: pointer;
        text-align: left;
        font-size: 1.25rem;
        font-weight: 700;
        color: #0f172a;
        transition: color 0.3s ease;
    }

    .faq-question i {
        font-size: 1.5rem;
        color: #94a3b8;
        transition: transform 0.4s cubic-bezier(0.19, 1, 0.22, 1);
    }

    .faq-item.active .faq-question {
        color: var(--accent-color);
    }

    .faq-item.active .faq-question i {
        transform: rotate(45deg);
        /* Το + γίνεται x */
        color: var(--accent-color);
    }

    .faq-answer {
        max-height: 0;
        overflow: hidden;
        transition: max-height 0.4s cubic-bezier(0.19, 1, 0.22, 1);
    }

    .faq-answer p {
        padding-bottom: 30px;
        color: #64748b;
        line-height: 1.8;
        font-size: 1.1rem;
    }
</style>



<div class="booking-modal-overlay" id="customRunModal">
    <div class="booking-modal-window">
        <button class="modal-close-btn" onclick="closeCustomModal()"><i class="bi bi-x-lg"></i></button>

        <div class="modal-progress-container">
            <div class="modal-progress-bar" id="modalProgress"></div>
        </div>

        <form id="customRunForm" class="modal-steps-wrapper">

            <div class="modal-step active" data-step="1">
                <span class="step-counter">01/03</span>
                <h3 class="step-title">Choose your terrain</h3>
                <div class="options-grid">
                    <label class="option-card">
                        <input type="radio" name="terrain" value="coastal">
                        <div class="option-content">
                            <i class="fa fa-water"></i>
                            <span>Coastal</span>
                        </div>
                    </label>
                    <label class="option-card">
                        <input type="radio" name="terrain" value="mountain">
                        <div class="option-content">
                            <i class="fa fa-mountain"></i>
                            <span>Mountain</span>
                        </div>
                    </label>
                    <label class="option-card">
                        <input type="radio" name="terrain" value="mixed">
                        <div class="option-content">
                            <i class="fa fa-shuffle"></i>
                            <span>Mixed Path</span>
                        </div>
                    </label>
                </div>
            </div>

            <div class="modal-step" data-step="2">
                <span class="step-counter">02/03</span>
                <h3 class="step-title">Select Distance</h3>
                <div class="distance-selector-grid">
                    <label class="dist-card">
                        <input type="radio" name="distance" value="5-10k" required>
                        <div class="dist-content">
                            <span class="dist-km">5-10</span>
                            <span class="dist-label">Kilometers</span>
                        </div>
                    </label>
                    <label class="dist-card">
                        <input type="radio" name="distance" value="10-20k">
                        <div class="dist-content">
                            <span class="dist-km">10-20</span>
                            <span class="dist-label">Kilometers</span>
                        </div>
                    </label>
                    <label class="dist-card">
                        <input type="radio" name="distance" value="custom">
                        <div class="dist-content">
                            <span class="dist-km">?</span>
                            <span class="dist-label">Let's talk</span>
                        </div>
                    </label>
                </div>
            </div>

            <div class="modal-step" data-step="3">
                <span class="step-counter">03/03</span>
                <h3 class="step-title">Final Details</h3>
                <div class="input-fields-stack">
                    <input type="text" name="name" id="nameInput" placeholder="Full Name" required>
                    <input type="email" name="email" id="emailInput" placeholder="Email Address" required>

                    <div class="phone-input-wrapper">
                        <input type="tel" id="phone" name="phone" required>
                    </div>

                    <div class="calendar-wrapper">
                        <input type="text" id="runDate" name="date" placeholder="Select Date" required>
                    </div>

                    <textarea name="message" placeholder="Notes (Injuries, pace, etc.)"></textarea>

                    <div class="terms-wrapper">
                        <label class="terms-checkbox-label">
                            <input type="checkbox" name="agree_terms" id="termsCheckbox">
                            <span class="checkmark"></span>
                            <span class="terms-text">
                                I agree to the <a href="/terms" target="_blank">Terms of Service</a> and <a href="/privacy" target="_blank">Privacy Policy</a>.
                            </span>
                        </label>
                    </div>

                </div>
            </div>

            <div class="modal-nav">
                <button type="button" class="btn-prev" id="btnPrev" style="display:none;">Back</button>
                <button type="button" class="btn-next" id="btnNext">Continue <i class="bi bi-arrow-right"></i></button>
                <button type="submit" class="btn-submit" id="btnSubmit" style="display:none;">Submit Request</button>
            </div>

        </form>
    </div>
</div>


<style>
    .booking-modal-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(15, 23, 42, 0.95);
        backdrop-filter: blur(10px);
        z-index: 9999;
        display: none;
        align-items: center;
        justify-content: center;
        padding: 20px;
    }

    .booking-modal-window {
        background: #ffffff;
        width: 100%;
        max-width: 600px;
        border-radius: 30px;
        position: relative;
        padding: 50px;
        overflow: hidden;
    }

    /* Progress Bar */
    .modal-progress-container {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 6px;
        background: #f1f5f9;
    }

    .modal-progress-bar {
        height: 100%;
        background: var(--accent-color);
        width: 33.33%;
        transition: width 0.4s ease;
    }

    /* Modal Elements */
    .modal-close-btn {
        position: absolute;
        top: 25px;
        right: 25px;
        border: none;
        background: #f1f5f9;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        cursor: pointer;
    }

    .step-counter {
        font-family: 'Courier New', monospace;
        color: var(--accent-color);
        font-weight: 700;
        display: block;
        margin-bottom: 10px;
    }

    .step-title {
        font-size: 2rem;
        font-weight: 900;
        color: #0f172a;
        margin-bottom: 30px;
        letter-spacing: -0.02em;
    }

    /* Options Grid */
    .options-grid {
        display: grid;
        grid-template-columns: 1fr 1fr 1fr;
        gap: 15px;
    }

    .option-card {
        cursor: pointer;
        text-align: center;
    }

    .option-card input {
        display: none;
    }

    .option-content {
        padding: 30px 10px;
        border: 2px solid #f1f5f9;
        border-radius: 20px;
        transition: all 0.3s ease;
    }

    .option-content i {
        font-size: 2rem;
        display: block;
        margin-bottom: 10px;
        color: #64748b;
    }

    .option-card input:checked+.option-content {
        border-color: var(--accent-color);
        background: rgba(41, 194, 133, 0.05);
    }

    .option-card input:checked+.option-content i {
        color: var(--accent-color);
    }

    /* Step Visibility */
    .modal-step {
        display: none;
    }

    .modal-step.active {
        display: block;
        animation: stepFade 0.4s ease forwards;
    }


    @keyframes stepFade {
        from {
            opacity: 0;
            transform: translateY(10px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* Controls */
    .modal-nav {
        margin-top: 40px;
        display: flex;
        gap: 15px;
    }

    .btn-next,
    .btn-submit {
        flex: 1;
        background: #0f172a;
        color: #fff;
        border: none;
        padding: 18px;
        border-radius: 100px;
        font-weight: 800;
        cursor: pointer;
        text-transform: uppercase;
    }

    .btn-prev {
        background: #f1f5f9;
        color: #64748b;
        border: none;
        padding: 18px 30px;
        border-radius: 100px;
        font-weight: 700;
        cursor: pointer;
    }

    /* Inputs */
    .input-group-grid input,
    .input-group-grid textarea {
        width: 100%;
        padding: 15px;
        border: 2px solid #f1f5f9;
        border-radius: 12px;
        margin-bottom: 15px;
        font-family: inherit;
    }

    /* Distance Cards Redesign */
    .distance-selector-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 15px;
    }

    .dist-card {
        cursor: pointer;
        position: relative;
    }

    .dist-card input {
        display: none;
    }

    .dist-content {
        background: #f8fafc;
        border: 2px solid #f1f5f9;
        padding: 25px 10px;
        border-radius: 20px;
        text-align: center;
        transition: all 0.3s ease;
    }

    .dist-km {
        font-size: 1.8rem;
        font-weight: 900;
        display: block;
        color: #0f172a;
    }

    .dist-label {
        font-size: 0.7rem;
        text-transform: uppercase;
        letter-spacing: 1px;
        color: #64748b;
    }

    .dist-card input:checked+.dist-content {
        border-color: var(--accent-color);
        background: #ffffff;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.05);
        transform: translateY(-3px);
    }

    /* Phone & Calendar UI */
    .iti {
        width: 100%;
        margin-bottom: 15px;
    }

    /* Plugin width */
    .input-fields-stack input,
    .input-fields-stack textarea {
        width: 100%;
        padding: 16px;
        border: 2px solid #f1f5f9;
        border-radius: 12px;
        margin-bottom: 15px;
        font-size: 1rem;
    }

    .input-fields-stack input:focus {
        border-color: var(--accent-color);
        outline: none;
    }

    /* Validation Error Style */
    input.invalid {
        border-color: #ef4444 !important;
        background: #fff1f2;
    }

    .error-msg {
        color: #ef4444;
        font-size: 0.8rem;
        margin-top: -10px;
        margin-bottom: 10px;
        display: block;
    }

    /* Shake animation για όταν δεν επιλέγει τίποτα */
    @keyframes shakeError {

        0%,
        100% {
            transform: translateX(0);
        }

        25% {
            transform: translateX(-5px);
        }

        75% {
            transform: translateX(5px);
        }
    }

    .shake-error {
        animation: shakeError 0.4s ease;
    }

    /* Loader Spinner */
    .spinner-loader {
        width: 18px;
        height: 18px;
        border: 3px solid rgba(255, 255, 255, 0.3);
        border-radius: 50%;
        border-top-color: #fff;
        display: inline-block;
        animation: spin 0.8s linear infinite;
        margin-right: 10px;
    }

    @keyframes spin {
        to {
            transform: rotate(360deg);
        }
    }

    /* Success/Error State Layout */
    .modal-response-state {
        text-align: center;
        padding: 40px 0;
        animation: stepFade 0.5s ease forwards;
    }

    /* Invalid Input Style */
    input.invalid {
        border-color: #ef4444 !important;
        background: #fff1f2 !important;
    }

    .terms-wrapper {
        margin-top: 10px;
        padding: 0 5px;
    }

    .terms-checkbox-label {
        display: flex;
        align-items: flex-start;
        gap: 12px;
        cursor: pointer;
        font-size: 0.85rem;
        color: #64748b;
        line-height: 1.4;
    }

    .terms-checkbox-label input {
        position: absolute;
        opacity: 0;
        cursor: pointer;
        height: 0;
        width: 0;
    }

    .checkmark {
        width: 20px;
        height: 20px;
        border: 2px solid #e2e8f0;
        border-radius: 6px;
        flex-shrink: 0;
        position: relative;
        transition: all 0.2s ease;
        background: #fff;
    }

    .terms-checkbox-label input:checked+.checkmark {
        background-color: var(--accent-color);
        border-color: var(--accent-color);
    }

    .terms-checkbox-label input:checked+.checkmark::after {
        content: '\F26E';
        /* Bootstrap Icon check */
        font-family: "bootstrap-icons";
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        color: white;
        font-size: 0.8rem;
    }

    .terms-text a {
        color: var(--run-dark);
        text-decoration: underline;
        font-weight: 600;
    }

    .terms-checkbox-label.invalid .checkmark {
        border-color: #ef4444;
        background: #fff1f2;
    }
</style>








<?php
function hook_end_scripts()
{
?>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/ScrollTrigger.min.js"></script>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.19/css/intlTelInput.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.19/js/intlTelInput.min.js"></script>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" type="text/css" href="https://npmcdn.com/flatpickr/dist/themes/dark.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

    <script>
        document.addEventListener("DOMContentLoaded", () => {
            let currentStep = 1;
            const form = document.getElementById('customRunForm');
            const modal = document.getElementById('customRunModal');
            const progressBar = document.getElementById('modalProgress');
            const btnNext = document.getElementById('btnNext');
            const btnPrev = document.getElementById('btnPrev');
            const btnSubmit = document.getElementById('btnSubmit');

            // --- 1. INITIALIZE PLUGINS ---
            const phoneInputField = document.querySelector("#phone");
            const phoneInput = window.intlTelInput(phoneInputField, {
                preferredCountries: ["gr", "gb", "de"],
                separateDialCode: true,
                utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.19/js/utils.js"
            });

            const datePicker = flatpickr("#runDate", {
                minDate: "today",
                dateFormat: "d-m-Y",
                disableMobile: "true"
            });

            // --- 2. MODAL CONTROLS ---
            window.openCustomModal = function() {
                modal.style.display = 'flex';
                document.body.style.overflow = 'hidden';
                resetForm();
            };

            window.closeCustomModal = function() {
                modal.style.display = 'none';
                document.body.style.overflow = '';
            };

            // Close on overlay click
            modal.addEventListener('click', (e) => {
                if (e.target === modal) closeCustomModal();
            });

            // --- 3. NAVIGATION LOGIC ---
            btnNext.addEventListener('click', () => {
                if (validateStep(currentStep)) {
                    goToStep(currentStep + 1);
                }
            });

            btnPrev.addEventListener('click', () => {
                goToStep(currentStep - 1);
            });

            function goToStep(step) {
                document.querySelector(`.modal-step[data-step="${currentStep}"]`).classList.remove('active');
                currentStep = step;
                document.querySelector(`.modal-step[data-step="${currentStep}"]`).classList.add('active');
                updateUI();
            }

            function updateUI() {
                progressBar.style.width = (currentStep / 3) * 100 + '%';
                btnPrev.style.display = (currentStep === 1) ? 'none' : 'block';
                btnNext.style.display = (currentStep === 3) ? 'none' : 'block';
                btnSubmit.style.display = (currentStep === 3) ? 'block' : 'none';
            }

            // --- 4. VALIDATION ENGINE ---
            function validateStep(step) {
                const currentStepEl = document.querySelector(`.modal-step[data-step="${step}"]`);
                let isValid = true;

                // Reset previous errors
                currentStepEl.querySelectorAll('.invalid').forEach(el => el.classList.remove('invalid'));

                if (step === 1 || step === 2) {
                    const radioChecked = currentStepEl.querySelector('input[type="radio"]:checked');
                    if (!radioChecked) {
                        // Shake the cards for visual feedback
                        const container = currentStepEl.querySelector('.options-grid') || currentStepEl.querySelector('.distance-selector-grid');
                        container.classList.add('shake-error');
                        setTimeout(() => container.classList.remove('shake-error'), 500);
                        isValid = false;
                    }
                }

                if (step === 3) {
                    const inputs = currentStepEl.querySelectorAll('input[required]');
                    inputs.forEach(input => {
                        if (!input.value.trim()) {
                            input.classList.add('invalid');
                            isValid = false;
                        }
                        if (input.type === 'email' && !validateEmail(input.value)) {
                            input.classList.add('invalid');
                            isValid = false;
                        }
                    });

                    if (!phoneInput.isValidNumber()) {
                        phoneInputField.classList.add('invalid');
                        isValid = false;
                    }

                    const termsCheckbox = document.getElementById('termsCheckbox');
                    const termsLabel = termsCheckbox.closest('.terms-checkbox-label');

                    if (!termsCheckbox.checked) {
                        termsLabel.classList.add('invalid'); // Γίνεται κόκκινο το δικό μας UI
                        isValid = false;
                    } else {
                        termsLabel.classList.remove('invalid');
                    }

                }
                return isValid;
            }

            function validateEmail(email) {
                return String(email).toLowerCase().match(/^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/);
            }

            // --- 5. AJAX SUBMISSION ---
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                if (!validateStep(3)) return;

                // Show Loader on Button
                const originalBtnText = btnSubmit.innerHTML;
                btnSubmit.disabled = true;
                btnSubmit.innerHTML = '<span class="spinner-loader"></span> Processing...';

                // Prepare Data
                const formData = new FormData(form);
                formData.append('action', 'custom_run_request');
                formData.append('full_phone', phoneInput.getNumber()); // Full international number

                // AJAX POST to /includes/ajax.php
                fetch('/includes/ajax.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            showFinalMessage('success', 'Request Sent!', 'We will contact you shortly to design your perfect run.');
                        } else {
                            showFinalMessage('error', 'Oops!', data.message || 'Something went wrong. Please try again.');
                        }
                    })
                    .catch(error => {
                        showFinalMessage('error', 'Network Error', 'Please check your connection and try again.');
                    })
                    .finally(() => {
                        btnSubmit.disabled = false;
                        btnSubmit.innerHTML = originalBtnText;
                    });
            });

            function showFinalMessage(type, title, text) {
                const wrapper = document.querySelector('.modal-steps-wrapper');
                const icon = type === 'success' ? 'bi-check-circle' : 'bi-exclamation-circle';
                const iconColor = type === 'success' ? 'var(--accent-color)' : '#ef4444';

                wrapper.innerHTML = `
            <div class="modal-response-state">
                <i class="bi ${icon}" style="color: ${iconColor}; font-size: 4rem;"></i>
                <h2 style="margin-top:20px;">${title}</h2>
                <p style="color:#64748b;">${text}</p>
                <button type="button" class="btn-next" onclick="closeCustomModal()" style="margin-top:30px;">Close</button>
            </div>
        `;
            }

            function resetForm() {
                // Επαναφορά της φόρμας αν χρειάζεται να την ξανανοίξει ο χρήστης
                // (Προαιρετικό, ανάλογα αν θες να βλέπει το Success message αν το ξανανοίξει)
            }
        });
    </script>




    <script>
        document.addEventListener("DOMContentLoaded", () => {
            if (typeof gsap !== "undefined") {
                gsap.utils.toArray('.gs-reveal').forEach(function(elem) {
                    gsap.from(elem, {
                        y: 40,
                        opacity: 0,
                        duration: 1,
                        ease: "power3.out",
                        stagger: 0.2
                    });
                });
            }

            const filterBar = document.getElementById('filterBar');
            const filterBtns = document.querySelectorAll('.filter-btn');
            const indicator = document.getElementById('filterIndicator');
            const routesSection = document.querySelector('.routes-list-section');
            const fab = document.getElementById('mobileFilterFab');
            const sheet = document.getElementById('mobileFilterSheet');
            const overlay = document.getElementById('sheetOverlay');
            const closeBtn = document.getElementById('closeSheetBtn');
            const sheetBtns = document.querySelectorAll('.sheet-btn');
            const dff = document.getElementById('desktopFloatingFilter');
            const dffValue = document.getElementById('dffValue');

            function moveIndicator(btn) {
                if (!indicator || !filterBar) return;
                const leftPos = btn.offsetLeft;
                const width = btn.offsetWidth;
                indicator.style.left = `${leftPos}px`;
                indicator.style.width = `${width}px`;

                if (filterBar.scrollWidth > filterBar.clientWidth) {
                    const scrollTarget = btn.offsetLeft - (filterBar.offsetWidth / 2) + (btn.offsetWidth / 2);
                    filterBar.scrollTo({
                        left: scrollTarget,
                        behavior: 'smooth'
                    });
                }
            }

            setTimeout(() => {
                const activeBtn = document.querySelector('.filter-btn.active');
                if (activeBtn) moveIndicator(activeBtn);
            }, 100);

            // --- RESIZE LOGIC & RESET ---
            let isDesktop = window.innerWidth > 992;
            let prevWidth = window.innerWidth;

            window.addEventListener('resize', () => {
                const currentActive = document.querySelector('.filter-btn.active');
                if (currentActive) moveIndicator(currentActive);

                const currentWidth = window.innerWidth;
                const wasDesktop = prevWidth > 992;
                isDesktop = currentWidth > 992;

                // ΑΝ ΑΛΛΑΞΕΙ ΣΥΣΚΕΥΗ (Desktop <-> Mobile), ΚΑΝΟΥΜΕ RESET ΤΑ ΦΙΛΤΡΑ
                if (wasDesktop !== isDesktop) {
                    // Reset Desktop
                    filterBtns.forEach(b => b.classList.remove('active'));
                    const defaultDesktop = document.querySelector('.filter-btn[data-filter="all"]');
                    if (defaultDesktop) {
                        defaultDesktop.classList.add('active');
                        moveIndicator(defaultDesktop);
                        if (dffValue) dffValue.textContent = 'All Routes';
                    }

                    // Reset Mobile
                    mobileFilters = [];
                    sheetBtns.forEach(b => b.classList.remove('active'));
                    if (fabBadge) fabBadge.style.display = 'none';
                    if (mobileClearBtn) mobileClearBtn.style.display = 'none';

                    applyFilterLogic();
                }
                prevWidth = currentWidth;
            });

            setTimeout(() => {
                // Ελέγχουμε αν είμαστε σε κινητό/tablet ΚΑΙ αν η μπάρα όντως έχει κρυμμένο περιεχόμενο (overflow)
                if (window.innerWidth <= 992 && filterBar && filterBar.scrollWidth > filterBar.clientWidth) {

                    // 1. Κάνουμε ένα μικρό scroll προς τα δεξιά (50 pixels)
                    filterBar.scrollBy({
                        left: 50,
                        behavior: 'smooth'
                    });

                    // 2. Μετά από μισό δευτερόλεπτο, το επιστρέφουμε στο 0
                    setTimeout(() => {
                        filterBar.scrollTo({
                            left: 0,
                            behavior: 'smooth'
                        });
                    }, 500);
                }
            }, 1500);

            if (typeof gsap !== "undefined" && typeof ScrollTrigger !== "undefined") {
                gsap.utils.toArray('.gs-parallax').forEach(function(layer) {
                    const depth = layer.getAttribute('data-speed');
                    const movement = -(layer.offsetHeight * depth);
                    gsap.to(layer, {
                        y: movement,
                        ease: "none",
                        scrollTrigger: {
                            trigger: ".routes-list-section",
                            start: "top bottom",
                            end: "bottom top",
                            scrub: true
                        }
                    });
                });
            }

            window.addEventListener('scroll', () => {
                if (routesSection) {
                    const isPastHero = window.scrollY > (routesSection.offsetTop - 300);
                    if (window.innerWidth <= 992 && fab) {
                        isPastHero ? fab.classList.add('show') : fab.classList.remove('show');
                    } else if (window.innerWidth > 992 && dff) {
                        isPastHero ? dff.classList.add('show') : dff.classList.remove('show');
                    }
                }
            });

            if (dff && filterBar) {
                dff.addEventListener('click', () => {
                    window.scrollTo({
                        top: filterBar.offsetTop - 100,
                        behavior: 'smooth'
                    });
                });
            }

            function toggleSheet(show) {
                if (!sheet || !overlay) return;
                if (show) {
                    sheet.classList.add('active');
                    overlay.classList.add('active');
                    document.body.style.overflow = 'hidden';
                } else {
                    sheet.classList.remove('active');
                    overlay.classList.remove('active');
                    document.body.style.overflow = '';
                }
            }

            if (fab) fab.addEventListener('click', () => toggleSheet(true));
            if (closeBtn) closeBtn.addEventListener('click', () => toggleSheet(false));
            if (overlay) overlay.addEventListener('click', () => toggleSheet(false));

            // --- FILTERING LOGIC ---
            let mobileFilters = [];
            const fabBadge = document.getElementById('fabBadge');
            const mobileClearBtn = document.getElementById('mobileClearBtn');

            function applyFilterLogic() {
                const allRoutes = document.querySelectorAll('.route-card-editorial');
                let matchedRoutes = [];

                // Scroll μόνο αν ΔΕΝ είναι ανοιχτό το mobile menu
                if (routesSection && (!sheet || !sheet.classList.contains('active'))) {
                    window.scrollTo({
                        top: routesSection.offsetTop - 100,
                        behavior: 'smooth'
                    });
                }

                allRoutes.forEach(route => {
                    const routeCat = route.getAttribute('data-category');
                    let isMatch = false;

                    if (isDesktop) {
                        const activeDesktop = document.querySelector('.filter-btn.active');
                        const activeVal = activeDesktop ? activeDesktop.getAttribute('data-filter') : 'all';
                        isMatch = (activeVal === 'all' || activeVal === routeCat);
                    } else {
                        isMatch = (mobileFilters.length === 0 || mobileFilters.includes(routeCat));
                    }

                    if (isMatch) {
                        matchedRoutes.push(route);
                    } else {
                        gsap.to(route, {
                            opacity: 0,
                            scale: 0.95,
                            y: 20,
                            duration: 0.3,
                            onComplete: () => route.style.display = "none"
                        });
                    }
                });

                setTimeout(() => {
                    matchedRoutes.forEach(route => route.style.display = "flex");
                    gsap.fromTo(matchedRoutes, {
                        opacity: 0,
                        y: 40
                    }, {
                        opacity: 1,
                        y: 0,
                        duration: 0.6,
                        ease: "power2.out",
                        stagger: 0.15
                    });
                }, 300);
            }

            // DESKTOP: SINGLE SELECT CLICKS
            filterBtns.forEach(btn => {
                btn.addEventListener('click', function() {
                    const text = this.textContent;

                    filterBtns.forEach(b => b.classList.remove('active'));
                    this.classList.add('active');
                    moveIndicator(this);
                    if (dffValue) dffValue.textContent = text;

                    applyFilterLogic();
                });
            });

            // MOBILE: MULTI SELECT CLICKS
            sheetBtns.forEach(btn => {
                btn.addEventListener('click', function() {
                    const val = this.getAttribute('data-filter');

                    if (mobileFilters.includes(val)) {
                        mobileFilters = mobileFilters.filter(f => f !== val);
                        this.classList.remove('active');
                    } else {
                        mobileFilters.push(val);
                        this.classList.add('active');
                    }

                    // Update UI 
                    if (mobileFilters.length > 0) {
                        if (fabBadge) {
                            fabBadge.style.display = 'inline-flex';
                            fabBadge.textContent = mobileFilters.length;
                        }
                        if (mobileClearBtn) mobileClearBtn.style.display = 'block';
                    } else {
                        if (fabBadge) fabBadge.style.display = 'none';
                        if (mobileClearBtn) mobileClearBtn.style.display = 'none';
                    }

                    applyFilterLogic(); // Φιλτράρει live στο background
                    // ΠΡΟΣΟΧΗ: ΔΕΝ κλείνουμε το sheet πια! (Αφαιρέθηκε το toggleSheet(false))
                });
            });

            // MOBILE: CLEAR BUTTON CLICK
            if (mobileClearBtn) {
                mobileClearBtn.addEventListener('click', () => {
                    mobileFilters = [];
                    sheetBtns.forEach(b => b.classList.remove('active'));
                    if (fabBadge) fabBadge.style.display = 'none';
                    mobileClearBtn.style.display = 'none';
                    applyFilterLogic();
                });
            }




            document.querySelectorAll('.faq-question').forEach(button => {
                button.addEventListener('click', () => {
                    const item = button.parentElement;

                    
                    document.querySelectorAll('.faq-item').forEach(otherItem => {
                        if (otherItem !== item) {
                            otherItem.classList.remove('active');
                            otherItem.querySelector('.faq-answer').style.maxHeight = null;
                        }
                    });
                    

                    item.classList.toggle('active');
                    const answer = item.querySelector('.faq-answer');

                    if (item.classList.contains('active')) {
                        answer.style.maxHeight = answer.scrollHeight + "px";
                    } else {
                        answer.style.maxHeight = null;
                    }
                });
            });
        });
    </script>
<?php
}
?>
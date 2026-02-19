<section class="route-single-hero">
    <div class="hero-bg-wrapper gs-parallax" data-speed="0.4">
        <img src="https://images.unsplash.com/photo-1476480862126-209bfaa8edc8?q=80&w=2000&auto=format&fit=crop" alt="Route Peak" class="hero-bg-img">
    </div>
    <div class="hero-overlay"></div>

    <div class="route-hero-content">
        <span class="route-hero-badge gs-reveal">PEAK CHALLENGE</span>
        <h1 class="route-hero-title gs-reveal">Kouroupa<br>Summit.</h1>
        <div class="route-hero-location gs-reveal">
            <i class="bi bi-geo-alt-fill"></i> South Crete, Plakias Region
        </div>
    </div>

    <div class="route-stats-bar gs-reveal">
        <div class="stat-block">
            <span class="stat-label">Distance</span>
            <span class="stat-value">14 <small>KM</small></span>
        </div>
        <div class="stat-block">
            <span class="stat-label">Elevation Gain</span>
            <span class="stat-value">+850 <small>M</small></span>
        </div>
        <div class="stat-block">
            <span class="stat-label">Difficulty</span>
            <span class="stat-value">Advanced</span>
        </div>
        <div class="stat-block">
            <span class="stat-label">Est. Time</span>
            <span class="stat-value">03:30 <small>HRS</small></span>
        </div>
    </div>
</section>



<style>
    .route-single-hero {
        position: relative;
        height: 90vh;
        min-height: 700px;
        display: flex;
        align-items: center;
        overflow: hidden;
        background-color: var(--run-dark, #0f172a);
    }

    .hero-bg-wrapper {
        position: absolute;
        top: -10%;
        left: 0;
        width: 100%;
        height: 120%;
        z-index: 1;
        pointer-events: none;
    }

    .hero-bg-img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        filter: contrast(1.1) brightness(0.9);
    }

    .hero-overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        z-index: 2;
        background: linear-gradient(to bottom, rgba(15, 23, 42, 0.2) 0%, rgba(15, 23, 42, 0.8) 70%, rgba(15, 23, 42, 1) 100%);
        pointer-events: none;
    }

    .route-hero-content {
        position: relative;
        z-index: 3;
        width: 100%;
        max-width: 1400px;
        margin: 0 auto;
        padding: 0 40px;
        transform: translateY(-50px);
    }

    .route-hero-badge {
        display: inline-block;
        font-family: 'Courier New', monospace;
        font-size: 0.9rem;
        font-weight: 800;
        color: var(--accent-color);
        letter-spacing: 3px;
        margin-bottom: 20px;
        text-transform: uppercase;
    }

    .route-hero-title {
        font-size: clamp(4rem, 8vw, 7rem);
        font-weight: 900;
        line-height: 0.95;
        color: #ffffff;
        margin: 0 0 30px 0;
        letter-spacing: -0.03em;
        text-transform: uppercase;
    }

    .route-hero-location {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        font-size: 1.2rem;
        font-weight: 600;
        color: #cbd5e1;
        letter-spacing: 1px;
    }

    .route-hero-location i {
        color: var(--accent-color);
        font-size: 1.4rem;
    }

    .route-stats-bar {
        position: absolute;
        bottom: 0;
        left: 0;
        width: 100%;
        z-index: 4;
        background: rgba(255, 255, 255, 0.03);
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
        border-top: 1px solid rgba(255, 255, 255, 0.05);
        display: flex;
        justify-content: center;
    }

    .stat-block {
        flex: 1;
        max-width: 350px;
        padding: 35px 40px;
        display: flex;
        flex-direction: column;
        gap: 8px;
        border-right: 1px solid rgba(255, 255, 255, 0.05);
    }

    .stat-block:last-child {
        border-right: none;
    }

    .stat-label {
        font-family: 'Courier New', monospace;
        font-size: 0.8rem;
        font-weight: 700;
        color: #94a3b8;
        text-transform: uppercase;
        letter-spacing: 2px;
    }

    .stat-value {
        font-size: 2rem;
        font-weight: 800;
        color: #ffffff;
        line-height: 1;
    }

    .stat-value small {
        font-size: 1rem;
        color: var(--accent-color);
        font-weight: 700;
        margin-left: 4px;
    }

    @media (max-width: 992px) {
        .route-single-hero {
            height: auto;
            min-height: 100svh;
            padding: 120px 0 0 0;
            flex-direction: column;
            justify-content: flex-end;
            align-items: flex-start;
        }

        .route-hero-content {
            padding: 0 20px;
            transform: translateY(0);
            margin-bottom: 40px;
            margin-top: auto;
        }

        .route-hero-title {
            font-size: clamp(2.8rem, 12vw, 4rem);
            margin-bottom: 20px;
            word-wrap: break-word;
            hyphens: auto;
        }

        .route-hero-badge {
            font-size: 0.8rem;
            margin-bottom: 15px;
        }

        .route-stats-bar {
            position: relative;
            width: 100%;
            display: grid;
            grid-template-columns: 1fr 1fr;
            background: rgba(15, 23, 42, 0.85);
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }

        .stat-block {
            max-width: 100%;
            padding: 20px 15px;
            align-items: center;
            text-align: center;
            border-right: 1px solid rgba(255, 255, 255, 0.05);
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        }

        .stat-block:nth-child(even) {
            border-right: none;
        }

        .stat-block:nth-child(3),
        .stat-block:nth-child(4) {
            border-bottom: none;
        }

        .stat-value {
            font-size: 1.5rem;
        }

        .stat-label {
            font-size: 0.7rem;
        }
    }
</style>


<section class="route-briefing-section">
    <div class="briefing-container">

        <div class="briefing-content gs-reveal">
            <h2 class="briefing-title">The Experience</h2>
            <p class="briefing-lead">The day starts with the morning mist rising from the Libyan Sea. This route is not about speed; it's about endurance and taking in the raw, untouched beauty of South Crete.</p>
            <p class="briefing-text">As you leave the coastal path, the terrain quickly shifts to rocky ascents. The scent of wild thyme is everywhere. You will pass by ancient shepherd shelters before reaching the Kouroupa summit, where the 360-degree view of the island will make you forget your burning quads.</p>

            <div class="coach-note">
                <div class="coach-avatar">
                    <i class="bi bi-lightning-charge-fill"></i>
                </div>
                <div class="coach-text">
                    <strong>Coach's Tip</strong>
                    <span>"Keep your heart rate low for the first 3km. The real climb starts at kilometer 4, and you'll need fresh legs to navigate the loose rocks."</span>
                </div>
            </div>
        </div>

        <div class="briefing-visuals gs-reveal">
            <div class="map-wrapper">
                <div class="map-placeholder">
                    <i class="bi bi-map"></i>
                    <span>Interactive Map (GPS Track)</span>
                </div>
            </div>

            <div class="elevation-wrapper">
                <span class="elevation-title">Elevation Profile</span>
                <svg viewBox="0 0 500 150" class="elevation-svg" preserveAspectRatio="none">
                    <path d="M0,140 L50,130 L100,80 L150,90 L200,40 L250,50 L300,20 L350,60 L400,100 L450,120 L500,130 L500,150 L0,150 Z" fill="rgba(41, 194, 133, 0.1)" />
                    <path d="M0,140 L50,130 L100,80 L150,90 L200,40 L250,50 L300,20 L350,60 L400,100 L450,120 L500,130" fill="none" stroke="var(--accent-color)" stroke-width="4" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
                <div class="elevation-labels">
                    <span>0 KM</span>
                    <span>7 KM</span>
                    <span>14 KM</span>
                </div>
            </div>
        </div>

    </div>
</section>


<style>
    .route-briefing-section {
        padding: 120px 0;
        background-color: #ffffff;
        color: #0f172a;
    }

    .briefing-container {
        max-width: 1400px;
        margin: 0 auto;
        padding: 0 40px;
        display: flex;
        gap: 80px;
        align-items: flex-start;
    }

    /* Left Column - Story */
    .briefing-content {
        flex: 1;
        max-width: 600px;
    }

    .briefing-title {
        font-size: 2.5rem;
        font-weight: 900;
        margin-bottom: 30px;
        letter-spacing: -0.02em;
    }

    .briefing-lead {
        font-size: 1.3rem;
        font-weight: 600;
        line-height: 1.6;
        color: #334155;
        margin-bottom: 20px;
    }

    .briefing-text {
        font-size: 1.1rem;
        line-height: 1.8;
        color: #64748b;
        margin-bottom: 40px;
    }

    /* Coach's Note */
    .coach-note {
        background: #f8fafc;
        border-left: 4px solid var(--accent-color);
        padding: 30px;
        border-radius: 0 20px 20px 0;
        display: flex;
        gap: 20px;
        align-items: flex-start;
    }

    .coach-avatar {
        width: 50px;
        height: 50px;
        background: #ffffff;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.05);
        flex-shrink: 0;
    }

    .coach-avatar i {
        color: var(--accent-color);
        font-size: 1.5rem;
    }

    .coach-text {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .coach-text strong {
        font-family: 'Courier New', monospace;
        font-size: 0.9rem;
        text-transform: uppercase;
        letter-spacing: 1px;
        color: #0f172a;
    }

    .coach-text span {
        font-size: 1.05rem;
        font-style: italic;
        color: #475569;
        line-height: 1.6;
    }

    /* Right Column - Visuals */
    .briefing-visuals {
        flex: 1;
        width: 100%;
    }

    .map-wrapper {
        width: 100%;
        height: 350px;
        background: #f1f5f9;
        border-radius: 20px;
        overflow: hidden;
        margin-bottom: 30px;
        border: 1px solid #e2e8f0;
    }

    /* Προσωρινό styling μέχρι να μπει αληθινός χάρτης */
    .map-placeholder {
        width: 100%;
        height: 100%;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        color: #94a3b8;
        gap: 15px;
    }

    .map-placeholder i {
        font-size: 3rem;
        color: #cbd5e1;
    }

    .elevation-wrapper {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 20px;
        padding: 30px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.02);
    }

    .elevation-title {
        display: block;
        font-family: 'Courier New', monospace;
        font-size: 0.85rem;
        font-weight: 700;
        color: #64748b;
        margin-bottom: 20px;
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    .elevation-svg {
        width: 100%;
        height: 150px;
        overflow: visible;
    }

    .elevation-labels {
        display: flex;
        justify-content: space-between;
        margin-top: 15px;
        font-family: 'Courier New', monospace;
        font-size: 0.75rem;
        color: #94a3b8;
        border-top: 1px dashed #e2e8f0;
        padding-top: 10px;
    }

    /* Mobile Responsive */
    @media (max-width: 992px) {
        .route-briefing-section {
            padding: 80px 0;
        }

        .briefing-container {
            flex-direction: column;
            padding: 0 20px;
            gap: 50px;
        }

        .briefing-content,
        .briefing-visuals {
            max-width: 100%;
        }
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

<?php
}
?>
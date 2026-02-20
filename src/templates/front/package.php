<?php
// Υποθέτουμε ότι έχεις κάνει include τα Database, Packages, Availability κλπ.

$package_id = isset($_GET['id']) ? (int)$_GET['id'] :4; // Δοκίμασε 3, 5, ή 6

$pkgObj = new Packages();
$availObj = new Availability();

// 1. Fetch Basic Package Data
$route = $pkgObj->fetchPackage($package_id);

if ($route) {
    // 2. Fetch Assigned Guides
    $guide_ids = $pkgObj->getTherapistIds($package_id);

    // 3. Υπολογισμός Πραγματικών Κρατήσεων & Spots
    $db = Database::getInstance();
    $countSql = "SELECT COUNT(*) as c FROM bookings WHERE package_id = ? AND status != 'canceled'";
    $res = $db->query($countSql, [$package_id]);
    $real_bookings = ($res && count($res) > 0) ? (int)$res[0]->c : 0;

    $total_bookings = (int)$route->manual_bookings + $real_bookings;
    $spots_left = max(0, (int)$route->max_attendants - $total_bookings);
    $spots_percentage = ($route->max_attendants > 0) ? ($total_bookings / $route->max_attendants) * 100 : 0;

    // 4. SMART ENGINE: Εύρεση Επόμενης Διαθέσιμης Ημερομηνίας
    $next_available_date = null;
    $is_fixed_event = ($route->is_group == 1);
    $is_private = ($route->is_group == 0 && $route->max_attendants == 1);

    if ($is_fixed_event && !empty($route->start_datetime)) {
        $next_available_date = date('D, d M Y - H:i', strtotime((string)$route->start_datetime));
    } else {
        if (!empty($guide_ids)) {
            foreach ($guide_ids as $gid) {
                $avail_date = $availObj->findFirstAvailableDate($gid, $package_id, $route->duration_minutes ?: 60, 90);
                if ($avail_date) {
                    if ($next_available_date === null || strtotime($avail_date) < strtotime($next_available_date)) {
                        $next_available_date = date('D, d M Y', strtotime($avail_date));
                    }
                }
            }
        }
    }

    // 5. JSON Parsing & Mocks (Αυτά που θα μπουν στο backend αργότερα)
    $includes_arr = !empty($route->includes) ? json_decode($route->includes, true) : [];
    $gear_arr = !empty($route->gear_requirements) ? json_decode($route->gear_requirements, true) : ['mandatory' => [], 'optional' => []];
    $has_gear = (!empty($gear_arr['mandatory']) || !empty($gear_arr['optional']));

    
    // MOCK DATA (Μέχρι να τα φτιάξεις στη βάση)
    $gallery = [
        "https://images.unsplash.com/photo-1476480862126-209bfaa8edc8?q=80&w=2000",
        "https://images.unsplash.com/photo-1502224562085-639556652f33?q=80&w=1000",
        "https://images.unsplash.com/photo-1517260739337-6799d239ce83?q=80&w=1000"
    ];
    $hero_img = $gallery[0];
    $coach_tip = "Keep your heart rate low for the first 3km. You'll need fresh legs for the final ascent.";
?>

    <section class="route-single-hero">
        <div class="hero-bg-wrapper gs-parallax" data-speed="0.4">
            <img src="<?= htmlspecialchars($hero_img) ?>" alt="<?= htmlspecialchars($route->title) ?>" class="hero-bg-img">
        </div>
        <div class="hero-overlay"></div>

        <div class="route-hero-content">
            <span class="route-hero-badge gs-reveal"><?= htmlspecialchars($route->category_name ?? 'RUNNING EXPERIENCE') ?></span>
            <h1 class="route-hero-title gs-reveal"><?= htmlspecialchars($route->title) ?></h1>
            <div class="route-hero-location gs-reveal">
                <i class="bi bi-geo-alt-fill"></i> Operating Area Route
            </div>
        </div>

        <div class="route-stats-bar gs-reveal">
            <div class="stat-block">
                <span class="stat-label">Distance</span>
                <span class="stat-value"><?= (float)$route->distance_km ?> <small>KM</small></span>
            </div>
            <div class="stat-block">
                <span class="stat-label">Elevation Gain</span>
                <span class="stat-value">+<?= (int)$route->elevation_gain ?> <small>M</small></span>
            </div>
            <div class="stat-block">
                <span class="stat-label">Difficulty</span>
                <span class="stat-value"><?= htmlspecialchars($route->difficulty) ?></span>
            </div>
            <div class="stat-block">
                <span class="stat-label">Est. Time</span>
                <span class="stat-value"><?= (int)$route->duration_minutes ?> <small>MIN</small></span>
            </div>
        </div>
    </section>

    <section class="route-briefing-section">
        <div class="briefing-container">

            <div class="briefing-content gs-reveal">

                <?php if (count($gallery) >= 3): ?>
                    <div class="route-gallery">
                        <a href="<?= htmlspecialchars($gallery[0]) ?>" class="glightbox gallery-main-link" data-gallery="route-gallery">
                            <img src="<?= htmlspecialchars($gallery[0]) ?>" alt="Route View" class="gallery-main">
                            <div class="hover-zoom"><i class="bi bi-zoom-in"></i></div>
                        </a>
                        <div class="gallery-side">
                            <a href="<?= htmlspecialchars($gallery[1]) ?>" class="glightbox" data-gallery="route-gallery">
                                <img src="<?= htmlspecialchars($gallery[1]) ?>" alt="Route Detail 1">
                                <div class="hover-zoom"><i class="bi bi-zoom-in"></i></div>
                            </a>
                            <a href="<?= htmlspecialchars($gallery[2]) ?>" class="glightbox" data-gallery="route-gallery">
                                <img src="<?= htmlspecialchars($gallery[2]) ?>" alt="Route Detail 2">
                                <div class="hover-zoom"><i class="bi bi-zoom-in"></i></div>
                            </a>
                        </div>
                    </div>
                <?php endif; ?>

                <div class="route-dna">
                    <div class="dna-item"><i class="bi bi-geo-alt"></i>
                        <div class="dna-text"><strong>Distance</strong><span><?= (float)$route->distance_km ?> km</span></div>
                    </div>
                    <div class="dna-item"><i class="bi bi-graph-up-arrow"></i>
                        <div class="dna-text"><strong>Elevation</strong><span>+<?= (int)$route->elevation_gain ?>m</span></div>
                    </div>
                    <div class="dna-item"><i class="bi bi-signpost-split"></i>
                        <div class="dna-text"><strong>Terrain</strong><span><?= htmlspecialchars($route->terrain_type) ?></span></div>
                    </div>
                    <div class="dna-item"><i class="bi bi-speedometer2"></i>
                        <div class="dna-text"><strong>Level</strong><span><?= htmlspecialchars($route->difficulty) ?></span></div>
                    </div>
                    <div class="dna-item"><i class="bi bi-stopwatch"></i>
                        <div class="dna-text"><strong>Duration</strong><span>~<?= (int)$route->duration_minutes ?> min</span></div>
                    </div>
                </div>

                <h2 class="briefing-title">The Experience</h2>
                <p class="briefing-text"><?= nl2br(htmlspecialchars($route->description)) ?></p>

                <div class="coach-note">
                    <div class="coach-avatar"><i class="bi bi-lightning-charge-fill"></i></div>
                    <div class="coach-text">
                        <strong>Coach's Tip</strong>
                        <span>"<?= htmlspecialchars($coach_tip) ?>"</span>
                    </div>
                </div>

                <div class="route-logistics">
                    <?php if (!empty($includes_arr)): ?>
                        <div class="logistics-block">
                            <h3 class="logistics-title">What's Included</h3>
                            <ul class="logistics-list">
                                <?php foreach ($includes_arr as $item): ?>
                                    <li><i class="bi bi-check2"></i> <?= htmlspecialchars($item) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <?php if ($has_gear): ?>
                        <div class="logistics-block">
                            <h3 class="logistics-title">Equipment</h3>
                            <?php if (!empty($gear_arr['mandatory'])): ?>
                                <h4 class="gear-subtitle mandatory-text">Mandatory</h4>
                                <ul class="logistics-list mb-4">
                                    <?php foreach ($gear_arr['mandatory'] as $item): ?>
                                        <li class="mandatory"><i class="bi bi-exclamation-triangle"></i> <?= htmlspecialchars($item) ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php endif; ?>
                            <?php if (!empty($gear_arr['optional'])): ?>
                                <h4 class="gear-subtitle optional-text">Bring With You (Optional)</h4>
                                <ul class="logistics-list">
                                    <?php foreach ($gear_arr['optional'] as $item): ?>
                                        <li><i class="bi bi-check2-circle"></i> <?= htmlspecialchars($item) ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="sidebar-column gs-reveal">
                <div class="sticky-wrapper">

                    <div class="sidebar-widget map-widget">
                        <div class="map-visual-placeholder">
                            <i class="bi bi-geo"></i>
                            <div class="radar-circle"></div>
                        </div>
                        <div class="map-info">
                            <div class="map-text">
                                <strong>Operating Area</strong>
                                <span>Check starting point</span>
                            </div>
                            <?php if (!empty($route->meeting_point_url)): ?>
                                <a href="<?= htmlspecialchars($route->meeting_point_url) ?>" target="_blank" class="btn-map-link">Map <i class="bi bi-arrow-up-right"></i></a>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="sidebar-widget booking-widget">
                        <div class="bw-header">
                            <div class="bw-price-wrap">
                                <span class="bw-price">€<?= number_format((float)$route->price, 2) ?></span>
                                <span class="bw-person"><?= $is_private ? '/ session' : '/ person' ?></span>
                            </div>
                            <?php if ($is_private): ?>
                                <span class="bw-badge badge-private">1-ON-1 PRIVATE</span>
                            <?php else: ?>
                                <span class="bw-badge badge-group">GROUP RUN</span>
                            <?php endif; ?>
                        </div>

                        <div class="bw-body">
                            <?php if ($next_available_date === null): ?>
                                <div class="bw-info-row">
                                    <i class="bi bi-calendar-x" style="color:#ef4444;"></i>
                                    <div class="bw-info-text">
                                        <strong style="color:#ef4444;">Currently Unavailable</strong>
                                        <span>No upcoming dates scheduled.</span>
                                    </div>
                                </div>
                                <button class="btn-book-action" disabled style="background:#e2e8f0; color:#94a3b8; cursor:not-allowed;">Check Back Later</button>

                            <?php elseif ($is_fixed_event): ?>
                                <div class="bw-info-row">
                                    <i class="bi bi-calendar-event"></i>
                                    <div class="bw-info-text">
                                        <strong><?= $next_available_date ?></strong>
                                        <span>Fixed departure</span>
                                    </div>
                                </div>
                                <div class="bw-stock-box">
                                    <div class="stock-labels">
                                        <span>Availability</span>
                                        <strong><?= $spots_left ?> Spots Left</strong>
                                    </div>
                                    <div class="stock-bar-bg">
                                        <div class="stock-bar-fill" style="width: <?= $spots_percentage ?>%;"></div>
                                    </div>
                                </div>
                                <button class="btn-book-action">Secure Your Spot</button>

                            <?php else: ?>
                                <div class="bw-info-row">
                                    <i class="bi bi-calendar-check"></i>
                                    <div class="bw-info-text">
                                        <strong>Next: <?= $next_available_date ?></strong>
                                        <span>Multiple dates available</span>
                                    </div>
                                </div>
                                <div class="bw-info-row">
                                    <i class="bi bi-people"></i>
                                    <div class="bw-info-text">
                                        <?php if ($is_private): ?>
                                            <strong>Personalized Experience</strong>
                                            <span>Tailored just for you</span>
                                        <?php else: ?>
                                            <strong>Small Group</strong>
                                            <span>Max <?= (int)$route->max_attendants ?> runners</span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <button class="btn-book-action">View Dates & Book</button>
                            <?php endif; ?>
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </section>

<?php
} else {
    echo "<div style='padding: 150px; text-align:center;'><h2>Route not found</h2></div>";
}
?>

<style>
    /* HERO STYLES */
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
        font-size: clamp(3rem, 6vw, 6rem);
        font-weight: 900;
        line-height: 0.95;
        color: #ffffff;
        margin: 0 0 30px 0;
        letter-spacing: -0.03em;
        text-transform: uppercase;
        word-wrap: break-word;
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

    /* CONTENT & SIDEBAR STYLES */
    .route-briefing-section {
        padding: 80px 0 120px 0;
        background-color: #ffffff;
        color: #0f172a;
    }

    .briefing-container {
        max-width: 1400px;
        margin: 0 auto;
        padding: 0 40px;
        display: flex;
        gap: 80px;
    }

    .briefing-content {
        flex: 1.2;
        max-width: 750px;
    }

    /* Image Gallery (Bento & Lightbox) */
    .route-gallery {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 15px;
        margin-bottom: 50px;
    }

    .route-gallery a {
        position: relative;
        display: block;
        overflow: hidden;
        border-radius: 12px;
        cursor: pointer;
    }

    .gallery-main {
        width: 100%;
        height: 400px;
        object-fit: cover;
        transition: transform 0.4s ease;
    }

    .gallery-side {
        display: flex;
        flex-direction: column;
        gap: 15px;
    }

    .gallery-side img {
        width: 100%;
        height: calc(200px - 7.5px);
        object-fit: cover;
        transition: transform 0.4s ease;
    }

    .hover-zoom {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(15, 23, 42, 0.3);
        display: flex;
        align-items: center;
        justify-content: center;
        opacity: 0;
        transition: opacity 0.3s ease;
        color: white;
        font-size: 2rem;
    }

    .route-gallery a:hover img {
        transform: scale(1.05);
    }

    .route-gallery a:hover .hover-zoom {
        opacity: 1;
    }

    /* Route DNA */
    .route-dna {
        display: flex;
        flex-wrap: wrap;
        gap: 20px;
        margin-bottom: 50px;
        padding: 30px;
        background: #f8fafc;
        border-radius: 20px;
        border: 1px solid #e2e8f0;
    }

    .dna-item {
        display: flex;
        align-items: center;
        gap: 15px;
        flex: 1 1 calc(33.333% - 20px);
        min-width: 150px;
    }

    .dna-item i {
        font-size: 1.8rem;
        color: var(--accent-color);
    }

    .dna-text {
        display: flex;
        flex-direction: column;
    }

    .dna-text strong {
        font-size: 0.8rem;
        color: #94a3b8;
        text-transform: uppercase;
        letter-spacing: 1px;
        font-family: 'Courier New', monospace;
        font-weight: 700;
    }

    .dna-text span {
        font-size: 1.1rem;
        color: #0f172a;
        font-weight: 800;
    }

    .briefing-title {
        font-size: 2.5rem;
        font-weight: 900;
        margin-bottom: 25px;
        letter-spacing: -0.02em;
    }

    .briefing-text {
        font-size: 1.1rem;
        line-height: 1.8;
        color: #64748b;
        margin-bottom: 25px;
    }

    .coach-note {
        background: #f8fafc;
        border-left: 4px solid var(--accent-color);
        padding: 30px;
        border-radius: 0 20px 20px 0;
        display: flex;
        gap: 20px;
        align-items: flex-start;
        margin-bottom: 50px;
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

    .route-logistics {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 40px;
        border-top: 1px solid #e2e8f0;
        padding-top: 40px;
        margin-top: 20px;
    }

    .logistics-title {
        font-size: 1.2rem;
        font-weight: 800;
        margin-bottom: 20px;
        color: #0f172a;
    }

    .gear-subtitle {
        font-family: 'Courier New', monospace;
        font-size: 0.85rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 1px;
        margin-bottom: 15px;
        margin-top: 20px;
    }

    .mandatory-text {
        color: #ef4444;
    }

    .optional-text {
        color: #64748b;
    }

    .mb-4 {
        margin-bottom: 25px;
    }

    .logistics-list {
        list-style: none;
        padding: 0;
        margin: 0;
        display: flex;
        flex-direction: column;
        gap: 15px;
    }

    .logistics-list li {
        display: flex;
        align-items: flex-start;
        gap: 12px;
        font-size: 1.05rem;
        color: #475569;
        line-height: 1.4;
    }

    .logistics-list li i {
        color: var(--accent-color);
        font-size: 1.2rem;
        margin-top: -2px;
    }

    .logistics-list li.mandatory i {
        color: #ef4444;
    }

    /* Right Column & Sticky */
    .sidebar-column {
        flex: 0.8;
        width: 100%;
        position: relative;
    }

    .sticky-wrapper {
        position: sticky;
        top: 100px;
        display: flex;
        flex-direction: column;
        gap: 30px;
    }

    .sidebar-widget {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.03);
    }

    .map-visual-placeholder {
        height: 180px;
        background: #f8fafc;
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
        overflow: hidden;
    }

    .map-visual-placeholder i {
        font-size: 3rem;
        color: #cbd5e1;
        z-index: 2;
    }

    .radar-circle {
        position: absolute;
        width: 100px;
        height: 100px;
        background: rgba(41, 194, 133, 0.1);
        border-radius: 50%;
        z-index: 1;
        animation: radarPulse 3s infinite;
    }

    @keyframes radarPulse {
        0% {
            transform: scale(0.5);
            opacity: 1;
        }

        100% {
            transform: scale(3);
            opacity: 0;
        }
    }

    .map-info {
        padding: 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: #ffffff;
        border-top: 1px solid #e2e8f0;
    }

    .map-text {
        display: flex;
        flex-direction: column;
    }

    .map-text strong {
        font-size: 1rem;
        color: #0f172a;
    }

    .map-text span {
        font-size: 0.8rem;
        color: #64748b;
    }

    .btn-map-link {
        font-size: 0.85rem;
        font-weight: 700;
        color: var(--accent-color);
        text-decoration: none;
        display: flex;
        align-items: center;
        gap: 5px;
    }

    .bw-header {
        background: #0f172a;
        padding: 25px 30px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        color: white;
    }

    .bw-price-wrap {
        display: flex;
        align-items: baseline;
        gap: 5px;
    }

    .bw-price {
        font-size: 2rem;
        font-weight: 900;
        line-height: 1;
    }

    .bw-person {
        font-size: 0.9rem;
        color: #94a3b8;
    }

    .bw-badge {
        font-family: 'Courier New', monospace;
        font-size: 0.75rem;
        font-weight: 700;
        padding: 6px 12px;
        border-radius: 100px;
        letter-spacing: 1px;
    }

    .badge-private {
        background: rgba(41, 194, 133, 0.2);
        color: #29c285;
    }

    .badge-group {
        background: rgba(245, 158, 11, 0.2);
        color: #f59e0b;
    }

    .bw-body {
        padding: 30px;
    }

    .bw-info-row {
        display: flex;
        align-items: flex-start;
        gap: 15px;
        margin-bottom: 25px;
    }

    .bw-info-row i {
        font-size: 1.5rem;
        color: #64748b;
        margin-top: -3px;
    }

    .bw-info-text {
        display: flex;
        flex-direction: column;
        gap: 3px;
        width: 100%;
    }

    .bw-info-text strong {
        font-size: 1.05rem;
        color: #0f172a;
    }

    .bw-info-text span {
        font-size: 0.9rem;
        color: #64748b;
    }

    .bw-stock-box {
        background: #f8fafc;
        padding: 20px;
        border-radius: 12px;
        margin-bottom: 25px;
        border: 1px solid #e2e8f0;
    }

    .stock-labels {
        display: flex;
        justify-content: space-between;
        margin-bottom: 10px;
        font-size: 0.9rem;
    }

    .stock-labels strong {
        color: #f59e0b;
    }

    .stock-bar-bg {
        width: 100%;
        height: 6px;
        background: #e2e8f0;
        border-radius: 10px;
        overflow: hidden;
    }

    .stock-bar-fill {
        height: 100%;
        background: #f59e0b;
        border-radius: 10px;
        transition: width 0.5s ease;
    }

    .btn-book-action {
        width: 100%;
        background: var(--accent-color);
        color: #0f172a;
        border: none;
        padding: 18px;
        border-radius: 12px;
        font-size: 1.1rem;
        font-weight: 800;
        cursor: pointer;
        transition: all 0.3s ease;
        text-transform: uppercase;
    }

    .btn-book-action:hover:not([disabled]) {
        background: #0f172a;
        color: #ffffff;
        transform: translateY(-3px);
    }

    /* MOBILE RESPONSIVE */
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

        .briefing-container {
            flex-direction: column;
            padding: 0 20px;
            gap: 50px;
        }

        .briefing-content,
        .sidebar-column {
            max-width: 100%;
            flex: 1;
        }

        .route-gallery {
            grid-template-columns: 1fr;
        }

        .gallery-main {
            height: 300px;
        }

        .gallery-side {
            flex-direction: row;
        }

        .gallery-side img {
            height: 150px;
        }

        .route-dna {
            padding: 20px;
        }

        .dna-item {
            flex: 1 1 calc(50% - 20px);
        }

        .route-logistics {
            grid-template-columns: 1fr;
            gap: 30px;
        }

        .sticky-wrapper {
            position: relative;
            top: 0;
        }
    }
</style>

<?php
function hook_end_scripts()
{
?>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/ScrollTrigger.min.js"></script>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/glightbox/dist/css/glightbox.min.css" />
    <script src="https://cdn.jsdelivr.net/gh/mcstudios/glightbox/dist/js/glightbox.min.js"></script>

    <script>
        document.addEventListener("DOMContentLoaded", () => {
            // Initialize Lightbox
            const lightbox = GLightbox({
                selector: '.glightbox',
                touchNavigation: true,
                loop: true
            });

            // GSAP Animations
            gsap.utils.toArray('.gs-reveal').forEach(function(elem) {
                gsap.fromTo(elem, {
                    y: 50,
                    opacity: 0
                }, {
                    y: 0,
                    opacity: 1,
                    duration: 1,
                    ease: "power3.out",
                    scrollTrigger: {
                        trigger: elem,
                        start: "top 85%"
                    }
                });
            });
        });
    </script>
<?php
}
?>
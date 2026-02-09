<?php
// 1. Fetch Packages & Calculate Availability & Therapists
$db = Database::getInstance();
$pkgModel = new Packages();
$allPackages = $pkgModel->fetchPackages();

$groupedServices = [
    'adults' => [],
    'parents' => [],
    'kids' => [],
    'psychosexual' => []
];

if ($allPackages) {
    $now = time();
    $oneWeekAgo = $now - (7 * 24 * 60 * 60); // Timestamp 7 days ago

    foreach ($allPackages as $p) {
        $p->is_past = false; // Default flag

        // A. Availability & Time Logic
        if ($p->is_group == 1) {
            // 1. Time Filter: Check if date exists
            if (!empty($p->start_datetime)) {
                $eventTs = strtotime($p->start_datetime);

                // RULE: If older than 1 week, skip completely (don't show)
                if ($eventTs < $oneWeekAgo) {
                    continue;
                }

                // RULE: If past (but within the last week), mark as past
                if ($eventTs < $now) {
                    $p->is_past = true;
                }
            }

            // 2. Counts
            $res = $db->query("SELECT COUNT(*) as c FROM bookings WHERE package_id = ? AND status != 'canceled'", [$p->id]);
            $realBookings = ($res) ? (int)$res[0]->c : 0;
            $totalUsed = $realBookings + (int)$p->manual_bookings;
            $max = (int)$p->max_attendants;
            $p->remaining_spots = max(0, $max - $totalUsed);
            $p->is_full = ($p->remaining_spots === 0);
        } else {
            $p->remaining_spots = null;
            $p->is_full = false;
        }

        // B. Fetch Therapists
        $p->assigned_therapists = $db->query(
            "SELECT t.first_name, t.last_name, t.avatar 
             FROM therapists t 
             JOIN package_therapists pt ON pt.therapist_id = t.id 
             WHERE pt.package_id = ?",
            [$p->id]
        );

        // C. Grouping
        $cat = $p->category;
        if (array_key_exists($cat, $groupedServices)) {
            $groupedServices[$cat][] = $p;
        }
    }
}
?>

<style>
    .services-hero {
        padding: 160px 0 80px 0;
    }

    .mini-tag {
        color: var(--alma-orange);
        font-weight: 700;
        letter-spacing: 2px;
        font-size: 0.8rem;
        display: block;
        margin-bottom: 10px;
    }

    .font-serif {
        font-family: 'Manrope', serif;
        color: var(--alma-nav-text);
    }

    .serif-italic {
        font-style: italic;
        color: var(--alma-orange);
    }

    .service-editorial-section {
        padding: 0;
    }

    .section-divider {
        width: 100%;
        height: 1px;
        background: linear-gradient(90deg, transparent, rgba(51, 76, 71, 0.1), transparent);
        margin: 20px 0;
    }

    .editorial-title {
        font-family: 'Playfair Display', serif;
        font-size: 2.5rem;
        line-height: 1.2;
        color: var(--alma-nav-text);
        padding-top: 10px;
    }

    .title-accent-line {
        width: 40px;
        height: 3px;
        background-color: var(--alma-orange);
        margin-top: 15px;
        border-radius: 2px;
    }

    .clean-card {
        background: #ffffff;
        padding: 45px;
        border-radius: 16px;
        box-shadow: 0 10px 30px rgba(51, 76, 71, 0.05);
        border: 1px solid rgba(255, 255, 255, 0.8);
        transition: all 0.3s ease;
    }

    .clean-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 15px 40px rgba(51, 76, 71, 0.08);
    }

    .card-heading {
        font-family: 'Manrope', sans-serif;
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--alma-nav-text);
        margin: 0;
    }

    .card-text {
        font-size: 1.05rem;
        color: var(--alma-text);
        line-height: 1.7;
        margin-bottom: 15px;
    }

    .border-top-soft {
        border-top: 1px solid rgba(0, 0, 0, 0.06);
    }

    /* --- BADGES --- */
    .minimal-badge {
        font-size: 0.7rem;
        font-weight: 700;
        text-transform: uppercase;
        color: var(--alma-nav-text);
        letter-spacing: 0.5px;
        background: var(--alma-accent);
        padding: 6px 12px;
        border-radius: 6px;
        border: 1px solid rgba(0, 0, 0, 0.05);
    }

    .badge-sage {
        color: #fff;
        background: var(--alma-bg-button-main);
        border: none;
    }

    /* Availability Badges */
    .badge-spot-alert {
        font-size: 0.7rem;
        font-weight: 700;
        padding: 5px 10px;
        border-radius: 4px;
        display: inline-flex;
        align-items: center;
        gap: 5px;
        margin-left: 8px;
        /* Space from title */
    }

    .badge-spot-alert.low {
        background-color: #fff3cd;
        color: #856404;
        border: 1px solid #ffeeba;
    }

    .badge-spot-alert.critical {
        background-color: #f8d7da;
        color: #721c24;
        border: 1px solid #f5c6cb;
        animation: pulse-red 2s infinite;
    }

    .badge-spot-alert.plenty {
        background-color: #d4edda;
        color: #155724;
        border: 1px solid #c3e6cb;
    }

    .badge-spot-alert.sold-out {
        background-color: #e2e3e5;
        color: #383d41;
        border: 1px solid #d6d8db;
        text-decoration: line-through;
    }

    @keyframes pulse-red {
        0% {
            box-shadow: 0 0 0 0 rgba(220, 53, 69, 0.4);
        }

        70% {
            box-shadow: 0 0 0 6px rgba(220, 53, 69, 0);
        }

        100% {
            box-shadow: 0 0 0 0 rgba(220, 53, 69, 0);
        }
    }

    /* Buttons */
    .btn-clean-action {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        font-weight: 700;
        color: var(--alma-nav-text);
        text-decoration: none;
        border-bottom: 2px solid var(--alma-orange);
        padding-bottom: 3px;
        transition: all 0.3s ease;
    }

    .btn-clean-action:hover {
        color: var(--alma-orange);
        gap: 15px;
    }

    .btn-clean-action.disabled {
        pointer-events: none;
        opacity: 0.5;
        border-bottom-color: #ccc;
        color: #999;
    }

    .btn-clean-outline {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        font-weight: 600;
        color: var(--alma-nav-text);
        text-decoration: none;
        font-size: 0.9rem;
        border: 1px solid var(--alma-nav-text);
        padding: 10px 25px;
        border-radius: 50px;
        transition: all 0.3s ease;
    }

    .btn-clean-outline:hover {
        background-color: var(--alma-nav-text);
        color: #fff;
    }

    /* Sticky Nav */
    .nav-capsule-wrapper {
        position: fixed;
        bottom: 40px;
        left: 50%;
        transform: translateX(-50%) translateY(100px);
        z-index: 100;
        display: flex;
        justify-content: center;
        width: auto;
        max-width: 90%;
        opacity: 0;
        visibility: hidden;
        transition: all 0.3s cubic-bezier(0.25, 1, 0.5, 1);
    }

    .nav-capsule-wrapper.is-visible {
        transform: translateX(-50%) translateY(0);
        opacity: 1;
        visibility: visible;
    }

    .nav-capsule {
        position: relative;
        display: flex;
        align-items: center;
        background: rgba(253, 251, 245, 0.95);
        backdrop-filter: blur(12px);
        padding: 6px;
        border-radius: 50px;
        box-shadow: 0 10px 40px rgba(51, 76, 71, 0.2);
        border: 1px solid rgba(255, 255, 255, 0.5);
        overflow-x: auto;
    }

    .nav-capsule::-webkit-scrollbar {
        display: none;
    }

    .nav-glider {
        position: absolute;
        top: 6px;
        left: 6px;
        height: calc(100% - 12px);
        background-color: var(--alma-bg-button-main);
        border-radius: 40px;
        z-index: 1;
        transition: all 0.4s cubic-bezier(0.25, 1, 0.5, 1);
        width: 0;
        opacity: 0;
        box-shadow: 0 2px 10px rgba(137, 194, 170, 0.3);
    }

    .nav-link-item {
        position: relative;
        z-index: 2;
        padding: 12px 25px;
        font-size: 0.95rem;
        font-weight: 700;
        color: var(--alma-nav-text);
        text-decoration: none;
        white-space: nowrap;
        transition: color 0.3s ease;
        border-radius: 40px;
    }

    .nav-link-item a:hover {
        color: var(--alma-nav-text) !important;
    }

    .nav-link-item.active {
        color: #fff;
    }

    @media (max-width: 991px) {
        .editorial-title {
            font-size: 2rem;
            margin-bottom: 20px;
        }

        .clean-card {
            padding: 30px;
        }

        .nav-capsule-wrapper {
            bottom: 20px;
            width: 95%;
            justify-content: center;
        }

        .nav-capsule {
            width: 100%;
            justify-content: space-between;
            padding: 4px;
        }

        .nav-link-item {
            padding: 10px 10px;
            font-size: 0.75rem;
            flex: 1;
            text-align: center;
        }
    }

    /* --- STACKED AVATARS --- */
    .avatar-stack {
        display: flex;
        align-items: center;
        margin-top: 12px;
    }

    .avatar-circle {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        border: 2px solid #fff;
        object-fit: cover;
        margin-left: -10px;
        background-color: var(--alma-bg-button-main);
        /* Fallback χρώμα */
        color: #fff;
        font-size: 0.7rem;
        font-weight: 700;
        display: flex;
        align-items: center;
        justify-content: center;
        text-transform: uppercase;
        position: relative;
        transition: transform 0.2s ease, z-index 0.2s;
    }

    .avatar-circle:first-child {
        margin-left: 0;
        z-index: 5;
    }

    .avatar-circle:nth-child(2) {
        z-index: 4;
    }

    .avatar-circle:nth-child(3) {
        z-index: 3;
    }

    .avatar-circle:hover {
        transform: translateY(-3px);
        z-index: 10;
    }

    .therapist-names-label {
        font-size: 0.8rem;
        color: #777;
        margin-left: 8px;
    }
</style>

<section class="services-hero">
    <div class="container text-center">
        <span class="mini-tag">ΟΙ ΥΠΗΡΕΣΙΕΣ ΜΑΣ</span>
        <h1 class="display-4 font-serif mt-3">
            Οδηγός Φροντίδας & <span class="serif-italic">Εξέλιξης.</span>
        </h1>
    </div>
</section>

<div class="nav-capsule-wrapper">
    <div class="nav-capsule">
        <div class="nav-glider"></div>
        <a href="#adults" class="nav-link-item active">Ενήλικες</a>
        <a href="#parents" class="nav-link-item">Γονείς</a>
        <a href="#kids" class="nav-link-item">Παιδιά & Έφηβοι</a>
        <a href="#psychosexual" class="nav-link-item">Ψυχοσεξουαλική</a>
    </div>
</div>

<div id="services-wrapper">

    <section id="adults" class="service-editorial-section">
        <div class="container">
            <div class="row py-3 py-md-5">
                <div class="col-lg-3 mb-4 mb-lg-0">
                    <h2 class="editorial-title">Ενήλικες</h2>
                    <div class="title-accent-line"></div>
                </div>
                <div class="col-lg-8 offset-lg-1">
                    <?php if (!empty($groupedServices['adults'])): ?>
                        <?php foreach ($groupedServices['adults'] as $svc): ?>
                            <div class="clean-card reveal-item mb-4">
                                <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
                                    <div class="d-flex align-items-center flex-wrap gap-2">
                                        <h3 class="card-heading m-0"><?php echo htmlspecialchars($svc->title); ?></h3>

                                        <?php if ($svc->is_group == 1): ?>
                                            <?php if ($svc->is_past): ?>
                                                <span class="badge-spot-alert sold-out" style="background:#eee; color:#777; border-color:#ddd;"><i class="bi bi-calendar-x"></i> ΟΛΟΚΛΗΡΩΘΗΚΕ</span>
                                            <?php elseif ($svc->is_full): ?>
                                                <span class="badge-spot-alert sold-out"><i class="bi bi-x-circle"></i> ΕΞΑΝΤΛΗΘΗΚΕ</span>
                                            <?php elseif ($svc->remaining_spots <= 3): ?>
                                                <span class="badge-spot-alert critical" title="Κλείστε άμεσα!"><i class="bi bi-fire"></i> Μόνο <?php echo $svc->remaining_spots; ?> θέσεις!</span>
                                            <?php else: ?>
                                                <span class="badge-spot-alert plenty"><i class="bi bi-ticket-perforated"></i> <?php echo $svc->remaining_spots; ?> θέσεις</span>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    </div>

                                    <?php if ($svc->is_group == 1): ?>
                                        <span class="minimal-badge badge-sage">Workshop</span>
                                    <?php else: ?>
                                        <span class="minimal-badge">
                                            <?php
                                            if ($svc->type === 'mixed') echo 'Online & Δια Ζώσης';
                                            elseif ($svc->type === 'inPerson') echo 'Δια Ζώσης';
                                            elseif ($svc->type === 'online') echo 'Online';
                                            else echo htmlspecialchars(ucfirst($svc->type));
                                            ?>
                                        </span>
                                    <?php endif; ?>
                                </div>

                                <?php if (!empty($svc->assigned_therapists)): ?>
                                    <div class="avatar-stack" style="margin-top: 10px; display: flex; align-items: center;">
                                        <?php foreach ($svc->assigned_therapists as $th): ?>
                                            <?php
                                            $fullName = htmlspecialchars($th->first_name . ' ' . $th->last_name);
                                            $initials = mb_substr($th->first_name, 0, 1) . mb_substr($th->last_name, 0, 1);
                                            $avatarPath = !empty($th->avatar) ? htmlspecialchars($th->avatar) : '';
                                            ?>
                                            <?php if ($avatarPath): ?>
                                                <img src="<?php echo $avatarPath; ?>" class="avatar-circle" title="<?php echo $fullName; ?>" alt="<?php echo $fullName; ?>"
                                                    style="width: 32px; height: 32px; border-radius: 50%; border: 2px solid #fff; object-fit: cover; margin-left: -10px; position: relative; background: #eee;">
                                            <?php else: ?>
                                                <div class="avatar-circle" title="<?php echo $fullName; ?>"
                                                    style="width: 32px; height: 32px; border-radius: 50%; border: 2px solid #fff; margin-left: -10px; background-color: var(--alma-bg-button-main, #567c6d); color: #fff; font-size: 0.7rem; font-weight: 700; display: flex; align-items: center; justify-content: center; text-transform: uppercase; position: relative;">
                                                    <?php echo $initials; ?>
                                                </div>
                                            <?php endif; ?>
                                        <?php endforeach; ?>

                                        <?php if (count($svc->assigned_therapists) === 1): ?>
                                            <span class="therapist-names-label" style="font-size: 0.85rem; color: #666; margin-left: 8px; font-weight: 600;">
                                                <?php echo htmlspecialchars($svc->assigned_therapists[0]->first_name . ' ' . $svc->assigned_therapists[0]->last_name); ?>
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                    <style>
                                        .avatar-stack .avatar-circle:first-child {
                                            margin-left: 0 !important;
                                            z-index: 5;
                                        }
                                    </style>
                                <?php endif; ?>

                                <p class="card-text mt-3"><?php echo nl2br(htmlspecialchars($svc->description)); ?></p>

                                <div class="mt-4 pt-3 border-top-soft">
                                    <?php if ($svc->is_group == 1 && $svc->is_past): ?>
                                        <span class="btn-clean-action disabled text-muted" style="border-bottom-color: transparent;">
                                            Ολοκληρώθηκε <i class="bi bi-check2-all ms-2"></i>
                                        </span>
                                    <?php elseif ($svc->is_group == 1 && $svc->is_full): ?>
                                        <a href="/contact?subject=Waiting_List_<?php echo urlencode($svc->title); ?>" class="btn-clean-outline">Λίστα Αναμονής <i class="bi bi-hourglass ms-2"></i></a>
                                    <?php else: ?>
                                        <a href="#"
                                            class="btn-clean-action open-booking-modal"
                                            data-service-id="<?php echo $svc->id; ?>"
                                            data-remaining-spots="<?php echo $svc->remaining_spots; ?>">
                                            Κλείστε Ραντεβού <i class="bi bi-arrow-right ms-2"></i>
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="text-muted fst-italic">Δεν υπάρχουν διαθέσιμες υπηρεσίες.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>

    <div class="section-divider"></div>

    <section id="parents" class="service-editorial-section">
        <div class="container">
            <div class="row py-3 py-md-5">
                <div class="col-lg-3 mb-4 mb-lg-0">
                    <h2 class="editorial-title">Γονείς & <br>Οικογένεια</h2>
                    <div class="title-accent-line"></div>
                </div>
                <div class="col-lg-8 offset-lg-1">
                    <?php if (!empty($groupedServices['parents'])): ?>
                        <?php foreach ($groupedServices['parents'] as $svc): ?>
                            <div class="clean-card reveal-item mb-4">
                                <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
                                    <div class="d-flex align-items-center flex-wrap gap-2">
                                        <h3 class="card-heading m-0"><?php echo htmlspecialchars($svc->title); ?></h3>

                                        <?php if ($svc->is_group == 1): ?>
                                            <?php if ($svc->is_past): ?>
                                                <span class="badge-spot-alert sold-out" style="background:#eee; color:#777; border-color:#ddd;"><i class="bi bi-calendar-x"></i> ΟΛΟΚΛΗΡΩΘΗΚΕ</span>
                                            <?php elseif ($svc->is_full): ?>
                                                <span class="badge-spot-alert sold-out"><i class="bi bi-x-circle"></i> ΕΞΑΝΤΛΗΘΗΚΕ</span>
                                            <?php elseif ($svc->remaining_spots <= 3): ?>
                                                <span class="badge-spot-alert critical"><i class="bi bi-fire"></i> Μόνο <?php echo $svc->remaining_spots; ?> θέσεις!</span>
                                            <?php else: ?>
                                                <span class="badge-spot-alert plenty"><i class="bi bi-ticket-perforated"></i> <?php echo $svc->remaining_spots; ?> θέσεις</span>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    </div>

                                    <?php if ($svc->is_group == 1): ?>
                                        <span class="minimal-badge badge-sage">Workshop</span>
                                    <?php else: ?>
                                        <span class="minimal-badge">
                                            <?php
                                            if ($svc->type === 'mixed') echo 'Online & Δια Ζώσης';
                                            elseif ($svc->type === 'inPerson') echo 'Δια Ζώσης';
                                            elseif ($svc->type === 'online') echo 'Online';
                                            else echo htmlspecialchars(ucfirst($svc->type));
                                            ?>
                                        </span>
                                    <?php endif; ?>
                                </div>

                                <?php if (!empty($svc->assigned_therapists)): ?>
                                    <div class="avatar-stack" style="margin-top: 10px; display: flex; align-items: center;">
                                        <?php foreach ($svc->assigned_therapists as $th): ?>
                                            <?php
                                            $fullName = htmlspecialchars($th->first_name . ' ' . $th->last_name);
                                            $initials = mb_substr($th->first_name, 0, 1) . mb_substr($th->last_name, 0, 1);
                                            $avatarPath = !empty($th->avatar) ? htmlspecialchars($th->avatar) : '';
                                            ?>
                                            <?php if ($avatarPath): ?>
                                                <img src="<?php echo $avatarPath; ?>" class="avatar-circle" title="<?php echo $fullName; ?>" alt="<?php echo $fullName; ?>"
                                                    style="width: 32px; height: 32px; border-radius: 50%; border: 2px solid #fff; object-fit: cover; margin-left: -10px; position: relative; background: #eee;">
                                            <?php else: ?>
                                                <div class="avatar-circle" title="<?php echo $fullName; ?>"
                                                    style="width: 32px; height: 32px; border-radius: 50%; border: 2px solid #fff; margin-left: -10px; background-color: var(--alma-bg-button-main, #567c6d); color: #fff; font-size: 0.7rem; font-weight: 700; display: flex; align-items: center; justify-content: center; text-transform: uppercase; position: relative;">
                                                    <?php echo $initials; ?>
                                                </div>
                                            <?php endif; ?>
                                        <?php endforeach; ?>

                                        <?php if (count($svc->assigned_therapists) === 1): ?>
                                            <span class="therapist-names-label" style="font-size: 0.85rem; color: #666; margin-left: 8px; font-weight: 600;">
                                                <?php echo htmlspecialchars($svc->assigned_therapists[0]->first_name . ' ' . $svc->assigned_therapists[0]->last_name); ?>
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                    <style>
                                        .avatar-stack .avatar-circle:first-child {
                                            margin-left: 0 !important;
                                            z-index: 5;
                                        }
                                    </style>
                                <?php endif; ?>

                                <p class="card-text mt-3"><?php echo nl2br(htmlspecialchars($svc->description)); ?></p>

                                <div class="mt-4 pt-3 border-top-soft">
                                    <?php if ($svc->is_group == 1 && $svc->is_past): ?>
                                        <span class="btn-clean-action disabled text-muted" style="border-bottom-color: transparent;">
                                            Ολοκληρώθηκε <i class="bi bi-check2-all ms-2"></i>
                                        </span>
                                    <?php elseif ($svc->is_group == 1 && $svc->is_full): ?>
                                        <a href="/contact?subject=Waiting_List_<?php echo urlencode($svc->title); ?>" class="btn-clean-outline">Λίστα Αναμονής <i class="bi bi-hourglass ms-2"></i></a>
                                    <?php else: ?>
                                        <a href="#"
                                            class="btn-clean-action open-booking-modal"
                                            data-service-id="<?php echo $svc->id; ?>"
                                            data-remaining-spots="<?php echo $svc->remaining_spots; ?>">
                                            Κλείστε Ραντεβού <i class="bi bi-arrow-right ms-2"></i>
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>

    <div class="section-divider"></div>

    <section id="kids" class="service-editorial-section">
        <div class="container">
            <div class="row py-3 py-md-5">
                <div class="col-lg-3 mb-4 mb-lg-0">
                    <h2 class="editorial-title">Παιδιά & <br>Έφηβοι</h2>
                    <div class="title-accent-line"></div>
                </div>
                <div class="col-lg-8 offset-lg-1">
                    <?php if (!empty($groupedServices['kids'])): ?>
                        <?php foreach ($groupedServices['kids'] as $svc): ?>
                            <div class="clean-card reveal-item mb-4">
                                <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
                                    <div class="d-flex align-items-center flex-wrap gap-2">
                                        <h3 class="card-heading m-0"><?php echo htmlspecialchars($svc->title); ?></h3>
                                        <?php if ($svc->is_group == 1): ?>
                                            <?php if ($svc->is_past): ?>
                                                <span class="badge-spot-alert sold-out" style="background:#eee; color:#777; border-color:#ddd;"><i class="bi bi-calendar-x"></i> ΟΛΟΚΛΗΡΩΘΗΚΕ</span>
                                            <?php elseif ($svc->is_full): ?>
                                                <span class="badge-spot-alert sold-out"><i class="bi bi-x-circle"></i> ΕΞΑΝΤΛΗΘΗΚΕ</span>
                                            <?php elseif ($svc->remaining_spots <= 3): ?>
                                                <span class="badge-spot-alert critical"><i class="bi bi-fire"></i> Μόνο <?php echo $svc->remaining_spots; ?> θέσεις!</span>
                                            <?php else: ?>
                                                <span class="badge-spot-alert plenty"><i class="bi bi-ticket-perforated"></i> <?php echo $svc->remaining_spots; ?> θέσεις</span>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    </div>

                                    <?php if ($svc->is_group == 1): ?>
                                        <span class="minimal-badge badge-sage">Workshop</span>
                                    <?php else: ?>
                                        <span class="minimal-badge">
                                            <?php
                                            if ($svc->type === 'mixed') echo 'Online & Δια Ζώσης';
                                            elseif ($svc->type === 'inPerson') echo 'Δια Ζώσης';
                                            elseif ($svc->type === 'online') echo 'Online';
                                            else echo htmlspecialchars(ucfirst($svc->type));
                                            ?>
                                        </span>
                                    <?php endif; ?>
                                </div>

                                <?php if (!empty($svc->assigned_therapists)): ?>
                                    <div class="avatar-stack" style="margin-top: 10px; display: flex; align-items: center;">
                                        <?php foreach ($svc->assigned_therapists as $th): ?>
                                            <?php
                                            $fullName = htmlspecialchars($th->first_name . ' ' . $th->last_name);
                                            $initials = mb_substr($th->first_name, 0, 1) . mb_substr($th->last_name, 0, 1);
                                            $avatarPath = !empty($th->avatar) ? htmlspecialchars($th->avatar) : '';
                                            ?>
                                            <?php if ($avatarPath): ?>
                                                <img src="<?php echo $avatarPath; ?>" class="avatar-circle" title="<?php echo $fullName; ?>" alt="<?php echo $fullName; ?>"
                                                    style="width: 32px; height: 32px; border-radius: 50%; border: 2px solid #fff; object-fit: cover; margin-left: -10px; position: relative; background: #eee;">
                                            <?php else: ?>
                                                <div class="avatar-circle" title="<?php echo $fullName; ?>"
                                                    style="width: 32px; height: 32px; border-radius: 50%; border: 2px solid #fff; margin-left: -10px; background-color: var(--alma-bg-button-main, #567c6d); color: #fff; font-size: 0.7rem; font-weight: 700; display: flex; align-items: center; justify-content: center; text-transform: uppercase; position: relative;">
                                                    <?php echo $initials; ?>
                                                </div>
                                            <?php endif; ?>
                                        <?php endforeach; ?>

                                        <?php if (count($svc->assigned_therapists) === 1): ?>
                                            <span class="therapist-names-label" style="font-size: 0.85rem; color: #666; margin-left: 8px; font-weight: 600;">
                                                <?php echo htmlspecialchars($svc->assigned_therapists[0]->first_name . ' ' . $svc->assigned_therapists[0]->last_name); ?>
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                    <style>
                                        .avatar-stack .avatar-circle:first-child {
                                            margin-left: 0 !important;
                                            z-index: 5;
                                        }
                                    </style>
                                <?php endif; ?>

                                <p class="card-text mt-3"><?php echo nl2br(htmlspecialchars($svc->description)); ?></p>

                                <div class="mt-4 pt-3 border-top-soft">
                                    <?php if ($svc->is_group == 1 && $svc->is_past): ?>
                                        <span class="btn-clean-action disabled text-muted" style="border-bottom-color: transparent;">
                                            Ολοκληρώθηκε <i class="bi bi-check2-all ms-2"></i>
                                        </span>
                                    <?php elseif ($svc->is_group == 1 && $svc->is_full): ?>
                                        <a href="/contact?subject=Waiting_List_<?php echo urlencode($svc->title); ?>" class="btn-clean-outline">Λίστα Αναμονής <i class="bi bi-hourglass ms-2"></i></a>
                                    <?php else: ?>
                                        <a href="#"
                                            class="btn-clean-action open-booking-modal"
                                            data-service-id="<?php echo $svc->id; ?>"
                                            data-remaining-spots="<?php echo $svc->remaining_spots; ?>">
                                            Κλείστε Ραντεβού <i class="bi bi-arrow-right ms-2"></i>
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>

    <div class="section-divider"></div>

    <section id="psychosexual" class="service-editorial-section">
        <div class="container">
            <div class="row py-3 py-md-5">
                <div class="col-lg-3 mb-4 mb-lg-0">
                    <h2 class="editorial-title">Εξειδικευμένη <br>Θεραπεία</h2>
                    <div class="title-accent-line"></div>
                </div>
                <div class="col-lg-8 offset-lg-1">
                    <?php if (!empty($groupedServices['psychosexual'])): ?>
                        <?php foreach ($groupedServices['psychosexual'] as $svc): ?>
                            <div class="clean-card reveal-item mb-4">
                                <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
                                    <h3 class="card-heading"><?php echo htmlspecialchars($svc->title); ?></h3>

                                    <?php if ($svc->is_group == 1): ?>
                                        <?php if ($svc->is_past): ?>
                                            <span class="badge-spot-alert sold-out" style="background:#eee; color:#777; border-color:#ddd;"><i class="bi bi-calendar-x"></i> ΟΛΟΚΛΗΡΩΘΗΚΕ</span>
                                        <?php elseif ($svc->is_full): ?>
                                            <span class="badge-spot-alert sold-out"><i class="bi bi-x-circle"></i> ΕΞΑΝΤΛΗΘΗΚΕ</span>
                                        <?php else: ?>
                                            <span class="badge-spot-alert plenty"><i class="bi bi-ticket-perforated"></i> <?php echo $svc->remaining_spots; ?> θέσεις</span>
                                        <?php endif; ?>
                                    <?php endif; ?>

                                    <span class="minimal-badge">
                                        <?php
                                        if ($svc->type === 'mixed') echo 'Online & Δια Ζώσης';
                                        elseif ($svc->type === 'inPerson') echo 'Δια Ζώσης';
                                        elseif ($svc->type === 'online') echo 'Online';
                                        else echo htmlspecialchars(ucfirst($svc->type));
                                        ?>
                                    </span>
                                </div>

                                <?php if (!empty($svc->assigned_therapists)): ?>
                                    <div class="avatar-stack" style="margin-top: 10px; display: flex; align-items: center;">
                                        <?php foreach ($svc->assigned_therapists as $th): ?>
                                            <?php
                                            $fullName = htmlspecialchars($th->first_name . ' ' . $th->last_name);
                                            $initials = mb_substr($th->first_name, 0, 1) . mb_substr($th->last_name, 0, 1);
                                            $avatarPath = !empty($th->avatar) ? htmlspecialchars($th->avatar) : '';
                                            ?>
                                            <?php if ($avatarPath): ?>
                                                <img src="<?php echo $avatarPath; ?>" class="avatar-circle" title="<?php echo $fullName; ?>" alt="<?php echo $fullName; ?>"
                                                    style="width: 32px; height: 32px; border-radius: 50%; border: 2px solid #fff; object-fit: cover; margin-left: -10px; position: relative; background: #eee;">
                                            <?php else: ?>
                                                <div class="avatar-circle" title="<?php echo $fullName; ?>"
                                                    style="width: 32px; height: 32px; border-radius: 50%; border: 2px solid #fff; margin-left: -10px; background-color: var(--alma-bg-button-main, #567c6d); color: #fff; font-size: 0.7rem; font-weight: 700; display: flex; align-items: center; justify-content: center; text-transform: uppercase; position: relative;">
                                                    <?php echo $initials; ?>
                                                </div>
                                            <?php endif; ?>
                                        <?php endforeach; ?>

                                        <?php if (count($svc->assigned_therapists) === 1): ?>
                                            <span class="therapist-names-label" style="font-size: 0.85rem; color: #666; margin-left: 8px; font-weight: 600;">
                                                <?php echo htmlspecialchars($svc->assigned_therapists[0]->first_name . ' ' . $svc->assigned_therapists[0]->last_name); ?>
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                    <style>
                                        .avatar-stack .avatar-circle:first-child {
                                            margin-left: 0 !important;
                                            z-index: 5;
                                        }
                                    </style>
                                <?php endif; ?>

                                <p class="card-text mt-3"><?php echo nl2br(htmlspecialchars($svc->description)); ?></p>

                                <div class="mt-4 pt-3 border-top-soft">
                                    <?php if ($svc->is_group == 1 && $svc->is_past): ?>
                                        <span class="btn-clean-action disabled text-muted" style="border-bottom-color: transparent;">
                                            Ολοκληρώθηκε <i class="bi bi-check2-all ms-2"></i>
                                        </span>
                                    <?php elseif ($svc->is_group == 1 && $svc->is_full): ?>
                                        <a href="/contact?subject=Waiting_List_<?php echo urlencode($svc->title); ?>" class="btn-clean-outline">Λίστα Αναμονής <i class="bi bi-hourglass ms-2"></i></a>
                                    <?php else: ?>
                                        <a href="#"
                                            class="btn-clean-action open-booking-modal"
                                            data-service-id="<?php echo $svc->id; ?>"
                                            data-remaining-spots="<?php echo $svc->remaining_spots; ?>">
                                            Κλείστε Ραντεβού <i class="bi bi-arrow-right ms-2"></i>
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>

</div>


<div class="modal fade" id="bookingModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-fullscreen-lg-down">
        <div class="modal-content border-0 shadow-lg overflow-hidden" style="border-radius: 20px;">

            <div class="modal-header border-0 pb-0 d-flex flex-column align-items-center bg-white" style="z-index: 10;">
                <div class="w-100 d-flex justify-content-between align-items-center mb-3">
                    <h5 class="modal-title fw-bold font-serif" id="modalPkgTitle">Κράτηση Συνεδρίας</h5>
                    <button type="button" class="btn-close m-0" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="w-100 px-md-5 py-3">
                    <ul class="step-progress">
                        <li class="active">Επιλογές</li>
                        <li>Στοιχεία</li>
                        <li>Ημερομηνία</li>
                        <li>Ολοκλήρωση</li>
                    </ul>
                </div>
            </div>

            <div class="modal-body position-relative p-0" style="min-height: 500px; background-color: #f9fbfb;">

                <div id="wizardLoader" class="d-none position-absolute w-100 h-100 top-0 start-0 d-flex flex-column align-items-center justify-content-center bg-white" style="z-index: 50; opacity: 0.95;">
                    <div class="spinner-border text-primary mb-3" role="status"></div>
                    <span class="text-muted fw-bold small tracking-wide">ΦΟΡΤΩΣΗ...</span>
                </div>

                <div id="globalError" class="alert alert-danger position-absolute top-50 start-50 translate-middle-x mt-3 d-none shadow-sm" style="z-index: 60; width: 90%; max-width: 500px; border-radius: 10px;">
                    <i class="bi bi-exclamation-circle-fill me-2"></i> <span id="errorMsg"></span>
                </div>

                <form id="bookingForm" class="h-100 d-flex flex-column">

                    <div class="wizard-step active h-100 p-4 p-md-5 overflow-auto" data-step="0">



                        <div class="row justify-content-center g-4">

                            <div class="col-12 col-md-10" id="therapistSection">
                                <label class="d-block text-secondary small fw-bold mb-3 text-uppercase text-center" style="letter-spacing: 1px; font-size: 0.75rem;">ΕΠΙΛΟΓΗ ΘΕΡΑΠΕΥΤΗ</label>
                                <div class="d-flex gap-3 flex-wrap justify-content-center" id="therapistList">
                                </div>
                                <input type="hidden" id="selectedTherapistId">
                            </div>

                            <div class="col-12 col-md-10" id="typeSection">
                                <label class="d-block text-secondary small fw-bold mb-3 mt-4 text-uppercase text-center" style="letter-spacing: 1px; font-size: 0.75rem;">ΤΡΟΠΟΣ</label>
                                <div class="row g-3">
                                    <div class="col-6">
                                        <label class="custom-radio-card h-100">
                                            <input type="radio" name="sessionType" value="online">
                                            <div class="card-content py-3 text-center">
                                                <i class="bi bi-camera-video-fill fs-5 text-primary mb-2 d-block"></i>
                                                <span class="fw-bold d-block small">Online</span>
                                            </div>
                                        </label>
                                    </div>
                                    <div class="col-6">
                                        <label class="custom-radio-card h-100">
                                            <input type="radio" name="sessionType" value="inPerson">
                                            <div class="card-content py-3 text-center">
                                                <i class="bi bi-geo-alt-fill fs-5 text-warning mb-2 d-block"></i>
                                                <span class="fw-bold d-block small">Δια Ζώσης</span>
                                            </div>
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12 col-md-10 d-none" id="groupFixedInfo">
                                <div class="alert alert-light border text-center p-3">
                                    <i class="bi bi-calendar-check text-primary mb-2 fs-4 d-block"></i>
                                    <h6 class="fw-bold mb-1">Ημερομηνία Workshop</h6>
                                    <div id="groupDateText" class="text-muted small">...</div>
                                </div>
                            </div>


                            <div class="mb-5">
                                <label class="d-block text-secondary small fw-bold mb-2 text-uppercase" style="letter-spacing: 1px; font-size: 0.75rem;">ΕΠΙΛΕΓΜΕΝΟ ΠΑΚΕΤΟ</label>
                                <div class="d-flex align-items-center justify-content-between p-3 border rounded-3 bg-white shadow-sm">
                                    <div>
                                        <h5 class="fw-bold text-dark mb-1" id="modalPkgTitleDesc">Loading...</h5>

                                        <span class="text-muted small" id="pkgDurationText">
                                            <div class="spinner-border spinner-border-sm text-secondary"></div>
                                        </span>
                                    </div>
                                    <div class="text-end">
                                        <span class="fs-6 fw-bold text-primary" id="pkgPriceText"></span>
                                    </div>
                                </div>
                            </div>


                        </div>
                    </div>

                    <div class="wizard-step h-100 p-4 p-md-5 overflow-auto" data-step="1">
                        <div class="text-center mb-5">
                            <h3 class="fw-bold text-dark">Τα στοιχεία σας</h3>
                            <p class="text-muted">Χρειαζόμαστε μερικές πληροφορίες για την κράτηση.</p>
                        </div>

                        <div class="row justify-content-center">
                            <div class="col-md-8">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label small fw-bold text-muted">ΟΝΟΜΑ</label>
                                        <input type="text" class="form-control form-control-lg custom-input" id="inpFname">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small fw-bold text-muted">ΕΠΩΝΥΜΟ</label>
                                        <input type="text" class="form-control form-control-lg custom-input" id="inpLname">
                                    </div>

                                    <div class="col-12">
                                        <label class="form-label small fw-bold text-muted">ΤΗΛΕΦΩΝΟ</label>
                                        <div class="input-group custom-phone-group">
                                            <button class="btn btn-outline-light border dropdown-toggle d-flex align-items-center gap-2 bg-white text-dark" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                <img src="/assets/images/countries/gr.png" alt="GR" width="20" id="selectedFlag">
                                                <span id="selectedCode">+30</span>
                                            </button>
                                            <ul class="dropdown-menu shadow border-0" id="countryDropdown" style="max-height: 200px; overflow-y: auto;"></ul>
                                            <input type="tel" class="form-control form-control-lg border-start-0 ps-2 custom-input" id="inpPhone" value="6985878578">
                                        </div>
                                        <input type="hidden" id="finalCountryCode" value="30">
                                    </div>

                                    <div class="col-12">
                                        <label class="form-label small fw-bold text-muted">EMAIL</label>
                                        <input type="email" class="form-control form-control-lg custom-input" id="inpEmail">
                                    </div>

                                    <div class="col-12">
                                        <label class="form-label small fw-bold text-muted">ΣΗΜΕΙΩΣΕΙΣ</label>
                                        <textarea class="form-control custom-input" id="inpNotes" rows="2"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="wizard-step h-100 p-4 p-md-5 overflow-auto" data-step="2">
                        <div class="text-center mb-4">
                            <h3 class="fw-bold text-dark">Επιλογή Ημερομηνίας</h3>
                            <p class="text-muted">Διαλέξτε την ημέρα και ώρα που σας εξυπηρετεί.</p>
                        </div>

                        <div class="container-fluid px-0" style="max-width: 900px;">

                            <div id="selectedSlotAnchor" class="d-none alert alert-light border d-flex justify-content-between align-items-center py-2 px-3 mb-3 rounded-3 shadow-sm">
                                <small class="fw-bold text-success"><i class="bi bi-check-circle-fill me-1"></i> <span id="anchorText">...</span></small>
                                <button type="button" class="btn-close small" style="font-size: 0.7rem;" id="clearSlotBtn"></button>
                            </div>

                            <div class="d-flex justify-content-between align-items-center mb-4 bg-white p-2 rounded-pill shadow-sm border">
                                <button type="button" class="btn btn-circle" id="prevWeek"><i class="bi bi-chevron-left"></i></button>
                                <span class="fw-bold text-dark" id="currentMonthLabel">...</span>
                                <button type="button" class="btn btn-circle" id="nextWeek"><i class="bi bi-chevron-right"></i></button>
                            </div>

                            <div class="scroll-fade-wrapper mb-5">
                                <div class="d-flex justify-content-between gap-1 overflow-x-auto pb-2" id="weekDaysContainer">
                                </div>
                            </div>


                            <div class="row g-4">
                                <div class="col-md-6 border-end-md">
                                    <h6 class="text-muted fw-bold mb-3 small"><i class="bi bi-sun me-2"></i>ΠΡΩΙ (Έως 15:00)</h6>
                                    <div class="d-flex flex-wrap gap-2" id="morningSlots"></div>
                                </div>
                                <div class="col-md-6">
                                    <h6 class="text-muted fw-bold mb-3 small"><i class="bi bi-moon-stars me-2"></i>ΑΠΟΓΕΥΜΑ (Από 15:00)</h6>
                                    <div class="d-flex flex-wrap gap-2" id="afternoonSlots"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="wizard-step h-100 p-4 p-md-5 overflow-auto" data-step="3">
                        <div class="text-center mb-4">
                            <h3 class="fw-bold text-dark">Ολοκλήρωση</h3>
                            <p class="text-muted">Ελέγξτε τα στοιχεία και προχωρήστε σε πληρωμή.</p>
                        </div>

                        <div class="row g-4 justify-content-center">
                            <div class="col-md-6">
                                <div class="card border-0 shadow-sm bg-light h-100" style="border-radius: 16px;">
                                    <div class="card-body p-4">
                                        <h6 class="fw-bold text-uppercase text-muted small mb-3">ΣΤΟΙΧΕΙΑ</h6>
                                        <div class="mb-3">
                                            <span class="d-block text-muted small">Υπηρεσία</span>
                                            <span class="fw-bold text-dark" id="reviewPkgTitle">...</span>
                                        </div>
                                        <div class="mb-3">
                                            <span class="d-block text-muted small">Θεραπευτής</span>
                                            <span class="fw-bold text-dark" id="reviewTherapist">...</span>
                                        </div>
                                        <div class="row">
                                            <div class="col-6 mb-3">
                                                <span class="d-block text-muted small">Ημερομηνία</span>
                                                <span class="fw-bold text-dark" id="reviewDate">...</span>
                                            </div>
                                            <div class="col-6 mb-3">
                                                <span class="d-block text-muted small">Ώρα</span>
                                                <span class="fw-bold text-dark" id="reviewTime">...</span>
                                            </div>
                                        </div>
                                        <div class="mb-0">
                                            <span class="d-block text-muted small">Τρόπος</span>
                                            <span class="badge bg-white text-dark border px-3 py-2 mt-1" id="reviewType">...</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="card border-0 shadow-sm bg-white" style="border-radius: 16px;">
                                    <div class="card-body p-4 d-flex flex-column">
                                        <h6 class="fw-bold text-uppercase text-muted small mb-3">ΠΛΗΡΩΜΗ</h6>
                                        <div class="d-flex justify-content-between align-items-end mb-3">
                                            <span class="text-muted">Σύνολο</span>
                                            <span class="h3 mb-0 fw-bold text-primary" id="reviewPrice">...</span>
                                        </div>
                                        <hr class="text-muted opacity-25">
                                        <div class="d-flex align-items-center gap-3 mb-4">
                                            <div class="bg-light-success text-success rounded-circle d-flex align-items-center justify-content-center" style="width:40px;height:40px;"><i class="bi bi-person-check-fill"></i></div>
                                            <div style="line-height:1.2;">
                                                <span class="d-block fw-bold small" id="reviewClientName">...</span>
                                                <span class="d-block text-muted small" id="reviewClientEmail">...</span>
                                            </div>
                                        </div>
                                        <div class="form-check mb-3">
                                            <input class="form-check-input float-none me-2" type="checkbox" id="termsCheck">
                                            <label class="form-check-label small text-muted" for="termsCheck">Συμφωνώ με τους <a href="#">Όρους Χρήσης</a>.</label>
                                        </div>
                                        <div class="p-3 rounded text-center border border-dashed">
                                            <div id="pay-form" class="" disabled></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </form>
            </div>

            <div class="modal-footer border-0 p-4 bg-white justify-content-between">
                <button type="button" class="btn btn-light rounded-pill px-4 fw-bold text-muted" id="btnBack" style="visibility: hidden;">
                    <i class="bi bi-arrow-left me-2"></i> Πίσω
                </button>
                <button type="button" class="btn btn-dark rounded-pill px-5 py-2 fw-bold shadow-sm" id="btnNext">
                    Συνέχεια <i class="bi bi-arrow-right ms-2"></i>
                </button>
                <button type="button" class="btn btn-success rounded-pill px-5 py-2 fw-bold shadow-sm d-none" id="btnBook">
                    Πληρωμή & Κράτηση
                </button>
            </div>
        </div>
    </div>
</div>

<style>
    #everypayContainer {
        position: relative !important;
        visibility: visible !important;
        min-height: 400px;
        /* Give it initial height */
        display: block !important;
    }

    #everypayContainer iframe {
        width: 100% !important;
        height: 100% !important;
    }

    /* Wizard Container & Transitions */
    .wizard-step {
        display: none;
        opacity: 0;
        transform: translateY(10px);
        transition: all 0.4s ease-out;
    }

    .wizard-step.active {
        display: block;
        opacity: 1;
        transform: translateY(0);
    }

    /* Stepper Header */
    .step-progress {
        list-style: none;
        padding: 0;
        display: flex;
        justify-content: space-between;
        position: relative;
        margin-bottom: 0;
    }

    .step-progress::before {
        content: '';
        position: absolute;
        top: 14px;
        left: 0;
        width: 100%;
        height: 2px;
        background: #eee;
        z-index: 0;
    }

    .step-progress li {
        position: relative;
        z-index: 1;
        text-align: center;
        width: 25%;
        font-size: 0.8rem;
        color: #aaa;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .step-progress li::before {
        content: '';
        width: 12px;
        height: 12px;
        background: #eee;
        border-radius: 50%;
        display: block;
        margin: 8px auto 8px auto;
        border: 2px solid #fff;
        box-shadow: 0 0 0 2px #fff;
        transition: all 0.3s;
    }

    .step-progress li.active {
        color: var(--alma-bg-button-main);
    }

    .step-progress li.active::before {
        background: var(--alma-bg-button-main);
        transform: scale(1.3);
    }

    /* Custom Radio Cards (Type) */
    .custom-radio-card {
        display: block;
        cursor: pointer;
        position: relative;
    }

    .custom-radio-card input {
        position: absolute;
        opacity: 0;
    }

    .custom-radio-card .card-content {
        background: white;
        border: 2px solid #eee;
        border-radius: 16px;
        padding: 20px;
        text-align: center;
        transition: all 0.2s;
    }

    .custom-radio-card input:checked+.card-content {
        border-color: var(--alma-bg-button-main);
        background: rgba(51, 76, 71, 0.03);
        box-shadow: 0 4px 15px rgba(51, 76, 71, 0.1);
    }

    .icon-circle {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto;
        font-size: 1.4rem;
    }

    .bg-light-blue {
        background: #eef7ff;
        color: #0d6efd;
    }

    .bg-light-orange {
        background: #fff5eb;
        color: #fd7e14;
    }

    /* Custom Therapist Card */
    .therapist-select-card {
        width: 140px;
        cursor: pointer;
        position: relative;
    }

    .therapist-select-card input {
        position: absolute;
        opacity: 0;
    }

    .therapist-select-card .t-content {
        background: white;
        border: 2px solid #eee;
        border-radius: 12px;
        padding: 15px;
        text-align: center;
        transition: all 0.2s;
        opacity: 0.7;
        filter: grayscale(100%);
    }

    .therapist-select-card img {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        object-fit: cover;
        margin-bottom: 10px;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    }

    .therapist-select-card:hover .t-content {
        opacity: 1;
        filter: grayscale(0%);
        border-color: #ddd;
    }

    .therapist-select-card input:checked+.t-content {
        border-color: var(--alma-bg-button-main);
        opacity: 1;
        filter: grayscale(0%);
        transform: translateY(-3px);
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
    }

    .therapist-select-card input:checked+.t-content::after {
        content: '\f26a';
        font-family: bootstrap-icons;
        position: absolute;
        top: -8px;
        right: -8px;
        background: var(--alma-bg-button-main);
        color: white;
        border-radius: 50%;
        width: 24px;
        height: 24px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.8rem;
    }

    /* Inputs */
    .custom-input {
        border: 2px solid #eee;
        border-radius: 10px;
        font-size: 0.95rem;
        padding: 12px;
        transition: border-color 0.2s;
    }

    .custom-input:focus {
        border-color: var(--alma-bg-button-main);
        box-shadow: none;
        background: #fff;
    }

    .section-label {
        font-size: 0.8rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 1px;
        color: #999;
        display: block;
        text-align: center;
    }

    #weekDaysContainer {
        /* Enable horizontal scrolling */
        overflow-x: auto;
        overflow-y: hidden;
        white-space: nowrap;

        /* Smooth scrolling momentum for iOS */
        -webkit-overflow-scrolling: touch;

        /* Hide the scrollbar for a cleaner look */
        scrollbar-width: none;
        /* Firefox */
        -ms-overflow-style: none;
        /* IE 10+ */

        /* Add some padding so shadows aren't cut off */
        padding: 5px;
    }

    #weekDaysContainer::-webkit-scrollbar {
        display: none;
        /* Chrome/Safari/Webkit */
    }

    /* Specific styling for mobile screens */
    @media (max-width: 768px) {
        #weekDaysContainer {
            /* Overwrite Bootstrap's justify-content-between so items start from left */
            justify-content: flex-start !important;
            gap: 10px !important;
            /* Add consistent space between items */
        }

        .cal-day-btn {
            /* Don't shrink the buttons! Keep them a touch-friendly size */
            flex: 0 0 auto;
            width: 60px;
            /* Fixed width ensures they don't get squashed */
        }
    }

    /* 1. Wrapper για το Fade Effect */
    .scroll-fade-wrapper {
        position: relative;
        /* Αφήνουμε λίγο χώρο δεξιά για να μην κόβεται απότομα */
        margin-right: -10px;
        padding-right: 10px;
    }

    /* 2. Το Σβήσιμο (Gradient) στα δεξιά */
    .scroll-fade-wrapper::after {
        content: '';
        position: absolute;
        top: 0;
        right: 0;
        bottom: 0;
        width: 50px;
        /* Πόσο πλατύ είναι το σβήσιμο */
        /* Διαφανές -> Λευκό */
        background: linear-gradient(to right, rgba(255, 255, 255, 0), #ffffff);
        pointer-events: none;
        /* Επιτρέπει το scroll/click από κάτω */
        z-index: 2;
        opacity: 0;
        /* Κρυφό σε desktop */
        transition: opacity 0.3s ease;
    }

    /* Εμφάνιση μόνο σε Mobile/Tablet */
    @media (max-width: 768px) {
        .scroll-fade-wrapper::after {
            opacity: 1;
        }

        /* 3. Animation: Μικρό "σκούντημα" για να φανεί ότι κινείται */
        #weekDaysContainer {
            animation: slide-hint 1s ease-out 0.5s;
            /* Καθυστέρηση 0.5s για να το δει */
        }
    }

    /* Το Keyframe για την κίνηση */
    @keyframes slide-hint {
        0% {
            transform: translateX(0);
        }

        25% {
            transform: translateX(-20px);
        }

        /* Κουνιέται αριστερά */
        50% {
            transform: translateX(0);
        }

        /* Επιστρέφει */
        100% {
            transform: translateX(0);
        }
    }

    /* Calendar Day Buttons */
    .cal-day-btn {
        min-width: 65px;
        border: 1px solid #eee;
        background: white;
        border-radius: 12px;
        padding: 10px 5px;
        text-align: center;
        cursor: pointer;
        transition: all 0.2s;
        flex: 1;
    }

    .cal-day-btn:hover {
        background: #f8f9fa;
        transform: translateY(-2px);
    }

    .cal-day-btn.active {
        background: var(--alma-bg-button-main);
        color: white;
        border-color: var(--alma-bg-button-main);
        box-shadow: 0 4px 10px rgba(86, 124, 109, 0.4);
    }

    .cal-day-btn.disabled {
        opacity: 0.4;
        pointer-events: none;
        background: #f9f9f9;
    }

    /* Slot Buttons */
    .slot-btn {
        border: 1px solid var(--alma-bg-button-main);
        color: var(--alma-bg-button-main);
        background: transparent;
        padding: 8px 16px;
        border-radius: 8px;
        font-size: 0.9rem;
        transition: all 0.2s;
    }

    .slot-btn:hover {
        background: rgba(86, 124, 109, 0.1);
    }

    .slot-btn.active {
        background: var(--alma-bg-button-main);
        color: white;
        box-shadow: 0 2px 8px rgba(86, 124, 109, 0.4);
    }

    /* Misc */
    .btn-circle {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 1px solid #eee;
        background: white;
    }

    .btn-circle:hover {
        background: #f1f1f1;
    }

    .dropdown-item img {
        vertical-align: middle;
        margin-right: 8px;
    }
</style>

<div style="height: 120px;"></div>

<?php
function hook_end_scripts()
{
?>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/ScrollTrigger.min.js"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            gsap.registerPlugin(ScrollTrigger);

            const navLinks = document.querySelectorAll('.nav-link-item');
            const glider = document.querySelector('.nav-glider');
            const navWrapper = document.querySelector('.nav-capsule-wrapper');
            const servicesWrapper = document.getElementById('services-wrapper');

            function moveGlider(element) {
                const rect = element.getBoundingClientRect();
                const parentRect = element.parentElement.getBoundingClientRect();
                const relativeLeft = rect.left - parentRect.left - 6.5;

                glider.style.width = rect.width + 'px';
                glider.style.transform = `translateX(${relativeLeft}px)`;
                glider.style.opacity = '1';

                navLinks.forEach(link => link.classList.remove('active'));
                element.classList.add('active');
            }

            navLinks.forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    moveGlider(this);
                    const targetId = this.getAttribute('href');
                    const targetSection = document.querySelector(targetId);
                    const offset = 40;
                    const bodyRect = document.body.getBoundingClientRect().top;
                    const elementRect = targetSection.getBoundingClientRect().top;
                    const elementPosition = elementRect - bodyRect;
                    const offsetPosition = elementPosition - offset;

                    window.scrollTo({
                        top: offsetPosition,
                        behavior: "smooth"
                    });
                });
            });

            if (navLinks.length > 0) setTimeout(() => moveGlider(navLinks[0]), 200);

            const sections = document.querySelectorAll('.service-editorial-section');
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const id = entry.target.getAttribute('id');
                        const activeLink = document.querySelector(`.nav-link-item[href="#${id}"]`);
                        if (activeLink) {
                            moveGlider(activeLink);
                        }
                    }
                });
            }, {
                rootMargin: '-40% 0px -50% 0px'
            });
            sections.forEach(section => observer.observe(section));

            function checkNavVisibility() {
                if (!servicesWrapper) return;
                const rect = servicesWrapper.getBoundingClientRect();
                const windowHeight = window.innerHeight;
                const startVisible = rect.top < windowHeight - 100;
                const endVisible = rect.bottom > 600;

                if (startVisible && endVisible) {
                    navWrapper.classList.add('is-visible');
                } else {
                    navWrapper.classList.remove('is-visible');
                }
            }

            window.addEventListener('scroll', checkNavVisibility);
            checkNavVisibility();

            gsap.utils.toArray('.reveal-item').forEach(item => {
                gsap.fromTo(item, {
                    opacity: 0,
                    y: 30
                }, {
                    opacity: 1,
                    y: 0,
                    duration: 0.8,
                    ease: "power2.out",
                    scrollTrigger: {
                        trigger: item,
                        start: "top 85%"
                    }
                });
            });
        });
    </script>



    <script src="https://cdn.jsdelivr.net/npm/google-libphonenumber@3.2.13/dist/libphonenumber.js"></script>


    <script>
        document.addEventListener("DOMContentLoaded", function() {

            // --- STATE ---
            const wizardState = {
                step: 0,
                pkgId: null,
                pkgData: null,
                therapistId: null,
                therapistName: '',
                sessionType: null,
                groupSpots: null,
                client: {},
                slot: null,
                calendar: {
                    currentDate: new Date(),
                    weekOffset: 0
                }
            };

            const els = {
                modal: new bootstrap.Modal(document.getElementById('bookingModal')),
                modalEl: document.getElementById('bookingModal'),
                loader: document.getElementById('wizardLoader'),
                errorAlert: document.getElementById('globalError'),
                errorMsg: document.getElementById('errorMsg'),
                steps: document.querySelectorAll('.wizard-step'),
                progressItems: document.querySelectorAll('.step-progress li'),
                btnNext: document.getElementById('btnNext'),
                btnBack: document.getElementById('btnBack'),
                btnBook: document.getElementById('btnBook')
            };

            function getLocalISODate(d) {
                const z = n => ('0' + n).slice(-2);
                return d.getFullYear() + '-' + z(d.getMonth() + 1) + '-' + z(d.getDate());
            }

            // --- OPEN MODAL ---
            document.querySelectorAll('.open-booking-modal').forEach(btn => {
                btn.addEventListener('click', (e) => {
                    e.preventDefault();
                    const pid = btn.dataset.serviceId;
                    const spots = btn.dataset.remainingSpots;
                    initWizard(pid, spots);
                });
            });

            els.modalEl.addEventListener('show.bs.modal', () => {
                document.body.style.overflow = 'hidden';
                document.documentElement.style.overflow = 'hidden';
            });

            // Όταν κλείνει το Modal -> Επαναφορά Scroll & Reset Wizard
            els.modalEl.addEventListener('hidden.bs.modal', () => {
                document.body.style.overflow = '';
                document.documentElement.style.overflow = '';

                // Καλό είναι να κάνουμε reset εδώ, για να καθαρίζει αν ο χρήστης το κλείσει με ESC ή κλικ έξω
                resetWizard();
            });

            async function initWizard(pkgId, spots) {
                resetWizard();
                wizardState.pkgId = pkgId;
                wizardState.groupSpots = spots;
                els.modal.show();
                toggleLoader(true);

                try {
                    const csrf_token = document.querySelector('meta[name="csrf_token"]')?.getAttribute('content') || '';

                    const fd = new FormData();
                    fd.append('action', 'fetchPublicPackage');
                    fd.append('csrf_token', csrf_token);
                    fd.append('id', pkgId);
                    const res = await fetch('includes/ajax.php', {
                        method: 'POST',
                        body: fd
                    }).then(r => r.json());

                    if (res.success) {
                        wizardState.pkgData = res.data;
                        renderStep1();
                        toggleLoader(false);
                    } else {
                        showError('Error loading package.');
                        setTimeout(() => els.modal.hide(), 2000);
                    }
                } catch (e) {
                    console.error(e);
                    showError('Connection error.');
                    toggleLoader(false);
                }
            }

            // --- NAVIGATION ---
            els.btnNext.addEventListener('click', () => changeStep(1));
            els.btnBack.addEventListener('click', () => changeStep(-1));

            function changeStep(dir) {
                const nextStep = wizardState.step + dir;

                if (dir === 1) {
                    if (!validateStep(wizardState.step)) return;
                }

                // Group Skip Logic (Skip Calendar Step 2)
                if (wizardState.pkgData.is_group == 1 && nextStep === 2) {
                    // Auto-set slot
                    const dt = wizardState.pkgData.start_datetime.split(' ');
                    wizardState.slot = {
                        date: dt[0],
                        time: dt[1].substring(0, 5)
                    };

                    if (dir === 1) renderStep(3); // Jump to Review
                    else renderStep(1); // Jump back to Info
                    return;
                }

                renderStep(nextStep);
            }

            function renderStep(idx) {
                wizardState.step = idx;

                // UI Updates
                els.steps.forEach(s => s.classList.remove('active'));
                if (els.steps[idx]) els.steps[idx].classList.add('active');

                els.progressItems.forEach((li, i) => {
                    if (i <= idx) li.classList.add('active');
                    else li.classList.remove('active');
                });

                els.btnBack.style.visibility = idx === 0 ? 'hidden' : 'visible';

                // Step 4: Review
                if (idx === 3) {
                    els.btnNext.classList.add('d-none');
                    els.btnBook.classList.remove('d-none');
                    renderReview();
                } else {
                    els.btnNext.classList.remove('d-none');
                    els.btnBook.classList.add('d-none');
                }

                // Step 3: Calendar
                if (idx === 2) {
                    // Scenario A: User has already picked a slot (or we manually set it)
                    if (wizardState.slot && wizardState.slot.date) {
                        jumpToDate(new Date(wizardState.slot.date));
                    }
                    // Scenario B: First time entering step, no slot. Find next available.
                    else {
                        findAndJumpToFirstDate();
                    }
                    updateAnchorLabel();
                }

                hideError();
            }

            // Jumps calendar state to specific date
            function jumpToDate(targetDate) {
                // Μηδενισμός ώρας για αποφυγή προβλημάτων
                targetDate.setHours(0, 0, 0, 0);

                const today = new Date();
                today.setHours(0, 0, 0, 0);

                // Βρίσκουμε τη Δευτέρα (Start of Week)
                const getMonday = (d) => {
                    const day = d.getDay(),
                        diff = d.getDate() - day + (day == 0 ? -6 : 1);
                    return new Date(d.setDate(diff));
                }

                const mondayTarget = getMonday(new Date(targetDate));
                const mondayToday = getMonday(new Date(today));

                // Υπολογισμός εβδομάδων διαφοράς
                const diffTime = mondayTarget.getTime() - mondayToday.getTime();
                const diffWeeks = Math.round(diffTime / (1000 * 60 * 60 * 24 * 7));

                wizardState.calendar.weekOffset = diffWeeks;
                wizardState.calendar.currentDate = mondayTarget;

                // ΣΗΜΑΝΤΙΚΟ: Αποθηκεύουμε την ημερομηνία-στόχο με το σωστό format
                wizardState.calendar.autoSelectDate = getLocalISODate(targetDate);

                initCalendar();
            }

            // AJAX call to find first available day
            async function findAndJumpToFirstDate() {
                // Show loader on calendar area
                document.getElementById('weekDaysContainer').innerHTML = '<div class="text-center py-3"><div class="spinner-border spinner-border-sm text-secondary"></div></div>';
                const csrf_token = document.querySelector('meta[name="csrf_token"]')?.getAttribute('content') || '';

                const fd = new FormData();
                fd.append('action', 'getNextAvailableDate');
                fd.append('therapist_id', wizardState.therapistId);
                fd.append('duration', wizardState.pkgData.duration_minutes);
                fd.append('csrf_token', csrf_token);

                try {
                    const res = await fetch('includes/ajax.php', {
                        method: 'POST',
                        body: fd
                    }).then(r => r.json());
                    if (res.success && res.date) {
                        jumpToDate(new Date(res.date));
                    } else {
                        // Fallback to today
                        initCalendar();
                    }
                } catch (e) {
                    initCalendar();
                }
            }

            // --- VALIDATION ---
            function validateStep(idx) {
                // STEP 0: Therapist & Type
                if (idx === 0) {
                    if (wizardState.pkgData.is_group != 1) {
                        const tChecked = document.querySelector('input[name="therapist"]:checked');
                        if (!tChecked) {
                            showError('Επιλέξτε θεραπευτή.');
                            return false;
                        }
                        wizardState.therapistId = tChecked.value;
                        const card = tChecked.closest('.therapist-select-card');
                        wizardState.therapistName = card ? card.querySelector('.t-name').innerText : '';
                    } else {
                        wizardState.therapistId = 0;
                        wizardState.therapistName = 'Workshop Team';
                    }

                    if (wizardState.pkgData.type === 'mixed') {
                        const typeChecked = document.querySelector('input[name="sessionType"]:checked');
                        if (!typeChecked) {
                            showError('Επιλέξτε τρόπο.');
                            return false;
                        }
                        wizardState.sessionType = typeChecked.value;
                    } else {
                        wizardState.sessionType = wizardState.pkgData.type;
                    }
                    return true;
                }

                // STEP 1: Details (Name, Email, Phone)
                if (idx === 1) {
                    const fname = document.getElementById('inpFname').value.trim();
                    const lname = document.getElementById('inpLname').value.trim();
                    const phone = document.getElementById('inpPhone').value.trim();
                    const email = document.getElementById('inpEmail').value.trim();
                    const clintNotes = document.getElementById('inpNotes').value.trim();

                    // 1. Έλεγχος Κενών Πεδίων
                    if (!fname || !lname || !email || !phone) {
                        showError('Συμπληρώστε όλα τα απαραίτητα πεδία (*).');
                        return false;
                    }

                    // 2. ΝΕΟ: Έλεγχος Εγκυρότητας Email
                    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                    if (!emailRegex.test(email)) {
                        showError('Παρακαλώ εισάγετε ένα έγκυρο email.');
                        return false;
                    }

                    // 3. Έλεγχος Τηλεφώνου (Libphonenumber)
                    const code = document.getElementById('finalCountryCode').value;
                    const fullPhone = '+' + code + phone;

                    if (typeof libphonenumber !== 'undefined') {
                        try {
                            const u = libphonenumber.PhoneNumberUtil.getInstance();
                            const n = u.parseAndKeepRawInput(fullPhone);
                            if (!u.isValidNumber(n)) {
                                showError('Μη έγκυρος αριθμός τηλεφώνου.');
                                return false;
                            }
                        } catch (e) {
                            showError('Μη έγκυρος αριθμός τηλεφώνου.');
                            return false;
                        }
                    }

                    // Αποθήκευση στο State
                    wizardState.client = {
                        fname,
                        lname,
                        email,
                        phone: fullPhone,
                        notes: clintNotes
                    };
                    return true;
                }

                // STEP 2: Calendar Slot
                if (idx === 2) {
                    if (!wizardState.slot) {
                        showError('Επιλέξτε ώρα για το ραντεβού.');
                        return false;
                    }
                    return true;
                }

                return true;
            }

            // --- RENDERERS ---
            function renderStep1() {
                const pkg = wizardState.pkgData;

                // 1. UPDATE SUMMARY CARD (The clean design)
                document.getElementById('modalPkgTitle').innerText = pkg.title;
                document.getElementById('modalPkgTitleDesc').innerText = pkg.title;
                document.getElementById('pkgDurationText').innerHTML = `<i class="bi bi-clock me-1"></i>${pkg.duration_minutes} λεπτά`;

                // Price Logic
                const price = parseFloat(pkg.price);
                const priceEl = document.getElementById('pkgPriceText');

                if (price > 0) {
                    priceEl.innerText = price.toFixed(2) + '€';
                    priceEl.className = "fs-5 fw-bold text-dark"; // Clean dark text
                } else {
                    priceEl.innerHTML = '<span class="badge bg-light text-dark border fw-normal">Δωρεάν</span>';
                }

                // 2. THERAPISTS LIST
                const tContainer = document.getElementById('therapistList');
                tContainer.innerHTML = '';
                const tSection = document.getElementById('therapistSection');
                const gInfo = document.getElementById('groupFixedInfo');

                if (pkg.is_group == 1) {
                    // --- Group Session ---
                    tSection.classList.add('d-none');
                    gInfo.classList.remove('d-none');

                    const d = new Date(pkg.start_datetime);
                    let badge = '';
                    if (wizardState.groupSpots && wizardState.groupSpots < 5) {
                        badge = `<div class="mt-1"><span class="badge bg-danger-subtle text-danger border border-danger-subtle" style="font-size:0.75rem">Τελευταίες ${wizardState.groupSpots} θέσεις!</span></div>`;
                    }

                    document.getElementById('groupDateText').innerHTML = d.toLocaleString('el-GR', {
                        weekday: 'long',
                        day: 'numeric',
                        month: 'long',
                        hour: '2-digit',
                        minute: '2-digit'
                    }) + badge;

                } else {
                    // --- 1-on-1 Session ---
                    tSection.classList.remove('d-none');
                    gInfo.classList.add('d-none');

                    if (pkg.therapists && pkg.therapists.length > 0) {
                        pkg.therapists.forEach(t => {
                            // Avatar logic
                            const av = t.avatar ?
                                `<img src="${t.avatar}" class="rounded-circle mb-2" style="width:85px;height:85px;object-fit:cover;">` :
                                `<div class="bg-light text-secondary rounded-circle d-flex align-items-center justify-content-center mx-auto mb-2 border" style="width:50px;height:50px;"><span class="fw-bold fs-5">${t.first_name[0]}</span></div>`;

                            const html = `
                                <label class="therapist-select-card" style="width: 145px; cursor: pointer;">
                                    <input type="radio" name="therapist" value="${t.id}">
                                    <div class="t-content p-3 rounded-3 border bg-white text-center h-100 transition-all">
                                        ${av}
                                        <div class="fw-bold small t-name text-truncate w-100">${t.first_name}</div>
                                        <div class="small text-muted" style="font-size: 0.7rem;">${t.last_name}</div>
                                    </div>
                                </label>`;
                            tContainer.insertAdjacentHTML('beforeend', html);
                        });

                        // Auto-select if only one
                        if (pkg.therapists.length === 1) {
                            tContainer.querySelector('input').checked = true;
                        }
                    } else {
                        // Any Therapist Option
                        tContainer.innerHTML = `
                            <label class="therapist-select-card" style="width: 145px; cursor: pointer;">
                                <input type="radio" name="therapist" value="any" checked>
                                <div class="t-content p-3 rounded-3 border bg-white text-center h-100">
                                    <div class="bg-light text-secondary rounded-circle d-flex align-items-center justify-content-center mx-auto mb-2 border" style="width:50px;height:50px;">
                                        <i class="bi bi-people-fill fs-5"></i>
                                    </div>
                                    <div class="fw-bold small t-name">Οποιοσδήποτε</div>
                                    <div class="small text-muted" style="font-size: 0.7rem;">Διαθέσιμος</div>
                                </div>
                            </label>`;
                    }
                }

                // 3. LOCATION / TYPE
                const typeSec = document.getElementById('typeSection');
                if (pkg.type === 'mixed') {
                    typeSec.classList.remove('d-none');
                } else {
                    typeSec.classList.add('d-none');
                }
            }

            // Step 4 Review
            // STEP 4: REVIEW & PAY
            function renderReview() {
                const pkg = wizardState.pkgData;
                const slot = wizardState.slot;
                const cli = wizardState.client;

                // 1. Fill Summary Data
                document.getElementById('reviewPkgTitle').innerText = pkg.title;
                document.getElementById('reviewTherapist').innerText = wizardState.therapistName || 'Οποιοσδήποτε';

                // --- Date Logic ---
                const d = new Date(slot.date);
                document.getElementById('reviewDate').innerText = d.toLocaleDateString('el-GR', {
                    weekday: 'long',
                    day: 'numeric',
                    month: 'long'
                });

                // --- Time Logic (Πρωί/Απόγευμα) ---
                let timeDisplay = slot.time;
                if (slot.time !== 'Fixed' && slot.time.includes(':')) {
                    const h = parseInt(slot.time.split(':')[0]);
                    let period = 'Βράδυ';
                    if (h < 12) period = 'Πρωί';
                    else if (h < 17) period = 'Μεσημέρι';
                    else if (h < 20) period = 'Απόγευμα';

                    timeDisplay += ` (${period})`;
                }
                document.getElementById('reviewTime').innerText = timeDisplay;

                // --- Type Logic (Icons) ---
                const tEl = document.getElementById('reviewType');
                if (wizardState.sessionType === 'online') {
                    tEl.innerHTML = '<i class="bi bi-camera-video-fill me-1"></i> Online (Video)';
                    tEl.className = 'badge bg-primary text-white border px-3 py-2 mt-1';
                } else {
                    tEl.innerHTML = '<i class="bi bi-geo-alt-fill me-1"></i> Δια Ζώσης';
                    tEl.className = 'badge bg-white text-dark border px-3 py-2 mt-1';
                }

                // Price & Client
                const price = parseFloat(pkg.price);
                const finalPrice = price > 0 ? price : 0;
                document.getElementById('reviewPrice').innerText = finalPrice > 0 ? finalPrice.toFixed(2) + '€' : 'Δωρεάν';
                document.getElementById('reviewClientName').innerText = cli.fname + ' ' + cli.lname;
                document.getElementById('reviewClientEmail').innerText = cli.email;

                // 2. Initialize Payment (EveryPay)
                // const payContainer = document.getElementById('everypayContainer');

                // Only clear/init if we haven't already initialized for this price
                // (Optional check to prevent flickering, but clearing is safer to ensure fresh state)
                // payContainer.innerHTML = '';
                // payContainer.innerHTML = '';

                if (finalPrice > 0) {
                    const priceCents = Math.round(finalPrice * 100);

                    try {
                        everypay.payform({
                            pk: 'pk_1pRyetzc0CbZBwvNVNXpdFafvspjcnk8', // <--- PUT YOUR KEY HERE
                            amount: priceCents,
                            locale: 'el',
                            txnType: 'tds',
                            theme: 'default',
                            data: {
                                email: cli.email,
                                phone: cli.phone
                            },
                            display: {
                                button: false, // We use our own button
                                billing: true,
                                mobile: true
                            },
                            formOptions: {
                                border: '0',
                                size: 'lg'
                            }
                        }, handlePaymentResponse);

                        setTimeout(() => {
                            if (typeof everypay.showForm === 'function') {
                                everypay.showForm();
                            }
                        }, 200);

                    } catch (err) {
                        console.error("EveryPay Init Error:", err);
                        // payContainer.innerHTML = '<div class="alert alert-danger small">Σφάλμα φόρτωσης πληρωμής.</div>';
                    }
                } else {
                    // payContainer.innerHTML = '<div class="alert alert-success small"><i class="bi bi-check-circle me-2"></i>Δεν απαιτείται πληρωμή.</div>';
                }
            }

            // --- BUTTON CLICK (Triggers Payment or Direct Submit) ---
            els.btnBook.addEventListener('click', () => {
                // 1. Check Terms
                if (!document.getElementById('termsCheck').checked) {
                    showError('Πρέπει να αποδεχτείτε τους όρους χρήσης.');
                    return;
                }

                // 2. Check Price
                const price = parseFloat(wizardState.pkgData.price);

                if (price > 0) {
                    // Trigger EveryPay Validation & Tokenization
                    // This will call handlePaymentResponse on success
                    if (typeof everypay !== 'undefined') {
                        everypay.onClick();
                    } else {
                        showError('Το σύστημα πληρωμών δεν έχει φορτώσει.');
                    }
                }
            });

            // --- PAYMENT CALLBACK ---
            window.handlePaymentResponse = function(r) {
                console.log('Payment Response:', r);
                if (r.response === 'success') {
                    // Success! Send token to backend to capture charge & save booking
                    submitBooking(r.token);
                } else {
                    // Error handling (EveryPay usually shows errors in the form, but we can alert too)
                    if (r.error) showError('Η πληρωμή απέτυχε: ' + r.error.message);
                }
            }

            // --- FINAL SUBMIT ---
            async function submitBooking(paymentToken) {
                toggleLoader(true);
                els.btnBook.classList.add('d-none');
                els.btnBack.classList.add('d-none');
                const fd = new FormData();

                const metaToken = document.querySelector('meta[name="csrf_token"]')?.getAttribute('content')

                // Backend Action
                fd.append('action', 'createPublicBooking');
                fd.append('csrf_token', metaToken);

                // Package & Config
                fd.append('package_id', wizardState.pkgId);
                fd.append('therapist_id', wizardState.therapistId);
                fd.append('type', wizardState.sessionType);

                // Slot
                fd.append('date', wizardState.slot.date);
                fd.append('time', wizardState.slot.time);

                // Client
                const c = wizardState.client;
                fd.append('first_name', c.fname);
                fd.append('last_name', c.lname);
                fd.append('email', c.email);
                fd.append('phone', c.phone);
                fd.append('notes', c.notes || '');

                // Payment
                if (paymentToken) fd.append('payment_token', paymentToken);

                try {
                    const res = await fetch('includes/ajax.php', {
                        method: 'POST',
                        body: fd
                    }).then(r => r.json());

                    toggleLoader(false);

                    if (res.success) {
                        // HIDE FORM, SHOW SUCCESS MESSAGE
                        document.getElementById('bookingForm').classList.add('d-none'); // Hide entire form

                        // Show Success Step (You can add a hidden div for this in HTML or inject it)
                        const body = els.modalEl.querySelector('.modal-body');
                        body.innerHTML = `
                    <div class="d-flex flex-column align-items-center justify-content-center h-100 text-center p-5">
                        <div class="mb-4 text-success" style="font-size: 4rem;"><i class="bi bi-check-circle-fill"></i></div>
                        <h3 class="fw-bold mb-3">Η κράτηση ολοκληρώθηκε!</h3>
                        <p class="text-muted mb-4">Σας ευχαριστούμε. Ένα email επιβεβαίωσης έχει σταλεί στο <strong>${c.email}</strong>.</p>
                        <button class="btn btn-dark rounded-pill px-4" onclick="window.location.reload()">Εντάξει</button>
                    </div>
                `;

                        // Hide Footer buttons
                        document.querySelector('.modal-footer').classList.add('d-none');

                    } else {
                        showError('Σφάλμα: ' + (res.errors ? res.errors.join(', ') : 'Άγνωστο σφάλμα.'));
                    }
                } catch (e) {
                    console.error(e);
                    showError('Σφάλμα επικοινωνίας με τον server.');
                    toggleLoader(false);
                    els.btnBack.classList.add('d-none');
                }
            }

            // --- CALENDAR (Standard) ---
            document.getElementById('prevWeek').onclick = () => {
                wizardState.calendar.weekOffset--;
                initCalendar();
            };
            document.getElementById('nextWeek').onclick = () => {
                wizardState.calendar.weekOffset++;
                initCalendar();
            };

            function initCalendar() {
                const d = new Date();
                const day = d.getDay();
                const diff = d.getDate() - day + (day == 0 ? -6 : 1);
                d.setDate(diff + (wizardState.calendar.weekOffset * 7));
                wizardState.calendar.currentDate = d;
                renderWeek();
            }

            function renderWeek() {
                const container = document.getElementById('weekDaysContainer');
                if (!container) return;

                container.innerHTML = '';
                document.getElementById('currentMonthLabel').innerText =
                    wizardState.calendar.currentDate.toLocaleString('el-GR', {
                        month: 'long',
                        year: 'numeric'
                    });

                const start = new Date(wizardState.calendar.currentDate);
                const today = new Date();

                // Get Limits from State
                // Find selected therapist in pkgData
                const tid = wizardState.therapistId;
                let maxDays = 60; // Default

                if (wizardState.pkgData && wizardState.pkgData.therapists) {
                    const tObj = wizardState.pkgData.therapists.find(t => t.id == tid);
                    if (tObj && tObj.booking_window_days) {
                        maxDays = parseInt(tObj.booking_window_days);
                    }
                }

                // Calculate Max Date
                const maxDate = new Date();
                maxDate.setDate(today.getDate() + maxDays);
                const maxDateStr = getLocalISODate(maxDate);
                const todayStr = getLocalISODate(today);

                let autoClickTarget = null;

                for (let i = 0; i < 7; i++) {
                    const day = new Date(start);
                    day.setDate(start.getDate() + i);

                    const iso = getLocalISODate(day);

                    const btn = document.createElement('div');
                    btn.className = 'cal-day-btn d-flex flex-column align-items-center justify-content-center flex-shrink-0';
                    btn.innerHTML = `<span class="small fw-bold text-uppercase">${day.toLocaleString('el-GR', {weekday:'short'})}</span><span class="fs-5">${day.getDate()}</span>`;

                    // --- LOGIC: Disable if Past OR Future > Max Window ---
                    if (iso < todayStr || iso > maxDateStr) {
                        btn.classList.add('disabled', 'opacity-50'); // Add opacity for visual clue
                        btn.style.cursor = 'not-allowed';
                    } else {
                        btn.onclick = () => loadSlots(iso, btn);
                    }

                    // ... (rest of selection logic) ...
                    if (wizardState.slot && wizardState.slot.date === iso) {
                        btn.classList.add('active');
                        autoClickTarget = {
                            iso: iso,
                            el: btn
                        };
                    }
                    if (!wizardState.slot && wizardState.calendar.autoSelectDate === iso) {
                        // Ensure we don't auto-select a disabled date
                        if (iso <= maxDateStr) {
                            autoClickTarget = {
                                iso: iso,
                                el: btn
                            };
                        }
                        wizardState.calendar.autoSelectDate = null;
                    }

                    container.appendChild(btn);
                }

                if (autoClickTarget) {
                    loadSlots(autoClickTarget.iso, autoClickTarget.el);
                } else {
                    const ms = document.getElementById('morningSlots');
                    const as = document.getElementById('afternoonSlots');
                    if (ms) ms.innerHTML = '<span class="small text-muted w-100 text-center py-2">Επιλέξτε ημέρα...</span>';
                    if (as) as.innerHTML = '';
                }
            }

            async function loadSlots(dateStr, btnEl) {
                // Active visual for Day Button
                if (btnEl) {
                    document.querySelectorAll('.cal-day-btn').forEach(b => b.classList.remove('active'));
                    btnEl.classList.add('active');
                }

                const ms = document.getElementById('morningSlots');
                const as = document.getElementById('afternoonSlots');
                if (!ms || !as) return;

                // Show Spinner
                ms.innerHTML = '<div class="spinner-border spinner-border-sm text-secondary mx-auto"></div>';
                as.innerHTML = '';

                const fd = new FormData();
                const csrf_token = document.querySelector('meta[name="csrf_token"]')?.getAttribute('content') || '';

                fd.append('action', 'getPublicSlots');
                fd.append('therapist_id', wizardState.therapistId);
                fd.append('csrf_token', csrf_token);
                fd.append('date', dateStr);
                fd.append('duration', wizardState.pkgData.duration_minutes);
                fd.append('type', wizardState.sessionType);

                try {
                    const res = await fetch('includes/ajax.php', {
                        method: 'POST',
                        body: fd
                    }).then(r => r.json());

                    // --- FIX 1: Clear BOTH containers immediately ---
                    ms.innerHTML = '';
                    as.innerHTML = '';

                    if (res.success && res.slots.length > 0) {
                        let hasM = false,
                            hasA = false;
                        const seenTimes = new Set(); // --- FIX 2: Deduplicate slots ---

                        res.slots.forEach(time => {
                            if (seenTimes.has(time)) return; // Skip if we already added this time
                            seenTimes.add(time);

                            const hour = parseInt(time.split(':')[0]);
                            const btn = document.createElement('button');
                            btn.type = 'button';
                            btn.className = 'slot-btn';
                            btn.innerText = time;

                            // Highlight if selected
                            if (wizardState.slot && wizardState.slot.date === dateStr && wizardState.slot.time === time) {
                                btn.classList.add('active');
                            }

                            btn.onclick = () => selectSlot(btn, dateStr, time);

                            const div = document.createElement('div');
                            div.appendChild(btn);

                            if (hour < 15) {
                                ms.appendChild(div);
                                hasM = true;
                            } else {
                                as.appendChild(div);
                                hasA = true;
                            }
                        });

                        if (!hasM) ms.innerHTML = '<span class="small text-muted fst-italic w-100 text-center bg-warning-subtle p-4 rounded">Δεν βρέθηκαν διαθέσιμα πρωινά ραντεβού για αυτή την ημέρα.</span>';
                        if (!hasA) as.innerHTML = '<span class="small text-muted fst-italic w-100 text-center bg-warning-subtle p-4 rounded">Δεν υπάρχουν διαθέσιμα απογευματινά ραντεβού για την επιλεγμένη ημερομηνία.</span>';

                    } else {
                        ms.innerHTML = '<span class="small text-muted fst-italic w-100 text-center bg-danger-subtle p-4 rounded">Δεν υπάρχουν διαθέσιμα ραντεβού για αυτή την ημέρα. Παρακαλώ επιλέξτε μια άλλη ημερομηνία.</span>';
                    }
                } catch (e) {
                    console.error(e);
                    ms.innerHTML = '<span class="text-danger small">Παρουσιάστηκε πρόβλημα κατά τη φόρτωση. Παρακαλώ δοκιμάστε ξανά σε λίγο.</span>';
                }
            }

            function selectSlot(el, date, time) {
                document.querySelectorAll('.slot-btn').forEach(b => b.classList.remove('active'));
                el.classList.add('active');
                wizardState.slot = {
                    date,
                    time
                };
                updateAnchorLabel(); // Update the label
            }

            // --- HELPERS ---
            function toggleLoader(s) {
                if (s) els.loader.classList.remove('d-none');
                else els.loader.classList.add('d-none');
            }

            function showError(m) {
                els.errorMsg.innerText = m;
                els.errorAlert.classList.remove('d-none');
                setTimeout(() => els.errorAlert.classList.add('d-none'), 3000);
            }

            function hideError() {
                els.errorAlert.classList.add('d-none');
            }

            function resetWizard() {
                wizardState.step = 0;
                wizardState.slot = null;
                document.getElementById('bookingForm').reset();
                document.getElementById('morningSlots').innerHTML = '';
                document.getElementById('afternoonSlots').innerHTML = '';
                renderStep(0);
            }

            function selectSlot(el, date, time) {
                document.querySelectorAll('.slot-btn').forEach(b => b.classList.remove('active'));
                el.classList.add('active');
                wizardState.slot = {
                    date,
                    time
                };

                // Update Anchor Label
                updateAnchorLabel();
            }

            // --- Anchor Label Logic ---
            function updateAnchorLabel() {
                const anchor = document.getElementById('selectedSlotAnchor');
                if (wizardState.slot && wizardState.slot.time !== 'Fixed') {
                    const d = new Date(wizardState.slot.date);
                    const dateStr = d.toLocaleDateString('el-GR', {
                        day: 'numeric',
                        month: 'short',
                        weekday: 'short'
                    });
                    document.getElementById('anchorText').innerText = `Επιλογή: ${dateStr} @ ${wizardState.slot.time}`;
                    anchor.classList.remove('d-none');
                } else {
                    anchor.classList.add('d-none');
                }
            }

            // Clear Button Listener
            const clrBtn = document.getElementById('clearSlotBtn');
            if (clrBtn) {
                clrBtn.addEventListener('click', () => {
                    wizardState.slot = null;
                    updateAnchorLabel();
                    document.querySelectorAll('.slot-btn.active').forEach(b => b.classList.remove('active'));
                });
            }

            // Country Dropdown
            function initCountries() {
                const dd = document.getElementById('countryDropdown');
                const img = document.getElementById('selectedFlag');
                const span = document.getElementById('selectedCode');
                const inp = document.getElementById('finalCountryCode');

                const list = [{
                        c: '30',
                        r: 'gr',
                        n: 'Greece'
                    }, {
                        c: '357',
                        r: 'cy',
                        n: 'Cyprus'
                    },
                    {
                        c: '44',
                        r: 'gb',
                        n: 'UK'
                    }, {
                        c: '49',
                        r: 'de',
                        n: 'Germany'
                    }, {
                        c: '1',
                        r: 'us',
                        n: 'USA'
                    }
                ];
                dd.innerHTML = '';
                list.forEach(x => {
                    const li = document.createElement('li');
                    li.innerHTML = `<a class="dropdown-item d-flex align-items-center gap-2" href="#" data-c="${x.c}" data-img="/assets/images/countries/${x.r}.png"><img src="/assets/images/countries/${x.r}.png" width="20"> ${x.n} (+${x.c})</a>`;
                    li.querySelector('a').onclick = (e) => {
                        e.preventDefault();
                        img.src = e.currentTarget.dataset.img;
                        span.innerText = '+' + e.currentTarget.dataset.c;
                        inp.value = e.currentTarget.dataset.c;
                    };
                    dd.appendChild(li);
                });
            }
            initCountries();

        });
    </script>
<?php
}
?>
<button type="button" id="backToTop" class="back-to-top">
    <i class="fas fa-arrow-up"></i>
</button>

<footer class="epic-footer mt-auto">
    <div class="footer-container">

        <div class="footer-top">

            <div class="footer-brand gs-reveal">
                <a href="/">
                    <img src="/assets/images/logo/logo.png" alt="Plakias Running Logo" class="footer-logo mb-4">
                </a>
                <p>
                    Experience the raw beauty of Crete step by step. Exclusive trails, expert guiding, and memories that last a lifetime.
                </p>

                <div class="footer-meta mt-4">
                    <?php if (!empty($mainSettings->companySettings->physicalAddress)): ?>
                        <a href="<?= $mainSettings->companySettings->mapUrl ?>" target="_blank" class="meta-link">
                            <i class="bi bi-geo-alt"></i> <?= $mainSettings->companySettings->physicalAddress ?>
                        </a>
                    <?php endif; ?>

                    <?php if (!empty($mainSettings->companySettings->businessHours)): ?>
                        <p class="meta-text mt-2">
                            <span class="meta-label">HOURS:</span> <?= $mainSettings->companySettings->businessHours ?>
                        </p>
                    <?php endif; ?>
                </div>
            </div>

            <div class="footer-col gs-reveal">
                <h5 class="footer-heading">Explore</h5>
                <ul class="footer-links">
                    <li><a href="#routes">The Routes</a></li>
                    <li><a href="#private-coaching">Private Coaching</a></li>
                    <li><a href="#experience">What's Included</a></li>
                    <li><a href="/about">Our Story</a></li>
                </ul>
            </div>

            <div class="footer-col gs-reveal">
                <h5 class="footer-heading">Connect</h5>
                <ul class="footer-links">
                    <?php if (!empty($mainSettings->companySettings->socialInstagram)): ?>
                        <li><a href="<?= $mainSettings->companySettings->socialInstagram ?>" target="_blank">Instagram</a></li>
                    <?php endif; ?>

                    <?php if (!empty($mainSettings->companySettings->socialFacebook)): ?>
                        <li><a href="<?= $mainSettings->companySettings->socialFacebook ?>" target="_blank">Facebook</a></li>
                    <?php endif; ?>
                    <li><a href="#" target="_blank">Strava Club</a></li>

                    <li class="mt-3" style="pointer-events: none;"><span class="meta-label">CONTACT</span></li>

                    <?php if (!empty($mainSettings->companySettings->contactEmail)): ?>
                        <li><a href="mailto:<?= $mainSettings->companySettings->contactEmail ?>"><?= $mainSettings->companySettings->contactEmail ?></a></li>
                    <?php endif; ?>

                    <?php if (!empty($mainSettings->companySettings->contactPhoneNumber)): ?>
                        <li><a href="tel:<?= $mainSettings->companySettings->contactPhoneNumber ?>"><?= $mainSettings->companySettings->contactPhoneNumber ?></a></li>
                    <?php endif; ?>
                </ul>
            </div>

            <div class="footer-col gs-reveal">
                <h5 class="footer-heading">Join the Pack</h5>
                <p class="footer-desc mb-3" style="color: rgba(255,255,255,0.5); font-size: 0.85rem; line-height: 1.5;">
                    Subscribe for exclusive trails, tips, and runner news. No spam.
                </p>
                <form id="newsletterForm" class="minimal-form mt-2">
                    <div class="input-group">
                        <input type="email" class="form-control minimal-input" id="newsletterEmail" name="newsletterEmail" placeholder="Your email address" required>
                        <button type="submit" class="btn-minimal-submit"><i class="bi bi-arrow-right"></i></button>
                    </div>
                </form>
            </div>

            <div class="footer-col gs-reveal">
                <h5 class="footer-heading">Legal Info</h5>
                <ul class="footer-links">
                    <li><a href="legal">Privacy Policy</a></li>
                    <li><a href="cookies-policy">Cookies Policy</a></li>
                    <li><a href="terms">Terms & Conditions</a></li>
                    <li><a href="cancellation-policy">Cancellation Policy</a></li>
                </ul>
                <div class="mt-4 pt-2">
                    <img src="/assets/images/payments.png" alt="Secure Payments" class="footer-payments-img">
                </div>
            </div>

        </div>

        <div class="footer-massive">
            <svg viewBox="0 0 1600 180" class="massive-svg-text" preserveAspectRatio="xMidYMid meet">
                <text x="50%" y="55%" dominant-baseline="middle" text-anchor="middle">PLAKIAS RUNNING</text>
            </svg>
        </div>

        <div class="footer-bottom">
            <div class="copyright">
                &copy; <?= date('Y') ?> Plakias Running. All rights reserved.
            </div>

            <div class="credits">
                <span class="credits-text">Digital Experience by</span>
                <a href="https://hankatt.com" class="hankatt-badge" target="_blank">
                    <span class="hk-dot"></span> HANKATT
                </a>
            </div>
        </div>

    </div>
</footer>

<style>
    /* ========================
   EPIC FOOTER STYLES
   ======================== */
    .epic-footer {
        background-color: #050505;
        color: white;
        padding: 100px 0 30px 0;
        position: relative;
        overflow: hidden;
        border-top: 1px solid rgba(255, 255, 255, 0.05);
    }

    .footer-container {
        max-width: 1600px;
        margin: 0 auto;
        padding: 0 40px;
    }

    /* --- TOP GRID --- */
    .footer-top {
        display: grid;
        /* 5 Στήλες: Brand είναι διπλό, Newsletter 1.5, τα άλλα 1 */
        grid-template-columns: 2fr 1fr 1fr 1.5fr 1fr;
        gap: 40px;
        padding-bottom: 80px;
    }

    .footer-logo {
        height: 132px;
        width: auto;
        object-fit: contain;
        display: block;
        margin-bottom: 20px;
        /* Αν το logo σου είναι σκούρο, άσε το. Αν είναι ήδη λευκό/έγχρωμο ΣΒΗΣΕ την παρακάτω γραμμή: */
        filter: brightness(0) invert(1);
    }

    .footer-brand p {
        color: rgba(255, 255, 255, 0.6);
        font-size: 0.95rem;
        line-height: 1.6;
        max-width: 340px;
        margin-bottom: 0;
    }

    .meta-link {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        color: rgba(255, 255, 255, 0.8);
        text-decoration: none;
        font-size: 0.9rem;
        transition: color 0.3s ease;
    }

    .meta-link:hover {
        color: var(--accent-color, #29c285);
    }

    .meta-text {
        font-size: 0.9rem;
        color: rgba(255, 255, 255, 0.5);
    }

    .meta-label {
        font-weight: 700;
        color: white;
        letter-spacing: 1px;
        font-size: 0.75rem;
    }

    /* Headings & Links */
    .footer-heading {
        font-family: 'Courier New', monospace;
        font-size: 0.75rem;
        font-weight: 700;
        color: var(--accent-color, #29c285);
        letter-spacing: 2px;
        text-transform: uppercase;
        margin-bottom: 25px;
    }

    .footer-links {
        list-style: none;
        padding: 0;
        margin: 0;
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .footer-links li a {
        color: rgba(255, 255, 255, 0.85);
        text-decoration: none;
        font-size: 0.95rem;
        font-weight: 500;
        display: inline-flex;
        position: relative;
        padding-bottom: 4px;
        transition: color 0.3s ease;
    }

    .footer-links li a::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        width: 100%;
        height: 1px;
        background-color: var(--accent-color, #29c285);
        transform: scaleX(0);
        transform-origin: right;
        transition: transform 0.4s cubic-bezier(0.19, 1, 0.22, 1);
    }

    .footer-links li a:hover {
        color: white;
    }

    .footer-links li a:hover::after {
        transform: scaleX(1);
        transform-origin: left;
    }

    /* Payments Image */
    .footer-payments-img {
        max-width: 160px;
        height: auto;
        opacity: 0.3;
        filter: grayscale(100%);
        transition: all 0.3s ease;
    }

    .footer-payments-img:hover {
        opacity: 0.9;
        filter: grayscale(0%);
    }


    /* --- MINIMAL NEWSLETTER FORM --- */
    .minimal-form .input-group {
        display: flex;
        align-items: center;
        border-bottom: 1px solid rgba(255, 255, 255, 0.2);
        transition: border-color 0.3s ease;
        padding-bottom: 5px;
    }

    .minimal-form:hover .input-group {
        border-color: var(--accent-color, #29c285);
    }

    .minimal-input {
        background: transparent !important;
        border: none !important;
        color: white !important;
        padding: 10px 0;
        width: 100%;
        outline: none !important;
        box-shadow: none !important;
        font-size: 0.95rem;
    }

    .minimal-input::placeholder {
        color: rgba(255, 255, 255, 0.3);
        font-weight: 400;
    }

    .btn-minimal-submit {
        background: transparent;
        border: none;
        color: var(--accent-color, #29c285);
        font-size: 1.2rem;
        cursor: pointer;
        transition: transform 0.3s ease;
        padding: 0 10px;
    }

    .btn-minimal-submit:hover {
        transform: translateX(5px);
    }


    /* --- MIDDLE: MASSIVE SVG TEXT (NEVER CUTS OFF) --- */
    .footer-massive {
        border-top: 1px solid rgba(255, 255, 255, 0.1);
        padding-top: 40px;
        width: 100%;
    }

    .massive-svg-text {
        width: 100%;
        height: auto;
        max-height: 180px;
        /* Όριο ύψους για πολύ μεγάλες οθόνες */
        display: block;
    }

    .massive-svg-text text {
        fill: transparent;
        stroke: rgba(255, 255, 255, 0.15);
        stroke-width: 3px;
        /* Λίγο πιο παχύ για να φαίνεται τέλεια στις κινητές οθόνες */
        font-family: inherit;
        font-weight: 900;
        font-size: 150px;
        /* Μεγαλώσαμε το font-size γιατί μεγαλώσαμε το κουτί */
        letter-spacing: 5px;
        /* Έξτρα αέρας στα γράμματα */
        transition: stroke 0.4s ease;
    }

    .footer-massive:hover .massive-svg-text text {
        stroke: rgba(255, 255, 255, 0.5);
        /* Φωτίζει στο hover */
    }


    /* --- BOTTOM: COPYRIGHT & HANKATT CREDIT --- */
    .footer-bottom {
        margin-top: 40px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 0.8rem;
        color: rgba(255, 255, 255, 0.4);
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    /* Το νέο εντυπωσιακό Credit */
    .credits {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .credits-text {
        font-size: 0.75rem;
    }

    .hankatt-badge {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        color: white;
        font-weight: 800;
        text-decoration: none;
        letter-spacing: 1px;
        padding: 6px 14px;
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 50px;
        background: rgba(255, 255, 255, 0.03);
        transition: all 0.3s ease;
    }

    .hankatt-badge:hover {
        border-color: var(--accent-color, #29c285);
        background: rgba(41, 194, 133, 0.1);
        transform: translateY(-2px);
        color: white;
    }

    /* Το πράσινο λαμπάκι */
    .hk-dot {
        width: 6px;
        height: 6px;
        background-color: var(--accent-color, #29c285);
        border-radius: 50%;
        box-shadow: 0 0 8px var(--accent-color, #29c285);
        animation: pulse-hk 2s infinite;
    }

    @keyframes pulse-hk {
        0% {
            transform: scale(0.95);
            opacity: 0.8;
        }

        50% {
            transform: scale(1.3);
            opacity: 1;
        }

        100% {
            transform: scale(0.95);
            opacity: 0.8;
        }
    }


    /* --- RESPONSIVE FIXES --- */
    @media (max-width: 1200px) {
        .footer-top {
            grid-template-columns: 2fr 1fr 1fr;
        }

        .footer-brand,
        .footer-col:nth-child(4),
        .footer-col:nth-child(5) {
            grid-column: span 3;
            margin-top: 20px;
        }
    }

    @media (max-width: 992px) {
        .epic-footer {
            padding: 80px 0 20px 0;
        }

        .footer-container {
            padding: 0 20px;
        }

        .footer-top {
            grid-template-columns: 1fr;
            gap: 40px;
            padding-bottom: 50px;
        }

        .footer-brand,
        .footer-col {
            grid-column: span 1 !important;
            margin-top: 0;
        }

        .footer-bottom {
            flex-direction: column;
            gap: 20px;
            text-align: center;
        }

        .credits {
            flex-direction: column;
            gap: 5px;
        }
    }
</style>
<script>
    const backToTopBtn = document.getElementById('backToTop');
    if (backToTopBtn) {
        window.addEventListener('scroll', () => {
            if (window.scrollY > 300) {
                backToTopBtn.classList.add('show');
            } else {
                backToTopBtn.classList.remove('show');
            }
        });
        backToTopBtn.addEventListener('click', () => {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
    }
</script>

<script src="https://code.jquery.com/jquery-3.5.1.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js" integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js" integrity="sha384-cVKIPhGWiC2Al4u+LWgxfKTRIcfu0JTxR+EQDz/bgldoEyl4H0zUF0QKbrJ0EcQF" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.5.7/jquery.fancybox.min.js"></script>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" integrity="sha512-xodZBNTC5n17Xt2atTPuE1HxjVMSvLVW9ocqUKLsCC5CXdbqCmblAshOMAS6/keqq/sMZMZ19scR4PsZChSR7A==" crossorigin="" />

<script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js" integrity="sha512-XQoYMqMTK8LvdxXYG3nZ448hOEQiglfqkJs1NOQV44cWnUrBc8PkAOcXy20w0vlaXaVUearIOBhiXZ5V3ynxwA==" crossorigin=""></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js" integrity="sha512-A7AYk1fGKX6S2SsHywmPkrnzTZHrgiVT7GcQkLGDe2ev0aWb8zejytzS8wjo7PGEXKqJOrjQ4oORtnimIRZBtw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css" integrity="sha512-1cK78a1o+ht2JcaW6g8OXYwqpev9+6GqOkz9xmBN9iUUhIndKtxwILGWYOSibOKjLsEdjyjZvYDq/cZwNeak0w==" crossorigin="anonymous" referrerpolicy="no-referrer" />
<script src="https://sandbox-js.everypay.gr/v3"></script>

<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

<!-- Scripts -->
<script src="<?php echo $GLOBALS['config']['base_url']; ?>assets/vendor/plugins/moment/moment.js"></script>

<script src="<?php echo $GLOBALS['config']['base_url']; ?>assets/vendor/plugins/slick/slick.min.js"></script>

<script src="<?php echo $GLOBALS['config']['base_url']; ?>assets/js/main.js"></script>
<!-- Optional: jQuery and Bootstrap JS -->

<?php
if (function_exists('hook_end_scripts'))
    hook_end_scripts();
?>

<script>
    document.getElementById('newsletterForm').addEventListener('submit', function(e) {
        e.preventDefault();

        // Show loader or disable submit button
        const submitButton = this.querySelector('button[type="submit"]');
        submitButton.disabled = true;

        // Prepare data
        const email = document.getElementById('newsletterEmail').value.trim();

        const formData = new FormData();
        formData.append('action', 'subscribeNewsletter');
        formData.append('email', email);

        // Include CSRF token if needed
        const csrf_token = document.querySelector('meta[name="csrf_token"]')?.getAttribute('content');
        if (csrf_token) {
            formData.append('csrf_token', csrf_token);
        }

        fetch('/includes/ajax.php', {
                method: 'POST',
                body: formData,
            })
            .then(response => response.json())
            .then(data => {
                submitButton.disabled = false;
                if (data.success) {
                    alert(data.message);
                    // Reset the form
                    this.reset();
                } else {
                    alert('Σφάλμα: ' + data.errors.join('\n'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                submitButton.disabled = false;
                alert('Υπήρξε πρόβλημα κατά την εγγραφή σας στο newsletter.');
            });
    });
</script>

</body>

</html>
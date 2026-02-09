<button type="button" id="backToTop" class="back-to-top">
    <i class="fas fa-arrow-up"></i>
</button>

<footer class="site-footer mt-auto">
    <div class="container">

        <div class="row mb-5 pb-5 border-bottom-soft align-items-end">
            <div class="col-lg-8">
                <h2 class="footer-title">
                    Κάντε το πρώτο βήμα για εσάς <br>και την <span class="serif-italic">υγεία σας.</span>
                </h2>
            </div>
            <div class="col-lg-4 text-lg-end mt-4 mt-lg-0">
                <a href="contact" class="btn-alma-solid">Κλείστε Ραντεβού</a>
            </div>
        </div>

        <div class="row py-5">

            <div class="col-lg-4 mb-5 mb-lg-0">
                <img src="/assets/images/logo/logo.png" class="footer-logo mb-4" alt="Logo">

                <div class="address-block">
                    <a href="<?= $mainSettings->companySettings->mapUrl ?>" target="_blank" class="address-link">
                        <?= $mainSettings->companySettings->physicalAddress ?>
                    </a>
                    <p class="hours-text mt-2">
                        <span class="label">ΩΡΑΡΙΟ:</span><br>
                        <?= $mainSettings->companySettings->businessHours ?>
                    </p>
                </div>
            </div>

            <div class="col-lg-4 mb-5 mb-lg-0">
                <span class="footer-label">ΕΠΙΚΟΙΝΩΝΙΑ</span>
                <ul class="footer-contact-list">
                    <li>
                        <a href="mailto:<?= $mainSettings->companySettings->contactEmail ?>" class="big-contact-link">
                            <?= $mainSettings->companySettings->contactEmail ?>
                        </a>
                    </li>
                    <li>
                        <a href="tel:<?= $mainSettings->companySettings->contactPhoneNumber ?>" class="big-contact-link">
                            <?= $mainSettings->companySettings->contactPhoneNumber ?>
                        </a>
                    </li>
                </ul>

                <div class="social-links mt-4">
                    <?php if (!empty($mainSettings->companySettings->socialFacebook)): ?>
                        <a href="<?= $mainSettings->companySettings->socialFacebook ?>" class="social-icon"><i class="bi bi-facebook"></i></a>
                    <?php endif; ?>

                    <?php if (!empty($mainSettings->companySettings->socialInstagram)): ?>
                        <a href="<?= $mainSettings->companySettings->socialInstagram ?>" class="social-icon"><i class="bi bi-instagram"></i></a>
                    <?php endif; ?>

                    <?php if (!empty($mainSettings->companySettings->socialTwitter)): ?>
                        <a href="<?= $mainSettings->companySettings->socialTwitter ?>" class="social-icon"><i class="bi bi-tiktok"></i></a>
                    <?php endif; ?>
                </div>
            </div>

            <div class="col-lg-3 offset-lg-1">
                <span class="footer-label">NEWSLETTER</span>
                <p class="footer-desc mb-4">
                    Εγγραφείτε για άρθρα, νέα και συμβουλές ψυχικής υγείας.
                </p>

                <form id="newsletterForm" class="minimal-form">
                    <div class="input-group">
                        <input type="email" class="form-control minimal-input" id="newsletterEmail" name="newsletterEmail" placeholder="Το email σας" required>
                        <button type="submit" class="btn-minimal-submit"><i class="bi bi-arrow-right"></i></button>
                    </div>
                </form>

                <div class="mt-5">
                    <img src="/assets/images/payments.png" class="img-fluid opacity-50" alt="Payments" style="max-width:180px;">
                </div>
            </div>

        </div>

        <div class="row pt-4 copyright-row">
            <div class="col-md-6 mb-3 mb-md-0">
                <div class="legal-links">
                    <a href="legal"><?= TRS('privacy') ?></a>
                    <span class="separator">/</span>
                    <a href="cookies-policy"><?= TRS('cookies_policy') ?></a>
                    <span class="separator">/</span>
                    <a href="payments-policy">Πολιτική Πληρωμών</a>
                </div>
            </div>
            <div class="col-md-6 text-md-end">
                <small class="text-muted">Design/Development <a href="https://hankatt.com" class="credits-link">hankatt</a></small>
            </div>
        </div>

    </div>
</footer>

<style>
    /* ========================
   ALMA FOOTER STYLES
   ======================== */
    .site-footer {
        background-color: var(--alma-accent);
        /* ή var(--alma-bg) αν έχεις το μπεζ */
        padding: 100px 0 40px 0;
        color: var(--alma-text);
        border-top: 1px solid rgba(0, 0, 0, 0.05);
    }

    /* --- TOP SECTION --- */
    .footer-title {
        font-family: 'Playfair Display', serif;
        font-size: 3rem;
        line-height: 1.2;
        color: var(--alma-text);
        margin: 0;
    }

    .border-bottom-soft {
        border-bottom: 1px solid rgba(0, 0, 0, 0.08);
    }

    /* --- INFO COLUMNS --- */
    .footer-logo {
        height: 80px;
        /* Προσαρμογή ύψους για κομψότητα */
        width: auto;
        object-fit: contain;
    }

    .address-link {
        font-size: 1.1rem;
        color: #555;
        text-decoration: none;
        line-height: 1.6;
        display: block;
        transition: color 0.3s ease;
    }

    .address-link:hover {
        color: var(--alma-orange);
    }

    .hours-text {
        font-size: 0.95rem;
        color: #888;
        line-height: 1.6;
    }

    .hours-text .label {
        font-weight: 700;
        font-size: 0.75rem;
        letter-spacing: 1px;
        color: var(--alma-text);
    }

    .footer-label {
        display: block;
        font-size: 0.75rem;
        font-weight: 700;
        letter-spacing: 1.5px;
        color: #999;
        margin-bottom: 25px;
    }

    /* --- BIG CONTACT LINKS --- */
    .footer-contact-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .big-contact-link {
        font-family: 'Playfair Display', serif;
        font-size: 1.6rem;
        /* Μεγάλα γράμματα */
        color: var(--alma-text);
        text-decoration: none;
        display: block;
        margin-bottom: 8px;
        transition: color 0.3s ease;
    }

    .big-contact-link:hover {
        color: var(--alma-orange);
    }

    /* --- SOCIALS --- */
    .social-links {
        display: flex;
        gap: 15px;
    }

    .social-icon {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        border: 1px solid rgba(0, 0, 0, 0.1);
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--alma-text);
        text-decoration: none;
        transition: all 0.3s ease;
    }

    .social-icon:hover {
        background-color: var(--alma-text);
        color: #fff;
        border-color: var(--alma-text);
    }

    /* --- MINIMAL FORM --- */
    .minimal-form .input-group {
        border-bottom: 1px solid rgba(0, 0, 0, 0.2);
    }

    .minimal-input {
        border: none;
        background: transparent;
        padding: 10px 0;
        border-radius: 0;
        box-shadow: none !important;
    }

    .minimal-input:focus {
        background: transparent;
    }

    .btn-minimal-submit {
        background: transparent;
        border: none;
        color: var(--alma-text);
        font-size: 1.2rem;
        transition: transform 0.3s ease;
    }

    .btn-minimal-submit:hover {
        transform: translateX(5px);
        color: var(--alma-orange);
    }

    /* --- BOTTOM BAR --- */
    .copyright-row {
        border-top: 1px solid rgba(0, 0, 0, 0.08);
        font-size: 0.9rem;
    }

    .legal-links a {
        color: #666;
        text-decoration: none;
        transition: color 0.2s ease;
    }

    .legal-links a:hover {
        color: var(--alma-orange);
    }

    .separator {
        margin: 0 8px;
        color: #ccc;
    }

    .credits-link {
        color: var(--alma-text);
        text-decoration: none;
        font-weight: 600;
    }

    .opacity-50 {
        opacity: 0.5;
        filter: grayscale(100%);
        transition: all 0.3s;
    }

    .opacity-50:hover {
        opacity: 1;
        filter: grayscale(0%);
    }

    /* RESPONSIVE */
    @media (max-width: 991px) {
        .site-footer {
            padding: 60px 0 30px 0;
        }

        .footer-title {
            font-size: 2rem;
            margin-bottom: 20px;
        }

        .big-contact-link {
            font-size: 1.3rem;
        }

        .col-lg-4,
        .col-lg-3 {
            margin-bottom: 40px;
        }

        .text-lg-end {
            text-align: left !important;
        }
    }
</style>

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
<?php
if ($mainUser->isLoggedIn()) header("Location: profile", true, 302);
?>

<style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&family=JetBrains+Mono:wght@400&display=swap');

    /* Scoped CSS Wrapper */
    #hankatt-login-wrapper {
        --hk-bg: #FAF9F6;
        --hk-dark: #2e2e2e;
        --hk-green: #22b573;
        --hk-orange: #ef663e;
        --hk-text: #2e2e2e;
        --hk-text-muted: #666;
        --hk-border: #e0e0e0;

        font-family: 'Inter', sans-serif;
        color: var(--hk-text);
        background-color: var(--hk-bg);

        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        z-index: 9999;
        display: flex;
        width: 100%;
        height: 100vh;
        overflow: hidden;
    }

    #hankatt-login-wrapper * {
        box-sizing: border-box;
    }

    /* --- LEFT COLUMN --- */
    .brand-side {
        display: none;
        flex: 1;
        background-color: var(--hk-dark);
        position: relative;
        overflow: hidden;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        color: #fff;
    }

    .brand-side::before {
        content: '';
        position: absolute;
        width: 150%;
        height: 150%;
        background: radial-gradient(circle at 50% 10%, var(--hk-green), transparent 40%),
            radial-gradient(circle at 80% 80%, var(--hk-orange), transparent 40%);
        opacity: 0.15;
        filter: blur(60px);
        animation: rotateBg 20s linear infinite;
    }

    .brand-side-content {
        z-index: 2;
        text-align: center;
        padding: 40px;
    }

    /* --- LOGO --- */
    #hankatt-login-wrapper {
        --logo-svg: url("/assets/images/app_logo.svg");
    }

    .brand-big-icon {
        width: 220px;
        height: 120px;
        background-image: var(--logo-svg);
        background-size: contain;
        background-repeat: no-repeat;
        background-position: center;
        margin: 0 auto 2rem auto;
        filter: contrast(0.5);
        transition: transform 0.3s ease;
    }

    .brand-big-icon:hover {
        transform: scale(1.05) rotate(-2deg);
    }

    .brand-headline {
        font-size: 2.5rem;
        font-weight: 700;
        margin-bottom: 0.5rem;
        letter-spacing: -0.5px;
    }

    .brand-tagline {
        font-family: 'JetBrains Mono', monospace;
        font-size: 0.9rem;
        opacity: 0.7;
        color: #fff;
    }

    /* --- RIGHT COLUMN --- */
    .form-side {
        flex: 1;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        padding: 40px;
        background-color: var(--hk-bg);
        position: relative;
    }

    .login-box {
        width: 100%;
        max-width: 360px;
    }

    .mobile-logo-view {
        display: block;
        width: 60px;
        height: 60px;
        background-image: var(--logo-svg);
        background-size: contain;
        background-repeat: no-repeat;
        background-position: center;
        margin: 0 auto 20px auto;
    }

    .login-header {
        margin-bottom: 1.5rem;
        text-align: center;
    }

    .login-header h2 {
        font-size: 1.75rem;
        font-weight: 600;
        margin: 0 0 0.5rem 0;
        color: var(--hk-dark);
    }

    .login-header p {
        color: var(--hk-text-muted);
        font-size: 0.95rem;
        margin: 0;
    }

    /* Messages Styling */
    .response-message {
        min-height: 20px;
        font-size: 0.9rem;
        text-align: center;
        margin-bottom: 1.5rem;
        font-weight: 500;
        border-radius: 6px;
    }

    .text-danger {
        color: var(--hk-orange);
    }

    .text-success {
        color: var(--hk-green);
    }

    /* Inputs */
    .hk-form-group {
        margin-bottom: 1.25rem;
    }

    .hk-label {
        display: block;
        font-size: 0.85rem;
        font-weight: 600;
        color: var(--hk-dark);
        margin-bottom: 0.5rem;
    }

    .hk-input-wrapper {
        position: relative;
    }

    .hk-input {
        width: 100%;
        padding: 12px 15px;
        font-size: 1rem;
        border: 2px solid var(--hk-border);
        background-color: #fff;
        border-radius: 6px;
        transition: all 0.2s ease;
        color: var(--hk-dark);
        font-family: 'Inter', sans-serif;
    }

    .hk-input:focus {
        outline: none;
        border-color: var(--hk-green);
        box-shadow: 0 4px 12px rgba(34, 181, 115, 0.1);
    }

    .hk-toggle-pass:hover {
        color: var(--hk-orange);
    }

    .toggle-password {
        float: right;
        cursor: pointer;
        margin-right: 10px;
        margin-top: -36px;
        z-index: auto;
        position: relative;
        font-size: 1rem;
    }


    .hk-btn {
        width: 100%;
        padding: 14px;
        background-color: var(--hk-dark);
        color: #fff;
        border: none;
        border-radius: 6px;
        font-size: 1rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
        margin-top: 1rem;
    }

    .hk-btn:hover {
        background-color: var(--hk-green);
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }

    .hk-btn:disabled {
        background-color: #ccc;
        cursor: not-allowed;
        transform: none;
    }

    .hk-credits {
        position: absolute;
        bottom: 20px;
        width: 100%;
        text-align: center;
        font-size: 0.8rem;
        color: var(--hk-text-muted);
    }

    .hk-credits a {
        color: var(--hk-dark);
        text-decoration: none;
        font-weight: 600;
        border-bottom: 2px solid var(--hk-orange);
    }

    @keyframes rotateBg {
        0% {
            transform: rotate(0deg);
        }

        100% {
            transform: rotate(360deg);
        }
    }

    @media (min-width: 992px) {
        .brand-side {
            display: flex;
        }

        .mobile-logo-view {
            display: none;
        }

        .login-header {
            text-align: left;
        }

        .response-message {
            text-align: left;
        }

        /* Align message left on desktop */
    }

    footer {
        display: none !important;
    }
</style>

<div id="hankatt-login-wrapper">

    <div class="brand-side">
        <div class="brand-side-content">
            <div class="brand-big-icon"></div>
            <div class="brand-tagline">POWERED BY HANKATT</div>
        </div>
    </div>

    <div class="form-side">
        <div class="login-box login-form">

            <div class="mobile-logo-view"></div>

            <div class="login-header">
                <h2>Admin Login</h2>
                <p>Enter your credentials to continue.</p>
            </div>

            <div class="response-message"></div>

            <form id="loginForm">
                <div class="hk-form-group">
                    <label for="email" class="hk-label">Email Address</label>
                    <input type="email" class="hk-input" id="email" name="email" placeholder="user@hankatt.com" autofocus required>
                </div>

                <div class="hk-form-group">
                    <label for="password" class="hk-label">Password</label>
                    <div class="hk-input-wrapper">
                        <input type="password" class="hk-input is-pass" id="password" name="password" placeholder="••••••••" required>
                        <i class="hk-toggle-pass toggle-password bi bi-eye" onclick="showpwd()"></i>
                    </div>
                </div>

                <button type="submit" class="hk-btn" id="loginBtn">Sign In</button>
            </form>
        </div>

        <div class="hk-credits">
            Designed & Developed by <a href="https://hankatt.com">Hankatt</a>
        </div>
    </div>
</div>

<?php
function hook_end_scripts()
{
?>
    <script src="<?php echo $GLOBALS['config']['base_url']; ?>assets/js/login.js"></script>

    <script>
        if (typeof showLoader === 'undefined') {
            window.showLoader = function(selector, status) {
                // Simple opacity effect if real loader is missing
                if (!status) $(selector).css('opacity', '0.7');
                else $(selector).css('opacity', '1');
            };
        }
    </script>
<?php
}
?>
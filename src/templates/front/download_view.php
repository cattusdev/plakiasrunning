<?php
// PHP Logic (Παραμένει ίδια)
$token = Input::get('t');
$error = null;
$productTitle = '';
$readyToDownload = false;

$dp = new DigitalProducts();

if ($token) {
    $order = $dp->getOrderByToken($token);

    if (is_object($order)) {
        $productTitle = $order->title;
        $readyToDownload = true;

        if (Input::exists() && Input::get('action') === 'download_file') {
            $dp->serveFile($order);
            exit();
        }
    } elseif ($order === 'expired') {
        $error = "Λυπούμαστε, ο σύνδεσμος λήψης έχει λήξει (ισχύει για 7 ημέρες).";
    } elseif ($order === 'limit_reached') {
        $error = "Έχετε ξεπεράσει το μέγιστο όριο λήψεων (2 φορές).";
    } else {
        $error = "Ο σύνδεσμος δεν είναι έγκυρος.";
    }
} else {
    $error = "Δεν βρέθηκε κωδικός λήψης.";
}
?>

<style>
    body {
        background-color: #f8f9fa;
        /* Απαλό γκρι φόντο */
    }

    .download-container {
        min-height: 85vh;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 20px;
    }

    .status-card {
        background: white;
        border-radius: 20px;
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.08);
        padding: 40px 30px;
        max-width: 480px;
        width: 100%;
        text-align: center;
        position: relative;
        overflow: hidden;
        border: none;
    }

    /* Icon Styling */
    .icon-wrapper {
        width: 100px;
        height: 100px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 25px auto;
        font-size: 3rem;
    }

    .icon-success {
        background-color: rgba(81, 187, 160, 0.1);
        /* Primary color very light */
        color: #51BBA0;
        animation: pulse 2s infinite;
    }

    .icon-error {
        background-color: #fdecea;
        color: #e74c3c;
    }

    @keyframes pulse {
        0% {
            box-shadow: 0 0 0 0 rgba(81, 187, 160, 0.4);
        }

        70% {
            box-shadow: 0 0 0 15px rgba(81, 187, 160, 0);
        }

        100% {
            box-shadow: 0 0 0 0 rgba(81, 187, 160, 0);
        }
    }

    /* File Info Box */
    .file-box {
        background: #f1f5f9;
        border-radius: 12px;
        padding: 15px;
        margin: 20px 0;
        display: flex;
        align-items: center;
        gap: 15px;
        border: 1px solid #e2e8f0;
    }

    .file-icon {
        font-size: 2rem;
        color: #64748b;
    }

    .file-details {
        text-align: left;
        overflow: hidden;
    }

    .file-name {
        font-weight: 700;
        color: #334155;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        display: block;
        max-width: 100%;
    }

    .file-label {
        font-size: 0.8rem;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    /* Button */
    .btn-download-hero {
        background-color: #51BBA0;
        color: white;
        border: none;
        border-radius: 50px;
        padding: 12px 30px;
        font-weight: 600;
        font-size: 1.1rem;
        transition: all 0.3s ease;
        box-shadow: 0 4px 15px rgba(81, 187, 160, 0.3);
    }

    .btn-download-hero:hover {
        background-color: #3fa58a;
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(81, 187, 160, 0.4);
    }

    .btn-download-hero:disabled {
        background-color: #a0dbc9;
        cursor: not-allowed;
        transform: none;
    }

    /* Links */
    .back-link {
        color: #6c757d;
        text-decoration: none;
        font-weight: 500;
        font-size: 0.9rem;
        transition: color 0.2s;
    }

    .back-link:hover {
        color: #333;
    }
</style>

<div class="download-container">
    <div class="status-card">

        <?php if ($error): ?>
            <div class="icon-wrapper icon-error">
                <i class="bi bi-x-lg"></i>
            </div>

            <h3 class="fw-bold text-danger mb-3">Σφάλμα</h3>
            <p class="text-muted mb-4"><?php echo $error; ?></p>

            <a href="/" class="btn btn-outline-secondary rounded-pill px-4">
                <i class="bi bi-arrow-left"></i> Επιστροφή
            </a>

        <?php elseif ($readyToDownload): ?>
            <div class="icon-wrapper icon-success">
                <i class="bi bi-cloud-arrow-down-fill"></i>
            </div>

            <h4 class="fw-bold mb-2">Το αρχείο σας είναι έτοιμο!</h4>
            <p class="text-muted small">Η λήψη είναι διαθέσιμη και ασφαλής.</p>

            <div class="file-box">
                <div class="file-icon">
                    <i class="bi bi-file-earmark-pdf-fill text-danger"></i>
                </div>
                <div class="file-details">
                    <span class="file-label">ΨΗΦΙΑΚΟ ΑΡΧΕΙΟ</span>
                    <span class="file-name"><?php echo htmlspecialchars($productTitle); ?></span>
                </div>
            </div>

            <form method="POST">
                <input type="hidden" name="action" value="download_file">

                <button type="submit" class="btn btn-download-hero w-100" id="btnDownload">
                    <i class="bi bi-download me-2"></i> Κατέβασμα Τώρα
                </button>
            </form>

            <div class="mt-4 pt-3 border-top">
                <p class="small text-muted mb-0"> 
                    <i class="bi bi-shield-check text-success me-1"></i> Ασφαλής σύνδεσμος Alma Psychology
                </p>
            </div>
        <?php endif; ?>

    </div>
</div>

<script>
    const btn = document.getElementById('btnDownload');
    if (btn) {
        btn.parentElement.addEventListener('submit', function() {
            // Κλειδώνουμε το κουμπί για να μην πατηθεί πολλές φορές
            // ΑΛΛΑ, επειδή το download δεν κάνει refresh τη σελίδα, 
            // απλά αλλάζουμε το στυλ για UX feedback.

            setTimeout(() => {
                btn.innerHTML = '<i class="bi bi-check-circle-fill me-2"></i> Η λήψη ξεκίνησε';
                btn.style.backgroundColor = '#2c3e50'; // Darker color
                btn.style.boxShadow = 'none';
                // Δεν κάνουμε disable true μόνιμα, γιατί μπορεί να θέλει να ξαναπατήσει αν κολλήσει
            }, 500);

            // Επαναφορά μετά από 3 δευτερόλεπτα
            setTimeout(() => {
                btn.innerHTML = '<i class="bi bi-download me-2"></i> Κατέβασμα ξανά';
                btn.style.backgroundColor = ''; // Reset to CSS
            }, 4000);
        });
    }
</script>
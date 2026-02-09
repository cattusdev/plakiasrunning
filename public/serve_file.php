<?php
// Φόρτωση του Core (Database, Config κλπ)
require_once __DIR__ . '/../src/core/init.php';

// Έλεγχος ότι ήρθε POST request (για να μην το ανοίγουν απλά με URL στον browser)
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die("Μη επιτρεπτή μέθοδος.");
}

$token = Input::get('token');

if (!$token) {
    die("Λείπει το token.");
}

$db = Database::getInstance();

// 1. Βρες την παραγγελία
$order = $db->query("SELECT * FROM product_orders WHERE download_token = ?", [$token])->first();

if (!$order) {
    die("Λάθος Token.");
}

// 2. Βρες το προϊόν
$product = $db->query("SELECT * FROM digital_products WHERE id = ?", [$order->product_id])->first();

if (!$product || empty($product->file_path)) {
    die("Το προϊόν δεν βρέθηκε.");
}

// 3. Κατασκεύασε το Path (ΠΡΟΣΟΧΗ: Το path στη βάση είναι π.χ. uploads/ebooks/file.pdf)
// Το 'app_root_public' πρέπει να είναι το απόλυτο path στον server (π.χ. /var/www/html/)
$fullPath = Config::get('app_root_public') . '/' . $product->file_path;

if (!file_exists($fullPath)) {
    die("Σφάλμα συστήματος: Το αρχείο λείπει.");
}

// 4. Καταγραφή Download (Log)
$db->query("UPDATE product_orders SET downloads_count = downloads_count + 1 WHERE id = ?", [$order->id]);

// 5. Στείλε το αρχείο (Force Download)
// Καθαρισμός output buffers για να μην καταστραφεί το PDF
if (ob_get_level()) ob_end_clean();

header('Content-Description: File Transfer');
header('Content-Type: application/pdf'); // Ή application/octet-stream για όλα
header('Content-Disposition: attachment; filename="' . basename($fullPath) . '"');
header('Expires: 0');
header('Cache-Control: must-revalidate');
header('Pragma: public');
header('Content-Length: ' . filesize($fullPath));

readfile($fullPath);
exit;

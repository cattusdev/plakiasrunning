<?php
class DigitalProducts
{
    private $db;
    private $crud;
    private $_data;
    private $_lastInsertedID;

    public function __construct()
    {
        $this->db = Database::getInstance();
        $this->crud = new Crud();
    }

    /**
     * Add a new digital product
     * @param array $fields
     * @return bool
     */
    public function addProduct($fields = [])
    {
        $result = $this->crud->add('digital_products', $fields);
        if ($result['affected_rows'] > 0) {
            $this->_lastInsertedID = $result['insert_id'];
            return true;
        }
        return false;
    }

    /**
     * Update a product
     * @param array $fields
     * @param int $id
     * @return bool
     */
    public function updateProduct($fields, $id)
    {
        $where = ['id' => $id];
        $result = $this->crud->update('digital_products', $fields, $where);
        return $result['affected_rows'] > 0; // Update logic might vary based on your Crud class return
    }

    /**
     * Delete a product
     * @param int $id
     * @return bool
     */
    public function deleteProduct($id)
    {
        // First get the file paths to delete actual files (handled in controller usually, but good to know)
        $where = ['id' => $id];
        $result = $this->crud->delete('digital_products', $where);
        return isset($result['affected_rows']) && $result['affected_rows'] > 0;
    }

    public function fetchProduct($id)
    {
        return $this->crud->getSpecific('digital_products', 'id', '=', $id);
    }

    public function fetchProducts()
    {
        $products = $this->crud->getAll('digital_products');
        if ($products) {
            return $this->_data = $products;
        }
        return false;
    }

    public function fetchProductsFront()
    {
        // ΕΠΙΛΕΓΟΥΜΕ ΜΟΝΟ ΤΑ ΔΗΜΟΣΙΑ ΠΕΔΙΑ (Χωρίς το file_path)
        $sql = "SELECT id, title, description, price, cover_image, created_at 
                FROM digital_products 
                ORDER BY created_at DESC";

        $results = $this->db->query($sql);

        // Έλεγχος αν ο πίνακας δεν είναι άδειος
        if (!empty($results) && is_array($results)) {
            return $this->_data = $results;
        }

        return false;
    }

    public function data()
    {
        return $this->_data;
    }

    /**
     * Fetch all orders with client and product details
     * @return array|bool
     */
    
    public function fetchOrders()
    {
        $sql = "SELECT 
                    po.id, 
                    po.created_at, 
                    po.downloads_count, 
                    po.payment_token,
                    c.first_name, 
                    c.last_name, 
                    c.email, 
                    dp.title as product_title,
                    dp.price
                FROM product_orders po
                JOIN clients c ON po.client_id = c.id
                JOIN digital_products dp ON po.product_id = dp.id
                ORDER BY po.created_at DESC";

        $results = $this->db->query($sql);

        // ΔΙΟΡΘΩΣΗ: Ελέγχουμε αν ο πίνακας δεν είναι άδειος
        if (!empty($results) && is_array($results)) {
            return $results; // Επιστρέφουμε απευθείας τον πίνακα
        }

        return false;
    }

    /**
     * Reset order limits (downloads or expiration)
     * @param int $orderId
     * @param string $type 'downloads' or 'expiration'
     * @return bool
     */
    public function resetOrder($orderId, $type)
    {
        // Ορίζουμε τα πεδία προς ανανέωση
        $fields = [];

        if ($type === 'downloads') {
            $fields['downloads_count'] = 0;
        } elseif ($type === 'expiration') {
            $fields['created_at'] = date('Y-m-d H:i:s');
        } else {
            return false;
        }

        $where = ['id' => $orderId];

        $result = $this->crud->update('product_orders', $fields, $where);

        if (isset($result['error']) && $result['error']) {
            return false;
        }

        return true;
    }

    /**
     * Get order details by download token with validation
     * @param string $token
     * @return object|string|false
     */
    public function getOrderByToken($token)
    {
        // --- ΡΥΘΜΙΣΕΙΣ ---
        $maxDownloads = 2; // Μέγιστος αριθμός κατεβασμάτων
        $expireDays = 7;   // Ο σύνδεσμος λήγει μετά από 7 ημέρες
        // -----------------

        $sql = "SELECT po.*, dp.title, dp.file_path 
                FROM product_orders po 
                JOIN digital_products dp ON po.product_id = dp.id 
                WHERE po.download_token = ?";
        
        $result = $this->db->query($sql, [$token]);
        
        if (!empty($result)) {
            $order = $result[0];

            // 1. Έλεγχος Λήξης Χρόνου
            // Αν η ημερομηνία αγοράς είναι παλαιότερη από Χ μέρες
            if (strtotime($order->created_at) < strtotime("-{$expireDays} days")) {
                return 'expired'; // Επιστρέφει ειδικό κωδικό λάθους
            }

            // 2. Έλεγχος Ορίου Λήψεων
            if ($order->downloads_count >= $maxDownloads) {
                return 'limit_reached'; // Επιστρέφει ειδικό κωδικό λάθους
            }

            return $order;
        }
        return false;
    }

    /**
     * Serve the file for download
     * @param object $orderData
     */
    public function serveFile($orderData)
    {
        // Κατασκευή του απόλυτου path
        // Υποθέτουμε ότι το 'file_path' στη βάση είναι relative (π.χ. uploads/ebooks/file.pdf)
        $fullPath = Config::get('app_root_public') . '/' . $orderData->file_path;

        if (!file_exists($fullPath)) {
            die("Σφάλμα: Το αρχείο δεν βρέθηκε στον server.");
        }

        // Καταγραφή download count
        $this->db->query("UPDATE product_orders SET downloads_count = downloads_count + 1 WHERE id = ?", [$orderData->id]);

        // Καθαρισμός buffer
        if (ob_get_level()) ob_end_clean();

        // Headers για download
        header('Content-Description: File Transfer');
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="' . basename($fullPath) . '"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($fullPath));

        readfile($fullPath);
        exit;
    }
}

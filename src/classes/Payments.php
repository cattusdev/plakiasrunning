<?php
class Payments
{
    private $_db;
    private $crud;
    private $_data;
    private $_everypayPrivateKey = 'sk_eKU9TD2sQbOYN91JzwYQ0qMgxxmsTMEp';
    private $_cardLinkPrivateKey = '';
    private $_cardLinkMid = '';

    public $_lastID;

    public function __construct()
    {
        $this->_db = Database::getInstance();
        $this->crud = new Crud();

    }


    /**
     * Get last inserted ID
     *
     * @return int
     */
    public function lastInsertedID()
    {
        return $this->_lastID;
    }

    public function fetchPayments()
    {
        $query = "SELECT payments.*, clients.first_name, clients.last_name 
        FROM payments LEFT JOIN clients 
        ON clients.id = payments.client_id";

        $payments = $this->_db->query($query);
        if ($payments) {
            return $this->_data = $payments;
        }
        return false;
    }

    public function getPaymentsByBooking($bookingId)
    {
        $sql = "SELECT * FROM payments WHERE reservation_id = :bid ORDER BY created_at DESC";
        return $this->_db->query($sql, [':bid' => $bookingId]) ?: [];
    }
    


    public function fetchPayment($paymentID)
    {
        return $this->crud->getSpecific('payments', 'id', '=', $paymentID);
    }

    public function addPayment($fields = [])
    {
        $result = $this->crud->add('payments', $fields);
        if ($result['affected_rows'] > 0) {
            $this->_lastID = $result['insert_id'];
            return true;
        }
        return false;
    }


    public function updatePayment($fields, $paymentID)
    {
        // Αφαιρούμε το var_dump
        // exit(var_dump($fields)); 

        $where = ['id' => $paymentID];
        $result = $this->crud->update('payments', $fields, $where);

        // ΔΙΟΡΘΩΣΗ:
        // Ελέγχουμε αν υπάρχει το 'error' στο αποτέλεσμα.
        // Αν ΔΕΝ υπάρχει error, θεωρούμε ότι πέτυχε, ακόμα κι αν affected_rows είναι 0.
        if (!isset($result['error']) || empty($result['error'])) {
            return true;
        }

        // Εναλλακτικά, αν η κλάση Crud επιστρέφει true/false αντί για array σε περίπτωση λάθους:
        // if ($result !== false) { return true; }

        return false;
    }


    public function deletePayment($paymentID)
    {
        $where = ['id' => $paymentID];
        $result = $this->crud->delete('payments', $where);
        if (isset($result['affected_rows']) && $result['affected_rows'] > 0) {
            return true;
        }
        return false;
    }

    public function data()
    {
        return $this->_data;
    }



    public function doPaymentEveryPay($token, $chargeAmount, $description = null, $email = null)
    {
        $pk = $this->_everypayPrivateKey;
        $postRequest = array(
            'token' => $token,
            'amount' => $chargeAmount,
            'description' => $description,
            'payee_email' => $email,

        );

        // $cURLConnection = curl_init('https://api.everypay.gr/payments');
        $cURLConnection = curl_init('https://sandbox-api.everypay.gr/payments');
        curl_setopt($cURLConnection, CURLOPT_USERPWD, $pk);
        curl_setopt($cURLConnection, CURLOPT_POSTFIELDS, $postRequest);
        curl_setopt($cURLConnection, CURLOPT_RETURNTRANSFER, true);

        $apiResponse = curl_exec($cURLConnection);
        curl_close($cURLConnection);

        $jsonArrayResponse = json_decode($apiResponse);

        return $jsonArrayResponse;

        exit($apiResponse);
        // $apiResponse - available data from the API request
        $jsonArrayResponse = json_decode($apiResponse);

        exit($jsonArrayResponse);
    }


    //CardLink

    static function get_acquirer_url($environment, $acquirer)
    {
        if ($environment == "test") {
            switch ($acquirer) {
                case 0:
                    return $post_url = "https://ecommerce-test.cardlink.gr/vpos/shophandlermpi";
                case 1:
                    return $post_url = "https://alphaecommerce-test.cardlink.gr/vpos/shophandlermpi";
                case 2:
                    return $post_url = "https://eurocommerce-test.cardlink.gr/vpos/shophandlermpi";
            }
        } else {
            switch ($acquirer) {
                case 0:
                    return $post_url = "https://ecommerce.cardlink.gr/vpos/shophandlermpi";
                case 1:
                    return $post_url = "https://www.alphaecommerce.gr/vpos/shophandlermpi";
                case 2:
                    return $post_url = "https://vpos.eurocommerce.gr/vpos/shophandlermpi";
            }
        }
    }
    static function calculate_digest($input)
    {
        $digest = base64_encode(hash('sha256', ($input), true));

        return $digest;
    }



    static function get_acquirers()
    {
        return [
            'Cardlink Checkout',
            'Nexi Checkout',
            'Worldline Greece Checkout'
        ];
    }

    function createPayForm($formDataArr = array())
    {

        // $locale = get_locale();
        // if ($locale == 'el') {
        // $lang = 'el';
        // } else {
        // $lang = 'en';
        // }

        $version  = 2;
        $currency = 'EUR';
        // $merchant_id = "0024362675"; //Nexi
        // $form_secret = "jvTJx8jb3u4wMTPuvXEKlN";


        ///TESTING
        $merchant_id = "9000004556"; //Nexi
        // $merchant_id = "9000004275"; //CardLink
        $form_secret = "Cardlink1";


        //get post urls 
        $post_url = self::get_acquirer_url('test', 1);


        $form_data_array = array(
            'version'              => $version,
            'mid'                  => $merchant_id,
            // 'lang'                 => $lang,
            'orderid'              => 'Order' . $formDataArr['orderid'],
            'orderDesc'            => 'Order #' . $formDataArr['orderDesc'],
            'orderAmount'          => $formDataArr['orderAmount'],
            'currency'             => $currency,
            'payerEmail'           => $formDataArr['payerEmail'],
            // 'payerPhone'           => $order->get_billing_phone(),
            'billCountry'          => 'GR', //$formDataArr['billCountry'],
            // 'billState'            => $state_code,
            'billZip'              => $formDataArr['billZip'],
            'billCity'             => $formDataArr['billCity'],
            'billAddress'          => $formDataArr['billAddress'],
            // 'shipCountry'          => $order->get_shipping_country(),
            // 'shipZip'              => $order->get_shipping_postcode(),
            // 'shipCity'             => $order->get_shipping_city(),
            // 'shipAddress'          => $order->get_shipping_address_1(),
            // 'trType'               => $trType,
            // 'extInstallmentoffset' => 0,
            // 'extInstallmentperiod' => $installments,
            // 'cssUrl'               => $this->css_url,
            'confirmUrl'           => "https://dimarasapartments.gr/thank-you",
            'cancelUrl'            => "https://dimarasapartments.gr/transaction-failed",
        );


        $form_data   = iconv('utf-8', 'utf-8//IGNORE', implode("", $form_data_array)) . $form_secret;
        $digest      = self::calculate_digest($form_data);

        $use_redirection = true;
        $form_target     = $use_redirection ? '_top' : 'payment_iframe';
        $html            = '<form action="' . htmlspecialchars($post_url) . '" method="POST" id="payment_form" target="' . $form_target . '" accept-charset="UTF-8">';

        foreach ($form_data_array as $key => $value) {
            $html .= '<input type="hidden" id ="' . $key . '" name ="' . $key . '" value="' . iconv('utf-8', 'utf-8//IGNORE', $value) . '"/>';
        }

        $html .= '<input type="hidden" id="digest" name ="digest" value="' . htmlspecialchars($digest) . '"/>';
        $html .= '<!-- Button Fallback -->
            <div class="payment_buttons d-none" style="display:none;">
                <input type="submit" class="button alt" id="submit_cardlink_payment_form" value="' . 'Pay via Cardlink' . ' cardlink-payment-gateway' . '" /> 
            </div>
            <script type="text/javascript">
                
            </script>';
        $html .= '</form>';

        $html .= '
                <div class="modal fade" id="paymentModal" tabindex="-1" role="dialog" aria-labelledby="paymentModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="paymentModalLabel">Payment</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">';

        if ($use_redirection) {
            $html .= '<script type="text/javascript">
                              $(document).ready(function() {
                                  
                              });
                      </script>';
        } else {
            // $html .= '<div class="' . $order_id . '_modal">';
            // $html .= '<div class="' . $order_id . '_modal_wrapper">';
            $html .= '<iframe name="payment_iframe" id="payment_iframe" src="" frameBorder="0" data-order-id="' . $formDataArr['id'] . '"></iframe>';
            // $html .= '</div>';
            // $html .= '</div>';
        }

        $html .= '      
                </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>
              
                        
                <!-- JavaScript to open modal -->
                <script type="text/javascript">
                    $(document).ready(function() {
                        
                       

                        $("#submit_cardlink_payment_form").click(function() {
                            $("#paymentModal").modal("show");
                        });
                        $("#paymentModal").on("hidden.bs.modal", function () {
                            location.reload();
                        });
                    });
                </script>
                ';

        return $html;
    }
}

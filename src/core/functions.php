<?php

require_once __DIR__ . '/../../src/plugins/PHPMailer-master/src/Exception.php';
require_once __DIR__ . '/../../src/plugins/PHPMailer-master/src/PHPMailer.php';
require_once __DIR__ . '/../../src/plugins/PHPMailer-master/src/SMTP.php';


use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;


function checkEmailConnection($host, $port, $username, $password)
{
    $mail = new PHPMailer;
    $mail->CharSet = 'UTF-8';
    // $mail->SMTPDebug = 2;
    $mail->Timeout = 5;
    $mail->isSMTP();
    $mail->SMTPSecure = 'ssl';
    $mail->SMTPAuth = true;
    $mail->Host = $host;
    $mail->Port = $port;
    $mail->Username = $username;
    $mail->Password = $password;

    // Check the connection
    if (!$mail->smtpConnect()) {
        $mail->smtpClose();
        return false;
    } else {
        $mail->smtpClose();
        return true;
    }

    // Disconnect from SMTP
    $mail->smtpClose();
    return false;
}

function sendMail($credentials = array(),  $subject, $message,  $addresses)
{
    $mail = new PHPMailer;
    $mail->CharSet = 'UTF-8';
    $mail->isSMTP();
    $mail->Timeout = 15;
    $mail->SMTPSecure = 'ssl';
    $mail->SMTPAuth = true;
    $mail->Host = (isset($credentials['smtp_host']) && !empty($credentials['smtp_host'])) ? $credentials['smtp_host'] : Config::get('SMTP/smtp_host');
    $mail->Port = (isset($credentials['smtp_port']) && !empty($credentials['smtp_port'])) ? $credentials['smtp_port'] : Config::get('SMTP/smtp_port');
    $mail->Username = (isset($credentials['smtp_user']) && !empty($credentials['smtp_user'])) ? $credentials['smtp_user'] : Config::get('SMTP/smtp_user');
    $mail->Password = (isset($credentials['smtp_password']) && !empty($credentials['smtp_password'])) ? $credentials['smtp_password'] : Config::get('SMTP/smtp_password');
    $mail->isHTML(true);
    $mail->setFrom((isset($credentials['sender']) && !empty($credentials['sender'])) ? $credentials['sender'] : Config::get('SMTP/smtp_user'), (isset($credentials['from']) && !empty($credentials['from'])) ? $credentials['from'] : Config::get('siteInfo/siteName'), false);
    $mail->Subject = $subject;
    $mail->Body = $message;


    // Split addresses by comma, trim whitespace, and add each address
    $addressArray = array_filter(array_map('trim', explode(',', $addresses)));

    // Send individual emails to each recipient
    $success = true;
    foreach ($addressArray as $address) {
        $mail->clearAddresses(); // Clear previous addresses
        $mail->addAddress($address);

        // Log the address being added
        error_log("Adding email address: $address");

        if (!$mail->send()) {
            // Log the error message
            error_log("PHPMailer error: " . $mail->ErrorInfo);
            $success = false;
        }
    }

    $mail->smtpClose();
    return $success;
}


function secure_compare($a, $b)
{
    $diff = strlen($a) ^ strlen($b);
    for ($i = 0; $i < strlen($a) && $i < strlen($b); $i++) {
        $diff |= ord($a[$i]) ^ ord($b[$i]);
    }
    return $diff === 0;
}


function TRS($strName, $forceLang = "")
{
    global $GLOBAL_LANGUAGE;
    global $GLOBAL_LANGUAGE_OBJ;

    if ($forceLang != "")
        $lang = $forceLang;
    else
        $lang = $GLOBAL_LANGUAGE;

    if (array_key_exists($lang, $GLOBAL_LANGUAGE_OBJ)) {
        if (array_key_exists($strName, $GLOBAL_LANGUAGE_OBJ[$lang]))
            return $GLOBAL_LANGUAGE_OBJ[$lang][$strName];
        else
            return "TRANSLATION_STRING_UNDEFINED";
    } else
        return "TRANSLATION_LANG_UNDEFINED";
}


function parseCurrencyInput($input)
{
    // Remove currency symbols and other non-numeric characters except for the decimal point
    $numberString = preg_replace('/[^\d\.-]/', '', $input);

    // Convert to a float value
    $numberFloat = floatval($numberString);

    return $numberFloat;
}


function generateStructuredData()
{
    // Determine the page type
    $pageType = $GLOBALS['pageType'] ?? 'WebPage';

    // Get meta information
    $metaTitle = $GLOBALS['dynamicMeta']['title'] ?? $GLOBALS['pageInfo']['meta']['title'] ?? 'Default Title';
    $metaDescription = $GLOBALS['dynamicMeta']['description'] ?? $GLOBALS['pageInfo']['meta']['description'] ?? 'Default description';
    $metaImage = $GLOBALS['dynamicMeta']['image'] ?? $GLOBALS['config']['base_url'] . 'assets/images/default-image.jpg';
    $metaUrl = $GLOBALS['dynamicMeta']['url'] ?? $GLOBALS['config']['base_url'];
    $metaDatePublished = $GLOBALS['dynamicMeta']['datePublished'] ?? date(DATE_ISO8601);
    $metaDateModified = $GLOBALS['dynamicMeta']['dateModified'] ?? date(DATE_ISO8601);

    // Base structured data
    $structuredData = [
        "@context" => "https://schema.org",
        "@type" => $pageType,
        "name" => $metaTitle,
        "description" => $metaDescription,
        "url" => $metaUrl,
    ];

    // Add image if available
    if ($metaImage) {
        $structuredData['image'] = $metaImage;
    }


    //EXAMPLE OF DYNAMIC PAGE PRODUCT 

    // $GLOBALS['pageType'] = 'Product';
    // // Make product data available globally
    // $GLOBALS['productData'] = $productData;
    // // Prepare product-specific variables
    // $productName = $productData->meta_title;
    // $productDescription = $productData->meta_description ?? substr($productData->description, 0, 150);
    // $productImage = $mainImage->image_path ?? '/assets/images/default/imagepreview.png';
    // $productUrl = $GLOBALS['config']['base_url'] . 'product/' . rawurlencode($productData->url_slug);

    // // Set dynamic meta tags
    // $GLOBALS['dynamicMeta']['title'] = "{$productName} | Επαγγελματικός Εξοπλισμός | ZANTEQUIP";
    // $GLOBALS['dynamicMeta']['description'] = $productDescription;
    // $GLOBALS['dynamicMeta']['image'] = $GLOBALS['config']['base_url'] . $productImage;
    // $GLOBALS['dynamicMeta']['url'] = $productUrl;


    // Add additional properties based on page type
    switch ($pageType) {
        case 'Product':
            $productData = $GLOBALS['productData'] ?? null;
            if ($productData) {
                $structuredData['sku'] = $productData->productSku ?? '';
                $structuredData['brand'] = [
                    "@type" => "Brand",
                    "name" => $productData->manufacturer->name ?? '',
                ];
                $structuredData['offers'] = [
                    "@type" => "Offer",
                    "url" => $metaUrl,
                    "priceCurrency" => "EUR", // Adjust currency as needed
                    "price" => $productData->price ?? '0.00',
                    "availability" => "https://schema.org/" . ($productData->availabilitySchema ?? 'InStock'),
                    "itemCondition" => "https://schema.org/NewCondition",
                ];
                // Optionally add aggregateRating if available
                if (isset($productData->ratingValue) && isset($productData->reviewCount)) {
                    $structuredData['aggregateRating'] = [
                        "@type" => "AggregateRating",
                        "ratingValue" => $productData->ratingValue,
                        "reviewCount" => $productData->reviewCount,
                    ];
                }
            }
            break;

        case 'Article':
        case 'NewsArticle':
            $newsData = $GLOBALS['newsData'] ?? null;
            if ($newsData) {
                $structuredData['headline'] = $metaTitle;
                $structuredData['datePublished'] = $metaDatePublished;
                $structuredData['dateModified'] = $metaDateModified;
                $structuredData['author'] = [
                    "@type" => "Organization",
                    "name" => $GLOBALS['config']['siteInfo']['siteName'] ?? 'My Site',
                ];
                $structuredData['publisher'] = [
                    "@type" => "Organization",
                    "name" => $GLOBALS['config']['siteInfo']['siteName'] ?? 'My Site',
                    "logo" => [
                        "@type" => "ImageObject",
                        "url" => $GLOBALS['config']['base_url'] . 'assets/images/logo.png',
                    ],
                ];
                $structuredData['mainEntityOfPage'] = [
                    "@type" => "WebPage",
                    "@id" => $metaUrl,
                ];
            }
            break;

            // You can add more cases for other page types as needed

        default:
            // For default WebPage or other types
            break;
    }

    // Output the structured data
    echo '<script type="application/ld+json">' . json_encode($structuredData, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . '</script>';
}

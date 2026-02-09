<?php
class Template
{
	public static function returnShortcuts($source, $options = array())
	{
		return str_replace(
			[
				"{fullName}",
				"{customerAddress}",
				"{customerEmail}",
				"{customerMessage}",
				"{messageContent}",
				"{customerName}",
				"{customerPhone}",
				"{email}",
				"{eventEnd}",
				"{eventStart}",
				"{eventTitle}",
				"{newsletterToken}",
				"{orderDate}",
				"{orderItems}",
				"{orderReference}",
				"{orderTotal}",
				"{registrationDate}",
				"{packageTitle}",
				"{price}",
				"{paymentToken}",
				"{bookingID}",
				// --- NEW SHORTCUTS ---
				"{date}",
				"{time}",
				"{appointmentType}"
			],
			[
				$options['fullName'] ?? '',
				$options['customerAddress'] ?? '',
				$options['customerEmail'] ?? '',
				$options['customerMessage'] ?? '',
				$options['messageContent'] ?? '',
				$options['customerName'] ?? '',
				$options['customerPhone'] ?? '',
				$options['email'] ?? '',
				$options['eventEnd'] ?? '',
				$options['eventStart'] ?? '',
				$options['eventTitle'] ?? '',
				$options['newsletterToken'] ?? null,
				$options['orderDate'] ?? '',
				$options['orderItems'] ?? '',
				$options['orderReference'] ?? '',
				$options['orderTotal'] ?? '',
				$options['registrationDate'] ?? '',
				$options['packageTitle'] ?? '',
				$options['price'] ?? '',
				$options['paymentToken'] ?? '',
				$options['bookingID'] ?? '',
				// --- NEW MAPPINGS ---
				$options['date'] ?? '',
				$options['time'] ?? '',
				$options['type'] ?? '' // Maps to the 'type' key in your $userMailData
			],
			$source
		);
	}

	public static function mailTemplate($options = array(), $lang = 'en')
	{
		$mainSettingsCT = new Settings();
		$allSettingsT = $mainSettingsCT->fetchSettings();
		$mainSettingsT = new SettingsObject($allSettingsT);

		// Process variables in the message
		$message = self::returnShortcuts($message = $options['message'], $options);

		// Site URL for images/links
		$siteUrl = $GLOBALS['config']['siteInfo']['siteURL'];

		// --- LOGIC FOR SOCIAL MEDIA VISIBILITY ---
		// Preserving your logic: if set to "block", hide it.
		$displayFb = ($mainSettingsT->companySettings->socialFacebook != "block") ? "inline-block" : "none";
		$displayTw = ($mainSettingsT->companySettings->socialTwitter != "block") ? "inline-block" : "none";
		$displayIg = ($mainSettingsT->companySettings->socialInstagram != "block") ? "inline-block" : "none";

		// --- UNSUBSCRIBE LINK ---
		// Refactored to be a clean div instead of a table cell for better flex/centering
		$unsubscribeBlock = '';
		if (!empty($options['newsletterToken'])) {
			$unsubUrl = $siteUrl . 'unsubscribe/?key=unsubscribe&email=' . $options['email'] . '&sec_token=' . $options['newsletterToken'];
			$unsubscribeBlock = '
            <div style="margin-top: 20px; font-size: 12px; color: #89c2aa;">
                <a href="' . $unsubUrl . '" style="color: #334c47; text-decoration: underline;">Unsubscribe</a>
            </div>';
		}

		// --- THE MODERN TEMPLATE ---
		$mailTemplate = '
<!DOCTYPE html>
<html lang="en" xmlns="http://www.w3.org/1999/xhtml" xmlns:o="urn:schemas-microsoft-com:office:office">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <meta name="x-apple-disable-message-reformatting">
    <title></title>
    <style>
        /* BRAND COLORS MAPPING */
        /* --alma-orange: #e9a871;
           --alma-accent: #fdfbf5;
           --alma-nav-text: #334c47;
           --alma-text: #232323;
           --alma-bg: #faf6f1;
           --alma-bg-button-main: #89c2aa;
        */
        
        table, td, div, h1, p {font-family: Helvetica, Arial, sans-serif;}
        body { margin: 0; padding: 0; background-color: #faf6f1; }
        
        /* Mobile Resets */
        @media screen and (max-width: 530px) {
            .col-lge {
                max-width: 100% !important;
            }
            .content-padding {
                padding: 20px !important;
            }
        }
    </style>
</head>
<body style="margin:0;padding:0;background-color:#faf6f1;">
    <div role="article" aria-roledescription="email" lang="en" style="background-color:#faf6f1; font-family:Helvetica, Arial, sans-serif;">
        
        <table role="presentation" style="width:100%;border:none;border-spacing:0;">
            <tr>
                <td align="center" style="padding:20px 0;">
                
                    <table role="presentation" style="width:94%;max-width:600px;border:none;border-spacing:0;text-align:center;font-family:Arial,sans-serif;">
                        <tr>
                            <td style="padding:20px 0;text-align:center;">
                                <a href="' . $siteUrl . '" style="text-decoration:none;">
                                    <img src="' . $siteUrl . $mainSettingsT->companySettings->logoPath . '" alt="Logo" width="120" style="width:120px;max-width:80%;height:auto;border:none;text-decoration:none;color:#334c47;">
                                </a>
                            </td>
                        </tr>
                    </table>

                    <table role="presentation" style="width:94%;max-width:600px;border:none;border-spacing:0;text-align:left;font-family:Arial,sans-serif;font-size:16px;line-height:26px;color:#232323;">
                        <tr>
                            <td class="content-padding" style="padding:40px;background-color:#ffffff;border-radius:8px;box-shadow: 0 4px 6px rgba(0,0,0,0.02);">
                                
                                <div style="color: #232323; font-size: 16px; line-height: 1.6;">
                                    ' . $message . '
                                </div>
                                
                                </td>
                        </tr>
                    </table>

                    <table role="presentation" style="width:94%;max-width:600px;border:none;border-spacing:0;text-align:center;font-family:Arial,sans-serif;font-size:14px;line-height:22px;color:#334c47;">
                        <tr>
                            <td style="padding:30px 0;">
                                
                                <div style="margin-bottom: 20px;">
                                    <a href="' . $mainSettingsT->companySettings->socialFacebook . '" style="display:' . $displayFb . ';margin:0 8px;text-decoration:none;">
                                        <img src="https://app-rsrc.getbee.io/public/resources/social-networks-icon-sets/t-circle-dark-gray/facebook@2x.png" width="28" alt="FB" style="border:0;">
                                    </a>
                                    <a href="' . $mainSettingsT->companySettings->socialTwitter . '" style="display:' . $displayTw . ';margin:0 8px;text-decoration:none;">
                                        <img src="https://app-rsrc.getbee.io/public/resources/social-networks-icon-sets/t-circle-dark-gray/twitter@2x.png" width="28" alt="TW" style="border:0;">
                                    </a>
                                    <a href="' . $mainSettingsT->companySettings->socialInstagram . '" style="display:' . $displayIg . ';margin:0 8px;text-decoration:none;">
                                        <img src="https://app-rsrc.getbee.io/public/resources/social-networks-icon-sets/t-circle-dark-gray/instagram@2x.png" width="28" alt="IG" style="border:0;">
                                    </a>
                                </div>

                                <p style="margin:0;font-size:13px;color:#334c47;">
                                    <strong>' . $GLOBALS['config']['siteInfo']['siteName'] . '</strong><br>
                                    <a href="' . $mainSettingsT->companySettings->mapUrl . '" style="color:#334c47;text-decoration:none;">' . $mainSettingsT->companySettings->physicalAddress . '</a><br>
                                    <a href="mailto:' . $mainSettingsT->companySettings->contactEmail . '" style="color:#334c47;text-decoration:none;">' . $mainSettingsT->companySettings->contactEmail . '</a>
                                    <span style="margin:0 5px;">|</span>
                                    <a href="tel:' . $mainSettingsT->companySettings->contactPhoneNumber . '" style="color:#334c47;text-decoration:none;">' . $mainSettingsT->companySettings->contactPhoneNumber . '</a>
                                </p>

                                <p style="margin:20px 0 0 0;font-size:12px;color:#89c2aa;">
                                    &copy; ' . date('Y') . ' ' . $GLOBALS['config']['siteInfo']['siteName'] . '. All rights reserved.
                                </p>
                                ' . $unsubscribeBlock . '
                            </td>
                        </tr>
                    </table>

                </td>
            </tr>
        </table>
    </div>
</body>
</html>';

		return $mailTemplate;
	}

	public static function unique()
	{
		return bin2hex(openssl_random_pseudo_bytes(32));
	}
}

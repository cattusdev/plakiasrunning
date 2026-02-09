<?php
$GLOBAL_INCLUDE_CHECK = true;

// Set security headers
header("Strict-Transport-Security: max-age=31536000; includeSubDomains; preload");
header("X-Frame-Options: DENY");
header("X-XSS-Protection: 1; mode=block");
header("X-Content-Type-Options: nosniff");


// Start session
ob_start();
session_start();


$GLOBALS['config'] = require_once __DIR__ . '/../config/config.php';


spl_autoload_register(function ($className) {
    require_once __DIR__ .  '/../classes/' . $className . '.php';
});

if (Config::get('dev_mode') === 'dev') {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}


date_default_timezone_set('Europe/Athens');

$mainUser = new User();

$languageManager = new LanguageManager();
$languageManager->initializeLanguage();


$mainSettingsC = new Settings();
$allSettings = $mainSettingsC->fetchSettings();

$mainSettings = new SettingsObject($allSettings);

require_once __DIR__ . '/../core/functions.php';




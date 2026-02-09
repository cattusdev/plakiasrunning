<?php

$hostname = 'https://plakiasrunning.gr/';
define('BASE_PATH', '');

$DEV_MODE = 'dev';

switch ($DEV_MODE) {
    case 'dev':
        $hostname = 'http://' . getenv('HTTP_HOST') . '/' . BASE_PATH;
        break;
    case 'local':
        $hostname = 'https://' . getHostByName(php_uname('n')) . '/' . BASE_PATH;
        break;
    default:
        break;
}

return [
    'dev_mode' => $DEV_MODE,
    'base_path' => BASE_PATH,
    'base_url' => $hostname,
    'timezone' => 'Europe/Athens',
    'app_root' => dirname(dirname(__FILE__)),
    'app_root_public' => dirname(dirname(dirname(__FILE__))) .'/public',
    'siteInfo' => [
        'siteName' => 'Plakias Running',
        'siteURL' => 'https://plakiasrunning.gr/',
        'developer' => 'Hankatt',
        'developerURL' => 'https://hankatt.com/'
    ],
    'mysql' => [
        'host' => 'localhost',
        //Dev
        'userName' => 'root',
        'password' => '',
        'dbName' => 'plakias'
        //Production
        // 'userName' => 'ugpohznpvknpy',
        // 'password' => 'iwoh02hwg0h9',
        // 'dbName' => 'db4iwdlojuepkb'
    ],
    'session' => [
        'session_name' => 'plakias_user',
        'csrf_token' => 'csrf_token'
    ],
    'SMTP' => [
        'smtp_host' => '',
        'smtp_port' => 465,
        'smtp_user' => '',
        'smtp_password' => ''
    ]
];

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf_token" content="<?php echo Token::genToken('csrf_token'); ?>">
    <meta name="robots" content="noindex">
    <title><?php echo $GLOBALS['config']['siteInfo']['siteName'] . ' - ' . $pageInfo['title'] ?></title>

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="<?php echo $GLOBALS['config']['base_url']; ?>assets/css/back/side_menu.css">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />


    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />

    <link rel="stylesheet" href="<?php echo $GLOBALS['config']['base_url']; ?>assets/css/back/main.css">
    <link rel="stylesheet" href="<?php echo $GLOBALS['config']['base_url']; ?>assets/css/back/child.css">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <!-- <link href="https://fonts.googleapis.com/css2?family=Quicksand&display=swap" rel="stylesheet"> -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:ital,wght@0,300..800;1,300..800&display=swap" rel="stylesheet">

</head>

<div id="preloader">
    <div id="status" class="">&nbsp;
        <svg version="1.1" id="L9" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 100 100" enable-background="new 0 0 0 0" xml:space="preserve">
            <rect x="20" y="50" width="4" height="10" fill="#fff">
                <animateTransform attributeType="xml" attributeName="transform" type="translate" values="0 0; 0 20; 0 0" begin="0" dur="0.6s" repeatCount="indefinite"></animateTransform>
            </rect>
            <rect x="30" y="50" width="4" height="10" fill="#fff">
                <animateTransform attributeType="xml" attributeName="transform" type="translate" values="0 0; 0 20; 0 0" begin="0.2s" dur="0.6s" repeatCount="indefinite"></animateTransform>
            </rect>
            <rect x="40" y="50" width="4" height="10" fill="#fff">
                <animateTransform attributeType="xml" attributeName="transform" type="translate" values="0 0; 0 20; 0 0" begin="0.4s" dur="0.6s" repeatCount="indefinite"></animateTransform>
            </rect>
        </svg>
    </div>
</div>
<button id="scrollTopBtn" class="btn border rounded bg-primary" style="display: none; position: fixed; bottom: 20px; right: 20px; z-index: 1000;">
    <i class="bi bi-arrow-up-circle-fill"></i>
</button>
<!-- <button id="theme-toggle">Toggle Theme</button> -->
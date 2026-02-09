<!DOCTYPE html>
<html lang="<?= $GLOBAL_LANGUAGE ?>">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf_token" content="<?php echo Token::genToken('csrf_token'); ?>">
    <meta name="robots" content="noindex">
    <title>
        <?php
        $siteName = $GLOBALS['config']['siteInfo']['siteName'] ?? '';
        echo htmlspecialchars(
            $GLOBALS['dynamicMeta']['title'] ??
                ($pageInfo['meta']['title'] ?? ($siteName . ' - ' . ($pageInfo['title'] ?? 'Σελίδα')))
        );
        ?>
    </title>
    <link rel="icon" href="/assets/images/logo/favicon.ico" type="image/x-icon">
    <link rel="icon" type="image/png" sizes="32x32" href="/assets/images/logo/favicon.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/assets/images/logo/favicon.png">
    <link rel="apple-touch-icon" sizes="180x180" href="/assets/images/logo/favicon.png">

    <?php
    $metaDescription = $GLOBALS['dynamicMeta']['description'] ??
        $pageInfo['meta']['description'] ??
        '';

    $metaKeywords = $GLOBALS['dynamicMeta']['keywords'] ??
        $pageInfo['meta']['keywords'] ??
        '';

    $metaImage = $GLOBALS['dynamicMeta']['image'] ??
        $GLOBALS['config']['base_url'] . 'assets/images/logo/social_logo.png';

    $metaUrl = $GLOBALS['dynamicMeta']['url'] ??
        $GLOBALS['config']['base_url'] . !isset($pageInfo['name']) ? '' : $pageInfo['name'];

    $metaTitle = $GLOBALS['dynamicMeta']['title'] ??
        ($pageInfo['meta']['title'] ?? $siteName);

    $language = $GLOBAL_LANGUAGE ?? 'el';

    // Echo meta tags
    echo '<meta name="description" content="' . htmlspecialchars($metaDescription) . '" />' . PHP_EOL;
    echo '<meta name="keywords" content="' . htmlspecialchars($metaKeywords) . '" />' . PHP_EOL;

    echo '<meta property="og:locale" content="' . ($language === 'en' ? 'en_US' : 'el_GR') . '" />' . PHP_EOL;
    echo '<meta property="og:site_name" content="' . htmlspecialchars($siteName) . '" />' . PHP_EOL;
    echo '<meta property="og:title" content="' . htmlspecialchars($metaTitle) . '" />' . PHP_EOL;
    echo '<meta property="og:url" content="' . htmlspecialchars($metaUrl) . '" />' . PHP_EOL;
    echo '<meta property="og:type" content="' . ($pageType ?? 'website') . '" />' . PHP_EOL;
    echo '<meta property="og:description" content="' . htmlspecialchars($metaDescription) . '" />' . PHP_EOL;
    echo '<meta property="og:image" content="' . htmlspecialchars($metaImage) . '" />' . PHP_EOL;
    echo '<meta property="og:image:url" content="' . htmlspecialchars($metaImage) . '" />' . PHP_EOL;
    echo '<meta property="og:image:secure_url" content="' . htmlspecialchars($metaImage) . '" />' . PHP_EOL;
    echo '<meta property="og:image:width" content="1200" />' . PHP_EOL;
    echo '<meta property="og:image:height" content="600" />' . PHP_EOL;
    echo '<meta property="og:image:alt" content="' . htmlspecialchars($metaTitle) . '" />' . PHP_EOL;

    echo '<meta itemprop="name" content="' . htmlspecialchars($metaTitle) . '" />' . PHP_EOL;
    echo '<meta itemprop="headline" content="' . htmlspecialchars($metaTitle) . '" />' . PHP_EOL;
    echo '<meta itemprop="description" content="' . htmlspecialchars($metaDescription) . '" />' . PHP_EOL;
    echo '<meta itemprop="image" content="' . htmlspecialchars($metaImage) . '" />' . PHP_EOL;

    echo '<meta name="twitter:card" content="summary_large_image" />' . PHP_EOL;
    echo '<meta name="twitter:site" content="@yourtwitterhandle" />' . PHP_EOL; // Replace with your Twitter handle
    echo '<meta name="twitter:title" content="' . htmlspecialchars($metaTitle) . '" />' . PHP_EOL;
    echo '<meta name="twitter:description" content="' . htmlspecialchars($metaDescription) . '" />' . PHP_EOL;
    echo '<meta name="twitter:image" content="' . htmlspecialchars($metaImage) . '" />' . PHP_EOL;

    echo '<link rel="canonical" href="' . htmlspecialchars($metaUrl) . '" />' . PHP_EOL;

    // Alternate language URLs
    // echo '<link rel="alternate" hreflang="el" href="' . htmlspecialchars($GLOBALS['dynamicMeta']['url']) . '" />' . PHP_EOL;
    // echo '<link rel="alternate" hreflang="en" href="' . htmlspecialchars($GLOBALS['dynamicMeta']['url']) . '?lang=en" />' . PHP_EOL;

    generateStructuredData();
    ?>
    <!-- Bootstrap CSS -->

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.5.7/jquery.fancybox.min.css" />
    <!-- <link rel="stylesheet" href="<?php echo $GLOBALS['config']['base_url']; ?>assets/css/main.css"> -->
    <!-- <link rel="stylesheet" href="<?php echo $GLOBALS['config']['base_url']; ?>assets/css/child.css"> -->
    <link rel="stylesheet" href="<?php echo $GLOBALS['config']['base_url']; ?>assets/css/child_.css">
    <link rel="stylesheet" type="text/css" href="/assets/vendor/plugins/slick/slick.css" />
    <link rel="stylesheet" type="text/css" href="/assets/vendor/plugins/slick/slick-theme.css" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@200..800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />

</head>
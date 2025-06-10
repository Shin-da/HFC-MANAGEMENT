<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo Page::getTitle(); ?></title>

    <!-- FAVICON -->
    <link rel="icon" href="../resources/images/henrichlogo.png">

    <!-- FONTS -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat&display=swap" rel="stylesheet">

    <!-- ICONS -->
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>

    <!-- Core CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">

    <!-- Base styles -->
    <link rel="stylesheet" type="text/css"  href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/sidebar.css">
    <link rel="stylesheet" href="../assets/css/navbar.css">
    <link rel="stylesheet" type="text/css" href="../assets/css/dashboard.css">
    <link rel="stylesheet" type="text/css" href="../assets/css/shared-dashboard.css">
    <link rel="stylesheet" type="text/css" href="../assets/css/admin.css">
    <link rel="stylesheet" type="text/css" href="../assets/css/admin-dashboard.css">
    <link rel="stylesheet" type="text/css" href="../assets/css/admin-layout.css">
    <link rel="stylesheet" type="text/css" href="../assets/css/calendar.css">
    <link rel="stylesheet" type="text/css" href="../assets/css/customer-pages.css">
    <link rel="stylesheet" type="text/css" href="../assets/css/form.css">

    <!-- Include additional styles -->
    <?php foreach (Page::getStyles() as $style): ?>
        <link rel="stylesheet" href="<?php echo htmlspecialchars($style); ?>">
    <?php endforeach; ?>

    <!-- JAVASCRIPTS -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.7.1/dist/chart.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <!-- Custom Scripts -->
    <script src="../assets/js/script.js"></script>
    <script src="../assets/js/datetime.js"></script>
    <script src="../assets/js/weather.js"></script>
    <script src="../assets/js/product_rank.js"></script>
    <script src="../assets/js/holidays.js"></script>
    <script src="../assets/js/search.js"></script>

    <?php
    // Fix the path resolution
    $alertsPath = __DIR__ . '/alerts.php';
    require_once $alertsPath;
    ?>
</head>
<body class="<?php echo Page::getBodyClass(); ?>">
    <?php 
    include dirname(__DIR__) . '/includes/sidebar.php';
    include dirname(__DIR__) . '/includes/navbar.php';
    ?>
    <section class="home-section">
        <div class="home-content">
            <div class="content-wrapper">
                <!-- Content will be placed here -->
</body>
</html>

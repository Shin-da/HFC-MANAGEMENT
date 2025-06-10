<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo Page::getTitle(); ?></title>
    
    <!-- Core Styles -->
    <link rel="stylesheet" href="/assets/css/variables.css">
    <link rel="stylesheet" href="/assets/css/style.css">
    
    <!-- Component Styles -->
    <link rel="stylesheet" href="/assets/css/sidebar.css">
    <link rel="stylesheet" href="/assets/css/navbar.css">
    
    <!-- BoxIcons -->
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    
    <!-- Page Specific Styles -->
    <?php foreach (Page::getStyles() as $style): ?>
        <link rel="stylesheet" href="<?php echo htmlspecialchars($style); ?>">
    <?php endforeach; ?>
</head>
<body class="<?php echo Page::getBodyClass(); ?>">
    <?php 
    include dirname(__DIR__) . '/includes/sidebar.php';
    include dirname(__DIR__) . '/includes/navbar.php';
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

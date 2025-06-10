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

    <!-- Core Styles - Load in correct order -->
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/variables.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/theme.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/main.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/style.css">

    <!-- Component Styles -->
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/sidebar.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/navbar.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/admin-navbar.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/dashboard.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/admin.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/admin-dashboard.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/admin-layout.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/calendar.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/customer-pages.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/form.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/table.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/sales.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/nav-sidebar.css">

    <!-- Third Party CSS -->
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">

    <!-- Page Specific Styles -->
    <?php foreach (Page::getStyles() as $style): ?>
        <link rel="stylesheet" href="<?php echo htmlspecialchars($style); ?>">
    <?php endforeach; ?>

    <!-- Third Party JavaScript -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script>
        // Ensure jQuery is loaded
        if (typeof jQuery === 'undefined') {
            console.error('jQuery not loaded!');
        }
    </script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- Load core scripts AFTER third-party scripts -->
    <?php foreach (Page::getScripts() as $script): ?>
        <script src="<?php echo htmlspecialchars($script); ?>" defer></script>
    <?php endforeach; ?>

    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script>
        // Move Chart.js configuration here
        if (typeof Chart !== 'undefined') {
            Chart.defaults.font.family = "'Montserrat', sans-serif";
            Chart.defaults.responsive = true;
            Chart.defaults.maintainAspectRatio = false;
            Chart.defaults.plugins.tooltip.enabled = true;
            Chart.defaults.plugins.legend.display = true;
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <!-- Core JavaScript -->
    <script src="../assets/js/script.js"></script>
    <script src="../assets/js/datetime.js"></script>
    <script src="../assets/js/weather.js"></script>
    <script src="../assets/js/product_rank.js"></script>
    <script src="../assets/js/holidays.js"></script>
    <script src="../assets/js/search.js"></script>
    <script src="../assets/js/notifications.js"></script>
    <script src="../assets/js/calendar.js"></script>
    <script src="../assets/js/customer-pages.js"></script>
    <script src="../assets/js/form.js"></script>
    <script src="../assets/js/table.js"></script>
    <script src="../assets/js/sales.js"></script>
    <script src="../assets/css/customer.css"></script>

    <!-- Layout Management -->
    <script src="<?php echo BASE_URL; ?>assets/js/layout-init.js"></script>
    <script src="<?php echo BASE_URL; ?>assets/js/layout-manager.js"></script>

    <!-- Theme script -->
    <script src="/assets/js/theme.js" defer></script>

    <?php
    // Fix the path resolution
    $alertsPath = __DIR__ . '/alerts.php';
    require_once $alertsPath;
    ?>

    <script src="../includes/js/alerts.js"></script>

    <!-- Theme Management -->
    <script>
        // Prevent FOUC (Flash of Unstyled Content)
        const theme = localStorage.getItem('theme') ||
            (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
        document.documentElement.setAttribute('data-theme', theme);
    </script>

    <!-- Theme Styles -->
    <link rel="stylesheet" href="/assets/css/themes.css">

    <!-- Theme Manager -->
    <script src="/assets/js/theme-manager.js" defer></script>
</head>

<body class="<?php echo Page::getBodyClass(); ?>">
    <!-- Only include the basic structure, no wrappers -->
</body>

</html>
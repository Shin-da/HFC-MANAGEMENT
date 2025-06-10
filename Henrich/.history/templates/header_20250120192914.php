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
    
    <!-- Core Styles -->
    <link rel="stylesheet" href="../assets/css/variables.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    
    <!-- Theme styles -->
    <link rel="stylesheet" href="/assets/css/variables.css">
    <link rel="stylesheet" href="/assets/css/theme-toggle.css">
    <link rel="stylesheet" href="/assets/css/theme.css">

    <!-- Component Styles -->
    <link rel="stylesheet" href="../assets/css/sidebar.css">
    <link rel="stylesheet" href="../assets/css/navbar.css">
    <link rel="stylesheet" href="../assets/css/admin-navbar.css">
    <link rel="stylesheet" href="../assets/css/dashboard.css">
    <link rel="stylesheet" href="../assets/css/shared-dashboard.css">
    <link rel="stylesheet" href="../assets/css/admin.css">
    <link rel="stylesheet" href="../assets/css/admin-dashboard.css">
    <link rel="stylesheet" href="../assets/css/admin-layout.css">
    <link rel="stylesheet" href="../assets/css/calendar.css">
    <link rel="stylesheet" href="../assets/css/customer-pages.css">
    <link rel="stylesheet" href="../assets/css/form.css">
    
    <!-- Third Party CSS -->
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
    
    <!-- Page Specific Styles -->
    <?php foreach (Page::getStyles() as $style): ?>
        <link rel="stylesheet" href="<?php echo htmlspecialchars($style); ?>">
    <?php endforeach; ?>

    <!-- Core JavaScript -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="../assets/js/script.js"></script>
    <script src="../assets/js/datetime.js"></script>
    <script src="../assets/js/weather.js"></script>
    <script src="../assets/js/product_rank.js"></script>
    <script src="../assets/js/holidays.js"></script>
    <script src="../assets/js/search.js"></script>
    
    <!-- Theme script -->
    <script src="/assets/js/theme.js" defer></script>

    <!-- Third Party JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.7.1/dist/chart.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

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

    <!-- Prevent FOUC (Flash of unstyled content) -->
    <script>
        // Apply saved theme immediately
        const savedTheme = localStorage.getItem('theme') || 
            (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
        document.documentElement.setAttribute('data-theme', savedTheme);
        document.documentElement.classList.add('theme-transition');
    </script>

    <!-- Theme Management -->
    <script>
        // Apply saved theme immediately to prevent flash
        const savedTheme = localStorage.getItem('theme') || 'light';
        document.body.classList.add(savedTheme);
    </script>
    
    <!-- Theme Management Script -->
    <script src="/assets/js/theme-toggle.js" defer></script>
</head>
<body class="<?php echo Page::getBodyClass(); ?>">
    <?php 
    include dirname(__DIR__) . '/includes/sidebar.php';
    include dirname(__DIR__) . '/includes/navbar.php';
    ?>
    <!-- Remove these nested wrappers and use a single content-wrapper -->
    <main class="content-wrapper" style="margin-top: 80px;">
        <!-- Content will be placed here -->
    </main>
</body>
</html>

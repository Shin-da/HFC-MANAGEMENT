<!DOCTYPE html>
<html lang="en" data-theme="light">

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
    <!-- variables.css defines the background color as --bg-primary: var(--neutral-50) (#f8fafc) -->
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

    <!-- Preload critical resources -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&display=swap">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <!-- Include fonts and icons first -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">

    <!-- Third Party JavaScript -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script>
        // Define BASE_URL as a global JavaScript variable
        const BASE_URL = '<?php echo BASE_URL; ?>';
        
        // Initialize system settings
        const systemSettings = {
            appName: '<?php echo isset($appSettings['app_name']) ? htmlspecialchars($appSettings['app_name']) : 'HFC Management System'; ?>',
            theme: '<?php echo isset($userSettings['theme']) ? htmlspecialchars($userSettings['theme']) : 'light'; ?>',
            dateFormat: '<?php echo isset($userSettings['date_format']) ? htmlspecialchars($userSettings['date_format']) : 'yyyy-mm-dd'; ?>',
            notificationsEnabled: <?php echo isset($userSettings['notifications_enabled']) && $userSettings['notifications_enabled'] ? 'true' : 'false'; ?>
        };
        
        // Prevent flash of unstyled content
        document.documentElement.setAttribute('data-theme', 
            localStorage.getItem('theme') || 
            (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light')
        );
    </script>

    <!-- Theme Styles -->
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/theme.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/theme-toggle.css">
</head>

<body class="<?php echo Page::getBodyClass(); ?>">
    <!-- Only include the basic structure, no wrappers -->
</body>

</html>
<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title ?? 'HFC Management'; ?></title>
    <?php require_once '../reusable/head.php'; ?>
    
    <!-- Add Boxicons -->
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    
    <!-- Theme styles - add these before your other CSS files -->
    <link rel="stylesheet" href="/assets/css/variables.css">
    <link rel="stylesheet" href="/assets/css/theme-toggle.css">
</head>
<body class="<?php echo $body_class ?? ''; ?>">
    <!-- Sidebar -->
    <?php include 'sidebar.php'; ?>

    <!-- Main Content Wrapper -->
    <div class="content-wrapper">
        <!-- Top Navbar -->
        <?php include 'navbar.php'; ?>

        <!-- Main Content -->
        <main class="main-content">
            <div class="page-container">
                <?php if (isset($content)) echo $content; ?>
            </div>
            <?php include 'footer.php'; ?>
        </main>
    </div>

    <!-- Core Scripts -->
    <script src="../assets/js/sidebar.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize sidebar
            initSidebar();
            
            // Initialize notifications
            initNotifications();
        });
    </script>
    
    <!-- Add theme script before closing body tag -->
    <script src="/assets/js/theme-toggle.js"></script>
</body>
</html>

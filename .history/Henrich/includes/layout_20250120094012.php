<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title ?? 'HFC Management'; ?></title>
    <?php require_once '../reusable/head.php'; ?>
</head>
<body class="<?php echo $body_class ?? ''; ?>">
    <!-- Sidebar -->
    <?php include '../includes/sidebar.php'; ?>

    <!-- Main Content Wrapper -->
    <div class="content-wrapper">
        <!-- Top Navbar -->
        <?php include '../includes/navbar.php'; ?>

        <!-- Main Content -->
        <main class="main-content">
            <div class="page-container">
                <?php if (isset($content)) echo $content; ?>
            </div>
            <?php include '../includes/footer.php'; ?>
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
</body>
</html>

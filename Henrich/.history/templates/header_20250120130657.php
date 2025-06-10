<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo Page::getTitle(); ?></title>
    
    <!-- Include BoxIcons -->
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    
    <!-- Include Animate.css -->
    
    <!-- Include additional styles -->
    <?php foreach (Page::getStyles() as $style): ?>
        <link rel="stylesheet" href="<?php echo $style; ?>">
    <?php endforeach; ?>
    
    <!-- Include BoxIcons -->
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>
<body class="<?php echo Page::getBodyClass(); ?>">
    <?php 
    include dirname(__DIR__) . '/includes/sidebar.php';
    include dirname(__DIR__) . '/includes/navbar.php';  // Changed from topbar.php to navbar.php
    ?>
    <section class="home-section">
        <div class="home-content">
            <div class="content-wrapper">
                <!-- Content will be placed here -->
</body>
</html>

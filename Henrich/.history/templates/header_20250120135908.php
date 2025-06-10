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
    <link rel="stylesheet" href="../assets/css/sidebar.css">
    <link rel="stylesheet" href="../assets/css/navbar.css">
    
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
    ?>
    <section class="home-section">
        <div class="home-content">
            <div class="content-wrapper">
                <!-- Content will be placed here -->
</body>
</html>

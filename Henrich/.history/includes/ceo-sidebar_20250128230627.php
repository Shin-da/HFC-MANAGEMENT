<?php
$current_page = basename($_SERVER['PHP_SELF'], '.php');
?>

<nav class="sidebar">
    <header>
        <div class="image-text">
            <span class="image">
                <img src="../resources/images/hfclogo.png" alt="logo">
            </span>
            <div class="header-text">
                <span class="name">Executive Panel</span>
            </div>
        </div>
    </header>

    <div class="menu-bar">
        <!-- Session Info -->
        <div class="session">
            <i class="bx bx-crown icon"></i>
            <span class="text">CEO DASHBOARD</span>
        </div>

        <!-- Menu Links -->
        <ul class="menu-links">
            <!-- Dashboard -->
            <li class="nav-link <?php echo $current_page === 'index' ? 'active' : ''; ?>">
                <a href="index.php">
                    <i class="bx bx-grid-alt"></i>
                    <span class="text">Executive Dashboard</span>
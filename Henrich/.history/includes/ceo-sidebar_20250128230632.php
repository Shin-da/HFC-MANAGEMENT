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
                </a>
            </li>

            <li class="break">
                <p>Analytics</p>
            </li>

            <!-- Business Intelligence -->
            <li class="nav-link <?php echo $current_page === 'intelligence' ? 'active' : ''; ?>">
                <a href="intelligence.php">
                    <i class="bx bx-line-chart"></i>
                    <span class="text">Business Intelligence</span>
                </a>
            </li>

            <!-- Financial Overview -->
            <li class="nav-link <?php echo $current_page === 'financial' ? 'active' : ''; ?>">
                <a href="financial.php">
                    <i class="bx bx-money"></i>
                    <span class="text">Financial Overview</span>
                </a>
            </li>

            <li class="break">
                <p>Operations</p>
            </li>

            <!-- Supply Chain -->
            <li class="nav-link <?php echo $current_page === 'supply-chain' ? 'active' : ''; ?>">
                <a href="supply-chain.php">
                    <i class="bx bx-package"></i>
                    <span class="text">Supply Chain</span>
                </a>
            </li>

            <!-- HR Analytics -->
            <li class="nav-link <?php echo $current_page === 'hr' ? 'active' : ''; ?>">
                <a href="hr.php">
                    <i class="bx bx-group"></i>
                    <span class="text">HR Analytics</span>
                </a>
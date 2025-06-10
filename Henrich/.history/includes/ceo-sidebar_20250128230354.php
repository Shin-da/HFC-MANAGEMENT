<?php
$current_page = basename($_SERVER['PHP_SELF'], '.php');
?>

<nav class="sidebar">
    <header>
        <div class="image-text">
            <span class="image">
                <img src="../resources/images/hfclogo.png" alt="HFC Logo">
            </span>
            <div class="header-text">
                <span class="name">Executive Panel</span>
                <span class="role">CEO Dashboard</span>
            </div>
        </div>
    </header>

    <div class="menu-bar">
        <div class="menu">
            <ul class="menu-links">
                <!-- Dashboard -->
                <li class="nav-link <?php echo $current_page === 'index' ? 'active' : ''; ?>">
                    <a href="index.php">
                        <i class="bx bxs-dashboard icon"></i>
                        <span class="text">Dashboard</span>
                    </a>
                </li>

                <li class="break">
                    <p>Analytics</p>
                </li>

                <!-- Business Intelligence -->
                <li class="nav-link <?php echo $current_page === 'intelligence' ? 'active' : ''; ?>">
                    <a href="intelligence.php">
                        <i class="bx bx-line-chart icon"></i>
                        <span class="text">Business Intelligence</span>
                    </a>
                </li>

                <!-- Financial Analytics -->
                <li class="nav-link <?php echo $current_page === 'financial' ? 'active' : ''; ?>">
                    <a href="financial.php">
                        <i class="bx bx-money icon"></i>
                    <span class="text">Executive Reports</span>
                </a>
            </li>

            <li class="break">
                <p>Management</p>
            </li>

            <li class="nav-link <?php echo $current_page === 'branches' ? 'active' : ''; ?>">
                <a href="branches.php">
                    <i class="bx bx-building"></i>
                    <span class="text">Branch Management</span>
                </a>
            </li>

            <li class="nav-link <?php echo $current_page === 'employees' ? 'active' : ''; ?>">
                <a href="employees.php">
                    <i class="bx bx-group"></i>
                    <span class="text">Employee Overview</span>
                </a>
            </li>

            <li class="nav-link <?php echo $current_page === 'settings' ? 'active' : ''; ?>">
                <a href="settings.php">
                    <i class="bx bx-cog"></i>
                    <span class="text">Company Settings</span>
                </a>
            </li>
        </ul>
    </div>
</nav>

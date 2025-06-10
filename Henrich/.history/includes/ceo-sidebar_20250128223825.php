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
        <div class="session">
            <i class="bx bx-crown icon"></i>
            <span class="text">CEO DASHBOARD</span>
        </div>

        <ul class="menu-links">
            <li class="nav-link <?php echo $current_page === 'index' ? 'active' : ''; ?>">
                <a href="index.php">
                    <i class="bx bx-grid-alt"></i>
                    <span class="text">Executive Dashboard</span>
                </a>
            </li>

            <li class="break">
                <p>Performance</p>
            </li>

            <li class="nav-link <?php echo $current_page === 'financial' ? 'active' : ''; ?>">
                <a href="financial.php">
                    <i class="bx bx-money"></i>
                    <span class="text">Financial Overview</span>
                </a>
            </li>

            <li class="nav-link <?php echo $current_page === 'reports' ? 'active' : ''; ?>">
                <a href="reports.php">
                    <i class="bx bx-file"></i>
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

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
                        <span class="text">Financial Analytics</span>
                    </a>
                </li>

                <li class="break">
                    <p>Operations</p>
                </li>

                <!-- Supply Chain -->
                <li class="nav-link <?php echo $current_page === 'supply-chain' ? 'active' : ''; ?>">
                    <a href="supply-chain.php">
                        <i class="bx bx-network-chart icon"></i>
                        <span class="text">Supply Chain</span>
                    </a>
                </li>

                <!-- Quality Control -->
                <li class="nav-link <?php echo $current_page === 'quality' ? 'active' : ''; ?>">
                    <a href="quality.php">
                        <i class="bx bx-check-circle icon"></i>
                        <span class="text">Quality Control</span>
                    </a>
                </li>

                <li class="break">
                    <p>Management</p>
                </li>

                <!-- HR Analytics -->
                <li class="nav-link <?php echo $current_page === 'hr' ? 'active' : ''; ?>">
                    <a href="hr.php">
                        <i class="bx bx-group icon"></i>
                        <span class="text">HR Analytics</span>
                    </a>
                </li>

                <!-- Compliance -->
                <li class="nav-link <?php echo $current_page === 'compliance' ? 'active' : ''; ?>">
                    <a href="compliance.php">
                        <i class="bx bx-shield-quarter icon"></i>
                        <span class="text">Compliance & Risk</span>
                    </a>
                </li>

                <!-- Reports -->
                <li class="nav-link <?php echo $current_page === 'reports' ? 'active' : ''; ?>">
                    <a href="reports.php">
                        <i class="bx bx-file icon"></i>
                        <span class="text">Executive Reports</span>
                    </a>
                </li>

                <!-- Settings -->
                <li class="nav-link <?php echo $current_page === 'settings' ? 'active' : ''; ?>">
                    <a href="settings.php">
                        <i class="bx bx-cog icon"></i>
                        <span class="text">Settings</span>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/components/ceo-sidebar.css">

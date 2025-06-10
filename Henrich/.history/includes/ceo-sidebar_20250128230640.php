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
            </li>

            <!-- Quality Control -->
            <li class="nav-link <?php echo $current_page === 'quality' ? 'active' : ''; ?>">
                <a href="quality.php">
                    <i class="bx bx-check-shield"></i>
                    <span class="text">Quality Control</span>
                </a>
            </li>

            <li class="break">
                <p>Management</p>
            </li>

            <!-- Reports -->
            <li class="nav-link <?php echo $current_page === 'reports' ? 'active' : ''; ?>">
                <a href="reports.php">
                    <i class="bx bx-file"></i>
                    <span class="text">Executive Reports</span>
                </a>
            </li>

            <!-- Compliance -->
            <li class="nav-link <?php echo $current_page === 'compliance' ? 'active' : ''; ?>">
                <a href="compliance.php">
                    <i class="bx bx-certification"></i>
                    <span class="text">Compliance & Risk</span>
                </a>
            </li>

            <!-- Settings -->
            <li class="nav-link <?php echo $current_page === 'settings' ? 'active' : ''; ?>">
                <a href="settings.php">
                    <i class="bx bx-cog"></i>
                    <span class="text">Company Settings</span>
                </a>
            </li>
        </ul>
    </div>
</nav>

<style>
.sidebar .break {
    margin-top: 20px;
    padding: 0 15px;
}

.sidebar .break p {
    color: var(--text-secondary);
    font-size: 0.8rem;
    text-transform: uppercase;
    letter-spacing: 1px;
    margin-bottom: 10px;
}

.sidebar .nav-link i {
    min-width: 40px;
    font-size: 1.2rem;
}

.sidebar .session {
    padding: 15px;
    color: var(--primary);
    font-weight: bold;
    display: flex;
    align-items: center;
    gap: 10px;
}

.sidebar .session .icon {
    font-size: 1.4rem;
}
</style>

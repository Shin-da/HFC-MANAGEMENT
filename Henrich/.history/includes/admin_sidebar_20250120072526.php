<nav class="sidebar" id="sidebar">
    <header>
        <div class="logo-details">
            <img src="../resources/images/hfclogo.png" alt="HFC Logo">
            <span>HFC Management</span>
        </div>
    </header>

    <div class="menu-wrapper">
        <ul class="nav-links">
            <li class="<?php echo ($current_page == 'dashboard') ? 'active' : ''; ?>">
                <a href="../admin/index.php">
                    <i class="bx bx-grid-alt"></i>
                    <span>Dashboard</span>
                </a>
            </li>

            <li class="break">
                <p class="disabled-link">Account Management</p>
            </li>
            
            <li class="<?php echo ($current_page == 'supervisors') ? 'active' : ''; ?>">
                <a href="../admin/manage-supervisors.php">
                    <i class="bx bx-user-pin"></i>
                    <span>Supervisors</span>
                </a>
            </li>

            <li class="<?php echo ($current_page == 'account-requests') ? 'active' : ''; ?>">
                <a href="../admin/manage-account-requests.php">
                    <i class="bx bx-user-plus"></i>
                    <span>Account Requests</span>
                    <?php if(isset($pending_requests) && $pending_requests > 0): ?>
                    <span class="badge"><?= $pending_requests ?></span>
                    <?php endif; ?>
                </a>
            </li>

            <li class="break">
                <p class="disabled-link">System</p>
            </li>

            <li class="<?php echo ($current_page == 'settings') ? 'active' : ''; ?>">
                <a href="../admin/system-settings.php">
                    <i class="bx bx-cog"></i>
                    <span>Settings</span>
                </a>
            </li>

            <li class="<?php echo ($current_page == 'logs') ? 'active' : ''; ?>">
                <a href="../admin/activity-logs.php">
                    <i class="bx bx-history"></i>
                    <span>Activity Logs</span>
                </a>
            </li>
        </ul>
    </div>

    <div class="user-info">
        <img src="../resources/images/default-avatar.png" alt="Admin Avatar">
        <div>
            <span class="user-name"><?php echo $_SESSION['username']; ?></span>
            <span class="user-role">Administrator</span>
        </div>
        <a href="../login/logout.php" class="logout-btn">
            <i class="bx bx-log-out"></i>
        </a>
    </div>
</nav>

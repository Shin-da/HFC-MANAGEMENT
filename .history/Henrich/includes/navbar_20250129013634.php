<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/session.php';


error_log("Navbar loaded - User ID: " . ($_SESSION['user_id'] ?? 'not set'));
?>

<nav class="navbar">
    <div class="nav-left">

    <button id="sidebar-toggle" class="nav-button" type="button" aria-label="Toggle Sidebar">
            <i class="bx bx-menu"></i>
        </button>
    </div>

    <div class="nav-right">
        <ul class="nav-actions">
            <li class="nav-item notification-dropdown">
                <?php require_once __DIR__ . '/notification_dropdown.php'; ?>
            </li>
            <li class="nav-item message-dropdown">
                <button class="nav-button message-btn" aria-label="Messages">
                    <i class="bx bx-message"></i>
                    <span class="message-badge" style="display: none;">0</span>
                </button>
                <div class="chat-preview-panel">
                    <div class="panel-header">
                        <h4>Messages</h4>
                        <button class="open-chat-btn">Open Chat</button>
                    </div>
                    <div class="recent-chats">
                        <!-- Chat previews will be loaded here -->
                    </div>
                </div>
            </li>
            <li class="nav-divider"></li>
            <li class="nav-item user-menu">
                <button class="nav-button user-btn" aria-label="User menu">
                    <div class="user-avatar">
                        <i class="bx bxs-user"></i>
                    </div>
                    <div class="user-info">
                        <span class="user-name"><?php echo $_SESSION['username']; ?></span>
                        <span class="user-role"><?php echo $_SESSION['role']; ?></span>
                    </div>
                    <i class="bx bx-chevron-down"></i>
                </button>
                <div class="dropdown-panel">
                    <div class="dropdown-header">
                        <h4>My Account</h4>
                    </div>
                    <ul class="menu-links">
                        <?php if ($_SESSION['role'] === 'ceo'): ?>
                            <li><a href="<?php echo BASE_URL; ?>ceo/reports.php"><i class="bx bx-file"></i>Executive Reports</a></li>
                            <li><a href="<?php echo BASE_URL; ?>ceo/settings.php"><i class="bx bx-cog"></i>Company Settings</a></li>
                        <?php endif; ?>
                        <li><a href="myaccount.php"><i class="bx bx-user-circle"></i>Profile</a></li>
                        <li><a href="Forms/changePasswordForm.php"><i class="bx bx-lock-alt"></i>Change Password</a></li>
                        <li class="theme-toggle-item">
                            <button id="themeToggle">
                                <i class="bx bx-moon"></i>
                                <span>Dark Mode</span>
                            </button>
                        </li>
                        <li><a href="../login/logout.php" class="logout-link">
                            <i class="bx bx-log-out"></i>Logout
                        </a></li>
                    </ul>
                </div>
            </li>
        </ul>
    </div>
    <script src="<?php echo BASE_URL; ?>assets/js/notification-handler.js"></script>
</nav>

<link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/components/navbar.css">
<link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/components/notification-dropdown.css">
<link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/components/message-dropdown.css">

<!-- Add this line -->
<script src="<?php echo BASE_URL; ?>assets/js/notification-dropdown.js"></script>

<!-- Remove or comment out the existing notification script -->
<script>
    // Only keep the non-notification related code
    document.querySelectorAll('.nav-button:not(.notification-btn)').forEach(button => {
        button.addEventListener('click', (e) => {
            e.stopPropagation();
            const item = button.closest('.nav-item');
            const isActive = item.classList.contains('active');

            // Close other dropdowns
            document.querySelectorAll('.nav-item.active').forEach(activeItem => {
                if (activeItem !== item) {
                    activeItem.classList.remove('active');
                }
            });

            // Toggle current dropdown
            item.classList.toggle('active', !isActive);
        });
    });

    // Close dropdowns when clicking outside
    document.addEventListener('click', () => {
        document.querySelectorAll('.nav-item.active').forEach(item => {
            item.classList.remove('active');
        });
    });

    // Prevent dropdown from closing when clicking inside
    document.querySelectorAll('.dropdown-panel').forEach(panel => {
        panel.addEventListener('click', (e) => e.stopPropagation());
    });
</script>
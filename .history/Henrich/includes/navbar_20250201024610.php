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

<style>
    /* Navbar Layout */
.navbar {
    position: fixed; 
    top: 0;
    right: 0;
    left: 260px;
    height: 64px;
    background: var(--surface);
    backdrop-filter: blur(10px);
    border-bottom: 1px solid var(--border);
    /* padding: 0.75rem 1.5rem; */
    z-index: 1000;
    display: flex;
    align-items: center;
    justify-content: space-between;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.04);
    transition: left 0.3s ease;
}

/* Navigation Components */
.nav-left, .nav-right {
    display: flex;
    align-items: center;
    gap: 1rem;
    margin: 0 1rem;
}

/* Toggle Button */
.toggle-sidebar {
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    border: none;
    background: transparent;
    color: var(--text-primary);
    cursor: pointer;
    border-radius: 8px;
    transition: all 0.2s ease;
}

.toggle-sidebar:hover {
    background: var(--sidebar-hover);
    color: var(--primary);
}

/* Actions */
.nav-actions {
    display: flex;
    align-items: center;
    gap: 1rem;
    list-style: none;
    padding: 0;
    margin: 0;
}

/* States */

/* Expanded Navbar */
.navbar.expanded {
    left: 260px;
}

.sidebar-collapsed ~ .navbar,
.page-wrapper.collapsed ~ .navbar {
    left: 70px;
}

.sidebar-hidden ~ .navbar,
.page-wrapper.expanded ~ .navbar {
    left: 0;
}

/* Navigation Items */
.nav-item {
    position: relative;
}

/* Nav buttons with better hover effects */
.nav-button {
    position: relative;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.625rem;
    background: transparent;
    border: none;
    color: var(--text-secondary);
    font-size: 1.25rem;
    cursor: pointer;
    border-radius: 0.75rem;
    transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
}

.nav-button:hover {
    color: var(--primary);
    background: var(--hover);
    transform: translateY(-1px);
}

.nav-button:active {
    transform: translateY(0);
}

/* Improved user button */
.nav-button.user-btn {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.5rem;
    padding-right: 1rem;
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: 2rem;
    font-size: 0.875rem;
    color: var(--text-primary);
}

.nav-button.user-btn:hover {
    border-color: var(--primary);
    background: var(--hover);
}

.nav-button.user-btn .bx-chevron-down {
    transition: transform 0.2s ease;
}

.nav-item.active .nav-button.user-btn .bx-chevron-down {
    transform: rotate(-180deg);
}

/* User Menu */
.user-menu .nav-button {
    padding: 0.5rem 1rem;
    font-size: 0.875rem;
    background: var(--cream-light);
    color: var(--sand);
    border-radius: 20px;
    transition: background 0.2s ease;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    justify-content: center;
}

.user-menu .nav-button:hover {
    background: var(--primary-dark);
}

/* Dropdown Panel */
.dropdown-panel {
    position: absolute;
    top: 100%;
    right: 0;
    min-width: 280px;
    background: var(--surface);
    border: 1px solid var(--tab-active-border);
    border-radius: 1rem;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
    opacity: 0;
    visibility: hidden;
    transform: translateY(10px);
    transition: all 0.2s ease;
    margin-top: 0.75rem;
    overflow: hidden;
}

.nav-item.active .dropdown-panel {
    opacity: 1;
    visibility: visible;
    transform: translateY(0);
}

/* Notification Dropdown */
.notification-dropdown {
    position: relative;
    z-index: 1001;
}

.notification-dropdown-content {
    position: absolute;
    top: 100%;
    right: 0;
    min-width: 320px;
    max-width: 400px;
    max-height: 400px;
    margin-top: 0.5rem;
    background: var(--surface, white);
    border: 1px solid var( --tab-active-border);
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    z-index: 1000;
    display: none;
    overflow-y: auto;
}

.notification-dropdown.active .notification-dropdown-content {
    display: block;
}

.notifications-list {
    max-height: 300px;
    overflow-y: auto;
}

.notification-item {
    padding: 1rem;
    border-bottom: 1px solid var(--border);
    background: var(--surface, white);
    cursor: pointer;
    transition: background-color 0.2s ease;
}

.notification-item:hover {
    background-color: var(--hover, #f5f5f5);
}

.notification-item:last-child {
    border-bottom: none;
}

.notification-content {
    p {
        margin: 0;
        color: var(--text-primary);
    }

    small {
        display: block;
        color: var(--text-secondary);
        margin-top: 0.25rem;
    }
}

.dropdown-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1rem;
    border-bottom: 1px solid var(--border);
}

/* Menu items with better hover states */
.menu-links {
    list-style: none;
    padding: 0;
    margin: 0;
}

.menu-links li {
    margin: 0;
}

.menu-links li a {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.875rem 1rem;
    color: var(--text-primary);
    transition: all 0.2s ease;
}

.menu-links li a:hover {
    background: var(--hover);
    color: var(--primary);
    padding-left: 1.25rem;
}

.menu-links li a i {
    font-size: 1.25rem;
    color: var(--text-secondary);
    transition: color 0.2s ease;
}

.menu-links li a:hover i {
    color: var(--primary);
}

/* Badge enhancements */
.badge {
    position: absolute;
    top: -2px;
    right: -2px;
    min-width: 20px;
    height: 20px;
    padding: 0 6px;
    background: var(--accent);
    color: white;
    font-size: 0.75rem;
    font-weight: 600;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    border: 2px solid var(--surface);
}

/* Divider styling */
.nav-divider {
    width: 1px;
    height: 24px;
    background: var(--border);
    margin: 0 0.75rem;
}

/* Responsive Behavior */
@media (max-width: 1200px) {
    .navbar {
        left: 70px;
    }
}

@media (max-width: 768px) {
    .navbar {
        left: 0;
        padding: 0 1rem;
        width: 100%;
    }
    
    .user-menu .nav-button .user-name {
        display: none;
    }
}

</style>

<link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/components/navbar.css">
<link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/components/notification-dropdown.css">
<link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/components/message-dropdown.css">

<!-- Add this line -->
<script src="<?php echo BASE_URL; ?>assets/js/notification-dropdown.js"></script>

<!-- Remove or comment out the existing notification script -->
<script>
   document.addEventListener('DOMContentLoaded', function() {
    // Navbar toggle functionality
    const sidebarToggle = document.getElementById('sidebar-toggle');
    const navbar = document.querySelector('.navbar');

    if (sidebarToggle) {
        sidebarToggle.addEventListener('click', function(e) {
            e.stopPropagation();
            document.body.classList.toggle('sidebar-collapsed');
        });
    }

    // Dropdown handling
    const dropdownButtons = document.querySelectorAll('.nav-button');
    
    dropdownButtons.forEach(button => {
        button.addEventListener('click', (e) => {
            e.stopPropagation();
            const navItem = button.closest('.nav-item');
            const wasActive = navItem.classList.contains('active');

            // Close all other dropdowns
            document.querySelectorAll('.nav-item.active').forEach(item => {
                if (item !== navItem) {
                    item.classList.remove('active');
                }
            });

            // Toggle current dropdown
            navItem.classList.toggle('active', !wasActive);
        });
    });

    // Close dropdowns when clicking outside
    document.addEventListener('click', () => {
        document.querySelectorAll('.nav-item.active').forEach(item => {
            item.classList.remove('active');
        });
    });

    // Prevent dropdown from closing when clicking inside
    document.querySelectorAll('.dropdown-panel, .notification-dropdown-content')
        .forEach(panel => {
            panel.addEventListener('click', (e) => e.stopPropagation());
        });

    // Theme toggle functionality
    const themeToggle = document.getElementById('themeToggle');
    if (themeToggle) {
        themeToggle.addEventListener('click', function() {
            document.body.classList.toggle('dark-mode');
            const isDarkMode = document.body.classList.contains('dark-mode');
            localStorage.setItem('darkMode', isDarkMode);
        });
    }
});

</script>
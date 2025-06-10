<?php
// Include database connection
require_once __DIR__ . '/config.php';

// Rest of navbar code
?>
<nav class="navbar">
    <div class="navbar-content">
        <div class="logo-image">
            <img src="/assets/images/henrichlogo.png" alt="Henrich Logo">
        </div>
        
        <div class="search-box">
            <i class='bx bx-search icon'></i>
            <input type="text" placeholder="Search...">
        </div>
        
        <?php
        // Use the database connection
        $stmt = $conn->prepare("SELECT * FROM notifications WHERE user_id = ? AND is_read = 0 ORDER BY created_at DESC LIMIT 5");
                $stmt->bind_param("i", $_SESSION['user_id']);
                $stmt->execute();
                $notifications = $stmt->get_result();
                $notif_count = $notifications->num_rows;
            } catch (Exception $e) {
                error_log("Notification query error: " . $e->getMessage());
                $notifications = false;
                $notif_count = 0;
            }
            ?>
            <!-- Replace the existing notification button with this -->
            <li class="nav-item notification-dropdown">
                <button class="nav-button notification-btn" type="button">
                    <i class='bx bx-bell'></i>
                    <?php if ($notif_count > 0): ?>
                        <span class="badge"><?php echo $notif_count ?></span>
                    <?php endif; ?>
                </button>
                <div class="dropdown-content">
                    <div class="dropdown-header">
                        <h4>Notifications</h4>
                        <a href="notifications.php">View All</a>
                    </div>
                    <div class="dropdown-body">
                        <?php if ($notifications && $notifications->num_rows > 0): ?>
                            <?php while ($notif = $notifications->fetch_assoc()): ?>
                                <div class="notification-item unread" data-id="<?= $notif['id'] ?>">
                                    <div class="notification-content">
                                        <p><?= htmlspecialchars($notif['message']) ?></p>
                                        <small><?= date('M d, H:i', strtotime($notif['created_at'])) ?></small>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <div class="notification-item empty">
                                <p>No new notifications</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </li>

            <!-- Messages -->
            <li class="nav-item">
                <button class="nav-button message-btn" aria-label="Messages">
                    <i class="bx bxs-message-rounded"></i>
                    <span class="badge" id="message-badge">2</span>
                </button>
                <div class="dropdown-panel" id="message-dropdown">
                    <div class="dropdown-header">
                        <h4>Messages</h4>
                    </div>
                    <div class="dropdown-body" id="messages-list">
                        <!-- Messages go here -->
                    </div>
                    <div class="dropdown-footer">
                        <a href="messages.php">View all messages</a>
                    </div>
                </div>
            </li>

            <!-- User Settings -->
            <li class="nav-item user-menu">
                <button class="nav-button user-btn" aria-label="User menu">
                    <i class="bx bxs-user-circle"></i>
                    <span class="user-name"><?php echo $_SESSION['username']; ?></span>
                </button>
                <div class="dropdown-panel" id="user-dropdown">
                    <div class="user-info">
                        <div class="user-avatar">
                            <i class="bx bxs-user"></i>
                        </div>
                        <div class="user-details">
                            <p class="name"><?php echo $_SESSION['username']; ?></p>
                            <p class="role"><?php echo $_SESSION['role']; ?></p>
                        </div>
                    </div>
                    <ul class="menu-links">
                        <li><a href="myaccount.php"><i class="bx bx-user"></i>My Account</a></li>
                        <li><a href="Forms/changePasswordForm.php"><i class="bx bx-key"></i>Change Password</a></li>
                        <li><a href="../login/logout.php"><i class="bx bx-log-out"></i>Logout</a></li>
                    </ul>
                </div>
            </li>
        </ul>
    </div>
</nav>

<style>
    /* Navbar Layout */
    .top {
        display: flex;
        justify-content: space-between;
        align-items: center;
        height: 60px;
        padding: 0 1.5rem;
        background: var(--sand);
        border-bottom: 1px solid var(--border);
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.04);
    }

    /* Nav Actions */
    .nav-actions {
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .nav-item {
        position: relative;
        z-index: 1000;
    }

    /* Buttons */
    .nav-button {
        position: relative;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.5rem;
        background: transparent;
        border: none;
        color: var(--dark);
        font-size: 1.25rem;
        cursor: pointer;
        border-radius: 0.375rem;
        transition: all 0.2s ease;

        &:hover {
            background: var(--sidebar-hover);
            color: var(--primary);
            transform: translateY(-1px);
        }

        &:active {
            transform: translateY(0);
        }

        .badge {
            position: absolute;
            top: 0;
            right: 0;
            min-width: 18px;
            height: 18px;
            padding: 0 5px;
            background: var(--accent);
            color: white;
            font-size: 0.75rem;
            font-weight: 600;
            border-radius: 9px;
            display: flex;
            align-items: center;
            justify-content: center;
            transform: translate(25%, -25%);
        }
    }

    /* Badges */
    .badge {
        position: absolute;
        top: -2px;
        right: -2px;
        padding: 0.25rem 0.5rem;
        font-size: 0.75rem;
        background: var(--accent);
        color: white;
        border-radius: 1rem;
    }

    /* Dropdowns */
    .dropdown-panel {
        position: absolute;
        top: 100%;
        right: 0;
        min-width: 280px;
        margin-top: 0.5rem;
        background: var(--sand);
        border: 1px solid var(--border);
        border-radius: 0.5rem;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1),
            0 2px 4px -1px rgba(0, 0, 0, 0.06);
        opacity: 0;
        visibility: hidden;
        transform: translateY(10px);
        transition: all 0.2s ease;
    }

    .nav-item.active .dropdown-panel {
        opacity: 1;
        visibility: visible;
        transform: translateY(0);
    }

    .dropdown-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 1rem;
        border-bottom: 1px solid var(--border);

        h4 {
            margin: 0;
            font-size: 1rem;
            font-weight: 600;
            color: var(--dark);
        }
    }

    .dropdown-body {
        max-height: 300px;
        overflow-y: auto;
        padding: 0.5rem 0;
    }

    .dropdown-footer {
        padding: 0.75rem 1rem;
        border-top: 1px solid var(--border);
        text-align: center;

        a {
            color: var(--primary);
            font-size: 0.875rem;
            text-decoration: none;

            &:hover {
                text-decoration: underline;
            }
        }
    }

    /* User Menu */
    .user-menu .nav-button {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .user-info {
        padding: 1rem;
        display: flex;
        align-items: center;
        gap: 1rem;
        border-bottom: 1px solid var(--border);
    }

    .user-avatar {
        width: 40px;
        height: 40px;
        background: var(--primary);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--sand);
    }

    .user-details {
        .name {
            font-weight: 500;
            color: var(--dark);
        }

        .role {
            font-size: 0.875rem;
            color: var(--secondary);
        }
    }

    /* Menu Links */
    .menu-links {
        padding: 0.5rem 0;

        li a {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.75rem 1rem;
            color: var(--dark);
            font-size: 0.875rem;
            transition: all 0.2s ease;

            &:hover {
                background: var(--sidebar-hover);
                color: var(--primary);

                i {
                    color: var(--primary);
                }
            }

            i {
                font-size: 1.25rem;
                color: var(--secondary);
            }
        }
    }

    /* Nav Right Adjustments */
    .nav-right {
        display: flex;
        align-items: center;
        margin-left: auto;
        padding-right: 1rem;
    }

    /* User Menu Button */
    .nav-button.user-btn {
        padding: 0.5rem 1rem;
        background: var(--primary);
        color: var (--sand);
        border-radius: 2rem;
        font-size: 0.875rem;

        i {
            font-size: 1.25rem;
        }

        .user-name {
            font-weight: 500;
            margin-left: 0.5rem;
            display: none;
        }

        @media (min-width: 768px) {
            .user-name {
                display: inline;
            }
        }

        &:hover {
            background: var(--secondary);
            color: var(--sand);
        }
    }

    /* Add these styles to fix notification visibility */
    .dropdown-menu {
        position: absolute;
        top: 100%;
        right: 0;
        min-width: 300px;
        background: white;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        opacity: 0;
        visibility: hidden;
        transform: translateY(10px);
        transition: all 0.2s ease;
    }

    .nav-item.active .dropdown-menu {
        opacity: 1;
        visibility: visible;
        transform: translateY(0);
    }

    .notifications-menu {
        margin-top: 0.5rem;
    }

    .notification-item {
        padding: 1rem;
        border-bottom: 1px solid var(--border);
        transition: background-color 0.2s ease;
    }

    .notification-item:hover {
        background-color: var(--sidebar-hover);
    }

    .notification-content p {
        margin: 0 0 0.25rem 0;
        font-size: 0.875rem;
    }

    .notification-content small {
        color: var(--text-secondary);
        font-size: 0.75rem;
    }

    .notification-item.empty {
        color: var(--text-secondary);
        text-align: center;
        padding: 1.5rem;
    }

    /* Ensure notifications stay on top */
    .nav-item {
        position: relative;
        z-index: 1000;
    }

    /* Badge positioning */
    .nav-button .badge {
        position: absolute;
        top: -5px;
        right: -5px;
    }

    /* Add these specific styles for notifications */
    .notification-dropdown {
        position: relative;
        z-index: 1001;
    }

    .notification-dropdown .dropdown-content {
        position: absolute;
        top: 100%;
        right: 0;
        min-width: 300px;
        background: white;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        opacity: 0;
        visibility: hidden;
        transform: translateY(10px);
        transition: all 0.2s ease;
    }

    .notification-dropdown.active .dropdown-content {
        opacity: 1;
        visibility: visible;
        transform: translateY(0);
    }

    /* Update existing dropdown styles */
    .notifications-menu {
        margin-top: 0.5rem;
        border: 1px solid var(--border);
    }

    /* Update these specific notification styles */
    .notification-dropdown {
        position: relative;
        z-index: 1001;
    }

    .notification-dropdown .dropdown-content {
        position: absolute;
        top: 100%;
        right: 0;
        width: 320px;
        background: white;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        border: 1px solid var(--border);
        opacity: 0;
        visibility: hidden;
        transform: translateY(10px);
        transition: all 0.2s ease;
    }

    .notification-dropdown.active .dropdown-content {
        opacity: 1;
        visibility: visible;
        transform: translateY(0);
    }

    .notification-item {
        padding: 1rem;
        border-bottom: 1px solid var(--border);
        cursor: pointer;
        transition: background-color 0.2s ease;
    }

    .notification-item:hover {
        background-color: var(--sidebar-hover);
    }

    .notification-item.unread {
        background-color: rgba(var(--primary), 0.05);
    }

    .notification-item.unread::before {
        content: '';
        display: inline-block;
        width: 8px;
        height: 8px;
        background-color: var(--accent);
        border-radius: 50%;
        margin-right: 8px;
        vertical-align: middle;
    }

    /* Remove any conflicting styles */
    .nav-item.notification-dropdown .dropdown-menu,
    .nav-item.notification-dropdown .dropdown-panel {
        display: none;
    }
</style>

<script>
    document.querySelectorAll('.nav-button').forEach(button => {
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

    // Update click handler to toggle notifications
    document.addEventListener('DOMContentLoaded', function() {
                const notificationBtn = document.querySelector('.notification-btn');
                const notificationItem = notificationBtn.closest('.nav-item');

                notificationBtn.addEventListener('click', (e) => {
                    e.stopPropagation();

                    // Close other dropdowns
                    document.querySelectorAll('.nav-item.active').forEach(item => {
                        if (item !== notificationItem) {
                            item.classList.remove('active');
                        }
                    });

                    // Toggle notifications
                    notificationItem.classList.toggle('active');
                    document.querySelectorAll('.dropdown-content, .dropdown-panel').forEach(panel => {
                        panel.addEventListener('click', (e) => {
                            e.stopPropagation();
                        });
                    });

                    // Notification read handler
                    document.querySelectorAll('.notification-item:not(.empty)').forEach(item => {
                        item.addEventListener('click', async () => {
                            const notifId = item.dataset.id;
                            if (!notifId) return;

                            try {
                                const response = await fetch('../includes/mark_notification_read.php', {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json'
                                    },
                                    body: JSON.stringify({
                                        id: notifId
                                    })
                                });

                                if (response.ok) {
                                    item.classList.remove('unread');
                                    const badge = notificationBtn.querySelector('.badge');
                                    if (badge) {
                                        const currentCount = parseInt(badge.textContent) - 1;
                                        if (currentCount <= 0) {
                                            badge.remove();
                                        } else {
                                            badge.textContent = currentCount;
                                        }
                                    }
                                }
                            } catch (error) {
                                console.error('Error marking notification as read:', error);
                            }
                        });
                    });
                });
            });
</script>
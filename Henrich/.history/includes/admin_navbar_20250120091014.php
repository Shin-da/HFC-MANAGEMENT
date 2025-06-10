<nav class="admin-navbar">
    <div class="nav-left">
        <button class="nav-toggle" id="sidebar-toggle">
            <i class='bx bx-menu'></i>
        </button>
    </div>

    <div class="nav-right">
        <div class="nav-items">
            <!-- Notifications -->
            <div class="nav-item dropdown">
                <button class="nav-link" id="notificationDropdown">
                    <i class='bx bx-bell'></i>
                    <?php
                    // Update notification query for admin
                    $notif_query = "SELECT n.*, al.activity_type, al.activity 
                                    FROM notifications n 
                                    LEFT JOIN activity_log al ON n.activity_id = al.log_id 
                                    WHERE (n.recipient_type = 'admin' OR n.recipient_id = ?) 
                                    AND n.is_read = 0 
                                    ORDER BY n.created_at DESC";
                    try {
                        $stmt = $conn->prepare($notif_query);
                        $stmt->bind_param("i", $_SESSION['user_id']);
                        $stmt->execute();
                        $notifications = $stmt->get_result();
                        $notif_count = $notifications->num_rows;
                    } catch (Exception $e) {
                        error_log("Admin notification query error: " . $e->getMessage());
                        $notifications = false;
                        $notif_count = 0;
                    }
                    if ($notif_count > 0):
                    ?>
                    <span class="badge"><?php echo $notif_count; ?></span>
                    <?php endif; ?>
                </button>
                <div class="dropdown-menu">
                    <div class="dropdown-header">
                        <h6>Notifications</h6>
                        <a href="notifications.php">View All</a>
                    </div>
                    <div class="dropdown-body">
                        <?php
                        </div>
                        <?php 
                            endwhile;
                        else:
                        ?>
                        <div class="notification-item empty">
                            <p>No new notifications</p>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- User Account -->
            <div class="nav-item dropdown">
                <button class="nav-link user-menu">
                    <img src="../resources/images/default-avatar.png" alt="Admin" class="user-avatar">
                    <span class="user-name"><?php echo $_SESSION['username']; ?></span>
                    <i class='bx bx-chevron-down'></i>
                </button>
                <div class="dropdown-menu">
                    <div class="dropdown-header user-header">
                        <img src="../resources/images/default-avatar.png" alt="Admin">
                        <div class="user-info">
                            <h6><?php echo $_SESSION['username']; ?></h6>
                            <span>Administrator</span>
                        </div>
                    </div>
                    <div class="dropdown-body">
                        <a href="profile.php" class="dropdown-item">
                            <i class='bx bx-user'></i> Profile
                        </a>
                        <a href="settings.php" class="dropdown-item">
                            <i class='bx bx-cog'></i> Settings
                        </a>
                        <hr>
                        <a href="../login/logout.php" class="dropdown-item text-danger">
                            <i class='bx bx-log-out'></i> Logout
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</nav>

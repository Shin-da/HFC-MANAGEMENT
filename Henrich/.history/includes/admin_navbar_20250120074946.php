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
                    $notif_query = "SELECT COUNT(*) as count FROM notifications 
                                   WHERE admin_read = 0 AND type = 'admin'";
                    $notif_result = $conn->query($notif_query);
                    $notif_count = $notif_result->fetch_assoc()['count'];
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
                        $notifications = $conn->query("
                            SELECT * FROM notifications 
                            WHERE admin_read = 0 AND type = 'admin'
                            ORDER BY created_at DESC LIMIT 5
                        ");
                        if ($notifications->num_rows > 0):
                            while($notif = $notifications->fetch_assoc()):
                        ?>
                        <div class="notification-item">
                            <i class='bx bx-info-circle'></i>
                            <div class="notification-content">
                                <p><?php echo htmlspecialchars($notif['message']); ?></p>
                                <small><?php echo date('M d, H:i', strtotime($notif['created_at'])); ?></small>
                            </div>
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
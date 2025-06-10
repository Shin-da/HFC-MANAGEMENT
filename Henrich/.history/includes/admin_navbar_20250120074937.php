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
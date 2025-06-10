
<nav class="navbar navbar-expand-lg">

    <div class="navbar-nav ml-auto">
        <div class="nav-item dropdown notification-dropdown">
            <?php
            $notif_query = "SELECT n.*, al.activity_type 
                            FROM notifications n 
                            LEFT JOIN activity_log al ON n.activity_id = al.log_id 
                            WHERE n.user_id = {$_SESSION['uid']} AND n.is_read = 0 
                            ORDER BY n.created_at DESC";
            $notifications = mysqli_query($conn, $notif_query);
            $notif_count = mysqli_num_rows($notifications);
            ?>
            <a class="nav-link dropdown-toggle" href="#" id="notificationDropdown" role="button" data-toggle="dropdown">
                <i class="fas fa-bell"></i>
                <?php if ($notif_count > 0): ?>
                    <span class="badge"><?php echo $notif_count; ?></span>
                <?php endif; ?>
            </a>
            <div class="dropdown-menu dropdown-menu-right notification-menu">
                <div class="notification-header">
                    <h6 class="m-0">Notifications</h6>
                    <a href="notifications.php" class="text-muted">View All</a>
                </div>
                <div class="notification-list">
                    <?php
                    if ($notif_count > 0) {
                        while ($notif = mysqli_fetch_assoc($notifications)) {
                            echo "<div class='dropdown-item unread' data-id='{$notif['id']}'>";
                            echo "<p class='mb-1'>" . htmlspecialchars($notif['message']) . "</p>";
                            echo "<small class='text-muted'>" . date('M d, H:i', strtotime($notif['created_at'])) . "</small>";
                            echo "</div>";
                        }
                    } else {
                        echo "<div class='dropdown-item'>No new notifications</div>";
                    }
                    ?>
                </div>
            </div>
        </div>
        <div class="nav-item dropdown user-dropdown">
            
    </div>
</nav>

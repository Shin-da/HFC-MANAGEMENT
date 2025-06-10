// ...existing code...
<nav class="navbar navbar-expand-lg">
    // ...existing code...
    <div class="navbar-nav ml-auto">
        <div class="nav-item dropdown notification-dropdown">
            <?php
            $notif_query = "SELECT COUNT(*) as count FROM notifications WHERE user_id = {$_SESSION['uid']} AND is_read = 0";
            $notif_result = mysqli_query($conn, $notif_query);
            $notif_count = mysqli_fetch_assoc($notif_result)['count'];
            ?>
            <a class="nav-link dropdown-toggle" href="#" id="notificationDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fas fa-bell"></i>
                <?php if ($notif_count > 0): ?>
                    <span class="badge"><?php echo $notif_count; ?></span>
                <?php endif; ?>
            </a>
            <div class="dropdown-menu dropdown-menu-right notification-menu" aria-labelledby="notificationDropdown">
                <div class="notification-header">
                    <h6 class="m-0">Notifications</h6>
                    <a href="notifications.php" class="text-muted">View All</a>
                </div>
                <div class="notification-list">
                    <?php
                    $notifications = mysqli_query($conn, "SELECT * FROM notifications WHERE user_id = {$_SESSION['uid']} ORDER BY created_at DESC LIMIT 5");
                    while ($notif = mysqli_fetch_assoc($notifications)) {
                        echo "<div class='dropdown-item " . ($notif['is_read'] ? 'read' : 'unread') . "'>";
                        echo "<p class='mb-1'>" . htmlspecialchars($notif['message']) . "</p>";
                        echo "<small class='text-muted'>" . date('M d, H:i', strtotime($notif['created_at'])) . "</small>";
                        echo "</div>";
                    }
                    if (mysqli_num_rows($notifications) == 0) {
                        echo "<div class='dropdown-item'>No new notifications</div>";
                    }
                    ?>
                </div>
            </div>
        </div>
        // ...existing code...
    </div>
</nav>

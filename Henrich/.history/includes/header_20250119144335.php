// ...existing code...
<div class="header-right">
    <div class="notification-bell">
        <?php
        $notif_query = "SELECT COUNT(*) as count FROM notifications WHERE user_id = {$_SESSION['user_id']} AND is_read = 0";
        $notif_result = mysqli_query($conn, $notif_query);
        $notif_count = mysqli_fetch_assoc($notif_result)['count'];
        ?>
        <a href="notifications.php" class="notification-icon">
            <i class="fas fa-bell"></i>
            <?php if ($notif_count > 0): ?>
                <span class="badge"><?php echo $notif_count; ?></span>
            <?php endif; ?>
        </a>
    </div>
    // ...existing code...
</div>

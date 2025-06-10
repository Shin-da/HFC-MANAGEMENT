<?php
require_once '.';
require_once 'includes/session.php';

// Mark notifications as read
if (isset($_POST['mark_read'])) {
    $notif_id = (int)$_POST['mark_read'];
    mysqli_query($conn, "UPDATE notifications SET is_read = 1 WHERE id = $notif_id AND user_id = {$_SESSION['user_id']}");
}

// Get notifications
$query = "SELECT n.*, al.activity, al.activity_type 
          FROM notifications n 
          LEFT JOIN activity_log al ON n.activity_id = al.log_id 
          WHERE n.user_id = {$_SESSION['user_id']} 
          ORDER BY n.created_at DESC";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Notifications</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container">
        <h2>Notifications</h2>
        
        <div class="notifications-list">
            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                <div class="notification-item <?php echo $row['is_read'] ? 'read' : 'unread'; ?>">
                    <div class="notification-content">
                        <p><?php echo htmlspecialchars($row['message']); ?></p>
                        <small><?php echo date('Y-m-d H:i:s', strtotime($row['created_at'])); ?></small>
                    </div>
                    <?php if (!$row['is_read']): ?>
                        <form method="POST" class="mark-read-form">
                            <input type="hidden" name="mark_read" value="<?php echo $row['id']; ?>">
                            <button type="submit" class="btn-mark-read">Mark as Read</button>
                        </form>
                    <?php endif; ?>
                </div>
            <?php endwhile; ?>
        </div>
    </div>
</body>
</html>

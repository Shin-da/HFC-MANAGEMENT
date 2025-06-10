<?php
if (!isset($conn) || !$conn instanceof mysqli || $conn->ping() === false) {
    // If connection is closed or invalid, create new connection
    require_once dirname(__DIR__) . '/includes/config.php';
}

// First ensure we have the config and session
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/session.php';

// Make connection available
global $conn;

// Debug connection status
error_log("Connection variable status: " . ($conn ? "Connected" : "Not connected"));
error_log("Connection error if any: " . ($conn->connect_error ?? "None"));

// Debug the connection
if (!$conn) {
    error_log("Database connection is null in notification_dropdown.php");
    die("Database connection failed. Please check the configuration.");
}

error_log("Loading notification dropdown");
error_log("Session user_id: " . ($_SESSION['user_id'] ?? 'not set'));

try {
    // Update query to match your table structure
    $query = "SELECT * FROM notifications 
              WHERE user_id = ? 
              ORDER BY created_at DESC 
              LIMIT 10";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    $notifications = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $notif_count = count(array_filter($notifications, function($n) {
        return !$n['is_read'];
    }));
    
    error_log("Found {$notif_count} notifications");
} catch (Exception $e) {
    error_log("Error in notification dropdown: " . $e->getMessage());
    $notifications = [];
    $notif_count = 0;
}
?>

<div class="notification-wrapper">
    <button class="nav-button notification-btn" type="button" aria-label="Notifications">
        <i class='bx bx-bell'></i>
        <?php if ($notif_count > 0): ?>
            <span class="badge"><?php echo $notif_count; ?></span>
        <?php endif; ?>
    </button>
    <div class="notification-dropdown-content">
        <div class="dropdown-header">
            <h4>Notifications <?php if ($notif_count > 0) echo "($notif_count)"; ?></h4>
            <?php if ($notif_count > 0): ?>
                <a href="#" class="mark-all-read">Mark all as read</a>
            <?php endif; ?>
        </div>
        <div class="notifications-list">
            <?php if ($notifications && count($notifications) > 0): ?>
                <?php foreach ($notifications as $notif): ?>
                    <div class="notification-item <?= $notif['is_read'] ? 'read' : 'unread' ?>" 
                         data-id="<?= htmlspecialchars($notif['id']) ?>">
                        <i class='bx bx-bell'></i>
                        <div class="notification-content">
                            <p><?= htmlspecialchars($notif['message']) ?></p>
                            <small><?= date('M j, g:i A', strtotime($notif['created_at'])) ?></small>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="notification-item empty">
                    <p>No notifications</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

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
    // Modify the query to include activity information
    $query = "SELECT n.*, 
              CASE 
                WHEN n.type LIKE 'order_%' THEN 'order'
                WHEN n.type LIKE 'inventory_%' THEN 'inventory'
                ELSE 'general'
              END as category
              FROM notifications n 
              WHERE n.user_id = ? 
              ORDER BY n.created_at DESC 
              LIMIT 10";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    $notifications = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $notif_count = count($notifications);
    
    error_log("Found {$notif_count} notifications");
} catch (Exception $e) {
    error_log("Error in notification dropdown: " . $e->getMessage());
    $notifications = [];
    $notif_count = 0;
}

// Add category-specific icons
$categoryIcons = [
    'order' => 'bx-package',
    'inventory' => 'bx-box',
    'general' => 'bx-bell'
];
?>

<div class="notification-wrapper">
    <button class="nav-button notification-btn" type="button">
        <i class='bx bx-bell'></i>
                <?php foreach ($notifications as $notif): ?>
                    <div class="notification-item unread" data-id="<?= htmlspecialchars($notif['id']) ?>">
                        <div class="notification-content">
                            <?php if ($notif['activity_type']): ?>
                                <span class="activity-type"><?= htmlspecialchars($notif['activity_type']) ?></span>
                            <?php endif; ?>
                            <p><?= htmlspecialchars($notif['message']) ?></p>
                            <small><?= $notif['formatted_date'] ?></small>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="notification-item empty">
                    <p>No new notifications</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

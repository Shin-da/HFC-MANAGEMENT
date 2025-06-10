<?php
require_once '../includes/session.php';
require_once '../includes/config.php';
require_once '../includes/Page.php';

// Set page title and other properties
Page::setTitle('Notifications');
Page::setBodyClass('notifications-page');

try {
    $query = "SELECT n.*, a.activity_type, a.activity,
              DATE_FORMAT(n.created_at, '%M %d, %Y %h:%i %p') as formatted_date 
              FROM notifications n 
              LEFT JOIN activity_log a ON n.activity_id = a.log_id 
              WHERE n.user_id = ? 
              ORDER BY n.is_read ASC, n.created_at DESC";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    $notifications = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
} catch (Exception $e) {
    error_log("Error fetching notifications: " . $e->getMessage());
    $notifications = [];
}

ob_start();
?>

<div class="notifications-page">
    <div class="page-header">
        <div class="header-content">
            <h1>Notifications</h1>
            <?php if (!empty($notifications)): ?>
                <button id="markAllRead" class="btn btn-secondary">
                    <i class='bx bx-check-double'></i>
                    Mark All as Read
                </button>
            <?php endif; ?>
        </div>
        <div class="notification-filters">
            <button class="filter-btn active" data-filter="all">All</button>
            <button class="filter-btn" data-filter="unread">Unread</button>
            <button class="filter-btn" data-filter="read">Read</button>
        </div>
    </div>

    <div class="notifications-container">
        <?php if (!empty($notifications)): ?>
            <?php foreach ($notifications as $notif): ?>
                <div class="notification-card <?= $notif['is_read'] ? 'read' : 'unread' ?>" 
                     data-id="<?= htmlspecialchars($notif['id']) ?>">
                    <div class="notification-icon">
                        <i class='bx bx-<?= getNotificationIcon($notif['activity_type']) ?>'></i>
                    </div>
                    <div class="notification-content">
                        <?php if ($notif['activity_type']): ?>
                            <span class="activity-badge"><?= htmlspecialchars($notif['activity_type']) ?></span>
                        <?php endif; ?>
                        <p class="notification-message"><?= htmlspecialchars($notif['message']) ?></p>
                        <div class="notification-meta">
                            <span class="notification-time">
                                <i class='bx bx-time-five'></i>
                                <?= $notif['formatted_date'] ?>
                            </span>
                            <?php if (!$notif['is_read']): ?>
                                <button class="mark-read-btn">
                                    <i class='bx bx-check'></i>
                                    Mark as read
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="empty-state">
                <i class='bx bx-bell-off'></i>
                <h3>No notifications</h3>
                <p>You're all caught up! No new notifications to display.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php
function getNotificationIcon($type) {
    $icons = [
        'stock_movement' => 'package',
        'order_placed' => 'shopping-bag',
        'inventory_alert' => 'error',
        'system' => 'info-circle',
        'default' => 'bell'
    ];
    return $icons[$type] ?? $icons['default'];
}

$content = ob_get_clean();
Page::render($content);
?>


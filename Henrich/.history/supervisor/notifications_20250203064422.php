<?php
require_once '../includes/config.php';
require_once '../includes/session.php';
require_once '../includes/Page.php';

$current_page = basename($_SERVER['PHP_SELF'], '.php');
Page::setCurrentPage($current_page);
Page::setTitle('Notifications');
Page::setBodyClass('supervisor-body');

// Add required styles and scripts
Page::addStyle('../assets/css/customer-order.css');
Page::addStyle('../assets/css/notifications-page.css');
Page::addScript('https://cdn.jsdelivr.net/npm/sweetalert2@11');

try {
    // Get all notifications for the user with pagination
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $limit = 20;
    $offset = ($page - 1) * $limit;

    $query = "SELECT SQL_CALC_FOUND_ROWS *, 
              CASE 
    $notifications = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
} catch (Exception $e) {
    error_log("Error fetching notifications: " . $e->getMessage());
    $notifications = [];
}

ob_start();
?>

<div class="page-container">
    <!-- Page Header -->
    <header class="page-header">
        <div class="header-content">
            <div class="header-title">
                <h1>Notifications</h1>
                <p class="text-secondary"><?= count($notifications) ?> Notifications</p>
            </div>
            <div class="header-actions">
                <?php if (!empty($notifications)): ?>
                    <button id="markAllRead" class="btn">
                        <i class='bx bx-check-double'></i>
                        Mark all as read
                    </button>
                <?php endif; ?>
            </div>
        </div>

        <!-- Filters Section -->
        <div class="notification-filters">
            <div class="filter-group">
                <button class="filter-btn active" data-filter="all">
                    <i class='bx bx-list-ul'></i>
                    All
                </button>
                <button class="filter-btn" data-filter="unread">
                    <i class='bx bx-envelope'></i>
                    Unread
                </button>
            </div>
            <div class="filter-group">
                <button class="filter-btn" data-type="inventory">
                    <i class='bx bx-package'></i>
                    Inventory
                </button>
                <button class="filter-btn" data-type="order">
                    <i class='bx bx-shopping-bag'></i>
                    Orders
                </button>
                <button class="filter-btn" data-type="system">
                    <i class='bx bx-bell'></i>
                    System
                </button>
            </div>
        </div>
    </header>

    <!-- Notifications List -->
    <div class="notifications-list">
        <?php if (!empty($notifications)): ?>
            <?php foreach ($notifications as $notif): ?>
                <div class="notification-card <?= $notif['is_read'] ? 'read' : 'unread' ?>" 
                     data-id="<?= htmlspecialchars($notif['id']) ?>"
                     data-type="<?= htmlspecialchars($notif['activity_type'] ?? 'system') ?>">
                    
                    <div class="notification-icon">
                        <i class='bx <?= getNotificationIcon($notif['activity_type']) ?>'></i>
                    </div>
                    
                    <div class="notification-content">
                        <div class="notification-header">
                            <?php if ($notif['activity_type']): ?>
                                <span class="badge badge-<?= $notif['activity_type'] ?>">
                                    <?= formatActivityType($notif['activity_type']) ?>
                                </span>
                            <?php endif; ?>
                            <time datetime="<?= $notif['exact_date'] ?>" 
                                  title="<?= $notif['exact_date'] ?>"
                                  class="notification-time">
                                <?= formatTimeAgo($notif['seconds_ago']) ?>
                            </time>
                        </div>
                        
                        <p class="notification-message">
                            <?= htmlspecialchars($notif['message']) ?>
                        </p>
                        
                        <?php if (!$notif['is_read']): ?>
                            <button class="mark-read-btn" onclick="markAsRead(<?= $notif['id'] ?>)">
                                <i class='bx bx-check'></i>
                                Mark as read
                            </button>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="empty-state">
                <i class='bx bx-bell-off'></i>
                <h3>No notifications</h3>
                <p>You're all caught up! Check back later for new updates.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php
function getNotificationIcon($type) {
    return match ($type) {
        'inventory' => 'bx-package',
        'order' => 'bx-shopping-bag',
        'system' => 'bx-bell',
        default => 'bx-bell'
    };
}

function formatActivityType($type) {
    return ucwords(str_replace('_', ' ', $type));
}

function formatTimeAgo($seconds) {
    if ($seconds < 60) {
        return 'Just now';
    } elseif ($seconds < 3600) {
        $minutes = floor($seconds / 60);
        return $minutes . 'm ago';
    } elseif ($seconds < 86400) {
        $hours = floor($seconds / 3600);
        return $hours . 'h ago';
    } else {
        $days = floor($seconds / 86400);
        return $days . 'd ago';
    }
}

$content = ob_get_clean();
Page::render($content);
?>

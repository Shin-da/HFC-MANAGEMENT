<?php
/**
 * Notifications Page
 * 
 * Displays notifications with filtering options and management controls
 */
require_once '../includes/config.php';
require_once '../includes/session.php';
require_once '../includes/classes/Page.php';
require_once '../includes/classes/NotificationManager.php';

// Initialize page
$page = new Page();
$page->setTitle("Notifications");
$page->addStyle("css/notifications.css");
$page->addScript("js/notifications.js");

// Get notification manager
$notificationManager = new NotificationManager($conn);

// Get filters from query string
$includeRead = isset($_GET['include_read']) && $_GET['include_read'] == '1';
$notificationType = isset($_GET['type']) ? $_GET['type'] : '';
$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 50;

// Get notifications
$notifications = $notificationManager->getNotifications($_SESSION['user_id'], $includeRead, $limit);

// Count unread notifications
$unreadCount = $notificationManager->getUnreadCount($_SESSION['user_id']);

// Start page
ob_start();
?>

<div class="notifications-page">
    <div class="notifications-header">
        <h1>Notifications <?php if ($unreadCount > 0): ?><span class="badge"><?php echo $unreadCount; ?></span><?php endif; ?></h1>
        
        <div class="filter-controls">
            <form method="get" action="" id="notification-filters">
                <div class="form-group">
                    <select name="type" class="form-control">
                        <option value="" <?php echo $notificationType == "" ? "selected" : ""; ?>>All Types</option>
                        <option value="inventory_alert" <?php echo $notificationType == "inventory_alert" ? "selected" : ""; ?>>Inventory Alerts</option>
                        <option value="order_update" <?php echo $notificationType == "order_update" ? "selected" : ""; ?>>Order Updates</option>
                        <option value="system_message" <?php echo $notificationType == "system_message" ? "selected" : ""; ?>>System Messages</option>
                    </select>
                </div>
                
                <div class="form-check">
                    <input type="checkbox" class="form-check-input" id="include_read" name="include_read" value="1" <?php echo $includeRead ? "checked" : ""; ?>>
                    <label class="form-check-label" for="include_read">Include Read Notifications</label>
                </div>
                
                <button type="submit" class="btn btn-primary">Apply Filters</button>
            </form>
            
            <div class="button-actions">
                <?php if ($unreadCount > 0): ?>
                <button id="mark-all-read-btn" class="mark-all-btn">Mark All as Read</button>
                <?php endif; ?>
                <button id="clear-all-btn" class="clear-all-btn">Clear All</button>
            </div>
        </div>
    </div>
    
    <div class="notifications-content">
        <?php if (empty($notifications)): ?>
            <div class="empty-state">
                <i class="fas fa-bell-slash"></i>
                <h3>No Notifications</h3>
                <p>You don't have any<?php echo $includeRead ? '' : ' unread'; ?> notifications<?php echo !empty($notificationType) ? ' of this type' : ''; ?>.</p>
            </div>
        <?php else: ?>
            <div class="notification-list">
                <?php foreach ($notifications as $notification): ?>
                    <div class="notification-item <?php echo $notification['is_read'] ? 'read' : 'unread'; ?>" data-id="<?php echo $notification['id']; ?>">
                        <?php
                        // Determine icon based on severity and type
                        $iconClass = 'info';
                        $iconName = 'info-circle';
                        
                        switch ($notification['severity']) {
                            case 'warning':
                                $iconClass = 'warning';
                                $iconName = 'exclamation-triangle';
                                break;
                            case 'danger':
                                $iconClass = 'danger';
                                $iconName = 'exclamation-circle';
                                break;
                            case 'success':
                                $iconClass = 'success';
                                $iconName = 'check-circle';
                                break;
                        }
                        ?>
                        
                        <div class="notification-icon <?php echo $iconClass; ?>">
                            <i class="fas fa-<?php echo $iconName; ?>"></i>
                        </div>
                        
                        <div class="notification-content">
                            <div class="notification-title"><?php echo htmlspecialchars($notification['title']); ?></div>
                            <div class="notification-message"><?php echo htmlspecialchars($notification['message']); ?></div>
                            <div class="notification-info">
                                <span class="notification-time"><?php echo date('F j, Y, g:i a', strtotime($notification['created_at'])); ?></span>
                                <?php if (!empty($notification['read_at'])): ?>
                                <span class="notification-read-at">Read: <?php echo date('F j, Y, g:i a', strtotime($notification['read_at'])); ?></span>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <div class="notification-actions">
                            <?php if (!$notification['is_read']): ?>
                            <button class="mark-read-btn" title="Mark as Read" data-id="<?php echo $notification['id']; ?>">
                                <i class="fas fa-check"></i>
                            </button>
                            <?php endif; ?>
                            
                            <button class="delete-btn" title="Delete" data-id="<?php echo $notification['id']; ?>">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Mark as read button click
    document.querySelectorAll('.mark-read-btn').forEach(function(button) {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const notificationId = this.getAttribute('data-id');
            if (notificationId) {
                NotificationManager.markAsRead(notificationId);
                const item = document.querySelector(`.notification-item[data-id="${notificationId}"]`);
                if (item) {
                    item.classList.remove('unread');
                    item.classList.add('read');
                    this.remove();
                }
            }
        });
    });
    
    // Delete button click
    document.querySelectorAll('.delete-btn').forEach(function(button) {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const notificationId = this.getAttribute('data-id');
            if (notificationId && confirm('Are you sure you want to delete this notification?')) {
                NotificationManager.deleteNotification(notificationId);
                const item = document.querySelector(`.notification-item[data-id="${notificationId}"]`);
                if (item) {
                    item.remove();
                }
            }
        });
    });
    
    // Mark all as read button click
    const markAllBtn = document.getElementById('mark-all-read-btn');
    if (markAllBtn) {
        markAllBtn.addEventListener('click', function(e) {
            e.preventDefault();
            if (confirm('Mark all notifications as read?')) {
                NotificationManager.markAllAsRead();
                document.querySelectorAll('.notification-item.unread').forEach(function(item) {
                    item.classList.remove('unread');
                    item.classList.add('read');
                    const markReadBtn = item.querySelector('.mark-read-btn');
                    if (markReadBtn) {
                        markReadBtn.remove();
                    }
                });
                this.style.display = 'none';
            }
        });
    }
    
    // Clear all button click
    const clearAllBtn = document.getElementById('clear-all-btn');
    if (clearAllBtn) {
        clearAllBtn.addEventListener('click', function(e) {
            e.preventDefault();
            if (confirm('Delete all displayed notifications? This cannot be undone.')) {
                // Send request to clear all displayed notifications
                fetch('../api/notifications.php?action=clear_all', {
                    method: 'DELETE'
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        window.location.reload();
                    } else {
                        alert('Failed to clear notifications: ' + (data.error || 'Unknown error'));
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while trying to clear notifications.');
                });
            }
        });
    }
});
</script>

<?php
// End page
$content = ob_get_clean();
$page->render($content);
?>

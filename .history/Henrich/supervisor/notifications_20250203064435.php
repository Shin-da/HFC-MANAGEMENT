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
                WHEN activity_id REGEXP '^SO-' THEN 'order'
                WHEN activity_id REGEXP '^INV-' THEN 'inventory'
                ELSE 'general'
              END as type
              FROM notifications 
              WHERE user_id = ? 
              ORDER BY created_at DESC 
              LIMIT ?, ?";
              
    $stmt = $conn->prepare($query);
    $stmt->bind_param("iii", $_SESSION['user_id'], $offset, $limit);
    $stmt->execute();
    $notifications = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    
    // Get total count
    $total = $conn->query("SELECT FOUND_ROWS()")->fetch_row()[0];
    $totalPages = ceil($total / $limit);

    ob_start();
?>

<div class="notifications-page">
    <div class="page-header">
        <h1>Notifications</h1>
        <?php if ($total > 0): ?>
            <button onclick="markAllAsRead()" class="btn-action">
                <i class='bx bx-check-double'></i> Mark all as read
            </button>
        <?php endif; ?>
    </div>

    <div class="notifications-container">
        <?php if (count($notifications) > 0): ?>
            <div class="notifications-list">
                <?php foreach ($notifications as $notif): ?>
                    <div class="notification-card <?= $notif['is_read'] ? 'read' : 'unread' ?>" 
                         data-id="<?= $notif['id'] ?>">
                        <div class="notification-icon">
                            <i class='bx <?= $notif['type'] === 'order' ? 'bx-package' : 
                                        ($notif['type'] === 'inventory' ? 'bx-box' : 'bx-bell') ?>'></i>
                        </div>
                        <div class="notification-content">
                            <p class="message"><?= htmlspecialchars($notif['message']) ?></p>
                            <div class="notification-meta">
                                <span class="time">
                                    <i class='bx bx-time'></i>
                                    <?= date('M j, Y g:i A', strtotime($notif['created_at'])) ?>
                                </span>
                                <?php if ($notif['activity_id']): ?>
                                    <a href="javascript:void(0)" onclick="viewDetail('<?= $notif['activity_id'] ?>', '<?= $notif['type'] ?>')" 
                                       class="view-detail">View Details</a>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php if (!$notif['is_read']): ?>
                            <button class="mark-read" onclick="markAsRead(<?= $notif['id'] ?>)">
                                <i class='bx bx-check'></i>
                            </button>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>

            <?php if ($totalPages > 1): ?>
                <div class="pagination">
                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <a href="?page=<?= $i ?>" 
                           class="page-link <?= $i === $page ? 'active' : '' ?>">
                            <?= $i ?>
                        </a>
                    <?php endfor; ?>
                </div>
            <?php endif; ?>
        <?php else: ?>
            <div class="empty-state">
                <i class='bx bx-bell-off'></i>
                <h3>No Notifications</h3>
                <p>You don't have any notifications at the moment.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
function markAsRead(id) {
    fetch('../includes/mark_notification_read.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ id: id })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            document.querySelector(`.notification-card[data-id="${id}"]`).classList.add('read');
        }
    });
}

function markAllAsRead() {
    fetch('../includes/mark_all_notifications_read.php', {
        method: 'POST'
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            Swal.fire({
                icon: 'success',
                title: 'All notifications marked as read',
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000
            }).then(() => {
                location.reload();
            });
        }
    });
}

function viewDetail(id, type) {
    let url;
    if (type === 'order') {
        url = `customerorderdetail.php?orderid=${id}`;
    } else if (type === 'inventory') {
        url = `inventory_detail.php?id=${id}`;
    }
    
    if (url) {
        window.location.href = url;
    }
}
</script>

<?php
    $content = ob_get_clean();
    Page::render($content);
} catch (Exception $e) {
    error_log("Error in notifications page: " . $e->getMessage());
    echo "Error loading notifications.";
}
?>

<?php
require_once '../includes/session.php';
require_once '../includes/config.php';
require_once '../includes/Page.php';
require_once './access_control.php';

$current_page = basename($_SERVER['PHP_SELF'], '.php');
$_SESSION['current_page'] = $current_page;

// Initialize database connection
try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME,
        DB_USER,
        DB_PASSWORD,
        array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION)
    );
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Fetch user data with status check
function getUserData($pdo, $userId) {
    $stmt = $pdo->prepare("
        SELECT *, 
        CONCAT(first_name, ' ', last_name) as full_name,
        CASE 
            WHEN status = 1 THEN 'Active'
            ELSE 'Inactive'
        END as account_status,
        TIME_TO_SEC(TIMEDIFF(NOW(), last_online)) as seconds_offline
        FROM users 
        WHERE user_id = :user_id
    ");
    $stmt->execute(['user_id' => $userId]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// Get user data
$userData = getUserData($pdo, $_SESSION['user_id']);

// Configure page
Page::setTitle('My Account | ' . ucfirst($_SESSION['role']));
Page::setBodyClass('account-page');
Page::set('current_page', 'myaccount');

// Add styles
Page::addStyle('../assets/css/style.css');
Page::addStyle('../assets/css/variables.css');
Page::addStyle('../assets/css/sidebar.css');
Page::addStyle('../assets/css/account.css');
Page::addStyle('../assets/css/components/avatar.css');
Page::addStyle('../assets/css/components/badge.css');

// Start output buffering
ob_start();
?>

<div class="account-wrapper">
    <div class="account-header">
        <div class="user-profile-header">
            <div class="user-avatar-section">
                <div class="user-avatar">
                    <?php 
                    $initials = strtoupper(substr($userData['first_name'], 0, 1) . substr($userData['last_name'], 0, 1));
                    echo $initials;
                    ?>
                </div>
                <div class="user-status <?= $userData['is_online'] ? 'online' : 'offline' ?>"></div>
            </div>
            <div class="user-info-section">
                <h1><?= htmlspecialchars($userData['full_name']) ?></h1>
                <div class="user-meta">
                    <span class="badge badge-<?= $userData['account_status'] === 'Active' ? 'success' : 'danger' ?>">
                        <?= $userData['account_status'] ?>
                    </span>
                    <span class="department-badge">
                        <?= htmlspecialchars($userData['department']) ?>
                    </span>
                    <span class="last-seen">
                        <?php
                        if ($userData['is_online']) {
                            echo '<span class="online-status">Online now</span>';
                        } else {
                            $seconds = $userData['seconds_offline'];
                            if ($seconds < 60) {
                                echo 'Just now';
                            } elseif ($seconds < 3600) {
                                echo floor($seconds/60) . ' minutes ago';
                            } elseif ($seconds < 86400) {
                                echo floor($seconds/3600) . ' hours ago';
                            } else {
                                echo 'Last seen ' . date('M j, Y', strtotime($userData['last_online']));
                            }
                        }
                        ?>
                    </span>
                </div>
            </div>
        </div>
    </div>

    <div class="account-content">
        <!-- Personal Information Card -->
        <div class="account-card">
            <div class="card-header">
                <h2>Personal Information</h2>
                               required>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-secondary">Request Password Change</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php
// Render the page
Page::render(ob_get_clean());
?>


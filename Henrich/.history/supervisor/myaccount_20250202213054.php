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

// Fetch user data
function getUserData($pdo, $userId) {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE user_id = :user_id");
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

// Start output buffering
ob_start();
?>

<div class="account-wrapper">
    <div class="account-header">
        <h1>My Account</h1>
        <p class="subtitle">Manage your account settings</p>
    </div>

    <div class="account-content">
        <div class="account-card">
            <div class="card-header">
                <h2>Profile Information</h2>
            </div>
            <div class="card-body">
                <form action="./process/edit-account.process.php" method="post" class="account-form">
                    <input type="hidden" name="user_id" value="<?= htmlspecialchars($_SESSION['user_id']) ?>">
                    
                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <input type="email" 
                               id="email" 
                               name="email" 
                               value="<?= htmlspecialchars($userData['useremail']) ?>" 
                               required>
                    </div>

                    <div class="form-group">
                        <label for="username">Username</label>
                        <input type="text" 
                               id="username" 
                               name="username" 
                               value="<?= htmlspecialchars($userData['username']) ?>" 
                               required>
                    </div>

                    <div class="form-group">
                        <label for="role">Role</label>
                        <input type="text" 
                               id="role" 
                               value="<?= htmlspecialchars(ucfirst($userData['role'])) ?>" 
                               disabled>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="account-card">
            <div class="card-header">
                <h2>Password Change</h2>
            </div>
            <div class="card-body">
                <form action="./process/request-password-change.process.php" method="post" class="password-form">
                <div class="input-group">
                    <label for="role">Role:</label>
                    <select name="role" disabled>
                        <option value="superadmin" <?php if ($role == 'superadmin') echo 'selected'; ?>>Super Admin</option>
                        <option value="admin" <?php if ($role == 'admin') echo 'selected'; ?>>Admin</option>
                        <option value="supervisor" <?php if ($role == 'supervisor') echo 'selected'; ?>>Supervisor</option>
                        <option value="cashier" <?php if ($role == 'cashier') echo 'selected'; ?>>Cashier</option>
                    </select>
                </div>
            </form>
            <form action="./process/request-password-change.process.php" method="post">
                <input type="hidden" name="user_id" value="<?php echo $user_id; ?>">
                <div class="input-group">
                    <label for="oldpassword">Old Password:</label>
                    <input type="password" name="oldpassword" required>
                </div>
                <div class="input-group">
                    <input type="submit" value="Request Password Change">
                </div>
            </form>
        </div>
    </div>
</div>

    <?php require '../reusable/footer.php'; ?>


<?php
require_once '../includes/session.php';
require_once '../includes/config.php';
require_once '../includes/Page.php';

// Set up page
Page::setTitle('My Account');
Page::setBodyClass('my-account-page');
Page::addStyle('../assets/css/myaccount.css');

// Get user data
$user_id = $_SESSION['user_id'];
$sql = "SELECT * FROM users WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if (!$result->num_rows) {
    header("Location: ../404.php");
    exit();
}

$userData = $result->fetch_assoc();

// Prepare content
$content = <<<HTML
    <div class="container-fluid">
        <div class="main-content">
            <h2>My Account</h2>
            <form action="./process/edit-account.process.php" method="post">
                <input type="hidden" name="user_id" value="{$user_id}">
                <div class="input-group">
                    <label for="email">Email:</label>
                    <input type="email" name="email" value="{$userData['useremail']}" required>
                </div>
                <div class="input-group">
                    <label for="username">Username:</label>
                    <input type="text" name="username" value="{$userData['username']}" required>
                </div>
                <div class="input-group">
                    <label for="role">Role:</label>
                    <select name="role" disabled>
                        <option value="superadmin" <?php echo ($userData['role'] == 'superadmin' ? 'selected' : ''); ?>>Super Admin</option>
                        <option value="admin" <?php echo ($userData['role'] == 'admin' ? 'selected' : ''); ?>>Admin</option>
                        <option value="supervisor" <?php echo ($userData['role'] == 'supervisor' ? 'selected' : ''); ?>>Supervisor</option>
                        <option value="cashier" <?php echo ($userData['role'] == 'cashier' ? 'selected' : ''); ?>>Cashier</option>
                    </select>
                </div>
                <div class="input-group">
                    <button type="submit" class="btn btn-primary">Update Profile</button>
                </div>
            </form>

            <form action="./process/request-password-change.process.php" method="post" class="password-change-form">
                <h3>Change Password</h3>
                <input type="hidden" name="user_id" value="{$user_id}">
                <div class="input-group">
                    <label for="oldpassword">Current Password:</label>
                    <input type="password" name="oldpassword" required>
                </div>
                <div class="input-group">
                    <button type="submit" class="btn btn-secondary">Request Password Change</button>
                </div>
            </form>
        </div>
    </div>
HTML;

// Render the page
Page::render($content);


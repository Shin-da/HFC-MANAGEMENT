<?php
require_once '../includes/config.php';
require_once 'access_control.php';
require_once '../includes/Page.php';

// Initialize page
Page::setTitle('My Account - HFC Admin');
Page::setBodyClass('admin-page');
Page::setCurrentPage('myaccount');
Page::setAdminPage(true);

// Add required styles
Page::addStyle('../assets/css/admin.css');
Page::addStyle('../assets/css/form.css');

// Add required scripts
Page::addScript('../assets/js/myaccount.js');

// Get admin data
try {
    $stmt = $GLOBALS['pdo']->prepare("
        SELECT u.*, d.department_name 
        FROM users u 
        LEFT JOIN departments d ON u.department = d.department_id 
        WHERE u.user_id = :user_id
    ");
    $stmt->execute([':user_id' => $_SESSION['user_id']]);
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$admin) {
        throw new Exception('Admin not found');
    }

    // Get recent activity
    $stmt = $GLOBALS['pdo']->prepare("
        SELECT * FROM admin_logs 
        WHERE admin_id = :admin_id 
        ORDER BY created_at DESC 
        LIMIT 5
    ");
    $stmt->execute([':admin_id' => $_SESSION['user_id']]);
    $recentActivity = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (Exception $e) {
    error_log("Error in myaccount.php: " . $e->getMessage());
    $error = "Failed to load admin data";
}

ob_start();
?>

<div class="container-fluid">
    <div class="row">
        <!-- Profile Information -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Profile Information</h3>
                </div>
                <div class="card-body">
                    <div class="text-center mb-4">
                        <img src="<?php echo $admin['profile_picture'] ?: '../assets/images/default-avatar.png'; ?>" 
                             class="rounded-circle" width="150" height="150" id="profilePreview">
                        <div class="mt-3">
                            <button type="button" class="btn btn-primary" onclick="document.getElementById('profilePicture').click()">
                                Change Picture
                            </button>
                            <input type="file" id="profilePicture" accept="image/*" style="display: none" 
                                   onchange="updateProfilePicture(this)">
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Username</label>
                        <input type="text" class="form-control" value="<?php echo htmlspecialchars($admin['username']); ?>" readonly>
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" class="form-control" value="<?php echo htmlspecialchars($admin['useremail']); ?>" readonly>
                    </div>
                    <div class="form-group">
                        <label>Department</label>
                        <input type="text" class="form-control" value="<?php echo htmlspecialchars($admin['department_name']); ?>" readonly>
                    </div>
                </div>
            </div>
        </div>

        <!-- Account Settings -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Account Settings</h3>
                </div>
                <div class="card-body">
                    <form id="updateProfileForm" method="POST" action="api/update-profile.php">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>First Name</label>
                                    <input type="text" class="form-control" name="first_name" 
                                           value="<?php echo htmlspecialchars($admin['first_name']); ?>" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Last Name</label>
                                    <input type="text" class="form-control" name="last_name" 
                                           value="<?php echo htmlspecialchars($admin['last_name']); ?>" required>
                                </div>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary">Update Profile</button>
                    </form>

                    <hr>

                    <h4>Change Password</h4>
                    <form id="changePasswordForm" method="POST" action="api/change-password.php">
                        <div class="form-group">
                            <label>Current Password</label>
                            <input type="password" class="form-control" name="current_password" required>
                        </div>
                        <div class="form-group">
                            <label>New Password</label>
                            <input type="password" class="form-control" name="new_password" required>
                        </div>
                        <div class="form-group">
                            <label>Confirm New Password</label>
                            <input type="password" class="form-control" name="confirm_password" required>
                        </div>
                        <button type="submit" class="btn btn-warning">Change Password</button>
                    </form>

                    <hr>

                    <h4>Security Settings</h4>
                    <div class="form-group">
                        <div class="custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" id="twoFactorSwitch" 
                                   <?php echo $admin['two_factor_enabled'] ? 'checked' : ''; ?>>
                            <label class="custom-control-label" for="twoFactorSwitch">Enable Two-Factor Authentication</label>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" id="emailNotificationsSwitch" 
                                   <?php echo $admin['email_notifications'] ? 'checked' : ''; ?>>
                            <label class="custom-control-label" for="emailNotificationsSwitch">Email Notifications</label>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="card mt-4">
                <div class="card-header">
                    <h3 class="card-title">Recent Activity</h3>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Action</th>
                                    <th>IP Address</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recentActivity as $activity): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($activity['action']); ?></td>
                                    <td><?php echo htmlspecialchars($activity['ip_address']); ?></td>
                                    <td><?php echo date('M d, Y H:i', strtotime($activity['created_at'])); ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Two-Factor Setup Modal -->
<div class="modal fade" id="twoFactorModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Set Up Two-Factor Authentication</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="text-center mb-4">
                    <img id="qrCode" src="" alt="QR Code" class="img-fluid">
                    <p class="mt-2">Scan this QR code with your authenticator app</p>
                    <p class="text-muted">Or enter this code manually: <span id="secretKey"></span></p>
                </div>
                <div class="form-group">
                    <label>Enter the 6-digit code from your authenticator app</label>
                    <input type="text" class="form-control" id="verificationCode" maxlength="6">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="verifyTwoFactor()">Verify & Enable</button>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
Page::render($content);
?>


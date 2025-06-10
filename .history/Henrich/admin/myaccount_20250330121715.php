<?php
require_once 'access_control.php';
require_once '../includes/Page.php';
require_once '../includes/functions.php';

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

ob_start();

// Fetch user data
try {
    $stmt = $GLOBALS['pdo']->prepare("SELECT * FROM users WHERE user_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        echo "<div class='alert alert-danger'>User not found.</div>";
        exit;
    }
} catch (PDOException $e) {
    echo "<div class='alert alert-danger'>Error: " . htmlspecialchars($e->getMessage()) . "</div>";
    exit;
}
?>

<div class="container-fluid">
    <section class="panel">
        <div class="container-fluid">
            <div class="form-header">
                <div class="title">
                    <span>
                        <h2>My Account</h2>
                    </span>
                    <span style="font-size: 12px;">Manage your account settings and profile</span>
                </div>
            </div>

            <div class="form-container">
                <form id="myAccountForm" method="POST" action="process/update-account.php" enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="profile-section">
                                <div class="profile-picture">
                                    <img src="<?php echo !empty($user['profile_picture']) ? htmlspecialchars($user['profile_picture']) : '../assets/images/default-profile.png'; ?>" 
                                         alt="Profile Picture" class="img-fluid rounded-circle">
                                    <div class="upload-overlay">
                                        <label for="profile_picture" class="upload-button">
                                            <i class="fas fa-camera"></i>
                                        </label>
                                        <input type="file" id="profile_picture" name="profile_picture" accept="image/*" style="display: none;">
                                    </div>
                                </div>
                                <h4 class="text-center mt-3"><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></h4>
                                <p class="text-center text-muted"><?php echo htmlspecialchars($user['role']); ?></p>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="account-details">
                                <h4>Account Information</h4>
                                <hr>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="username">Username</label>
                                            <input type="text" class="form-control" id="username" name="username" 
                                                   value="<?php echo htmlspecialchars($user['username']); ?>" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="email">Email</label>
                                            <input type="email" class="form-control" id="email" name="email" 
                                                   value="<?php echo htmlspecialchars($user['email']); ?>" required>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="first_name">First Name</label>
                                            <input type="text" class="form-control" id="first_name" name="first_name" 
                                                   value="<?php echo htmlspecialchars($user['first_name']); ?>" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="last_name">Last Name</label>
                                            <input type="text" class="form-control" id="last_name" name="last_name" 
                                                   value="<?php echo htmlspecialchars($user['last_name']); ?>" required>
                                        </div>
                                    </div>
                                </div>

                                <h4 class="mt-4">Change Password</h4>
                                <hr>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="current_password">Current Password</label>
                                            <div class="input-group">
                                                <input type="password" class="form-control" id="current_password" name="current_password">
                                                <div class="input-group-append">
                                                    <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('current_password')">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="new_password">New Password</label>
                                            <div class="input-group">
                                                <input type="password" class="form-control" id="new_password" name="new_password">
                                                <div class="input-group-append">
                                                    <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('new_password')">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="confirm_password">Confirm New Password</label>
                                            <div class="input-group">
                                                <input type="password" class="form-control" id="confirm_password" name="confirm_password">
                                                <div class="input-group-append">
                                                    <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('confirm_password')">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-actions mt-4">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i> Save Changes
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </section>
</div>

<script>
function togglePassword(fieldId) {
    const field = document.getElementById(fieldId);
    const button = field.nextElementSibling.querySelector('button');
    const icon = button.querySelector('i');
    
    if (field.type === 'password') {
        field.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        field.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}

document.getElementById('profile_picture').addEventListener('change', function(e) {
    if (e.target.files && e.target.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.querySelector('.profile-picture img').src = e.target.result;
        };
        reader.readAsDataURL(e.target.files[0]);
    }
});
</script>

<?php
$content = ob_get_clean();
Page::render($content);
?>


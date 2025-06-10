<?php
require_once 'access_control.php';
require_once '../includes/Page.php';
require_once '../includes/functions.php';

// Initialize page
Page::setTitle('Edit User - HFC Admin');
Page::setBodyClass('admin-page');
Page::setCurrentPage('manage-users');
Page::setAdminPage(true);

// Add required styles
Page::addStyle('../assets/css/admin.css');
Page::addStyle('../assets/css/form.css');

// Add required scripts
Page::addScript('../assets/js/edit-user.js');

ob_start();

// Get user ID from URL
$user_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Fetch user data
try {
    $stmt = $GLOBALS['pdo']->prepare("SELECT * FROM users WHERE user_id = ?");
    $stmt->execute([$user_id]);
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
                        <h2>Edit User</h2>
                    </span>
                    <span style="font-size: 12px;">Edit user information and permissions</span>
                </div>
                <div class="actions">
                    <button type="button" class="btn btn-secondary" onclick="history.back()">
                        <i class="fas fa-arrow-left"></i> Back
                    </button>
                </div>
            </div>

            <div class="form-container">
                <form id="editUserForm" method="POST" action="process/update-user.php" enctype="multipart/form-data">
                    <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($user['user_id']); ?>">
                    
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

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="role">Role</label>
                                <select class="form-control" id="role" name="role" required>
                                    <option value="user" <?php echo $user['role'] === 'user' ? 'selected' : ''; ?>>User</option>
                                    <option value="supervisor" <?php echo $user['role'] === 'supervisor' ? 'selected' : ''; ?>>Supervisor</option>
                                    <option value="admin" <?php echo $user['role'] === 'admin' ? 'selected' : ''; ?>>Admin</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="department">Department</label>
                                <select class="form-control" id="department" name="department" required>
                                    <option value="">Select Department</option>
                                    <?php
                                    try {
                                        $stmt = $GLOBALS['pdo']->query("SELECT * FROM departments ORDER BY department_name");
                                        $departments = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                        foreach ($departments as $dept) {
                                            $selected = $user['department'] === $dept['department_id'] ? 'selected' : '';
                                            echo "<option value='" . htmlspecialchars($dept['department_id']) . "' {$selected}>" 
                                                . htmlspecialchars($dept['department_name']) . "</option>";
                                        }
                                    } catch (PDOException $e) {
                                        echo "<option value=''>Error loading departments</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="status">Status</label>
                                <select class="form-control" id="status" name="status" required>
                                    <option value="active" <?php echo $user['status'] === 'active' ? 'selected' : ''; ?>>Active</option>
                                    <option value="inactive" <?php echo $user['status'] === 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="profile_picture">Profile Picture</label>
                                <input type="file" class="form-control" id="profile_picture" name="profile_picture" accept="image/*">
                                <?php if (!empty($user['profile_picture'])): ?>
                                <div class="current-image mt-2">
                                    <img src="<?php echo htmlspecialchars($user['profile_picture']); ?>" alt="Current profile picture" class="img-thumbnail" style="max-width: 100px;">
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="reset_password" name="reset_password">
                            <label class="custom-control-label" for="reset_password">Reset Password</label>
                            <small class="form-text text-muted">Check this to generate a new password for the user</small>
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Save Changes
                        </button>
                        <button type="button" class="btn btn-danger" onclick="deleteUser(<?php echo $user['user_id']; ?>)">
                            <i class="fas fa-trash"></i> Delete User
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </section>
</div>

<script>
function deleteUser(userId) {
    if (confirm('Are you sure you want to delete this user? This action cannot be undone.')) {
        fetch('process/delete-user.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'user_id=' + userId
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('User deleted successfully');
                window.location.href = 'manage-users.php';
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while deleting the user');
        });
    }
}
</script>

<?php
$content = ob_get_clean();
Page::render($content);
?>
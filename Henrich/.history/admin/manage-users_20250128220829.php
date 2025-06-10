<?php
require_once 'access_control.php';
require_once '../includes/Page.php';
require_once '../includes/functions.php';

// Initialize page
Page::setTitle('Manage Users - HFC Admin');
Page::setBodyClass('admin-page');
Page::setCurrentPage('manage-users');

// Add required styles & scripts
Page::addStyle('/assets/css/admin.css');
Page::addStyle('/assets/css/admin-forms.css');
Page::addStyle('/assets/css/manage-users.css');
Page::addScript('/assets/js/user-management.js');

ob_start();

// Process form submissions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'];
    
    switch($action) {
        case 'add':
            $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
            $fname = htmlspecialchars($_POST['first_name'], ENT_QUOTES, 'UTF-8');
            $lname = htmlspecialchars($_POST['last_name'], ENT_QUOTES, 'UTF-8');
            $role = htmlspecialchars($_POST['role'], ENT_QUOTES, 'UTF-8');
            $username = strtolower($fname[0] . $lname);
            $temp_password = bin2hex(random_bytes(8));
            
            $stmt = $conn->prepare("INSERT INTO users (username, useremail, first_name, last_name, department, role, password, status) VALUES (?, ?, ?, ?, ?, ?, ?, 'active')");
            $stmt->bind_param("sssssss", $username, $email, $fname, $lname, $_POST['department'], $role, $temp_password);
            
            if ($stmt->execute()) {
                mail($email, "Account Created", "Your account has been created.\nUsername: $username\nTemporary password: $temp_password");
                $_SESSION['success'] = "User added successfully";
            } else {
                $_SESSION['error'] = "Error adding user";
            }
            break;
            
        // ... existing status update code ...
    }
}

// Get users with role filter
$role_filter = isset($_GET['role']) ? $_GET['role'] : 'all';
$query = "SELECT * FROM users";
if ($role_filter !== 'all') {
    $query .= " WHERE role = ?";
}
$query .= " ORDER BY created_at DESC";

if ($role_filter !== 'all') {
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $role_filter);
    $stmt->execute();
    $users = $stmt->get_result();
} else {
    $users = $conn->query($query);
}
?>

<div class="dashboard-wrapper  users-container">
    <div class="users-header">
        <div>
            <h2>Manage Users</h2>
            <p class="text-muted">Total users: <?= $users->num_rows ?> • Last updated: <?= date('M j, Y') ?></p>
        </div>
        <div class="header-actions">
            <div class="view-toggle">
                <button class="btn-view-grid active" onclick="toggleView('grid')">
                    <i class='bx bx-grid-alt'></i>
                </button>
                <button class="btn-view-list" onclick="toggleView('list')">
                    <i class='bx bx-list-ul'></i>
                </button>
            </div>
            <button class="btn btn-primary" onclick="toggleAddForm()">
                <i class='bx bx-user-plus'></i>
                Add New User
            </button>
        </div>
    </div>

    <div class="filter-toolbar">
        <div class="search-box">
            <i class='bx bx-search'></i>
            <input type="text" 
                   class="search-input" 
                   placeholder="Search by name, email or department..." 
                   aria-label="Search users"
                   onkeyup="filterUsers(this.value)">
        </div>
        <div class="filter-controls">
            <select class="role-filter" onchange="filterByRole(this.value)">
                <option value="all">All Roles</option>
                <option value="admin">Administrators</option>
                <option value="supervisor">Supervisors</option>
                <option value="employee">Employees</option>
            </select>
            <select class="status-filter" onchange="filterByStatus(this.value)">
                <option value="all">All Status</option>
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
            </select>
        </div>
    </div>

    <div class="users-grid" id="usersContainer">
        <div class="loading-spinner" id="loadingSpinner">
            <i class='bx bx-loader-alt bx-spin'></i>
        </div>
        <?php while ($user = $users->fetch_assoc()): ?>
            <div class="user-card animate-in" data-role="<?= $user['role'] ?>" data-status="<?= $user['status'] ?>">
                <div class="user-header">
                    <div class="user-avatar">
                        <i class="fas <?= getRoleIcon($user['role']) ?>"></i>
                    </div>
                    <div class="user-status <?= $user['status'] ?>">
                        <span class="status-dot"></span>
                        <?= ucfirst($user['status']) ?>
                    </div>
                </div>
                <div class="user-info">
                    <h3 title="<?= htmlspecialchars($user['first_name'] ?? '') ?> <?= htmlspecialchars($user['last_name'] ?? '') ?>">
                        <?= htmlspecialchars($user['first_name'] ?? '') ?> <?= htmlspecialchars($user['last_name'] ?? '') ?>
                    </h3>
                    <p title="Username"><i class="fas fa-user"></i> <span><?= htmlspecialchars($user['username'] ?? '') ?></span></p>
                    <p title="Email"><i class="fas fa-envelope"></i> <span><?= htmlspecialchars($user['useremail'] ?? '') ?></span></p>
                    <p title="Role"><i class="fas fa-user-tag"></i> <span><?= ucfirst(htmlspecialchars($user['role'] ?? '')) ?></span></p>
                    <?php if (!empty($user['department'])): ?>
                        <p title="Department"><i class="fas fa-building"></i> <span><?= htmlspecialchars($user['department']) ?></span></p>
                    <?php endif; ?>
                </div>
                <div class="user-actions">
                    <?php if ($user['role'] !== 'admin' || $_SESSION['user_id'] == $user['user_id']): ?>
                    <button class="btn btn-edit" onclick="editUser(<?= $user['user_id'] ?>)">
                        <i class="fas fa-edit"></i>
                    </button>
                    <label class="switch">
                        <input type="checkbox" class="status-toggle" 
                               data-user-id="<?= $user['user_id'] ?>"
                               <?= $user['status'] === 'active' ? 'checked' : '' ?>>
                        <span class="slider round"></span>
                    </label>
                    <?php endif; ?>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
    
    <!-- Enhanced Modal -->
    <div class="modal" id="addUserModal">
        <div class="modal-backdrop" onclick="toggleAddForm()"></div>
        <div class="modal-content">
            <div class="modal-header">
                <h3>Add New User</h3>
                <button class="close-btn" onclick="toggleAddForm()">
                    <i class='bx bx-x'></i>
                </button>
            </div>
            <form method="POST" class="admin-form" id="addUserForm">
                <input type="hidden" name="action" value="add">
                <div class="form-grid">
                    <!-- ... existing form fields ... -->
                    <div class="form-group">
                        <label><i class="fas fa-user-tag"></i> Role</label>
                        <select name="role" required>
                            <option value="employee">Employee</option>
                            <option value="supervisor">Supervisor</option>
                            <option value="admin">Administrator</option>
                        </select>
                    </div>
                </div>
                <div class="form-actions">
                    <button type="submit" class="btn btn-success">Add User</button>
                    <button type="button" class="btn btn-secondary" onclick="toggleAddForm()">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
Page::render($content);

function getRoleIcon($role) {
    switch ($role) {
        case 'admin':
            return 'fa-user-shield';
        case 'supervisor':
            return 'fa-user-tie';
        default:
            return 'fa-user';
    }
}
?>

<script>
function toggleView(view) {
    const container = document.getElementById('usersContainer');
    const gridBtn = document.querySelector('.btn-view-grid');
    const listBtn = document.querySelector('.btn-view-list');
    
    if (view === 'grid') {
        container.classList.remove('view-list');
        gridBtn.classList.add('active');
        listBtn.classList.remove('active');
    } else {
        container.classList.add('view-list');
        listBtn.classList.add('active');
        gridBtn.classList.remove('active');
    }
    
    localStorage.setItem('usersViewPreference', view);
}

// Restore user's view preference
document.addEventListener('DOMContentLoaded', () => {
    const savedView = localStorage.getItem('usersViewPreference');
    if (savedView) {
        toggleView(savedView);
    }
});
</script>

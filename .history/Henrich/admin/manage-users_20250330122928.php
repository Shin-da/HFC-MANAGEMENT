<?php
require_once 'access_control.php';
require_once '../includes/Page.php';
require_once '../includes/functions.php';

// Initialize page
Page::setTitle('Manage Users - HFC Admin');
Page::setBodyClass('admin-page');
Page::setCurrentPage('manage-users');
Page::setAdminPage(true);

// Add required styles & scripts
Page::addStyle('../assets/css/variables.css');
Page::addStyle('../assets/css/style.css');
Page::addStyle('../assets/css/sidebar.css');
Page::addStyle('../assets/css/navbar.css');
Page::addStyle('../assets/css/theme.css');
Page::addStyle('../assets/css/admin-navbar.css');
Page::addStyle('../assets/css/dashboard.css');
Page::addStyle('../assets/css/admin.css');
Page::addStyle('../assets/css/admin-dashboard.css');
Page::addStyle('../assets/css/shared-dashboard.css');
Page::addStyle('../assets/css/admin-layout.css');
Page::addStyle('../assets/css/table.css');
Page::addStyle('../assets/css/form.css');
Page::addStyle('../assets/css/components/notification-dropdown.css');
Page::addStyle('../assets/css/components/message-dropdown.css');

Page::addScript('../assets/js/admin-dashboard.js');
Page::addScript('../assets/js/form.js');
Page::addScript('../assets/js/layout-init.js');
Page::addScript('../assets/js/layout-manager.js');
Page::addScript('../assets/js/notification-handler.js');
Page::addScript('../assets/js/notification-dropdown.js');
Page::addScript('../assets/js/sidebar-dropdown.js');
Page::addScript('../assets/js/sidebar.js');
Page::addScript('../assets/js/theme.js');
Page::addScript('../assets/js/theme-manager.js');
Page::addScript('../assets/js/navbar.js');
Page::addScript('../assets/js/user-management.js');

ob_start();

// Pagination settings
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 10;
$offset = ($page - 1) * $limit;

// Search and filter parameters
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$role = isset($_GET['role']) ? trim($_GET['role']) : '';
$status = isset($_GET['status']) ? trim($_GET['status']) : '';

// Build the query
$where_conditions = [];
$params = [];

if ($search) {
    $where_conditions[] = "(username LIKE ? OR useremail LIKE ? OR first_name LIKE ? OR last_name LIKE ?)";
    $search_param = "%$search%";
    $params = array_merge($params, [$search_param, $search_param, $search_param, $search_param]);
}

if ($role) {
    $where_conditions[] = "role = ?";
    $params[] = $role;
}

if ($status) {
    $where_conditions[] = "status = ?";
    $params[] = $status;
}

$where_clause = !empty($where_conditions) ? "WHERE " . implode(" AND ", $where_conditions) : "";

// Get total count for pagination
$count_sql = "SELECT COUNT(*) as total FROM users $where_clause";
$stmt = $pdo->prepare($count_sql);
$stmt->execute($params);
$total_users = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
$total_pages = ceil($total_users / $limit);

// Get users for current page
$sql = "SELECT * FROM users $where_clause ORDER BY created_at DESC LIMIT ? OFFSET ?";
$stmt = $pdo->prepare($sql);
$params[] = $limit;
$params[] = $offset;
$stmt->execute($params);
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="content-wrapper">
    <div class="content-header">
        <div class="header-left">
            <h1>Manage Users</h1>
            <p>View and manage system users</p>
        </div>
        <div class="header-right">
            <button class="btn btn-primary" onclick="showAddUserModal()">
                <i class="fas fa-plus"></i> Add New User
            </button>
        </div>
    </div>

    <div class="content-body">
        <!-- Search and Filter Section -->
        <div class="card">
            <div class="card-body">
                <form method="GET" class="search-filter-form">
                    <div class="form-row">
                        <div class="form-group col-md-4">
                            <input type="text" name="search" class="form-control" 
                                   placeholder="Search users..." value="<?php echo htmlspecialchars($search); ?>">
                        </div>
                        <div class="form-group col-md-3">
                            <select name="role" class="form-control">
                                <option value="">All Roles</option>
                                <option value="admin" <?php echo $role === 'admin' ? 'selected' : ''; ?>>Admin</option>
                                <option value="supervisor" <?php echo $role === 'supervisor' ? 'selected' : ''; ?>>Supervisor</option>
                                <option value="ceo" <?php echo $role === 'ceo' ? 'selected' : ''; ?>>CEO</option>
                            </select>
                        </div>
                        <div class="form-group col-md-3">
                            <select name="status" class="form-control">
                                <option value="">All Status</option>
                                <option value="active" <?php echo $status === 'active' ? 'selected' : ''; ?>>Active</option>
                                <option value="inactive" <?php echo $status === 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                                <option value="suspended" <?php echo $status === 'suspended' ? 'selected' : ''; ?>>Suspended</option>
                            </select>
                        </div>
                        <div class="form-group col-md-2">
                            <button type="submit" class="btn btn-primary btn-block">Search</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Users Table -->
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Username</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Last Online</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                            <?php foreach ($users as $user): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($user['username']); ?></td>
                                <td><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></td>
                                <td><?php echo htmlspecialchars($user['useremail']); ?></td>
                                <td>
                                    <span class="badge badge-<?php echo getRoleBadgeClass($user['role']); ?>">
                                        <?php echo ucfirst($user['role']); ?>
                            </span>
                        </td>
                        <td>
                                    <span class="badge badge-<?php echo getStatusBadgeClass($user['status']); ?>">
                                        <?php echo ucfirst($user['status']); ?>
                            </span>
                        </td>
                        <td>
                                    <?php 
                                    if ($user['last_online']) {
                                        echo date('M d, Y H:i', strtotime($user['last_online']));
                                    } else {
                                        echo 'Never';
                                    }
                                    ?>
                        </td>
                        <td>
                                    <div class="btn-group">
                                        <button class="btn btn-sm btn-info" onclick="editUser(<?php echo $user['user_id']; ?>)">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-sm btn-warning" onclick="resetPassword(<?php echo $user['user_id']; ?>)">
                                            <i class="fas fa-key"></i>
                                        </button>
                                        <button class="btn btn-sm btn-danger" onclick="deleteUser(<?php echo $user['user_id']; ?>)">
                                            <i class="fas fa-trash"></i>
                            </button>
                                    </div>
                        </td>
                    </tr>
                            <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        
                <!-- Pagination -->
                <?php if ($total_pages > 1): ?>
                <nav aria-label="Page navigation" class="mt-4">
                    <ul class="pagination justify-content-center">
                        <?php if ($page > 1): ?>
                        <li class="page-item">
                            <a class="page-link" href="?page=<?php echo $page-1; ?>&search=<?php echo urlencode($search); ?>&role=<?php echo urlencode($role); ?>&status=<?php echo urlencode($status); ?>">
                                Previous
                            </a>
                        </li>
                        <?php endif; ?>
                        
                        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                        <li class="page-item <?php echo $i === $page ? 'active' : ''; ?>">
                            <a class="page-link" href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>&role=<?php echo urlencode($role); ?>&status=<?php echo urlencode($status); ?>">
                                <?php echo $i; ?>
                            </a>
                        </li>
                        <?php endfor; ?>
                        
                        <?php if ($page < $total_pages): ?>
                        <li class="page-item">
                            <a class="page-link" href="?page=<?php echo $page+1; ?>&search=<?php echo urlencode($search); ?>&role=<?php echo urlencode($role); ?>&status=<?php echo urlencode($status); ?>">
                                Next
                            </a>
                        </li>
                        <?php endif; ?>
                    </ul>
                </nav>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Add/Edit User Modal -->
<div class="modal fade" id="userModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New User</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
                <div class="modal-body">
                <form id="userForm">
                    <input type="hidden" id="userId" name="userId">
                    <div class="form-group">
                        <label>Username</label>
                        <input type="text" class="form-control" id="username" name="username" required>
                        </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" class="form-control" id="useremail" name="useremail" required>
                        </div>
                    <div class="form-group">
                        <label>First Name</label>
                        <input type="text" class="form-control" id="firstName" name="firstName" required>
                    </div>
                    <div class="form-group">
                        <label>Last Name</label>
                        <input type="text" class="form-control" id="lastName" name="lastName" required>
                    </div>
                    <div class="form-group">
                        <label>Role</label>
                        <select class="form-control" id="role" name="role" required>
                                <option value="admin">Admin</option>
                                <option value="supervisor">Supervisor</option>
                                <option value="ceo">CEO</option>
                            </select>
                        </div>
                    <div class="form-group">
                        <label>Department</label>
                        <input type="text" class="form-control" id="department" name="department">
                    </div>
                    <div class="form-group">
                        <label>Status</label>
                        <select class="form-control" id="status" name="status" required>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                            <option value="suspended">Suspended</option>
                        </select>
                    </div>
                    <div class="form-group password-group">
                        <label>Password</label>
                        <input type="password" class="form-control" id="password" name="password">
                        <small class="form-text text-muted">Leave blank to keep current password when editing</small>
                </div>
            </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="saveUser()">Save</button>
            </div>
        </div>
    </div>
</div>

<?php
// Helper functions
function getRoleBadgeClass($role) {
    switch ($role) {
        case 'admin':
            return 'danger';
        case 'supervisor':
            return 'warning';
        case 'ceo':
            return 'info';
        default:
            return 'secondary';
    }
}

function getStatusBadgeClass($status) {
    switch ($status) {
        case 'active':
            return 'success';
        case 'inactive':
            return 'secondary';
        case 'suspended':
            return 'danger';
        default:
            return 'secondary';
    }
}

$content = ob_get_clean();
Page::render($content);
?>

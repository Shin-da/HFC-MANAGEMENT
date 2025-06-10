<?php
require_once 'access_control.php';
require_once '../includes/Page.php';
require_once '../includes/functions.php';

// Initialize page
Page::setTitle('Manage Supervisors');
Page::setBodyClass('admin-page');
Page::setCurrentPage('manage-supervisors');
Page::setAdminPage(true);
Page::setPageDescription('Manage and monitor all supervisor accounts');

// Add required styles
Page::addStyle('../assets/css/admin.css');
Page::addStyle('../assets/css/table.css');
Page::addStyle('../assets/css/admin-layout.css');

// Add required scripts
Page::addScript('../assets/js/supervisor-management.js');
Page::addScript('https://cdn.jsdelivr.net/npm/sweetalert2@11'); // For better alerts

ob_start();

// Process form submissions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        $action = $_POST['action'];
        
        switch($action) {
            case 'add':
                $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
                $fname = filter_var($_POST['first_name'], FILTER_SANITIZE_STRING);
                $lname = filter_var($_POST['last_name'], FILTER_SANITIZE_STRING);
                $username = strtolower($fname[0] . $lname); // Create username from first initial and last name
                
                $password = $_POST['password'];
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                
                $stmt = $GLOBALS['pdo']->prepare("
                    INSERT INTO users (username, useremail, first_name, last_name, role, password, department, status) 
                    VALUES (:username, :email, :fname, :lname, 'supervisor', :password, :department, 'active')
                ");
                
                $stmt->execute([
                    ':username' => $username,
                    ':email' => $email,
                    ':fname' => $fname,
                    ':lname' => $lname,
                    ':password' => $hashed_password,
                    ':department' => $_POST['department']
                ]);
                
                // Send email with credentials
                mail($email, "Account Created", "Your account has been created.\nUsername: $username\nPassword: $password");
                $_SESSION['success'] = "Supervisor added successfully";
                break;
                
            case 'update_status':
                $stmt = $GLOBALS['pdo']->prepare("
                    UPDATE users 
                    SET status = :status 
                    WHERE user_id = :user_id AND role = 'supervisor'
                ");
                
                $stmt->execute([
                    ':status' => $_POST['status'],
                    ':user_id' => $_POST['user_id']
                ]);
                break;

            case 'update':
                $params = [
                    ':email' => filter_var($_POST['email'], FILTER_SANITIZE_EMAIL),
                    ':fname' => filter_var($_POST['first_name'], FILTER_SANITIZE_STRING),
                    ':lname' => filter_var($_POST['last_name'], FILTER_SANITIZE_STRING),
                    ':department' => filter_var($_POST['department'], FILTER_SANITIZE_STRING),
                    ':user_id' => $_POST['user_id']
                ];
                
                $sql = "UPDATE users SET useremail = :email, first_name = :fname, last_name = :lname, department = :department";
                
                if (!empty($_POST['password'])) {
                    $sql .= ", password = :password";
                    $params[':password'] = password_hash($_POST['password'], PASSWORD_DEFAULT);
                }
                
                $sql .= " WHERE user_id = :user_id AND role = 'supervisor'";
                
                $stmt = $GLOBALS['pdo']->prepare($sql);
                $stmt->execute($params);
                
                $_SESSION['success'] = "Supervisor updated successfully";
                break;
        }
    } catch (PDOException $e) {
        error_log("Error in manage-supervisors.php: " . $e->getMessage());
        $_SESSION['error'] = "Operation failed: " . $e->getMessage();
    }
}

// Get supervisors with pagination
try {
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $limit = 10;
    $offset = ($page - 1) * $limit;

    // Get total count
    $countStmt = $GLOBALS['pdo']->query("SELECT COUNT(*) FROM users WHERE role = 'supervisor'");
    $total_records = $countStmt->fetchColumn();
    $total_pages = ceil($total_records / $limit);

    // Get supervisors with pagination
    $stmt = $GLOBALS['pdo']->prepare("
        SELECT * FROM users 
        WHERE role = 'supervisor' 
        ORDER BY created_at DESC 
        LIMIT :limit OFFSET :offset
    ");
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $supervisors = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Error in manage-supervisors.php: " . $e->getMessage());
    $_SESSION['error'] = "Error loading supervisors";
    $supervisors = [];
    $total_pages = 0;
}

// Set page variables for the template
Page::setPageVariable('headerActions', '
    <button class="btn btn-primary" onclick="showAddSupervisorModal()">
        <i class="fas fa-plus"></i> Add New Supervisor
    </button>
');

Page::setPageVariable('filterSection', '
    <div class="filter-group">
        <div class="search-box">
            <input type="text" id="searchInput" class="form-control" placeholder="Search supervisors...">
            <i class="fas fa-search"></i>
        </div>
        <select id="statusFilter" class="form-control">
            <option value="">All Status</option>
            <option value="active">Active</option>
            <option value="inactive">Inactive</option>
        </select>
    </div>
');

// Main content
$mainContent = '
<div class="table-container">
    <table class="admin-table">
        <thead>
            <tr>
                <th>Profile Picture</th>
                <th>Supervisor ID</th>
                <th>Username</th>
                <th>Email</th>
                <th>First Name</th>
                <th>Last Name</th>
                <th>Department</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody id="supervisorTableBody">';

if (count($supervisors) > 0) {
    foreach ($supervisors as $row) {
        $statusClass = $row['status'] === 'active' ? 'success' : 'danger';
        
        $mainContent .= "
            <tr>
                <td><img src='" . htmlspecialchars($row['profile_picture'] ?? '../assets/images/default-avatar.png') . "' alt='Profile' class='profile-thumb'></td>
                <td>" . htmlspecialchars($row['user_id']) . "</td>
                <td>" . htmlspecialchars($row['username']) . "</td>
                <td>" . htmlspecialchars($row['useremail']) . "</td>
                <td>" . htmlspecialchars($row['first_name']) . "</td>
                <td>" . htmlspecialchars($row['last_name']) . "</td>
                <td>" . htmlspecialchars($row['department']) . "</td>
                <td><span class='badge badge-{$statusClass}'>" . htmlspecialchars($row['status']) . "</span></td>
                <td class='actions'>
                    <button class='btn btn-sm btn-info' onclick='editSupervisor(" . $row['user_id'] . ")' title='Edit'>
                        <i class='fas fa-edit'></i>
                    </button>
                    <button class='btn btn-sm btn-warning' onclick='resetPassword(" . $row['user_id'] . ")' title='Reset Password'>
                        <i class='fas fa-key'></i>
                    </button>
                    <button class='btn btn-sm btn-danger' onclick='deleteSupervisor(" . $row['user_id'] . ")' title='Delete'>
                        <i class='fas fa-trash'></i>
                    </button>
                </td>
            </tr>";
    }
} else {
    $mainContent .= "<tr><td colspan='9' class='text-center'>No supervisors found</td></tr>";
}

$mainContent .= '
        </tbody>
    </table>';

if ($total_pages > 1) {
    $mainContent .= '
    <nav aria-label="Page navigation" class="mt-4">
        <ul class="pagination justify-content-center">';
    
    for ($i = 1; $i <= $total_pages; $i++) {
        $mainContent .= '
            <li class="page-item ' . ($i === $page ? 'active' : '') . '">
                <a class="page-link" href="?page=' . $i . '">' . $i . '</a>
            </li>';
    }
    
    $mainContent .= '
        </ul>
    </nav>';
}

$mainContent .= '
</div>';

// Add the modal
$mainContent .= '
<!-- Add/Edit Supervisor Modal -->
<div class="modal fade" id="supervisorModal" tabindex="-1" role="dialog" aria-labelledby="supervisorModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="supervisorModalLabel">Add New Supervisor</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="supervisorForm" class="admin-form">
                    <input type="hidden" id="supervisorId">
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="username">Username</label>
                            <input type="text" class="form-control" id="username" required>
                        </div>
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" class="form-control" id="email" required>
                        </div>
                        <div class="form-group">
                            <label for="firstName">First Name</label>
                            <input type="text" class="form-control" id="firstName" required>
                        </div>
                        <div class="form-group">
                            <label for="lastName">Last Name</label>
                            <input type="text" class="form-control" id="lastName" required>
                        </div>
                        <div class="form-group">
                            <label for="department">Department</label>
                            <input type="text" class="form-control" id="department" required>
                        </div>
                        <div class="form-group">
                            <label for="status">Status</label>
                            <select class="form-control" id="status" required>
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="password">Password</label>
                            <input type="password" class="form-control" id="password">
                            <small class="form-text text-muted">Leave blank to keep current password when editing</small>
                        </div>
                        <div class="form-group">
                            <label for="profilePicture">Profile Picture</label>
                            <input type="file" class="form-control-file" id="profilePicture" accept="image/*">
                            <small class="form-text text-muted">Leave empty to keep current picture when editing</small>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" onclick="saveSupervisor()">Save Supervisor</button>
            </div>
        </div>
    </div>
</div>';

Page::setPageVariable('mainContent', $mainContent);

// Render the page
$content = ob_get_clean();
Page::render($content);
?>

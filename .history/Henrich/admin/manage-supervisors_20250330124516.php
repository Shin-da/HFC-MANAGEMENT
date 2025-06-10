<?php
require_once 'access_control.php';
require_once '../includes/Page.php';
require_once '../includes/functions.php';

// Initialize page
Page::setTitle('Manage Supervisors - HFC Admin');
Page::setBodyClass('admin-page');
Page::setCurrentPage('manage-supervisors');
Page::setAdminPage(true);

// Add required styles
Page::addStyle('../assets/css/admin.css');
Page::addStyle('../assets/css/table.css');

// Add required scripts
Page::addScript('../assets/js/supervisor-management.js');

ob_start();

// Process form submissions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'];
    
    switch($action) {
        case 'add':
            $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
            $fname = filter_var($_POST['first_name'], FILTER_SANITIZE_STRING);
            $lname = filter_var($_POST['last_name'], FILTER_SANITIZE_STRING);
            $username = strtolower($fname[0] . $lname); // Create username from first initial and last name
            
            $password = $_POST['password']; // Get password from form
            $hashed_password = password_hash($password, PASSWORD_DEFAULT); // Hash the password
            
            $stmt = $conn->prepare("INSERT INTO users (username, useremail, first_name, last_name, role, password, department, status) VALUES (?, ?, ?, ?, 'supervisor', ?, ?, 'active')");
            $stmt->bind_param("ssssss", $username, $email, $fname, $lname, $hashed_password, $_POST['department']);
            
            if ($stmt->execute()) {
                // Send email with credentials
                mail($email, "Account Created", "Your account has been created.\nUsername: $username\nPassword: $password");
                $_SESSION['success'] = "Supervisor added successfully";
            } else {
                $_SESSION['error'] = "Error adding supervisor";
            }
            break;
            
        case 'update_status':
            $user_id = $_POST['user_id'];
            $status = $_POST['status'];
            $stmt = $conn->prepare("UPDATE users SET status = ? WHERE user_id = ? AND role = 'supervisor'");
            $stmt->bind_param("si", $status, $user_id);
            $stmt->execute();
            break;

        case 'update':
            $user_id = $_POST['user_id'];
            $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
            $fname = filter_var($_POST['first_name'], FILTER_SANITIZE_STRING);
            $lname = filter_var($_POST['last_name'], FILTER_SANITIZE_STRING);
            $department = filter_var($_POST['department'], FILTER_SANITIZE_STRING);
            
            $sql = "UPDATE users SET useremail = ?, first_name = ?, last_name = ?, department = ?";
            $params = array($email, $fname, $lname, $department);
            $types = "ssss";
            
            // If password is provided, update it
            if (!empty($_POST['password'])) {
                $hashed_password = password_hash($_POST['password'], PASSWORD_DEFAULT);
                $sql .= ", password = ?";
                $params[] = $hashed_password;
                $types .= "s";
            }
            
            $sql .= " WHERE user_id = ? AND role = 'supervisor'";
            $params[] = $user_id;
            $types .= "i";
            
            $stmt = $conn->prepare($sql);
            $stmt->bind_param($types, ...$params);
            
            if ($stmt->execute()) {
                $_SESSION['success'] = "Supervisor updated successfully";
            } else {
                $_SESSION['error'] = "Error updating supervisor";
            }
            break;
    }
}

// Get supervisors with pagination
try {
    // Pagination
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
?>

<div class="container-fluid">
    <section class="panel">
        <div class="container-fluid">
            <div class="table-header">
                <div class="title">
                    <span>
                        <h2>Manage Supervisors</h2>
                    </span>
                    <span style="font-size: 12px;">List of all supervisors</span>
                </div>
                <div class="title">
                    <span><?php echo date('l, F jS'); ?></span>
                </div>
            </div>

            <!-- Search and Filter Section -->
            <div class="search-filter-section">
                <div class="row">
                    <div class="col-md-4">
                        <div class="input-group">
                            <input type="text" id="searchInput" class="form-control" placeholder="Search supervisors...">
                            <div class="input-group-append">
                                <button class="btn btn-outline-secondary" type="button" id="searchButton">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <select id="statusFilter" class="form-control">
                            <option value="">All Status</option>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                    <div class="col-md-4 text-right">
                        <button class="btn btn-primary" onclick="showAddSupervisorModal()">
                            <i class="fas fa-plus"></i> Add New Supervisor
                        </button>
                    </div>
                </div>
            </div>

            <div class="table-container">
                <table class="table-striped">
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
                    <tbody id="supervisorTableBody">
                        <?php
                        try {
                            // Pagination
                            $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
                            $limit = 10;
                            $offset = ($page - 1) * $limit;

                            // Get total count
                            $countStmt = $GLOBALS['pdo']->query("SELECT COUNT(*) FROM users WHERE role = 'supervisor'");
                            $total_records = $countStmt->fetchColumn();
                            $total_pages = ceil($total_records / $limit);

                            // Get supervisors with pagination
                            $stmt = $GLOBALS['pdo']->prepare("SELECT * FROM users WHERE role = 'supervisor' LIMIT ? OFFSET ?");
                            $stmt->execute([$limit, $offset]);
                            $supervisors = $stmt->fetchAll(PDO::FETCH_ASSOC);

                            if (count($supervisors) > 0) {
                                foreach ($supervisors as $row) {
                                    $statusClass = $row['status'] === 'active' ? 'success' : 'danger';
                                    
                                    echo "<tr>
                                        <td><img src='" . htmlspecialchars($row['profile_picture'] ?? '../assets/images/default-avatar.png') . "' alt='Profile Picture' width='50' height='50'></td>
                                        <td>" . htmlspecialchars($row['user_id']) . "</td>
                                        <td>" . htmlspecialchars($row['username']) . "</td>
                                        <td>" . htmlspecialchars($row['useremail']) . "</td>
                                        <td>" . htmlspecialchars($row['first_name']) . "</td>
                                        <td>" . htmlspecialchars($row['last_name']) . "</td>
                                        <td>" . htmlspecialchars($row['department']) . "</td>
                                        <td><span class='badge badge-{$statusClass}'>" . htmlspecialchars($row['status']) . "</span></td>
                                        <td>
                                            <button class='btn btn-sm btn-info' onclick='editSupervisor(" . $row['user_id'] . ")'><i class='fas fa-edit'></i></button>
                                            <button class='btn btn-sm btn-warning' onclick='resetPassword(" . $row['user_id'] . ")'><i class='fas fa-key'></i></button>
                                            <button class='btn btn-sm btn-danger' onclick='deleteSupervisor(" . $row['user_id'] . ")'><i class='fas fa-trash'></i></button>
                                        </td>
                                    </tr>";
                                }
                            } else {
                                echo "<tr><td colspan='9'>0 results</td></tr>";
                            }
                        } catch (PDOException $e) {
                            echo "<tr><td colspan='9'>Error: " . htmlspecialchars($e->getMessage()) . "</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>

                <!-- Pagination -->
                <?php if ($total_pages > 1): ?>
                <nav aria-label="Page navigation" class="mt-3">
                    <ul class="pagination justify-content-center">
                        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                            <li class="page-item <?php echo $i === $page ? 'active' : ''; ?>">
                                <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                            </li>
                        <?php endfor; ?>
                    </ul>
                </nav>
                <?php endif; ?>
            </div>
        </div>
    </section>
</div>

<!-- Add/Edit Supervisor Modal -->
<div class="modal fade" id="supervisorModal" tabindex="-1" role="dialog" aria-labelledby="supervisorModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="supervisorModalLabel">Add New Supervisor</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="supervisorForm">
                    <input type="hidden" id="supervisorId">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="username">Username</label>
                                <input type="text" class="form-control" id="username" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="email">Email</label>
                                <input type="email" class="form-control" id="email" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="firstName">First Name</label>
                                <input type="text" class="form-control" id="firstName" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="lastName">Last Name</label>
                                <input type="text" class="form-control" id="lastName" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="department">Department</label>
                                <input type="text" class="form-control" id="department" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="status">Status</label>
                                <select class="form-control" id="status" required>
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="password">Password</label>
                                <input type="password" class="form-control" id="password">
                                <small class="form-text text-muted">Leave blank to keep current password when editing</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="profilePicture">Profile Picture</label>
                                <input type="file" class="form-control-file" id="profilePicture" accept="image/*">
                                <small class="form-text text-muted">Leave empty to keep current picture when editing</small>
                            </div>
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
</div>

<?php
$content = ob_get_clean();
Page::render($content);
?>

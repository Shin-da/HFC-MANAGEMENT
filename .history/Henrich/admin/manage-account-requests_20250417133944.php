<?php
error_reporting(E_ALL);
ini_set('display_errors', 0); // Don't display errors to browser
ini_set('log_errors', 1); // Log errors instead

require_once 'access_control.php';
require_once '../includes/Page.php';
require_once '../includes/functions.php';

// Initialize page
Page::setTitle('Manage Account Requests - HFC Admin');
Page::setBodyClass('admin-page');
Page::setCurrentPage('manage-account-requests');
Page::setAdminPage(true);

// Add required styles
Page::addStyle('../assets/css/admin.css');
Page::addStyle('../assets/css/table.css');

// Add required scripts
Page::addScript('../assets/js/account-requests.js');

ob_start();

// Handle request submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_request'])) {
    try {
        // Validate required fields
        $required = ['first_name', 'last_name', 'username', 'usermail', 'role'];
        foreach ($required as $field) {
            if (empty($_POST[$field])) {
                throw new Exception("All fields are required");
            }
        }

        // Validate email format
        if (!filter_var($_POST['usermail'], FILTER_VALIDATE_EMAIL)) {
            throw new Exception("Invalid email format");
        }

        // Check if email or username already exists in account_request
        $stmt = $conn->prepare("SELECT COUNT(*) FROM account_request WHERE usermail = ? OR username = ?");
        $stmt->bind_param("ss", $_POST['usermail'], $_POST['username']);
        $stmt->execute();
        $stmt->bind_result($count);
        $stmt->fetch();
        $stmt->close();

        if ($count > 0) {
            throw new Exception("Email or username already exists in pending requests");
        }

        // Check if email or username exists in approved_account
        $stmt = $conn->prepare("SELECT COUNT(*) FROM approved_account WHERE usermail = ? OR username = ?");
        $stmt->bind_param("ss", $_POST['usermail'], $_POST['username']);
        $stmt->execute();
        $stmt->bind_result($count);
        $stmt->fetch();
        $stmt->close();

        if ($count > 0) {
            throw new Exception("Email or username already exists in approved accounts");
        }

        // Insert new request
        $stmt = $conn->prepare("INSERT INTO account_requests (firstname, lastname, email, department, position, reason, status, request_date) VALUES (?, ?, ?, ?, ?, ?, 'pending', NOW())");
        $stmt->bind_param("ssssss", 
            $_POST['first_name'], 
            $_POST['last_name'], 
            $_POST['usermail'], 
            $_POST['department'], // Adjust form field name if needed
            $_POST['position'],   // Adjust form field name if needed
            $_POST['reason']      // Adjust form field name if needed
        );

        if ($stmt->execute()) {
            $response = [
                'status' => 'success',
                'message' => 'Account request submitted successfully'
            ];
        } else {
            throw new Exception("Error submitting request");
        }

    } catch (Exception $e) {
        $response = [
            'status' => 'error',
            'message' => $e->getMessage()
        ];
    }

    // Return JSON response for AJAX requests
    if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Set JSON header for all AJAX requests
    header('Content-Type: application/json');
    
    try {
        if (!isset($_POST['request_id']) || !isset($_POST['action'])) {
            throw new Exception("Missing required parameters");
        }
        
        $request_id = intval($_POST['request_id']);
        $action = $_POST['action'];
        $temp_password = bin2hex(random_bytes(8));
        
        if ($action === 'approve') {
            // Begin transaction
            $conn->begin_transaction();
            
            // Get request details
            $stmt = $conn->prepare("SELECT * FROM account_request WHERE user_id = ?");
            if (!$stmt) {
                throw new Exception("Database error: " . $conn->error);
            }
            
            $stmt->bind_param("i", $request_id);
            if (!$stmt->execute()) {
                throw new Exception("Error executing query: " . $stmt->error);
            }
            
            $result = $stmt->get_result();
            $request = $result->fetch_assoc();
            
            if (!$request) {
                throw new Exception("Request not found");
            }
            
            // Create approved account
            $hashed_password = password_hash($temp_password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO approved_account (usermail, username, role, password, first_name, last_name, status, created_at, updated_at, is_online) 
                VALUES (?, ?, ?, ?, ?, ?, 'active', NOW(), NOW(), FALSE)");
            
            if (!$stmt) {
                throw new Exception("Database error: " . $conn->error);
            }
            
            $stmt->bind_param("ssssss", 
                $request['usermail'],
                $request['username'],
                $request['role'],
                $hashed_password,
                $request['first_name'],
                $request['last_name']
            );
            
            if (!$stmt->execute()) {
                throw new Exception("Error creating account: " . $stmt->error);
            }
            
            // Delete from requests
            $stmt = $conn->prepare("DELETE FROM account_request WHERE user_id = ?");
            if (!$stmt) {
                throw new Exception("Database error: " . $conn->error);
            }
            
            $stmt->bind_param("i", $request_id);
            if (!$stmt->execute()) {
                throw new Exception("Error removing request: " . $stmt->error);
            }
            
            $conn->commit();
            
            echo json_encode([
                'status' => 'success',
                'message' => 'Account approved successfully',
                'password' => $temp_password,
                'username' => $request['username'],
                'email' => $request['usermail']
            ]);
            exit;
            
        } elseif ($action === 'reject') {
            $stmt = $conn->prepare("DELETE FROM account_request WHERE user_id = ?");
            if (!$stmt) {
                throw new Exception("Database error: " . $conn->error);
            }
            
            $stmt->bind_param("i", $request_id);
            if (!$stmt->execute()) {
                throw new Exception("Error deleting request: " . $stmt->error);
            }
            
            echo json_encode([
                'status' => 'success',
                'message' => 'Request rejected successfully'
            ]);
            exit;
        } else {
            throw new Exception("Invalid action specified");
        }
        
    } catch (Exception $e) {
        if (isset($conn) && $conn->inTransaction()) {
            $conn->rollback();
        }
        echo json_encode([
            'status' => 'error',
            'message' => $e->getMessage()
        ]);
        exit;
    }
}

// Get all fields from account_request
$result = $conn->query("SELECT 
    user_id, usermail, username, role, created_at, updated_at, 
    first_name, last_name, status, last_online, is_online 
    FROM account_request 
    ORDER BY created_at DESC");

// Debug any SQL errors
if (!$result) {
    die("Error fetching requests: " . $conn->error);
}

// Add validation query to verify role consistency
$validation_query = "SELECT ar.role as request_role, aa.role as approved_role 
                    FROM account_request ar 
                    LEFT JOIN approved_account aa ON ar.username = aa.username 
                    WHERE aa.user_id IS NOT NULL";
$validation_result = $conn->query($validation_query);
while ($row = $validation_result->fetch_assoc()) {
    if ($row['request_role'] !== $row['approved_role']) {
        error_log("Role mismatch found: Request role: {$row['request_role']}, Approved role: {$row['approved_role']}");
    }
}

// Check history record
$history_result = $conn->query("SELECT * FROM approvedaccount_history ORDER BY approved_at DESC LIMIT 1");

// Check approved account
$approved_result = $conn->query("SELECT * FROM approved_account ORDER BY created_at DESC LIMIT 1");

?>

<div class="container-fluid">
    <section class="panel">
        <div class="container-fluid">
            <div class="table-header">
                <div class="title">
                    <span>
                        <h2>Manage Account Requests</h2>
                    </span>
                    <span style="font-size: 12px;">List of all pending account requests</span>
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
                            <input type="text" id="searchInput" class="form-control" placeholder="Search requests...">
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
                            <option value="pending">Pending</option>
                            <option value="approved">Approved</option>
                            <option value="rejected">Rejected</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <select id="roleFilter" class="form-control">
                            <option value="">All Roles</option>
                            <option value="user">User</option>
                            <option value="supervisor">Supervisor</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="table-container">
                <table class="table-striped">
                    <thead>
                        <tr>
                            <th>Request ID</th>
                            <th>Username</th>
                        <th>Email</th>
                            <th>First Name</th>
                            <th>Last Name</th>
                        <th>Role</th>
                            <th>Department</th>
                        <th>Status</th>
                            <th>Requested On</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                    <tbody id="requestTableBody">
                        <?php
                        try {
                            // Pagination
                            $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
                            $limit = 10;
                            $offset = ($page - 1) * $limit;

                            // Get total count
                            $countStmt = $GLOBALS['pdo']->query("SELECT COUNT(*) FROM account_requests");
                            $total_records = $countStmt->fetchColumn();
                            $total_pages = ceil($total_records / $limit);

                            // Get requests with pagination
                            $stmt = $GLOBALS['pdo']->prepare("SELECT * FROM account_requests ORDER BY created_at DESC LIMIT ? OFFSET ?");
                            $stmt->execute([$limit, $offset]);
                            $requests = $stmt->fetchAll(PDO::FETCH_ASSOC);

                            if (count($requests) > 0) {
                                foreach ($requests as $row) {
                                    $statusClass = match($row['status']) {
                                        'pending' => 'warning',
                                        'approved' => 'success',
                                        'rejected' => 'danger',
                                        default => 'secondary'
                                    };
                                    
                                    $roleClass = $row['role'] === 'supervisor' ? 'info' : 'secondary';
                                    
                                    echo "<tr>
                                        <td>" . htmlspecialchars($row['request_id']) . "</td>
                                        <td>" . htmlspecialchars($row['username']) . "</td>
                                        <td>" . htmlspecialchars($row['email']) . "</td>
                                        <td>" . htmlspecialchars($row['first_name']) . "</td>
                                        <td>" . htmlspecialchars($row['last_name']) . "</td>
                                        <td><span class='badge badge-{$roleClass}'>" . htmlspecialchars($row['role']) . "</span></td>
                                        <td>" . htmlspecialchars($row['department']) . "</td>
                                        <td><span class='badge badge-{$statusClass}'>" . htmlspecialchars($row['status']) . "</span></td>
                                        <td>" . date('M d, Y', strtotime($row['created_at'])) . "</td>
                                        <td>
                                            <button class='btn btn-sm btn-info' onclick='viewRequest(" . $row['request_id'] . ")'><i class='fas fa-eye'></i></button>
                                            <button class='btn btn-sm btn-success' onclick='approveRequest(" . $row['request_id'] . ")'><i class='fas fa-check'></i></button>
                                            <button class='btn btn-sm btn-danger' onclick='rejectRequest(" . $row['request_id'] . ")'><i class='fas fa-times'></i></button>
                        </td>
                                    </tr>";
                                }
                            } else {
                                echo "<tr><td colspan='10'>0 results</td></tr>";
                            }
                        } catch (PDOException $e) {
                            echo "<tr><td colspan='10'>Error: " . htmlspecialchars($e->getMessage()) . "</td></tr>";
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

<!-- View Request Modal -->
<div class="modal fade" id="requestModal" tabindex="-1" role="dialog" aria-labelledby="requestModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="requestModalLabel">View Account Request</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Username</label>
                            <p id="viewUsername"></p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Email</label>
                            <p id="viewEmail"></p>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>First Name</label>
                            <p id="viewFirstName"></p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Last Name</label>
                            <p id="viewLastName"></p>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Role</label>
                            <p id="viewRole"></p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Department</label>
                            <p id="viewDepartment"></p>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label>Additional Information</label>
                            <p id="viewAdditionalInfo"></p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-success" onclick="approveRequest()">Approve</button>
                <button type="button" class="btn btn-danger" onclick="rejectRequest()">Reject</button>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
Page::render($content);
?>

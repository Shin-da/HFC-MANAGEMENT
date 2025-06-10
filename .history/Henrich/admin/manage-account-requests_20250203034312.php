<?php
error_reporting(E_ALL);
ini_set('display_errors', 0); // Don't display errors to browser
ini_set('log_errors', 1); // Log errors instead

require_once 'access_control.php';
require_once '../includes/Page.php';
require_once '../includes/functions.php';

// Initialize page
Page::setTitle('Account Requests - HFC Admin');
Page::setBodyClass('admin-page');
Page::setCurrentPage('manage-account-requests');

// Add required styles
Page::addStyle('../ssets/css/admin.css');
Page::addStyle('../ssets/css/requests.css');
Page::addStyle('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css');

// Add required scripts
Page::addScript('./ssets/js/account-requests.js');

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
        $stmt = $conn->prepare("INSERT INTO account_request (first_name, last_name, username, usermail, role, status, created_at) VALUES (?, ?, ?, ?, ?, 'pending', NOW())");
        $stmt->bind_param("sssss", 
            $_POST['first_name'],
            $_POST['last_name'],
            $_POST['username'],
            $_POST['usermail'],
            $_POST['role']
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

<style>
.action-buttons {
    display: flex;
    gap: 8px;
    justify-content: flex-start;
}

.btn-approve, .btn-reject {
    min-width: 100px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 5px;
    cursor: pointer;
}

.btn-approve i, .btn-reject i {
    font-size: 14px;
}

.btn-approve {
    background-color: #28a745;
    color: white;
    border: none;
    padding: 5px 10px;
    border-radius: 4px;
    transition: all 0.3s;
}

.btn-approve:hover {
    background-color: #218838;
    transform: translateY(-2px);
}

.btn-reject {
    background-color: #dc3545;
    color: white;
    border: none;
    padding: 5px 10px;
    border-radius: 4px;
    transition: all 0.3s;
}

.btn-reject:hover {
    background-color: #c82333;
    transform: translateY(-2px);
}

.status-badge {
    padding: 5px 15px;
    border-radius: 20px;
    font-size: 0.85em;
    font-weight: bold;
    background-color: #2ecc71;
    color: white;
    display: inline-flex;
    align-items: center;
    gap: 8px;
}

.status-badge::before {
    content: '';
    width: 8px;
    height: 8px;
    background-color: #fff;
    border-radius: 50%;
    box-shadow: 0 0 0 2px rgba(46, 204, 113, 0.4);
    animation: pulse 2s ease-in-out infinite;
}

@keyframes pulse {
    0% {
        box-shadow: 0 0 0 0 rgba(255, 255, 255, 0.7);
    }
    70% {
        box-shadow: 0 0 0 5px rgba(255, 255, 255, 0);
    }
    100% {
        box-shadow: 0 0 0 0 rgba(255, 255, 255, 0);
    }
}

.table-hover tbody tr:hover {
    background-color: rgba(0,0,0,.075);
    cursor: pointer;
}

.custom-alert {
    display: none;
    position: fixed;
    top: 20px;
    right: 20px;
    padding: 20px;
    border-radius: 8px;
    background: white;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    z-index: 1000;
    animation: slideIn 0.5s ease-out;
}

.custom-alert.success {
    border-left: 4px solid #28a745;
}

.custom-alert.error {
    border-left: 4px solid #dc3545;
}

.custom-alert-content {
    display: flex;
    align-items: center;
    gap: 12px;
}

.custom-alert i {
    font-size: 24px;
}

.custom-alert.success i {
    color: #28a745;
}

.custom-alert.error i {
    color: #dc3545;
}

@keyframes slideIn {
    from {
        transform: translateX(100%);
        opacity: 0;
    }
    to {
        transform: translateX(0);
        opacity: 1;
    }
}

@keyframes slideOut {
    from {
        transform: translateX(0);
        opacity: 1;
    }
    to {
        transform: translateX(100%);
        opacity: 0;
    }
}
</style>

<div id="customAlert" class="custom-alert">
    <div class="custom-alert-content">
        <i class="fas fa-check-circle"></i>
        <span id="alertMessage"></span>
    </div>
</div>

<!-- Make sure these scripts are included -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
function showCustomAlert(message, type = 'success') {
    const alert = document.getElementById('customAlert');
    const alertMessage = document.getElementById('alertMessage');
    
    alert.className = `custom-alert ${type}`;
    alertMessage.textContent = message;
    
    // Show alert
    alert.style.display = 'block';
    
    // Hide alert after 3 seconds
    setTimeout(() => {
        alert.style.animation = 'slideOut 0.5s ease-out';
        setTimeout(() => {
            alert.style.display = 'none';
            alert.style.animation = '';
        }, 500);
    }, 3000);
}

function processRequest(requestId, action) {
    const message = action === 'approve' ? 
        'Are you sure you want to approve this request?' : 
        'Are you sure you want to reject and delete this request?';
        
    if (confirm(message)) {
        $.ajax({
            type: 'POST',
            url: window.location.href,
            data: {
                request_id: requestId,
                action: action
            },
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    if (action === 'approve' && response.password) {
                        // Show success modal with credentials
                        $('#successModalBody').html(`
                            <div class="alert alert-success">
                                <h4><i class="fas fa-check-circle"></i> Account Approved!</h4>
                                <hr>
                                <p><strong>Username:</strong> ${response.username}</p>
                                <p><strong>Email:</strong> ${response.email}</p>
                                <p><strong>Temporary Password:</strong> <code>${response.password}</code></p>
                                <hr>
                                <p class="mb-0">Please securely communicate these credentials to the user.</p>
                            </div>
                        `);
                        $('#successModal').modal('show');
                    } else {
                        showCustomAlert(response.message, 'success');
                    }
                    // Remove the row after successful action
                    $(`tr[data-request-id="${requestId}"]`).fadeOut(500);
                } else {
                    showCustomAlert(response.message || 'Error processing request', 'error');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error:', xhr.responseText);
                showCustomAlert('Error processing request. Please check console for details.', 'error');
            }
        });
    }
}

function approveAccount(requestId) {
    // Debug log
    console.log('Approving account:', requestId);

    const button = $(`button[data-request-id="${requestId}"]`);
    button.prop('disabled', true)
          .html('<i class="fas fa-spinner fa-spin"></i> Processing...');

    $.ajax({
        type: 'POST',
        url: 'approve-request.php',
        data: { request_id: requestId },
        dataType: 'json',
        beforeSend: function() {
            console.clear();
            console.log('Sending approval request...');
        },
        success: function(response) {
            console.log('Success response:', response);
            handleApprovalResponse(response, requestId);
        },
        error: function(xhr, status, error) {
            console.error('Raw response:', xhr.responseText);
            console.error('Status:', status);
            console.error('Error:', error);
            handleApprovalError(xhr, requestId);
        },
        complete: function() {
            button.prop('disabled', false)
                  .html('<i class="fas fa-check"></i> Approve');
        }
    });
}

function handleApprovalResponse(response, requestId) {
    if (response.status === 'success') {
        $('#successModalBody').html(`
            <div class="alert alert-success">
                <h4><i class="fas fa-check-circle"></i> Account Approved!</h4>
                <hr>
                <p><strong>Username:</strong> ${response.username}</p>
                <p><strong>Email:</strong> ${response.email}</p>
                <p><strong>Password:</strong> <code>${response.password}</code></p>
            </div>
        `);
        $('#successModal').modal('show');
        $(`tr[data-request-id="${requestId}"]`).fadeOut(500);
    } else {
        showCustomAlert(response.message || 'Unknown error occurred', 'error');
    }
}

function handleApprovalError(xhr, requestId) {
    let errorMessage = 'Server error occurred';
    try {
        const response = JSON.parse(xhr.responseText);
        errorMessage = response.message || errorMessage;
    } catch (e) {
        console.error('Parse error:', e);
        console.error('Raw response:', xhr.responseText);
    }
    showCustomAlert(errorMessage, 'error');
}

function doApproval(requestId) {
    // Show loading state on button
    const button = $(`button[data-request-id="${requestId}"]`);
    button.prop('disabled', true)
          .html('<i class="fas fa-spinner fa-spin"></i> Processing...');

    $.ajax({
        type: 'POST',
        url: 'approve-request.php',
        data: { request_id: requestId },
        dataType: 'json',
        beforeSend: function() {
            console.clear(); // Clear any previous console errors
        },
        success: function(response) {
            try {
                if (response && response.status === 'success') {
                    $('#successModalBody').html(`
                        <div class="alert alert-success">
                            <h4><i class="fas fa-check-circle"></i> Account Approved!</h4>
                            <hr>
                            <p><strong>Username:</strong> ${response.username}</p>
                            <p><strong>Email:</strong> ${response.email}</p>
                            <p><strong>Temporary Password:</strong> <code>${response.password}</code></p>
                            <hr>
                            <p class="mb-0">Please securely communicate these credentials to the user.</p>
                        </div>
                    `);
                    $('#successModal').modal('show');
                    $(`tr[data-request-id="${requestId}"]`).fadeOut(500);
                } else {
                    throw new Error(response.message || 'Unknown error occurred');
                }
            } catch (e) {
                showCustomAlert(e.message, 'error');
            }
        },
        error: function(xhr, status, error) {
            console.error('Full response:', xhr.responseText);
            let errorMessage = 'Server error occurred';
            
            try {
                const response = JSON.parse(xhr.responseText);
                errorMessage = response.message || errorMessage;
            } catch (e) {
                console.error('Parse error:', e);
            }
            
            showCustomAlert(errorMessage, 'error');
        },
        complete: function() {
            button.prop('disabled', false)
                  .html('<i class="fas fa-check"></i> Approve');
        }
    });
}

// Update showCustomAlert to be more reliable
function showCustomAlert(message, type = 'success') {
    const alert = document.getElementById('customAlert');
    const alertMessage = document.getElementById('alertMessage');
    
    if (!alert || !alertMessage) return;
    
    alert.className = `custom-alert ${type}`;
    alertMessage.textContent = message;
    alert.style.display = 'block';
    
    setTimeout(() => {
        alert.style.animation = 'slideOut 0.5s ease-out';
        setTimeout(() => {
            alert.style.display = 'none';
            alert.style.animation = '';
        }, 500);
    }, 3000);
}
</script>

<div class="container-fluid">
    <div class="admin-container">
        <h2 class="mb-4"><i class="fas fa-user-plus"></i> Account Requests</h2>
        
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="thead-dark">
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Username</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($request = $result->fetch_assoc()): ?>
                    <tr data-request-id="<?= $request['user_id'] ?>">
                        <td>#<?= htmlspecialchars($request['user_id']) ?></td>
                        <td>
                            <i class="fas fa-user"></i>
                            <?= htmlspecialchars($request['first_name'] . ' ' . $request['last_name']) ?>
                        </td>
                        <td>
                            <i class="fas fa-envelope"></i>
                            <?= htmlspecialchars($request['usermail']) ?>
                        </td>
                        <td>
                            <i class="fas fa-user-tag"></i>
                            <?= htmlspecialchars($request['username']) ?>
                        </td>
                        <td>
                            <span class="role-badge role-<?= strtolower($request['role']) ?>">
                                <?php
                                $roleIcon = '';
                                switch(strtolower($request['role'])) {
                                    case 'admin':
                                        $roleIcon = 'fa-user-cog';
                                        break;
                                    case 'supervisor':
                                        $roleIcon = 'fa-user-tie';
                                        break;
                                    case 'ceo':
                                        $roleIcon = 'fa-user-shield';
                                        break;
                                    default:
                                        $roleIcon = 'fa-user';
                                }
                                ?>
                                <i class="fas <?= $roleIcon ?>"></i>
                                <?= htmlspecialchars(ucfirst($request['role'])) ?>
                            </span>
                        </td>
                        <td>
                            <span class="status-badge <?= strtolower($request['status']) ?>">
                                <?= htmlspecialchars(ucfirst($request['status'])) ?>
                            </span>
                        </td>
                        <td>
                            <i class="fas fa-calendar"></i>
                            <?= date('M d, Y', strtotime($request['created_at'])) ?>
                        </td>
                        <td>
                            <div class="action-buttons">
                                <button class="btn-approve" 
                                        onclick="approveAccount(<?= $request['user_id'] ?>)" 
                                        data-request-id="<?= $request['user_id'] ?>"
                                        title="Approve Request">
                                    <i class="fas fa-check"></i> Approve
                                </button>
                                <button class="btn-reject" onclick="processRequest(<?= $request['user_id'] ?>, 'reject')" title="Reject Request">
                                    <i class="fas fa-times"></i> Reject
                                </button>
                            </div>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
        
        <?php if ($result->num_rows === 0): ?>
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i> No account requests found.
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Success Modal -->
<div class="modal fade" id="successModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">
                    <i class="fas fa-check-circle"></i> Success
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body text-center" id="successModalBody">
                <i class="fas fa-check-circle fa-4x text-success mb-3"></i>
                <p class="mb-0">Account has been approved successfully!</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal" onclick="location.reload()">Close</button>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
Page::render($content);
?>

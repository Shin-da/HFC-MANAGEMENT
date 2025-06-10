<?php
require_once 'access_control.php';
require_once '../includes/Page.php';
require_once '../includes/functions.php';

// Add this helper function at the top of the file
function safe_html($value) {
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

// Update the formatUpdateParams function by removing department
function formatUpdateParams($user) {
    $params = [
        'id' => (int)$user['user_id'],
        'firstName' => addslashes($user['first_name'] ?? ''),
        'lastName' => addslashes($user['last_name'] ?? ''),
        'email' => addslashes($user['usermail'] ?? ''),
        'username' => addslashes($user['username'] ?? ''),
        'role' => addslashes($user['role'] ?? ''),
        'status' => addslashes($user['status'] ?? '')
    ];
    return htmlspecialchars(json_encode($params), ENT_QUOTES, 'UTF-8');
}

// Initialize page
Page::setTitle('Manage Users - HFC Admin');
Page::setBodyClass('admin-page');
Page::setCurrentPage('manage-users');

ob_start();

// Update the SQL query to use approvedaccount_history as the primary source
$sql = "SELECT aa.user_id, aa.status, aa.is_online, aa.last_online,
        ah.usermail, ah.username, ah.role, ah.password, 
        ah.first_name, ah.last_name, ah.created_at, ah.approved_at
        FROM approved_account aa
        INNER JOIN approvedaccount_history ah ON aa.username = ah.username
        ORDER BY ah.approved_at DESC";

$result = $conn->query($sql);

if (!$result) {
    die("Error fetching users: " . $conn->error . "<br>" . $sql);
}

// Also update the update handler to update both tables
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_user'])) {
    try {
        $conn->begin_transaction();

        // Update approved_account
        $stmt = $conn->prepare("UPDATE approved_account SET 
            status = ?,
            updated_at = NOW()
            WHERE user_id = ?");
            
        $stmt->bind_param("si", 
            $_POST['status'],
            $_POST['user_id']
        );
        
        if (!$stmt->execute()) {
            throw new Exception("Error updating approved_account");
        }

        // Update approvedaccount_history
        $stmt = $conn->prepare("UPDATE approvedaccount_history SET 
            first_name = ?,
            last_name = ?,
            usermail = ?,
            username = ?,
            role = ?,
            updated_at = NOW()
            WHERE user_id = ?");
            
        $stmt->bind_param("sssssi", 
            $_POST['first_name'],
            $_POST['last_name'],
            $_POST['usermail'],
            $_POST['username'],
            $_POST['role'],
            $_POST['user_id']
        );
        
        if (!$stmt->execute()) {
            throw new Exception("Error updating history");
        }

        $conn->commit();
        $response = ['status' => 'success', 'message' => 'User updated successfully!'];

        if (isset($_POST['ajax'])) {
            header('Content-Type: application/json');
            echo json_encode($response);
            exit;
        }
    } catch (Exception $e) {
        $conn->rollback();
        if (isset($_POST['ajax'])) {
            header('Content-Type: application/json');
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
            exit;
        }
    }
}

// Remove or comment out the pending query since it's not needed
// $sql_pending = "SELECT ar.role as request_role...

?>

<style>
.users-table {
    width: 100%;
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    table-layout: fixed;  /* Add this line */
}

.users-table th, 
.users-table td {
    white-space: normal;  /* Add this line */
    word-wrap: break-word;  /* Add this line */
}

/* Add specific column widths */
.users-table th:nth-child(1) { width: 5%; }  /* ID */
.users-table th:nth-child(2) { width: 15%; }  /* Name */
.users-table th:nth-child(3) { width: 15%; }  /* Email */
.users-table th:nth-child(4) { width: 10%; }  /* Username */
.users-table th:nth-child(5) { width: 10%; }  /* Password */
.users-table th:nth-child(6) { width: 10%; }  /* Role */
.users-table th:nth-child(7) { width: 10%; }  /* Status */
.users-table th:nth-child(8) { width: 10%; }  /* Last Online */
.users-table th:nth-child(9) { width: 10%; }  /* Created Date */
.users-table th:nth-child(10) { width: 5%; }  /* Actions */

.user-status {
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

.user-status::before {
    content: '';
    width: 8px;
    height: 8px;
    background-color: #fff;
    border-radius: 50%;
    box-shadow: 0 0 0 2px rgba(46, 204, 113, 0.4);
    animation: pulse 2s ease-in-out infinite;
}

.role-badge {
    padding: 5px 15px;
    border-radius: 15px;
    font-size: 0.85em;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 8px;
}

.role-admin { 
    background-color: #dc3545; 
    color: white; 
}

.role-supervisor { 
    background-color: #ffc107; 
    color: black; 
}

.role-ceo { 
    background-color: #17a2b8; 
    color: white;
    border: 2px solid #0f7a8d;
}

.role-badge i {
    font-size: 12px;
}

.user-source {
    font-size: 0.8em;
    color: #666;
    font-style: italic;
}

/* Update button styling */
.btn-update {
    background: linear-gradient(145deg, #2b8af5, #1a6cd1);
    color: white;
    border: none;
    padding: 8px 15px;
    border-radius: 6px;
    font-size: 0.9em;
    transition: all 0.3s ease;
    box-shadow: 0 2px 5px rgba(26, 108, 209, 0.2);
    display: inline-flex;
    align-items: center;
    gap: 8px;
}

.btn-update:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 10px rgba(26, 108, 209, 0.3);
    background: linear-gradient(145deg, #1a6cd1, #2b8af5);
}

.btn-update i {
    font-size: 14px;
}

/* Modal styling */
.modal-content {
    border: none;
    border-radius: 12px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.2);
}

.modal-header {
    background: linear-gradient(145deg, #2b8af5, #1a6cd1);
    color: white;
    border-radius: 12px 12px 0 0;
    padding: 15px 20px;
}

.modal-title {
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 10px;
}

.modal-body {
    padding: 20px;
}

.form-group {
    margin-bottom: 20px;
}

.form-group label {
    font-weight: 500;
    color: #444;
    margin-bottom: 8px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.form-control {
    border: 2px solid #e1e1e1;
    border-radius: 6px;
    padding: 8px 12px;
    transition: all 0.3s ease;
}

.form-control:focus {
    border-color: #2b8af5;
    box-shadow: 0 0 0 3px rgba(43, 138, 245, 0.1);
}

.modal-footer {
    border-top: 1px solid #eee;
    padding: 15px 20px;
}

.btn-save {
    background: linear-gradient(145deg, #2b8af5, #1a6cd1);
    color: white;
    padding: 8px 20px;
    border-radius: 6px;
    border: none;
    font-weight: 500;
    transition: all 0.3s ease;
}

.btn-save:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 10px rgba(26, 108, 209, 0.3);
}

/* Success Alert Styling */
.update-alert {
    position: fixed;
    top: 20px;
    right: 20px;
    padding: 15px 25px;
    background: #fff;
    border-radius: 8px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    display: flex;
    align-items: center;
    gap: 12px;
    z-index: 1050;
    animation: slideIn 0.5s ease-out;
}

.update-alert.success {
    border-left: 4px solid #28a745;
}

.update-alert i {
    color: #28a745;
    font-size: 20px;
}

/* Add these modal centering styles */
.modal-dialog {
    display: flex;
    align-items: center;
    min-height: calc(100% - 1rem);
}

.modal-content {
    width: 100%;
}

@media (min-width: 576px) {
    .modal-dialog {
        max-width: 500px;
        margin: 1.75rem auto;
    }
}

/* Updated table styles */
.users-table {
    width: 100%;
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    margin: 20px 0;
    border-collapse: collapse;
    font-size: 14px; /* Reduced font size */
}

.users-table th,
.users-table td {
    padding: 12px 8px;
    text-align: left;
    vertical-align: middle;
    border-bottom: 1px solid #eee;
}

/* Specific column widths */
.users-table th:nth-child(1) { width: 5%; }  /* ID */
.users-table th:nth-child(2) { width: 12%; } /* Name */
.users-table th:nth-child(3) { width: 15%; } /* Email */
.users-table th:nth-child(4) { width: 10%; } /* Username */
.users-table th:nth-child(5) { width: 10%; } /* Password */
.users-table th:nth-child(6) { width: 8%; }  /* Role */
.users-table th:nth-child(7) { width: 8%; }  /* Status */
.users-table th:nth-child(8) { width: 12%; } /* Last Online */
.users-table th:nth-child(9) { width: 12%; } /* Created Date */
.users-table th:nth-child(10) { width: 8%; } /* Actions */

/* Add horizontal scroll for small screens */
@media screen and (max-width: 1200px) {
    .table-container {
        overflow-x: auto;
        padding-bottom: 15px;
    }
    
    .users-table {
        min-width: 1100px;
    }
}

/* Add this style section in your existing styles */
.password-cell {
    position: relative;
}

.password-toggle {
    background: none;
    border: none;
    color: #007bff;
    cursor: pointer;
    padding: 2px 5px;
    font-size: 12px;
}

.password-toggle:hover {
    color: #0056b3;
}

.password-text {
    display: inline-block;
    word-break: break-all;
}

.password-hidden {
    filter: blur(4px);
    -webkit-filter: blur(4px);
    transition: all 0.3s ease;
}

.password-visible {
    filter: blur(0);
    -webkit-filter: blur(0);
}
</style>

<div class="container-fluid">
    <div class="admin-container">
        <h2 class="mb-4"><i class="fas fa-users"></i> Manage Users</h2>
        
        <div class="table-container">
            <table class="table table-hover users-table">
                <thead class="thead-dark">
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Username</th>
                        <th>Password</th>  <!-- New column -->
                        <th>Role</th>
                        <th>Status</th>
                        <th>Last Online</th>
                        <th>Created Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($user = $result->fetch_assoc()): ?>
                    <tr>
                        <td>#<?= safe_html($user['user_id']) ?></td>
                        <td>
                            <i class="fas fa-user"></i>
                            <?= safe_html($user['first_name'] . ' ' . $user['last_name']) ?>
                        </td>
                        <td>
                            <i class="fas fa-envelope"></i>
                            <?= safe_html($user['usermail']) ?>
                        </td>
                        <td>
                            <i class="fas fa-user-tag"></i>
                            <?= safe_html($user['username']) ?>
                        </td>
                        <td class="password-cell">
                            <div class="d-flex align-items-center gap-2">
                                <button type="button" 
                                        class="btn btn-sm btn-primary" 
                                        onclick="setNewPassword(<?= $user['user_id'] ?>, '<?= $user['username'] ?>')"
                                        title="Set New Password">
                                    <i class="fas fa-key"></i> Set Password
                                </button>
                            </div>
                        </td>
                        <td>
                            <span class="role-badge role-<?= strtolower($user['role'] ?? 'unknown') ?>">
                                <?php
                                $roleIcon = '';
                                switch(strtolower($user['role'] ?? '')) {
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
                                <?= safe_html(ucfirst($user['role'] ?? 'Unknown')) ?>
                            </span>
                        </td>
                        <td>
                            <span class="user-status <?= $user['status'] === 'active' ? 'online' : 'offline' ?>">
                                <?= safe_html(ucfirst($user['status'])) ?>
                            </span>
                        </td>
                        <td>
                            <i class="fas fa-clock"></i>
                            <?= $user['last_online'] ? date('M d, Y H:i', strtotime($user['last_online'])) : 'Never' ?>
                        </td>
                        <td>
                            <i class="fas fa-calendar"></i>
                            <?= date('M d, Y', strtotime($user['created_at'])) ?>
                        </td>
                        <td>
                            <button class="btn-update" 
                                    data-user='<?= formatUpdateParams($user) ?>'
                                    onclick="openUpdateModalFromData(this)">
                                <i class="fas fa-user-edit"></i>
                                Update
                            </button>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
        
        <?php if ($result->num_rows === 0): ?>
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i> No approved users found.
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Add the modal form -->
<div class="modal fade" id="updateUserModal" tabindex="-1" role="dialog" aria-labelledby="updateUserModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-user-edit"></i>
                    Update User Details
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="POST" id="updateUserForm">
                <div class="modal-body">
                    <input type="hidden" name="update_user" value="1">
                    <input type="hidden" name="ajax" value="1">
                    <input type="hidden" name="user_id" id="update_user_id">
                    
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label><i class="fas fa-user"></i> First Name</label>
                            <input type="text" class="form-control" name="first_name" id="update_first_name" required>
                        </div>
                        <div class="form-group col-md-6">
                            <label><i class="fas fa-user"></i> Last Name</label>
                            <input type="text" class="form-control" name="last_name" id="update_last_name" required>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label><i class="fas fa-envelope"></i> Email</label>
                            <input type="email" class="form-control" name="usermail" id="update_usermail" required>
                        </div>
                        <div class="form-group col-md-6">
                            <label><i class="fas fa-user-tag"></i> Username</label>
                            <input type="text" class="form-control" name="username" id="update_username" required>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label><i class="fas fa-user-shield"></i> Role</label>
                            <select class="form-control" name="role" id="update_role" required>
                                <option value="admin">Admin</option>
                                <option value="supervisor">Supervisor</option>
                                <option value="ceo">CEO</option>
                            </select>
                        </div>
                        <div class="form-group col-md-6">
                            <label><i class="fas fa-toggle-on"></i> Status</label>
                            <select class="form-control" name="status" id="update_status">
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn-save">
                        <i class="fas fa-save"></i> Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Add the modal form -->
<div class="modal fade" id="passwordManageModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">
                    <i class="fas fa-key"></i> Set New Password
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="passwordSetForm">
                <div class="modal-body">
                    <input type="hidden" id="passwordUserId" name="user_id">
                    <div class="form-group">
                        <label>Username</label>
                        <input type="text" id="passwordUsername" class="form-control" readonly>
                    </div>
                    <div class="form-group">
                        <label>New Password</label>
                        <div class="input-group">
                            <input type="text" id="newPassword" name="new_password" class="form-control" required>
                            <div class="input-group-append">
                                <button type="button" class="btn btn-outline-secondary" onclick="generatePassword()">
                                    Generate
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Save Password
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Add this modal -->
<div class="modal fade" id="passwordResetModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title">
                    <i class="fas fa-key"></i> Password Reset Successful
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle"></i>
                    Please copy this information and send it to the user securely.
                </div>
                <div class="p-3 bg-light rounded">
                    <p><strong>Username:</strong> <span id="resetUsername"></span></p>
                    <p><strong>Email:</strong> <span id="resetEmail"></span></p>
                    <p><strong>New Password:</strong> <code id="resetPassword"></code></p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Add these script tags before your custom scripts -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
function openUpdateModal(userId, firstName, lastName, email, username, role, status, department) {
    document.getElementById('update_user_id').value = userId || '';
    document.getElementById('update_first_name').value = firstName || '';
    document.getElementById('update_last_name').value = lastName || '';
    document.getElementById('update_usermail').value = email || '';
    document.getElementById('update_username').value = username || '';
    document.getElementById('update_role').value = role || 'supervisor'; // default value
    document.getElementById('update_status').value = status || 'active'; // default value
    
    $('#updateUserModal').modal('show');
}

// Update the openUpdateModalFromData function by removing department
function openUpdateModalFromData(button) {
    const userData = JSON.parse(button.getAttribute('data-user'));
    
    document.getElementById('update_user_id').value = userData.id;
    document.getElementById('update_first_name').value = userData.firstName;
    document.getElementById('update_last_name').value = userData.lastName;
    document.getElementById('update_usermail').value = userData.email;
    document.getElementById('update_username').value = userData.username;
    document.getElementById('update_role').value = userData.role || 'supervisor';
    document.getElementById('update_status').value = userData.status || 'active';
    
    $('#updateUserModal').modal('show');
}

// Add form submission handler
$('#updateUserForm').on('submit', function(e) {
    e.preventDefault();
    const btn = $(this).find('button[type="submit"]');
    btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Saving...');
    
    $.ajax({
        type: 'POST',
        url: window.location.href,
        data: $(this).serialize(),
        dataType: 'json',
        success: function(response) {
            $('#updateUserModal').modal('hide');
            showUpdateAlert(response.message, response.status);
            if (response.status === 'success') {
                setTimeout(() => window.location.reload(), 1500);
            }
        },
        error: function() {
            showUpdateAlert('Error updating user!', 'error');
        },
        complete: function() {
            btn.prop('disabled', false).html('<i class="fas fa-save"></i> Save Changes');
        }
    });
});

function showUpdateAlert(message, type = 'success') {
    const alert = $('<div>').addClass('update-alert ' + type)
        .append($('<i>').addClass('fas fa-check-circle'))
        .append(message);
    
    $('body').append(alert);
    
    setTimeout(() => {
        alert.fadeOut(() => alert.remove());
    }, 3000);
}

// Add success notification
<?php if (isset($_POST['update_user']) && !isset($error)): ?>
    $(document).ready(function() {
        showAlert('User updated successfully!', 'success');
    });
<?php endif; ?>

function togglePassword(userId) {
    const pwdElem = document.getElementById(`pwd-${userId}`);
    const eyeIcon = document.getElementById(`eye-${userId}`);
    
    if (pwdElem.classList.contains('password-hidden')) {
        // Show password
        pwdElem.classList.remove('password-hidden');
        pwdElem.classList.add('password-visible');
        eyeIcon.classList.remove('fa-eye');
        eyeIcon.classList.add('fa-eye-slash');
        
        // Hide password after 5 seconds
        setTimeout(() => {
            pwdElem.classList.remove('password-visible');
            pwdElem.classList.add('password-hidden');
            eyeIcon.classList.remove('fa-eye-slash');
            eyeIcon.classList.add('fa-eye');
        }, 5000);
    } else {
        // Hide password
        pwdElem.classList.remove('password-visible');
        pwdElem.classList.add('password-hidden');
        eyeIcon.classList.remove('fa-eye-slash');
        eyeIcon.classList.add('fa-eye');
    }
}

function resetPassword(userId) {
    if (confirm('Are you sure you want to reset this user\'s password?')) {
        $.ajax({
            type: 'POST',
            url: 'reset_password.php',
            data: { user_id: userId },
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    // Update modal with user details
                    $('#resetUsername').text(response.username);
                    $('#resetEmail').text(response.email);
                    $('#resetPassword').text(response.password);
                    
                    // Show the modal
                    $('#passwordResetModal').modal('show');
                } else {
                    showUpdateAlert(response.message || 'Error resetting password', 'error');
                }
            },
            error: function(xhr, status, error) {
                console.error('Reset password error:', error);
                showUpdateAlert('Error resetting password. Please try again.', 'error');
            }
        });
    }
}

function setNewPassword(userId, username) {
    document.getElementById('passwordUserId').value = userId;
    document.getElementById('passwordUsername').value = username;
    $('#passwordManageModal').modal('show');
}

function generatePassword() {
    // Generate a random password
    const length = 12;
    const charset = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*";
    let password = "";
    for (let i = 0; i < length; i++) {
        password += charset.charAt(Math.floor(Math.random() * charset.length));
    }
    document.getElementById('newPassword').value = password;
}

// Update the password form submission handler
$('#passwordSetForm').on('submit', function(e) {
    e.preventDefault();
    
    const formData = {
        user_id: $('#passwordUserId').val(),
        new_password: $('#newPassword').val()
    };
    
    $.ajax({
        type: 'POST',
        url: 'set_password.php',
        data: formData,
        dataType: 'json',
        success: function(response) {
            console.log('Response:', response); // Debug log
            if (response.status === 'success') {
                // Show success message with password
                $('#resetUsername').text($('#passwordUsername').val());
                $('#resetPassword').text(response.password);
                
                // Hide password set modal and show confirmation
                $('#passwordManageModal').modal('hide');
                $('#passwordResetModal').modal('show');
                
                // Clear the form
                $('#newPassword').val('');
            } else {
                showUpdateAlert(response.message || 'Error updating password', 'error');
            }
        },
        error: function(xhr, status, error) {
            console.error('Password update error:', error);
            console.error('Server response:', xhr.responseText); // Debug log
            showUpdateAlert('Error updating password. Please check console for details.', 'error');
        }
    });
});
</script>

<?php
$content = ob_get_clean();
Page::render($content);
?>

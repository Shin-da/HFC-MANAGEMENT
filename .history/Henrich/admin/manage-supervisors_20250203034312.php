<?php
require_once 'access_control.php';
require_once '../includes/Page.php';
require_once '../includes/functions.php';

// Initialize page
Page::setTitle('Manage Supervisors - HFC Admin');
Page::setBodyClass('admin-page');
Page::setCurrentPage('manage-supervisors');

// Add required styles & scripts
Page::addStyle('../assets/css/admin.css');
Page::addStyle('../assets/css/admin-forms.css');
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

// Get supervisors with their request status
$supervisors = $conn->query("
    SELECT u.*, r.status as request_status 
    FROM users u 
    LEFT JOIN requests r ON u.user_id = r.user_id 
    WHERE u.role = 'supervisor' 
    ORDER BY u.created_at DESC
");
?>

<div class="admin-container">
    <div class="page-header">
        <h2><i class="fas fa-user-tie"></i> Manage Supervisors</h2>
        <button class="btn btn-primary" onclick="toggleAddForm()">
            <i class="fas fa-plus"></i> Add Supervisor
        </button>
    </div>

    <div class="modal" id="addSupervisorModal">
        <div class="modal-content">
            <h3>Add New Supervisor</h3>
            <form method="POST" class="admin-form" id="addSupervisorForm">
                <input type="hidden" name="action" value="add">
                <div class="form-grid">
                    <div class="form-group">
                        <label><i class="fas fa-user"></i> First Name</label>
                        <input type="text" name="first_name" required>
                    </div>
                    <div class="form-group">
                        <label><i class="fas fa-user"></i> Last Name</label>
                        <input type="text" name="last_name" required>
                    </div>
                    <div class="form-group">
                        <label><i class="fas fa-envelope"></i> Email</label>
                        <input type="email" name="email" required>
                    </div>
                    <div class="form-group">
                        <label><i class="fas fa-lock"></i> Password</label>
                        <input type="password" name="password" required minlength="8">
                    </div>
                    <div class="form-group">
                        <label><i class="fas fa-building"></i> Department</label>
                        <input type="text" name="department">
                    </div>
                </div>
                <div class="form-actions">
                    <button type="submit" class="btn btn-success">Add Supervisor</button>
                    <button type="button" class="btn btn-secondary" onclick="toggleAddForm()">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <div class="modal" id="editSupervisorModal">
        <div class="modal-content">
            <h3>Edit Supervisor</h3>
            <form method="POST" class="admin-form" id="editSupervisorForm">
                <input type="hidden" name="action" value="update">
                <input type="hidden" name="user_id" id="edit_user_id">
                <div class="form-grid">
                    <div class="form-group">
                        <label><i class="fas fa-user"></i> First Name</label>
                        <input type="text" name="first_name" id="edit_first_name" required>
                    </div>
                    <div class="form-group">
                        <label><i class="fas fa-user"></i> Last Name</label>
                        <input type="text" name="last_name" id="edit_last_name" required>
                    </div>
                    <div class="form-group">
                        <label><i class="fas fa-envelope"></i> Email</label>
                        <input type="email" name="email" id="edit_email" required>
                    </div>
                    <div class="form-group">
                        <label><i class="fas fa-lock"></i> Password</label>
                        <input type="password" name="password" minlength="8" placeholder="Leave blank to keep current password">
                    </div>
                    <div class="form-group">
                        <label><i class="fas fa-building"></i> Department</label>
                        <input type="text" name="department" id="edit_department">
                    </div>
                </div>
                <div class="form-actions">
                    <button type="submit" class="btn btn-success">Update Supervisor</button>
                    <button type="button" class="btn btn-secondary" onclick="toggleEditForm()">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <div class="supervisors-grid">
        <table class="supervisor-table">
            <thead>
                <tr>
                    <th>User</th>
                    <th>Email</th>
                    <th>Username</th>
                    <th>Request Status</th>
                    <th>Account Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($supervisor = $supervisors->fetch_assoc()): ?>
                    <tr>
                        <td>
                            <div style="display: flex; align-items: center; gap: 10px;">
                                <div class="supervisor-avatar">
                                    <i class="fas fa-user-circle"></i>
                                </div>
                                <div>
                                    <?= htmlspecialchars($supervisor['first_name']) ?> <?= htmlspecialchars($supervisor['last_name']) ?>
                                </div>
                            </div>
                        </td>
                        <td><?= htmlspecialchars($supervisor['useremail']) ?></td>
                        <td><?= htmlspecialchars($supervisor['username']) ?></td>
                        <td>
                            <div class="request-status <?= $supervisor['request_status'] ?? 'no-request' ?>">
                                <?= ucfirst($supervisor['request_status'] ?? 'No Request') ?>
                            </div>
                        </td>
                        <td>
                            <div class="supervisor-status <?= $supervisor['status'] ?>">
                                <span class="status-dot"></span>
                                <?= ucfirst($supervisor['status']) ?>
                            </div>
                        </td>
                        <td>
                            <div class="supervisor-actions">
                                <button class="btn-edit" onclick="editSupervisor(<?= $supervisor['user_id'] ?>)">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <label class="switch">
                                    <input type="checkbox" class="status-toggle" 
                                           data-user-id="<?= $supervisor['user_id'] ?>"
                                           <?= $supervisor['status'] === 'active' ? 'checked' : '' ?>>
                                    <span class="slider round"></span>
                                </label>
                            </div>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

</div>

<?php
$content = ob_get_clean();
Page::render($content);
?>

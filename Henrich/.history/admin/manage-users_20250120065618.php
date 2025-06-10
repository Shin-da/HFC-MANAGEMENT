<?php
require_once 'access_control.php';
require_once '../includes/config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'];
    
    switch($action) {
        case 'add':
            $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
            $fname = filter_var($_POST['first_name'], FILTER_SANITIZE_STRING);
            $lname = filter_var($_POST['last_name'], FILTER_SANITIZE_STRING);
            $role = filter_var($_POST['role'], FILTER_SANITIZE_STRING);
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

// Get all users with role filter
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
<!DOCTYPE html>
<html>
<head>
    <title>Manage Users - HFC Management</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
    <link rel="stylesheet" href="../assets/css/admin-forms.css">
</head>
<body class="admin-body">
    <?php include '../includes/admin_header.php'; ?>
    
    <div class="admin-container">
        <div class="page-header">
            <h2><i class="fas fa-users"></i> Manage Users</h2>
            <div class="header-actions">
                <select class="role-filter" onchange="filterUsers(this.value)">
                    <option value="all" <?= $role_filter === 'all' ? 'selected' : '' ?>>All Users</option>
                    <option value="supervisor" <?= $role_filter === 'supervisor' ? 'selected' : '' ?>>Supervisors</option>
                    <option value="employee" <?= $role_filter === 'employee' ? 'selected' : '' ?>>Employees</option>
                    <option value="admin" <?= $role_filter === 'admin' ? 'selected' : '' ?>>Administrators</option>
                </select>
                <button class="btn btn-primary" onclick="toggleAddForm()">
                    <i class="fas fa-plus"></i> Add User
                </button>
            </div>
        </div>

        <div class="modal" id="addUserModal">
            <div class="modal-content">
                <h3>Add New User</h3>
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

        <div class="users-grid">
            <?php while ($user = $users->fetch_assoc()): ?>
            <div class="user-card" data-role="<?= $user['role'] ?>">
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
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
            $username = strtolower($fname[0] . $lname); // Create username from first initial and last name
            $temp_password = bin2hex(random_bytes(8));
            
            $stmt = $conn->prepare("INSERT INTO users (username, useremail, first_name, last_name, department, role, password, status) VALUES (?, ?, ?, ?, ?, 'supervisor', ?, 'active')");
            $stmt->bind_param("ssssss", $username, $email, $fname, $lname, $_POST['department'], $temp_password);
            
            if ($stmt->execute()) {
                // Send email with credentials
                mail($email, "Account Created", "Your account has been created.\nUsername: $username\nTemporary password: $temp_password");
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
    }
}

// Get all supervisors
$supervisors = $conn->query("SELECT * FROM users WHERE role = 'supervisor' ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Manage Supervisors - HFC Management</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
    <link rel="stylesheet" href="../assets/css/admin-forms.css">
</head>
<body class="admin-body">
    <?php include '../includes/admin_header.php'; ?>
    
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
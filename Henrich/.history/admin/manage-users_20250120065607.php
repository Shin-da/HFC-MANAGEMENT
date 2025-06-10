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
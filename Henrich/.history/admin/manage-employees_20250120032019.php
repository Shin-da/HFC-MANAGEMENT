<?php
require_once 'access_control.php';
require_once '../includes/config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'];
    
    switch($action) {
        case 'add':
            $employee_id = filter_var($_POST['employee_id'], FILTER_SANITIZE_STRING);
            $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
            $fname = filter_var($_POST['first_name'], FILTER_SANITIZE_STRING);
            $lname = filter_var($_POST['last_name'], FILTER_SANITIZE_STRING);
            
            $stmt = $conn->prepare("INSERT INTO employees (employee_id, first_name, last_name, email, department, position) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssss", $employee_id, $fname, $lname, $email, $_POST['department'], $_POST['position']);
            
            if ($stmt->execute()) {
                $success = "Employee added successfully";
            } else {
                $error = "Error adding employee";
            }
            break;
    }
}

// Get all employees
$employees = $conn->query("SELECT * FROM employees ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Manage Employees</title>
</head>
<body>
    <div class="container">
        <h2>Manage Employees</h2>
        
        <!-- Add Employee Form -->
        <form method="POST" class="add-employee-form">
            <input type="hidden" name="action" value="add">
            <input type="text" name="employee_id" placeholder="Employee ID" required>
            <input type="text" name="first_name" placeholder="First Name" required>
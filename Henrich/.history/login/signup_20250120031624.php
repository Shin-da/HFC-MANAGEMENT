<?php
require_once '../includes/config.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $username = filter_var($_POST['username'], FILTER_SANITIZE_STRING);
    $employee_id = filter_var($_POST['employee_id'], FILTER_SANITIZE_STRING);
    
    // Check if employee exists and doesn't have an account yet
    $stmt = $conn->prepare("SELECT * FROM employees WHERE employee_id = ? AND email = ?");
    $stmt->bind_param("ss", $employee_id, $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        // Create account request
        $stmt = $conn->prepare("INSERT INTO account_requests (username, email, employee_id, status) VALUES (?, ?, ?, 'pending')");
        $stmt->bind_param("sss", $username, $email, $employee_id);
        
        if ($stmt->execute()) {
            $success = "Account request submitted. Please wait for admin approval.";
        } else {
            $error = "Request failed. Please try again.";
        }
    } else {
        $error = "Employee ID and email do not match our records.";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Account Request</title>
</head>
<body>
    <div class="container">
        <h2>Request Account Access</h2>
        <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>
        <?php if (isset($success)) echo "<p class='success'>$success</p>"; ?>
        
        <form method="POST" class="form-signup">
            <input type="text" name="employee_id" placeholder="Employee ID" required>
            <input type="email" name="email" placeholder="Work Email" required>
            <input type="text" name="username" placeholder="Desired Username" required>
            <button type="submit">Submit Request</button>
        </form>
        
        <p>Already have an account? <a href="login.php">Login here</a></p>
    </div>
</body>
</html>
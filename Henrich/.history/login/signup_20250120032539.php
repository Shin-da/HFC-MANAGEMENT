<?php
require_once '../includes/config.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $username = filter_var($_POST['username'], FILTER_SANITIZE_STRING);
    
    // Check if username or email already exists
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ? OR useremail = ?");
    $stmt->bind_param("ss", $username, $email);
    $stmt->execute();
    
    
    if ($employee) {
        // Then check if request already exists
        $stmt = $conn->prepare("SELECT * FROM account_requests WHERE employee_id = ? AND status = 'pending'");
        $stmt->bind_param("s", $employee_id);
        $stmt->execute();
        
        if ($stmt->get_result()->num_rows == 0) {
            // Create new request
            $username = filter_var($_POST['username'], FILTER_SANITIZE_STRING);
            $stmt = $conn->prepare("INSERT INTO account_requests (username, email, employee_id, status) VALUES (?, ?, ?, 'pending')");
            $stmt->bind_param("sss", $username, $email, $employee_id);
            
            if ($stmt->execute()) {
                $success = "Account request submitted. Please wait for admin approval.";
            } else {
                $error = "Request failed. Please try again.";
            }
        } else {
            $error = "You already have a pending request.";
        }
    } else {
        $error = "Employee not found or inactive.";
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
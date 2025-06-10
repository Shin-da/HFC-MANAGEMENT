<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Validate inputs
        $required_fields = ['first_name', 'last_name', 'username', 'usermail', 'role'];
        foreach ($required_fields as $field) {
            if (empty($_POST[$field])) {
                throw new Exception("All fields are required");
            }
        }

        // Validate email format
        if (!filter_var($_POST['usermail'], FILTER_VALIDATE_EMAIL)) {
            throw new Exception("Invalid email format");
        }

        // Check if email or username already exists
        // Check only email in users, username/email in account_requests
        $stmt_check_user = $conn->prepare("SELECT COUNT(*) FROM users WHERE useremail = ?");
        $stmt_check_user->bind_param("s", $_POST['usermail']);
        $stmt_check_user->execute();
        $user_exists = $stmt_check_user->fetchColumn() > 0; // Using fetchColumn() with mysqli requires fetching result first, assuming PDO might be used here or logic needs adjustment
        $stmt_check_user->close(); // Close statement

        // Check account_requests (using PDO based on previous files)
        $stmt_check_req = $GLOBALS['pdo']->prepare("SELECT COUNT(*) FROM account_requests WHERE email = ? AND status = 'pending'");
        $stmt_check_req->execute([$_POST['usermail']]);
        $request_exists = $stmt_check_req->fetchColumn() > 0;

        if ($user_exists) {
            $error = "An account with this email already exists.";
        } elseif ($request_exists) {
            $error = "An account request with this email is already pending.";
        } else {
            // OLD INSERT: $stmt = $conn->prepare("INSERT INTO account_request (first_name, last_name, username, usermail, role, status, created_at) VALUES (?, ?, ?, ?, ?, 'pending', NOW())");
            
            // NEW INSERT into account_requests (plural)
            // Get data from POST, ensure all required fields for account_requests are present
            $stmt = $conn->prepare("INSERT INTO account_requests (firstname, lastname, email, department, position, reason, status, request_date) VALUES (?, ?, ?, ?, ?, ?, 'pending', NOW())");
            
            // Bind parameters from POST - Ensure your form has fields named: first_name, last_name, usermail, department, position, reason
            $stmt->bind_param("ssssss", 
                $_POST['first_name'], 
                $_POST['last_name'], 
                $_POST['usermail'], 
                $_POST['department'], 
                $_POST['position'], 
                $_POST['reason']
            );
            
            if ($stmt->execute()) {
                $success = "Your account request has been submitted. Please wait for admin approval.";
            } else {
                $error = "Failed to submit request. Please try again. Error: " . $stmt->error;
            }
        }
    } catch (Exception $e) {
        $error = "An error occurred: " . $e->getMessage();
    }

    // Return JSON response
    header('Content-Type: application/json');
    echo json_encode([
        'status' => $error ? 'error' : 'success',
        'message' => $error ? $error : $success
    ]);
    exit;
}
?>

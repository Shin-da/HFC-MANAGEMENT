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

        // Check if email or username already exists in approved_account
        $stmt = $conn->prepare("SELECT COUNT(*) FROM approved_account WHERE usermail = ? OR username = ?");
        $stmt->bind_param("ss", $_POST['usermail'], $_POST['username']);
        $stmt->execute();
        $stmt->bind_result($count);
        $stmt->fetch();
        $stmt->close();

        if ($count > 0) {
            throw new Exception("Email or username already exists");
        }

        // Check if there's already a pending request
        $stmt = $conn->prepare("SELECT COUNT(*) FROM account_request WHERE usermail = ? OR username = ?");
        $stmt->bind_param("ss", $_POST['usermail'], $_POST['username']);
        $stmt->execute();
        $stmt->bind_result($count);
        $stmt->fetch();
        $stmt->close();

        if ($count > 0) {
            throw new Exception("A request is already pending for this email or username");
        }

        // Insert the request
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
                'message' => 'Your account request has been submitted successfully. Please wait for admin approval.'
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

    // Return JSON response
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}
?>

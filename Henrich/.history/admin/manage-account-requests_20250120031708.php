<?php
require_once 'access_control.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $request_id = $_POST['request_id'];
    $action = $_POST['action'];
    $temp_password = bin2hex(random_bytes(8)); // Generate temporary password
    
    if ($action === 'approve') {
        // Begin transaction
        $conn->begin_transaction();
        try {
            // Get request details
            $stmt = $conn->prepare("SELECT * FROM account_requests WHERE id = ?");
            $stmt->bind_param("i", $request_id);
            $stmt->execute();
            $request = $stmt->get_result()->fetch_assoc();
            
            // Create user account
            $hashed_password = password_hash($temp_password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO users (username, useremail, role, password) VALUES (?, ?, 'employee', ?)");
            $stmt->bind_param("sss", $request['username'], $request['email'], $hashed_password);
            $stmt->execute();
            
            // Update request status
            $stmt = $conn->prepare("UPDATE account_requests SET status = 'approved' WHERE id = ?");
            $stmt->bind_param("i", $request_id);
            $stmt->execute();
            
            // Send email with temporary password
            mail($request['email'], 
                 "Account Approved", 
                 "Your account has been approved. Temporary password: $temp_password");
            
            $conn->commit();
            $success = "Account approved and created successfully";
        } catch (Exception $e) {
            $conn->rollback();
            $error = "Error processing request";
        }
    } elseif ($action === 'reject') {
        $stmt = $conn->prepare("UPDATE account_requests SET status = 'rejected' WHERE id = ?");
        $stmt->bind_param("i", $request_id);
        $stmt->execute();
    }
}
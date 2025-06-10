<?php
require '../../session/session.php';
require '../../database/dbconnect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $uid = filter_input(INPUT_POST, 'uid', FILTER_SANITIZE_NUMBER_INT);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
    
    // Validate inputs
    if (!$uid || !$email || !$username) {
        $_SESSION['error'] = "Invalid input data";
        header("Location: ../myaccount.php");
        exit();
    }

    try {
        // Start transaction
        $conn->begin_transaction();

        // Check if email exists for other users
        $check_stmt = $conn->prepare("SELECT uid FROM user WHERE useremail = ? AND uid != ?");
        $check_stmt->bind_param("si", $email, $uid);
        $check_stmt->execute();
        if ($check_stmt->get_result()->num_rows > 0) {
            throw new Exception("Email already in use by another account");
        }

        // Update user information
        $update_stmt = $conn->prepare("UPDATE user SET useremail = ?, username = ? WHERE uid = ?");
        $update_stmt->bind_param("ssi", $email, $username, $uid);
        if (!$update_stmt->execute()) {
            throw new Exception("Failed to update account information");
        }

        // Log the activity
        $activity = "Updated account information - Username: $username";
        $log_stmt = $conn->prepare("INSERT INTO activity_log (uid, activity, activity_type) VALUES (?, ?, 'Account Update')");
        $log_stmt->bind_param("is", $uid, $activity);
        $log_stmt->execute();

        // Commit transaction
        $conn->commit();
        $_SESSION['success'] = "Account information updated successfully";
        
    } catch (Exception $e) {
        // Rollback on error
        $conn->rollback();
        $_SESSION['error'] = $e->getMessage();
    } finally {
        // Close connections
        if (isset($check_stmt)) $check_stmt->close();
        if (isset($update_stmt)) $update_stmt->close();
        if (isset($log_stmt)) $log_stmt->close();
        $conn->close();
    }

    header("Location: ../myaccount.php");
    exit();
}

header("Location: ../myaccount.php");
exit();
?>

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
    $update_sql = "UPDATE user SET useremail = ?, username = ? WHERE uid = ?";
    $stmt = $conn->prepare($update_sql);
    $stmt->bind_param("ssi", $email, $username, $uid);
    
    if ($stmt->execute()) {
        // Log the activity
        $activity = "Updated account information - Username: $username";
        $log_sql = "INSERT INTO activity_log (uid, activity, activity_type) VALUES (?, ?, 'Account Update')";
        $log_stmt = $conn->prepare($log_sql);
        $log_stmt->bind_param("is", $uid, $activity);
        $log_stmt->execute();

        $_SESSION['success'] = "Account information updated successfully";
    } else {
        $_SESSION['error'] = "Error updating account information: " . $conn->error;
    }

    $stmt->close();
    $conn->close();
    
    header("Location: ../myaccount.php");
    exit();
} else {
    header("Location: ../myaccount.php");
    exit();
}
?>

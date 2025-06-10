<?php
require '../../session/session.php';
require '../../database/dbconnect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $uid = $_POST['uid'];
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $username = filter_var($_POST['username'], FILTER_SANITIZE_STRING);
    
    // Validate inputs
    if (empty($uid) || empty($email) || empty($username)) {
        $_SESSION['error'] = "All fields are required";
        header("Location: ../myaccount.php");
        exit();
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error'] = "Invalid email format";
        header("Location: ../myaccount.php");
        exit();
    }

    // Check if email already exists for other users
    $check_sql = "SELECT uid FROM user WHERE useremail = ? AND uid != ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("si", $email, $uid);
    $check_stmt->execute();
    $result = $check_stmt->get_result();
    
    if ($result->num_rows > 0) {
        $_SESSION['error'] = "Email already in use by another account";
        header("Location: ../myaccount.php");
        exit();
    }

    // Update user information
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

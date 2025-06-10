<?php
require_once '../../includes/session.php';
require_once '../../includes/config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        $uid = filter_input(INPUT_POST, 'uid', FILTER_SANITIZE_NUMBER_INT);
        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
        $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
        
        if (!$uid || !$email || !$username) {
            throw new Exception("All fields are required");
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception("Invalid email format");
        }

        $conn->begin_transaction();

        // Check email uniqueness
        $stmt = $conn->prepare("SELECT uid FROM user WHERE useremail = ? AND uid != ?");
        $stmt->bind_param("si", $email, $uid);
        $stmt->execute();
        if ($stmt->get_result()->num_rows > 0) {
            throw new Exception("Email already exists");
        }

        // Update user
        $stmt = $conn->prepare("UPDATE user SET useremail = ?, username = ? WHERE uid = ?");
        $stmt->bind_param("ssi", $email, $username, $uid);
        
        if (!$stmt->execute()) {
            throw new Exception("Error updating account");
        }

        $conn->commit();
        $_SESSION['success'] = "Account updated successfully";
        
    } catch (Exception $e) {
        $conn->rollback();
        $_SESSION['error'] = $e->getMessage();
    }

    header("Location: ../myaccount.php");
    exit();
}

header("Location: ../myaccount.php");
exit();
?>

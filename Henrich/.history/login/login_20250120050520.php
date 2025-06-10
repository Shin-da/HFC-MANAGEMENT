<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
require '../includes/session.php';
require '../includes/config.php';

if (isset($_POST['useremail']) && isset($_POST['password'])) {
    $useremail = trim($_POST['useremail']);
    $password = trim($_POST['password']);

    if (empty($useremail) || empty($password)) {
        header("Location: ../index.php?error=All fields are required");
        exit();
    }

    // Use prepared statement to prevent SQL injection
    $stmt = $conn->prepare("SELECT * FROM users WHERE useremail = ?");
    $stmt->bind_param("s", $useremail);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        // For testing purposes, using direct comparison (you should use password_verify in production)
        if ($password === $user['password']) {
            // Set all required session variables
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['useremail'] = $user['useremail'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['last_activity'] = time();

            // Log successful login
            error_log("Successful login - User: {$user['useremail']}, Role: {$user['role']}");

            // Redirect based on role
            switch($user['role']) {
                case 'admin':
                    header("Location: ../admin/index.php");
                    break;
                case 'supervisor':
                    header("Location: ../supervisor/index.php");
                    break;
                case 'ceo':
                    header("Location: ../ceo/index.php");
                    break;
                default:
                    header("Location: ../index.php");
            }
            exit();
        } else {
            header("Location: ../index.php?error=Invalid password");
            exit();
        }
    } else {
        header("Location: ../index.php?error=User not found");
        exit();
    }
}
?>


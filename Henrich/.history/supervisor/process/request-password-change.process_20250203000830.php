<?php
require_once '../../includes/session.php';
require_once '../../includes/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $pdo = new PDO(
            "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME,
            DB_USER,
            DB_PASSWORD,
            array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION)
        );

        $stmt = $pdo->prepare("SELECT password FROM users WHERE user_id = :user_id");
        $stmt->execute(['user_id' => $_POST['user_id']]);
        $user = $stmt->fetch();

        if ($user && password_verify($_POST['oldpassword'], $user['password'])) {
            // Generate password reset token and expiry
            $token = bin2hex(random_bytes(32));
            $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));

            $stmt = $pdo->prepare("
                UPDATE users 
                SET reset_token = :token,
                    reset_expires = :expires
                WHERE user_id = :user_id
            ");

            $stmt->execute([
                'token' => $token,
                'expires' => $expires,
                'user_id' => $_POST['user_id']
            ]);

            $_SESSION['success'] = "Password reset request approved. Please check your email.";
            // TODO: Add email sending functionality
        } else {
            $_SESSION['error'] = "Current password is incorrect.";
        }

    } catch (PDOException $e) {
        $_SESSION['error'] = "Database error: " . $e->getMessage();
    }

    header("Location: ../myaccount.php");
    exit();
}
?>

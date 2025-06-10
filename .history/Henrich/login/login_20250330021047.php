<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
require '../includes/config.php';

if (isset($_POST['useremail']) && isset($_POST['password'])) {
    $useremail = trim($_POST['useremail']);
    $password = trim($_POST['password']);

    if (empty($useremail) || empty($password)) {
        header("Location: ../index.php?error=All fields are required");
        exit();
    }

    try {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE useremail = ?");
        $stmt->execute([$useremail]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            // Check if account is active
            if ($user['status'] !== 'active') {
                header("Location: ../index.php?error=Account is not active");
                exit();
            }

            // Check for failed login attempts
            if ($user['failed_login_attempts'] >= 5) {
                $lockout_time = strtotime($user['last_failed_login']) + 1800; // 30 minutes
                if (time() < $lockout_time) {
                    header("Location: ../index.php?error=Account is temporarily locked. Please try again later.");
                    exit();
                }
            }

            // Reset failed login attempts on successful login
            $stmt = $pdo->prepare("UPDATE users SET failed_login_attempts = 0 WHERE user_id = ?");
            $stmt->execute([$user['user_id']]);

            // Set session variables
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['useremail'] = $user['useremail'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['last_activity'] = time();

            // Handle Remember Me
            if (isset($_POST['remember'])) {
                $token = bin2hex(random_bytes(32));
                $expires = date('Y-m-d H:i:s', strtotime('+30 days'));
                
                // Store remember token
                $stmt = $pdo->prepare("
                    INSERT INTO remember_tokens (user_id, token, expires) 
                    VALUES (?, ?, ?)
                ");
                $stmt->execute([$user['user_id'], $token, $expires]);
                
                // Set remember me cookie
                setcookie('remember_token', $token, strtotime('+30 days'), '/', '', true, true);
            }

            // Update online status
            $stmt = $pdo->prepare("
                UPDATE users 
                SET is_online = TRUE,
                    last_online = CURRENT_TIMESTAMP
                WHERE user_id = ?
            ");
            $stmt->execute([$user['user_id']]);

            // Log successful login
            logActivity($user['user_id'], 'login', 'Successful login');

            // Redirect based on role
            $redirect_path = match($user['role']) {
                'admin' => '../admin/index.php',
                'supervisor' => '../supervisor/index.php',
                'ceo' => '../ceo/index.php',
                default => '../index.php'
            };
            
            header("Location: $redirect_path");
            exit();
        } else {
            // Increment failed login attempts
            if ($user) {
                $stmt = $pdo->prepare("
                    UPDATE users 
                    SET failed_login_attempts = failed_login_attempts + 1,
                        last_failed_login = CURRENT_TIMESTAMP
                    WHERE user_id = ?
                ");
                $stmt->execute([$user['user_id']]);
            }
            header("Location: ../index.php?error=Invalid credentials");
            exit();
        }
    } catch (Exception $e) {
        error_log("Login error: " . $e->getMessage());
        header("Location: ../index.php?error=System error");
        exit();
    }
}
?>


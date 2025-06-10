<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
require '../includes/config.php';

// Function to generate remember me token
function generateRememberToken() {
    return bin2hex(random_bytes(32));
}

// Function to set remember me cookie
function setRememberMeCookie($token) {
    $expires = time() + (30 * 24 * 60 * 60); // 30 days
    setcookie('remember_token', $token, $expires, '/', '', true, true);
}

if (isset($_POST['useremail']) && isset($_POST['password'])) {
    $useremail = trim($_POST['useremail']);
    $password = trim($_POST['password']);
    $remember = isset($_POST['remember']) ? true : false;

    // Validate reCAPTCHA
    $recaptcha_secret = "YOUR_RECAPTCHA_SECRET_KEY";
    $recaptcha_response = $_POST['g-recaptcha-response'];
    
    $verify = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret={$recaptcha_secret}&response={$recaptcha_response}");
    $captcha_success = json_decode($verify);
    
    if (!$captcha_success->success) {
        echo "Please complete the CAPTCHA verification";
        exit();
    }

    if (empty($useremail) || empty($password)) {
        echo "All fields are required";
        exit();
    }

    try {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE useremail = ?");
        $stmt->execute([$useremail]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            // Check if account is active
            if ($user['status'] !== 'active') {
                echo "Account is not active";
                exit();
            }

            // Check for failed login attempts
            if ($user['failed_login_attempts'] >= 5) {
                $lockout_time = strtotime($user['last_failed_login']) + 1800; // 30 minutes
                if (time() < $lockout_time) {
                    echo "Account is temporarily locked. Please try again later.";
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

            // Handle remember me
            if ($remember) {
                $token = generateRememberToken();
                $expires = date('Y-m-d H:i:s', strtotime('+30 days'));
                
                // Store token in database
                $stmt = $pdo->prepare("INSERT INTO remember_tokens (user_id, token, expires_at) VALUES (?, ?, ?)");
                $stmt->execute([$user['user_id'], $token, $expires]);
                
                // Set cookie
                setRememberMeCookie($token);
            }

            // Log successful login
            $stmt = $pdo->prepare("INSERT INTO activity_logs (user_id, activity_type, details) VALUES (?, 'login', 'Successful login')");
            $stmt->execute([$user['user_id']]);

            // Update last login
            $stmt = $pdo->prepare("UPDATE users SET last_login = CURRENT_TIMESTAMP WHERE user_id = ?");
            $stmt->execute([$user['user_id']]);

            // Redirect based on role
            switch ($user['role']) {
                case 'admin':
                    echo "success";
                    break;
                case 'supervisor':
                    echo "success";
                    break;
                default:
                    echo "success";
            }
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

                // Log failed login attempt
                $stmt = $pdo->prepare("INSERT INTO activity_logs (user_id, activity_type, details) VALUES (?, 'login_failed', 'Invalid credentials')");
                $stmt->execute([$user['user_id']]);
            }
            echo "Invalid credentials";
        }
    } catch (Exception $e) {
        error_log("Login error: " . $e->getMessage());
        echo "System error";
    }
} else {
    echo "Invalid request";
}
?>


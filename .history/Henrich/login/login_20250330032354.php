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

    // Temporarily disabled reCAPTCHA validation
    /*
    $recaptcha_secret = "YOUR_RECAPTCHA_SECRET_KEY";
    $recaptcha_response = $_POST['g-recaptcha-response'];
    
    $verify = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret={$recaptcha_secret}&response={$recaptcha_response}");
    $captcha_success = json_decode($verify);
    
    if (!$captcha_success->success) {
        echo "Please complete the CAPTCHA verification";
        exit();
    }
    */

    if (empty($useremail) || empty($password)) {
        echo "All fields are required";
        exit();
    }

    try {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE useremail = ?");
        $stmt->execute([$useremail]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            if ($user['status'] === 'active') {
                // Reset failed attempts
                $stmt = $pdo->prepare("UPDATE users SET failed_attempts = 0, last_failed_attempt = NULL WHERE useremail = ?");
                $stmt->execute([$useremail]);

                // Set session variables
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['email'] = $user['useremail'];
                $_SESSION['role'] = $user['role'];
                $_SESSION['username'] = $user['username'];

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

                // Log the successful login
                try {
                    // Check if activity_logs table exists
                    $checkTable = $pdo->query("SHOW TABLES LIKE 'activity_logs'");
                    if ($checkTable->rowCount() > 0) {
                        $stmt = $pdo->prepare("INSERT INTO activity_logs (user_id, activity_type, details) VALUES (?, 'login', 'User logged in successfully')");
                        $stmt->execute([$user['user_id']]);
                    }
                } catch (Exception $e) {
                    error_log("Failed to log login activity: " . $e->getMessage());
                    // Continue with login even if logging fails
                }

                // Update last login
                try {
                    $stmt = $pdo->prepare("UPDATE users SET last_login = CURRENT_TIMESTAMP WHERE user_id = ?");
                    $stmt->execute([$user['user_id']]);
                } catch (Exception $e) {
                    error_log("Failed to update last login: " . $e->getMessage());
                    // Continue with login even if update fails
                }

                // Return success response with role
                header('Content-Type: application/json');
                echo json_encode([
                    'status' => 'success',
                    'role' => $user['role'],
                    'message' => 'Login successful'
                ]);
                exit;
            } else {
                header('Content-Type: application/json');
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Your account is not active. Please contact the administrator.'
                ]);
                exit;
            }
        } else {
            // Handle failed login attempt
            if ($user) {
                try {
                    $stmt = $pdo->prepare("UPDATE users SET failed_attempts = failed_attempts + 1, last_failed_attempt = NOW() WHERE useremail = ?");
                    $stmt->execute([$useremail]);
                } catch (Exception $e) {
                    error_log("Failed to update failed attempts: " . $e->getMessage());
                }
            }

            try {
                // Check if activity_logs table exists and log failed attempt
                if ($user) {
                    $checkTable = $pdo->query("SHOW TABLES LIKE 'activity_logs'");
                    if ($checkTable->rowCount() > 0) {
                        $stmt = $pdo->prepare("INSERT INTO activity_logs (user_id, activity_type, details) VALUES (?, 'failed_login', 'Failed login attempt')");
                        $stmt->execute([$user['user_id']]);
                    }
                }
            } catch (Exception $e) {
                error_log("Failed to log failed login attempt: " . $e->getMessage());
            }

            header('Content-Type: application/json');
            echo json_encode([
                'status' => 'error',
                'message' => 'Invalid email or password.'
            ]);
            exit;
        }
    } catch (Exception $e) {
        error_log("Login error: " . $e->getMessage());
        header('Content-Type: application/json');
        echo json_encode([
            'status' => 'error',
            'message' => 'System error occurred. Please try again.'
        ]);
        exit;
    }
} else {
    header('Content-Type: application/json');
    echo json_encode([
        'status' => 'error',
        'message' => 'Invalid request'
    ]);
    exit;
}
?>


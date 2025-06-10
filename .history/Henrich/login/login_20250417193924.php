<?php
// Prevent any output before headers
ob_start();

error_reporting(E_ALL);
ini_set('display_errors', 1); // Enable error display for debugging
ini_set('log_errors', 1);    // Enable error logging

session_start();

// Function to send JSON response
function sendJsonResponse($status, $message, $role = null) {
    header('Content-Type: application/json');
    $response = [
        'status' => $status,
        'message' => $message
    ];
    if ($role !== null) {
        $response['role'] = $role;
    }
    echo json_encode($response);
    exit;
}

try {
require '../includes/config.php';
} catch (Exception $e) {
    error_log("Config file error: " . $e->getMessage());
    sendJsonResponse('error', 'Configuration error occurred. Please try again.');
}

// --- Lockout Configuration ---
define('MAX_LOGIN_ATTEMPTS', 5); // Max attempts before lockout
define('LOCKOUT_DURATION_MINUTES', 15); // Lockout duration in minutes
// --- End Lockout Configuration ---

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

    if (empty($useremail) || empty($password)) {
        sendJsonResponse('error', 'All fields are required');
    }

    try {
        // Debug information
        error_log("Attempting login for email: " . $useremail);
        
        $stmt = $pdo->prepare("SELECT * FROM users WHERE useremail = ?");
        if (!$stmt) {
            error_log("Prepare failed: " . print_r($pdo->errorInfo(), true));
            sendJsonResponse('error', 'Database error occurred');
        }
        
        $stmt->execute([$useremail]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        error_log("User found: " . ($user ? "Yes" : "No"));

        if ($user) {
            // --- BEGIN LOCKOUT CHECK ---
            if ($user['failed_login_attempts'] >= MAX_LOGIN_ATTEMPTS) {
                if (!empty($user['last_failed_login'])) {
                    $last_failed_time = strtotime($user['last_failed_login']);
                    $lockout_expiry_time = $last_failed_time + (LOCKOUT_DURATION_MINUTES * 60);
                    
                    if (time() < $lockout_expiry_time) {
                        // Lockout is still active
                        $remaining_minutes = ceil(($lockout_expiry_time - time()) / 60);
                        error_log("Account locked for user: " . $useremail . ". Attempts: " . $user['failed_login_attempts']);
                        sendJsonResponse('error', 'Account locked due to too many failed login attempts. Please try again in ' . $remaining_minutes . ' minutes.');
                    }
                    // If lockout expired, proceed to password check below.
                }
            }
            // --- END LOCKOUT CHECK ---
            
            // Verify password IF NOT LOCKED OUT
            if (password_verify($password, $user['password'])) {
                if ($user['status'] === 'active') {
                    // Reset failed attempts
                    $stmt = $pdo->prepare("UPDATE users SET failed_login_attempts = 0, last_failed_login = NULL WHERE useremail = ?");
                    $stmt->execute([$useremail]);

                    // Set session variables
                    $_SESSION['user_id'] = $user['user_id'];
                    $_SESSION['email'] = $user['useremail'];
                    $_SESSION['role'] = $user['role'];
                    $_SESSION['username'] = $user['username'];

                    // Handle remember me - temporarily disabled
                    /*
                    if ($remember) {
                        $token = generateRememberToken();
                        $expires = date('Y-m-d H:i:s', strtotime('+30 days'));
                        
                        // Store token in database - Use 'expires' column
                        $stmt = $pdo->prepare("INSERT INTO remember_tokens (user_id, token, expires) VALUES (?, ?, ?)");
                        $stmt->execute([$user['user_id'], $token, $expires]);
                        
                        // Set cookie
                        error_log("[DEBUG] login.php: Attempting to set remember_token cookie with token: " . $token); // Debug Log
                        setRememberMeCookie($token);
                        error_log("[DEBUG] login.php: setRememberMeCookie function called."); // Debug Log
                    }
                    */

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
                    }

                    // Update last login and online status
                    try {
                        $stmt = $pdo->prepare("UPDATE users SET last_online = CURRENT_TIMESTAMP, is_online = TRUE WHERE user_id = ?");
                        $stmt->execute([$user['user_id']]);
                    } catch (Exception $e) {
                        error_log("Failed to update last login: " . $e->getMessage());
                    }

                    error_log("Login successful for user: " . $useremail);
                    sendJsonResponse('success', 'Login successful', $user['role']);
                } else {
                    sendJsonResponse('error', 'Your account is not active. Please contact the administrator.');
                }
            } else {
                // Handle failed login attempt
                if ($user) {
                    try {
                        $stmt = $pdo->prepare("UPDATE users SET failed_login_attempts = failed_login_attempts + 1, last_failed_login = NOW() WHERE useremail = ?");
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

                error_log("Login failed for user: " . $useremail);
                sendJsonResponse('error', 'Invalid email or password.');
            }
        } else {
            // User not found
            error_log("Login failed: User not found for email: " . $useremail);
            sendJsonResponse('error', 'Invalid email or password.'); // Keep error generic
        }
    } catch (Exception $e) {
        error_log("Login error: " . $e->getMessage());
        error_log("Stack trace: " . $e->getTraceAsString());
        sendJsonResponse('error', 'System error occurred. Please try again.');
    }
} else {
    sendJsonResponse('error', 'Invalid request');
}
?>


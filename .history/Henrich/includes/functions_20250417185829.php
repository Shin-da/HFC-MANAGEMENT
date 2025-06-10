<?php
function sanitize_input($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

function validate_email($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

function generate_random_password() {
    return bin2hex(random_bytes(8));
}

function generateRememberToken() {
    return bin2hex(random_bytes(32));
}

function setRememberMeCookie($token) {
    $expires = time() + (30 * 24 * 60 * 60); // 30 days expiry
    // Ensure parameters match setcookie signature: name, value, expires, path, domain, secure, httponly
    // Set secure flag to false for localhost HTTP testing
    setcookie('remember_token', $token, $expires, '/', '', false, true);
}

function send_notification($to, $subject, $message) {
    // Add your email configuration here
    $headers = "From: noreply@hfcmanagement.com\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
    
    return mail($to, $subject, $message, $headers);
}

function log_activity($user_id, $action, $details = '') {
    global $conn;
    $stmt = $conn->prepare("INSERT INTO activity_logs (user_id, action, details) VALUES (?, ?, ?)");
    $stmt->bind_param("iss", $user_id, $action, $details);
    return $stmt->execute();
}

function logActivity($userId, $action, $description) {
    global $conn;
    try {
        $stmt = $conn->prepare("INSERT INTO activity_logs (user_id, action, description) VALUES (?, ?, ?)");
        $stmt->bind_param("iss", $userId, $action, $description);
        return $stmt->execute();
    } catch (mysqli_sql_exception $e) {
        error_log("Error logging activity: " . $e->getMessage());
        return false;
    }
}

function check_session_timeout() {
    if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > 1800)) {
        session_destroy();
        header("Location: ../index.php?error=session_timeout");
        exit();
    }
    $_SESSION['last_activity'] = time();
}

function check_remember_token() {
    if (isset($_COOKIE['remember_token'])) {
        // Verify remember token logic here
    }
}

function hasPermission($requiredRole): bool {
    return $_SESSION['role'] === $requiredRole || $_SESSION['role'] === 'admin';
}

// --- Function to handle Remember Me cookie verification ---
function attemptRememberMeLogin() {
    // Check if user is already logged in via session
    if (isset($_SESSION['user_id'])) {
        return true; // Already logged in
    }

    // Check if remember me cookie exists
    if (isset($_COOKIE['remember_token'])) {
        // Need access to config for DB connection
        // This assumes config.php is already included or includes this file
        if (!isset($GLOBALS['pdo'])) {
            error_log("PDO connection not available in attemptRememberMeLogin");
            return false;
        }
        
        $token = $_COOKIE['remember_token'];
        
        // Verify token
        $stmt = $GLOBALS['pdo']->prepare("
            SELECT u.*
            FROM users u
            JOIN remember_tokens rt ON u.user_id = rt.user_id
            WHERE rt.token = ?
            AND expires > NOW()
            AND u.status = 'active'
        ");
        $stmt->execute([$token]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            // Valid token found - Log user in and rotate token
            $old_token = $_COOKIE['remember_token'];

            // 1. Delete the old token
            $stmt_delete = $GLOBALS['pdo']->prepare("DELETE FROM remember_tokens WHERE token = ?");
            $stmt_delete->execute([$old_token]);

            // 2. Generate a new token
            $new_token = generateRememberToken(); // Assumes this function is available
            $new_expires_db = date('Y-m-d H:i:s', strtotime('+30 days'));

            // 3. Store the new token
            $stmt_insert = $GLOBALS['pdo']->prepare("INSERT INTO remember_tokens (user_id, token, expires) VALUES (?, ?, ?)");
            $stmt_insert->execute([$user['user_id'], $new_token, $new_expires_db]);

            // 4. Set the new cookie
            setRememberMeCookie($new_token); // Assumes this function is available

            // 5. Restore session
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['useremail'] = $user['useremail']; // Changed from email to useremail to match DB
            $_SESSION['role'] = $user['role'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['timeout'] = time(); // Reset session timeout
            $_SESSION['last_activity'] = time(); // Update last activity
            
            // Update online status
            try {
                $stmt_online = $GLOBALS['pdo']->prepare("UPDATE users SET is_online = TRUE, last_online = CURRENT_TIMESTAMP WHERE user_id = ?");
                $stmt_online->execute([$user['user_id']]);
            } catch (Exception $e) {
                 error_log("Failed to update online status during remember me login: " . $e->getMessage());
            }
            
            error_log("User logged in via Remember Me: " . $user['useremail']);
            return true; // Login successful
        } else {
            // Invalid or expired token - clear the cookie
            error_log("Invalid or expired remember me token found: " . $token);
            setcookie('remember_token', '', time() - 3600, '/'); 
            return false; // Login failed
        }
    }
    
    return false; // No cookie or already logged in
}
// --- End Remember Me Function ---

?>

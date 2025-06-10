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
    setcookie('remember_token', $token, $expires, '/', '', true, true);
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
?>

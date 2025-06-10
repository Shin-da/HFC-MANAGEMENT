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
?>

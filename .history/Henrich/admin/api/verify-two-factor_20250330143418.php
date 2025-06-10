<?php
require_once '../../includes/config.php';
require_once '../access_control.php';
require_once '../../vendor/autoload.php'; // For Google Authenticator

use PHPGangsta\GoogleAuthenticator\GoogleAuthenticator;

header('Content-Type: application/json');

try {
    if (!isset($_POST['code'])) {
        throw new Exception('Verification code is required');
    }

    if (!isset($_SESSION['temp_2fa_secret'])) {
        throw new Exception('Two-factor setup session expired. Please try again.');
    }

    $ga = new GoogleAuthenticator();
    $code = $_POST['code'];
    $secret = $_SESSION['temp_2fa_secret'];

    // Verify the code
    if (!$ga->verifyCode($secret, $code, 2)) {
        throw new Exception('Invalid verification code');
    }

    // Store the secret in the database
    $stmt = $GLOBALS['pdo']->prepare("
        UPDATE users 
        SET two_factor_secret = :secret,
            two_factor_enabled = 1
        WHERE user_id = :user_id
    ");

    $stmt->execute([
        ':secret' => $secret,
        ':user_id' => $_SESSION['user_id']
    ]);

    // Clear temporary secret from session
    unset($_SESSION['temp_2fa_secret']);

    // Log the action
    logAdminAction("Enabled two-factor authentication");

    echo json_encode([
        'success' => true,
        'message' => 'Two-factor authentication enabled successfully'
    ]);

} catch (Exception $e) {
    error_log("Error in verify-two-factor.php: " . $e->getMessage());
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}

function logAdminAction($action) {
    $stmt = $GLOBALS['pdo']->prepare("
        INSERT INTO admin_logs (admin_id, action, ip_address)
        VALUES (:admin_id, :action, :ip)
    ");

    $stmt->execute([
        ':admin_id' => $_SESSION['user_id'],
        ':action' => $action,
        ':ip' => $_SERVER['REMOTE_ADDR']
    ]);
} 
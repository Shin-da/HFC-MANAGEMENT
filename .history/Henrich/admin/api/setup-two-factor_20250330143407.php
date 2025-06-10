<?php
require_once '../../includes/config.php';
require_once '../access_control.php';
require_once '../../vendor/autoload.php'; // For Google Authenticator

use PHPGangsta\GoogleAuthenticator\GoogleAuthenticator;

header('Content-Type: application/json');

try {
    $ga = new GoogleAuthenticator();
    
    // Generate secret key
    $secret = $ga->createSecret();
    
    // Generate QR code URL
    $userEmail = $_SESSION['user_email'];
    $qrCodeUrl = $ga->getQRCodeGoogleUrl(
        'HFC Management',
        $secret,
        $userEmail
    );

    // Store secret temporarily in session
    $_SESSION['temp_2fa_secret'] = $secret;

    echo json_encode([
        'success' => true,
        'secret' => $secret,
        'qrCodeUrl' => $qrCodeUrl
    ]);

} catch (Exception $e) {
    error_log("Error in setup-two-factor.php: " . $e->getMessage());
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
} 
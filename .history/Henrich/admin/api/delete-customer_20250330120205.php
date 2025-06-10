<?php
require_once '../../includes/config.php';
require_once '../access_control.php';

header('Content-Type: application/json');

try {
    // Get POST data
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($data['accountId'])) {
        throw new Exception('Account ID is required');
    }

    $accountId = (int)$data['accountId'];

    // Check if customer exists
    $stmt = $GLOBALS['pdo']->prepare("SELECT profilepicture FROM customeraccount WHERE accountid = ?");
    $stmt->execute([$accountId]);
    $customer = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$customer) {
        throw new Exception('Customer not found');
    }

    // Delete profile picture if it's not the default one
    if ($customer['profilepicture'] !== 'default.jpg') {
        $picturePath = '../../' . $customer['profilepicture'];
        if (file_exists($picturePath)) {
            unlink($picturePath);
        }
    }

    // Delete customer
    $stmt = $GLOBALS['pdo']->prepare("DELETE FROM customeraccount WHERE accountid = ?");
    $stmt->execute([$accountId]);

    echo json_encode([
        'status' => 'success',
        'message' => 'Customer deleted successfully'
    ]);
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
} 
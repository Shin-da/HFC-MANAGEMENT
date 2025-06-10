<?php
require_once '../../includes/config.php';
require_once '../access_control.php';

header('Content-Type: application/json');

try {
    if (!isset($_GET['accountId'])) {
        throw new Exception('Account ID is required');
    }

    $accountId = (int)$_GET['accountId'];

    $stmt = $GLOBALS['pdo']->prepare("SELECT * FROM customeraccount WHERE accountid = ?");
    $stmt->execute([$accountId]);
    $customer = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$customer) {
        throw new Exception('Customer not found');
    }

    // Remove sensitive data
    unset($customer['password']);

    echo json_encode([
        'status' => 'success',
        'customer' => $customer
    ]);
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
} 
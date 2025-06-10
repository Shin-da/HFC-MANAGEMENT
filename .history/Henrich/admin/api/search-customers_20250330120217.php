<?php
require_once '../../includes/config.php';
require_once '../access_control.php';

header('Content-Type: application/json');

try {
    $search = isset($_GET['search']) ? trim($_GET['search']) : '';
    $status = isset($_GET['status']) ? trim($_GET['status']) : '';
    
    // Build the query
    $sql = "SELECT * FROM customeraccount WHERE 1=1";
    $params = [];

    if (!empty($search)) {
        $sql .= " AND (
            customername LIKE ? OR 
            customeraddress LIKE ? OR 
            customerphonenumber LIKE ? OR 
            useremail LIKE ? OR 
            username LIKE ?
        )";
        $searchParam = "%$search%";
        $params = array_merge($params, [$searchParam, $searchParam, $searchParam, $searchParam, $searchParam]);
    }

    if (!empty($status)) {
        $sql .= " AND status = ?";
        $params[] = $status;
    }

    // Add ordering
    $sql .= " ORDER BY customername ASC";

    // Execute the query
    $stmt = $GLOBALS['pdo']->prepare($sql);
    $stmt->execute($params);
    $customers = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Mask passwords
    foreach ($customers as &$customer) {
        $customer['password'] = '••••••••';
    }

    echo json_encode([
        'status' => 'success',
        'customers' => $customers
    ]);
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
} 
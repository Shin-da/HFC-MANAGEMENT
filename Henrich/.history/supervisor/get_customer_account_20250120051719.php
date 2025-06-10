<?php

header('Content-Type: application/json');

try {
    if (!isset($_GET['id'])) {
        throw new Exception('No account ID provided');
    }

    $accountId = intval($_GET['id']);
    
    // Check if the account exists
    $sql = "SELECT cd.* FROM customerdetails cd 
            WHERE cd.customerid = ?";
            
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        throw new Exception("Prepare failed: " . $conn->error);
    }

    $stmt->bind_param("i", $accountId);
    $stmt->execute();
    $result = $stmt->get_result();
    $account = $result->fetch_assoc();

    if ($account) {
        // Get total orders
        $ordersSql = "SELECT COUNT(*) as orderCount 
                     FROM customerorder 
                     WHERE customername = ?";
        $orderStmt = $conn->prepare($ordersSql);
        $orderStmt->bind_param("s", $account['customername']);
        $orderStmt->execute();
        $orderCount = $orderStmt->get_result()->fetch_assoc()['orderCount'];

        echo json_encode([
            'success' => true,
            'account' => $account,
            'orderCount' => $orderCount
        ]);
    } else {
        throw new Exception('Account not found');
    }
} catch (Exception $e) {
    error_log("Error in get_customer_account.php: " . $e->getMessage());
    echo json_encode([
        'success' => false, 
        'message' => $e->getMessage()
    ]);
} finally {
    if (isset($stmt)) $stmt->close();
    if (isset($orderStmt)) $orderStmt->close();
}

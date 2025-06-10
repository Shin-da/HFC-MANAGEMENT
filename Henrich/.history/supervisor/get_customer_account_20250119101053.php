<?php
require '../database/dbconnect.php';
header('Content-Type: application/json');

try {
    if (!isset($_GET['id'])) {
        throw new Exception('No account ID provided');
    }

    $accountId = intval($_GET['id']);
    
    // Check if the account exists
            FROM customeraccount 
            WHERE accountid = ? AND accountstatus != 'Deleted'";
            
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $accountId);
    $stmt->execute();
    $result = $stmt->get_result();
    $account = $result->fetch_assoc();

    if ($account) {
        echo json_encode([
            'success' => true,
            'account' => [
                'customername' => htmlspecialchars_decode($account['customername']),
                'username' => htmlspecialchars_decode($account['username']),
                'useremail' => htmlspecialchars_decode($account['useremail']),
                'accountstatus' => $account['accountstatus'],
                'accounttype' => $account['accounttype'],
                'customeraddress' => htmlspecialchars_decode($account['customeraddress']),
                'customerphonenumber' => htmlspecialchars_decode($account['customerphonenumber'])
            ]
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Account not found']);
    }
} catch (Exception $e) {
    error_log("Error in get_customer_account.php: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Database error occurred']);
}

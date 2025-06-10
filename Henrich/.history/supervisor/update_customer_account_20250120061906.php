<?php
require_once '../includes/config.php';
header('Content-Type: application/json');

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Invalid request method');
    }

    $accountId = $_POST['accountid'];
    $customerName = $_POST['customername'];
    $userEmail = $_POST['useremail'];
    $username = $_POST['username'];
    $accountStatus = $_POST['accountstatus'];

    // Start transaction
    $conn->begin_transaction();

    // Check if email or username already exists
    $check_sql = "SELECT accountid FROM customeraccount 
                  WHERE (useremail = ? OR username = ?) 
                  AND accountid != ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("ssi", $userEmail, $username, $accountId);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();

    if ($check_result->num_rows > 0) {
        throw new Exception('Email or username already exists');
    }

    // Update account
    $sql = "UPDATE customeraccount 
            SET customername = ?, useremail = ?, username = ?, accountstatus = ?, 
                modified_at = CURRENT_TIMESTAMP 
            WHERE accountid = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssi", $customerName, $userEmail, $username, $accountStatus, $accountId);
    $stmt->execute();

    if ($stmt->affected_rows > 0 || $stmt->errno === 0) {
        $conn->commit();
        echo json_encode([
            'success' => true,
            'message' => 'Account updated successfully'
        ]);
    } else {
        throw new Exception('No changes made or update failed');
    }

} catch (Exception $e) {
    if ($conn->inTransaction()) {
        $conn->rollback();
    }
    error_log("Error in update_customer_account.php: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
} finally {
    if (isset($stmt)) $stmt->close();
    if (isset($check_stmt)) $check_stmt->close();
}

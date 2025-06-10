<?php
require '../inc/dbconnect.php';
header('Content-Type: application/json');

try {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($data['id'])) {
        throw new Exception('No account ID provided');
    }

    $accountId = $data['id'];

    // Start transaction
    $conn->begin_transaction();

    // Soft delete by updating status
    $sql = "UPDATE customeraccount 
            SET accountstatus = 'Deleted', 
                modified_at = CURRENT_TIMESTAMP 
            WHERE accountid = ? AND accountstatus != 'Deleted'";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $accountId);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        $conn->commit();
        echo json_encode([
            'success' => true,
            'message' => 'Account deleted successfully'
        ]);
    } else {
        throw new Exception('Account not found or already deleted');
    }

} catch (Exception $e) {
    if ($conn->inTransaction()) {
        $conn->rollback();
    }
    error_log("Error in delete_customer_account.php: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
} finally {
    if (isset($stmt)) $stmt->close();
}

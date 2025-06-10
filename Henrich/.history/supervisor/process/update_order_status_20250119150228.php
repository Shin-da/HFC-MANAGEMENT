<?php
require '../../session/session.php';
require '../../database/dbconnect.php';

header('Content-Type: application/json');

try {
    $input = json_decode(file_get_contents('php://input'), true);
    $orderId = $input['orderId'];
    $status = $input['status'];
    
    $stmt = $conn->prepare("UPDATE customerorder SET status = ?, datecompleted = CASE WHEN ? = 'Completed' THEN NOW() ELSE datecompleted END WHERE orderid = ?");
    $stmt->bind_param("ssi", $status, $status, $orderId);
    
    if ($stmt->execute()) {
        $_SESSION['sweetalert'] = [
            'icon' => 'success',
            'title' => 'Status Updated',
            'text' => "Order status has been updated to $status"
        ];
        echo json_encode(['success' => true]);
    } else {
        throw new Exception($stmt->error);
    }
} catch (Exception $e) {
    $_SESSION['sweetalert'] = [
        'icon' => 'error',
        'title' => 'Error',
        'text' => $e->getMessage()
    ];
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}

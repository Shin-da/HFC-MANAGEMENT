<?php
require '../database/dbconnect.php';

try {
    // Start transaction
    $conn->begin_transaction();

    // Get the maximum customerid from customerdetails
    $max_id_query = "SELECT COALESCE(MAX(customerid), 0) as max_id FROM customerdetails";
    $max_id_result = $conn->query($max_id_query);
                WHERE cd.customername = co.customername 
                AND cd.customerphonenumber = co.customerphonenumber
            )";
    
    $result = $conn->query($sql);
    
    if ($result) {
        echo json_encode(['success' => true, 'message' => 'Customers synchronized successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error synchronizing customers']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

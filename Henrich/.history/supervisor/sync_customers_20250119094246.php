<?php
require '../database/dbconnect.php';

try {
    // Get unique customers from customerorder
    $sql = "INSERT INTO customerdetails (customername, customeraddress, customerphonenumber)
            SELECT DISTINCT customername, customeraddress, customerphonenumber
            FROM customerorder co
            WHERE NOT EXISTS (
                SELECT 1 FROM customerdetails cd 
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

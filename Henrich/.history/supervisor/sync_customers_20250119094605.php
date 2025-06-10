<?php
require '../database/dbconnect.php';

try {
    // Start transaction
    $conn->begin_transaction();

    // Get the maximum customerid from customerdetails
    $max_id_query = "SELECT COALESCE(MAX(customerid), 0) as max_id FROM customerdetails";
    $max_id_result = $conn->query($max_id_query);
    $next_id = $max_id_result->fetch_assoc()['max_id'] + 1;

    // Get unique customers from customerorder that don't exist in customerdetails
    $sql = "INSERT INTO customerdetails (customerid, customername, customeraddress, customerphonenumber)
            SELECT 
                @row_number:=@row_number + 1 + $next_id,
                co.customername,
                co.customeraddress,
                co.customerphonenumber
            FROM (SELECT DISTINCT customername, customeraddress, customerphonenumber 
                  FROM customerorder) co
            LEFT JOIN customerdetails cd 
                ON cd.customername = co.customername 
                AND cd.customerphonenumber = co.customerphonenumber
            CROSS JOIN (SELECT @row_number:=0) r
            WHERE cd.customerid IS NULL";
    
    // Execute the query
    $result = $conn->query($sql);
    
    if ($result) {
        // Get number of affected rows
        $affected = $conn->affected_rows;
        $conn->commit();
        echo json_encode([
            'success' => true, 
            'message' => "Successfully synchronized $affected customers",
            'affected' => $affected
        ]);
    } else {
        throw new Exception('Error synchronizing customers: ' . $conn->error);
    }
} catch (Exception $e) {
    $conn->rollback();
    echo json_encode([
        'success' => false, 
        'message' => $e->getMessage()
    ]);
} finally {
    // Reset the MySQL variable
    $conn->query("SET @row_number = 0");
}

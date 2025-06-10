<?php
require '../database/dbconnect.php';
header('Content-Type: application/json');

try {
    $conn->begin_transaction();

    // First, create the customerdetails table if it doesn't exist
    $create_table = "CREATE TABLE IF NOT EXISTS customerdetails (
        customerid INT PRIMARY KEY AUTO_INCREMENT,
        customername VARCHAR(255) NOT NULL,
        customeraddress TEXT,
        customerphonenumber VARCHAR(20),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    $conn->query($create_table);

    // Get unique customers from customerorder that don't exist in customerdetails
    $insert_sql = "INSERT INTO customerdetails (customername, customeraddress, customerphonenumber)
            SELECT DISTINCT co.customername, co.customeraddress, co.customerphonenumber
            FROM customerorder co
            LEFT JOIN customerdetails cd 
                ON cd.customername = co.customername 
                AND cd.customerphonenumber = co.customerphonenumber
            WHERE cd.customerid IS NULL";
    
    $result = $conn->query($insert_sql);
    
    if ($result !== false) {
        $affected = $conn->affected_rows;
        $conn->commit();
        
        echo json_encode([
            'success' => true,
            'message' => "Successfully synchronized $affected customers",
            'affected' => $affected,
            'timestamp' => date('Y-m-d H:i:s')
        ]);
    } else {
        throw new Exception('Error executing sync query: ' . $conn->error);
    }
} catch (Exception $e) {
    if ($conn->connect_errno) {
        $error_message = "Database connection failed: " . $conn->connect_error;
    } else {
        $error_message = $e->getMessage();
    }
    
    try {
        $conn->rollback();
    } catch (Exception $e) {
        // Transaction was already rolled back or wasn't started
    }
    
    error_log("Sync error: " . $error_message);
    
    echo json_encode([
        'success' => false,
        'message' => "Sync failed: " . $error_message,
        'timestamp' => date('Y-m-d H:i:s')
    ]);
} finally {
    // Clean up
    if (isset($stmt)) {
        $stmt->close();
    }
}

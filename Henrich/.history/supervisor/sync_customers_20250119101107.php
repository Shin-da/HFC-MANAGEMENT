<?php
require '../database/dbconnect.php';
header('Content-Type: application/json');

try {
    $conn->begin_transaction();

    // Get maximum existing customerid
    $max_id_query = "SELECT COALESCE(MAX(customerid), 0) as max_id FROM customerdetails";
    $max_id_result = $conn->query($max_id_query);
    $next_id = $max_id_result->fetch_assoc()['max_id'] + 1;

    // Insert new unique customers
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
    if ($conn->inTransaction()) {
        $conn->rollback();
    }
    
    error_log("Sync error: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => "Sync failed: " . $e->getMessage(),
        'timestamp' => date('Y-m-d H:i:s')
    ]);
} finally {
    $conn->close();
}
    error_log("Sync error: " . $error_message);
    
    echo json_encode([
        'success' => false,
        'message' => "Sync failed: " . $error_message,
        'timestamp' => date('Y-m-d H:i:s')
    ]);
} finally {
    $conn->close();
}


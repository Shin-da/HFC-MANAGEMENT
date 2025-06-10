<?php
if (!isset($conn)) {
    require_once '../../includes/config.php';
    require_once '../../includes/session.php';
}

require_once '../../includes/notifications_helper.php';

try {
    error_log("==== START INVENTORY UPDATE DEBUG ====");
    error_log("Session data: " . json_encode($_SESSION['stockmovement_data']));
    
    $stockData = $_SESSION['stockmovement_data'] ?? null;
    
    if (!$stockData) {
        throw new Exception('No stock data found in session');
    }

    // Validate the stock data structure
    $requiredFields = ['productcode', 'productname', 'totalpacks', 'batchid'];
    foreach ($requiredFields as $field) {
        if (!isset($stockData[$field]) || !is_array($stockData[$field]) || empty($stockData[$field])) {
            throw new Exception("Missing or invalid required field: {$field}");
        }
    }

    $conn->begin_transaction();

    // Prepare statements once
    $updateSql = "
        UPDATE inventory 
        SET availablequantity = availablequantity + ?,
            onhandquantity = onhandquantity + ?,
            dateupdated = CURRENT_TIMESTAMP
        WHERE productcode = ?
    ";
    $updateStmt = $conn->prepare($updateSql);

    $insertSql = "
        INSERT INTO inventory (
            productcode,
            productname,
            productcategory,
            availablequantity,
            onhandquantity
        ) VALUES (?, ?, ?, ?, ?)
    ";
    $insertStmt = $conn->prepare($insertSql);
    
    // Prepare stockactivitylog statement
    $logSql = "
        INSERT INTO stockactivitylog (
            batchid,
            dateofarrival,
            dateencoded,
            encoder,
            description,
            totalNumberOfBoxes,
            overalltotalweight,
            activity_type,
            productcode,
            quantity_changed,
            movement_type,
            performed_by,
            details
        ) VALUES (?, CURRENT_DATE, CURRENT_TIMESTAMP, ?, ?, ?, ?, 'INVENTORY_UPDATE', ?, ?, 'IN', ?, ?)
    ";
    $logStmt = $conn->prepare($logSql);

    $successfulUpdates = 0;
    $failedUpdates = 0;
    $totalProducts = count($stockData['productcode']);
    $updateErrors = [];

    foreach ($stockData['productcode'] as $key => $productcode) {
        try {
            // Create local variables for binding
            $quantity = (int)$stockData['totalpacks'][$key];
            $shortCode = substr($productcode, 0, 3);
            $productName = $stockData['productname'][$key];
            $boxCount = isset($stockData['numberofbox'][$key]) ? (int)$stockData['numberofbox'][$key] : 0;
            $totalWeight = isset($stockData['totalweight'][$key]) ? (float)$stockData['totalweight'][$key] : 0.0;
            
            error_log("Processing product: {$shortCode} - {$productName} - Quantity: {$quantity}");
            
            // Bind all parameters at once for update
            $updateStmt->bind_param("iis", $quantity, $quantity, $shortCode);
            
            if (!$updateStmt->execute()) {
                throw new Exception("Error updating inventory: " . $updateStmt->error);
            }
            
            // Check if product was updated or needs to be inserted
            if ($updateStmt->affected_rows === 0) {
                error_log("No rows updated, attempting insert for product code: {$shortCode}");
                
                $insertQuantity = $quantity;
                $insertName = $productName;
                $insertCategory = 'DEFAULT';
                
                $insertStmt->bind_param("sssii", 
                    $shortCode,
                    $insertName,
                    $insertCategory,
                    $insertQuantity,
                    $insertQuantity
                );
                
                if (!$insertStmt->execute()) {
                    throw new Exception("Error inserting inventory: " . $insertStmt->error);
                }
                
                error_log("Successfully inserted product: {$shortCode}");
            } else {
                error_log("Successfully updated product: {$shortCode}");
            }
            
            // Log the activity for this product
            $encoderName = $_SESSION['username'] ?? 'system';
            $batchId = $stockData['batchid'];
            $description = "Stock movement IN - {$productName}";
            $details = "Added {$quantity} units of {$productName} (Code: {$shortCode}) to inventory";
            
            $logStmt->bind_param("sssidssss", 
                $batchId,
                $encoderName,
                $description,
                $boxCount,
                $totalWeight,
                $shortCode,
                $quantity,
                $encoderName,
                $details
            );
            
            if (!$logStmt->execute()) {
                error_log("Warning: Failed to log activity: " . $logStmt->error);
            }
            
            $successfulUpdates++;
        } catch (Exception $e) {
            error_log("Error processing product {$shortCode}: " . $e->getMessage());
            $updateErrors[] = "Product {$productName} ({$shortCode}): " . $e->getMessage();
            $failedUpdates++;
        }
    }

    // Create notification
    $notificationHelper = new NotificationHelper($conn);
    $notificationMessage = "Added new stock movement - Batch ID: {$stockData['batchid']} - Success: {$successfulUpdates}/{$totalProducts}";
    
    if ($failedUpdates > 0) {
        $notificationMessage .= " - Failed: {$failedUpdates}";
    }
    
    $activityId = $notificationHelper->logActivity(
        $_SESSION['user_id'],
        $notificationMessage,
        "stock_movement"
    );

    $conn->commit();
    
    // Clean up session data
    unset($_SESSION['stockmovement_data']);

    echo json_encode([
        'status' => 'success',
        'message' => 'Stock movement processed successfully',
        'stats' => [
            'total' => $totalProducts,
            'success' => $successfulUpdates,
            'failed' => $failedUpdates
        ],
        'errors' => $updateErrors,
        'swal' => [
            'icon' => 'success',
            'title' => 'Success!',
            'text' => "Stock movement has been processed ({$successfulUpdates}/{$totalProducts} items updated)"
        ],
        'redirect' => 'stockactivitylog.php'
    ]);

    $_SESSION['process_messages'][] = [
        'title' => 'Inventory Update',
        'text' => 'Inventory levels updated successfully',
        'icon' => 'success'
    ];

    error_log("==== END INVENTORY UPDATE DEBUG ====");
    return true;

} catch (Exception $e) {
    // Check if transaction is active and roll it back
    try {
        $conn->rollback();
    } catch (Exception $rollbackError) {
        error_log("Rollback error: " . $rollbackError->getMessage());
    }
    
    error_log("Inventory update error: " . $e->getMessage());
    error_log("Error trace: " . $e->getTraceAsString());
    
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage(),
        'trace' => $e->getTraceAsString()
    ]);    
    
    $_SESSION['process_messages'][] = [
        'title' => 'Inventory Error',
        'text' => $e->getMessage(),
        'icon' => 'error'
    ];
    throw $e;
}
?>


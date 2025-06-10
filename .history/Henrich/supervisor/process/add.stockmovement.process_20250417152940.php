<?php
ob_start();
error_reporting(E_ALL);
ini_set('display_errors', 0);

require_once '../../includes/config.php';
require_once '../../includes/session.php';
require_once '../../includes/helper_functions.php';

// Clean output buffer
while (ob_get_level()) ob_end_clean();
header('Content-Type: application/json');

try {
    // Validate incoming data
    if (!isset($_POST['batchid']) || !isset($_POST['productcode'])) {
        throw new Exception('Missing required data');
    }

    $conn->begin_transaction();
    
    // Initialize messages array
    $processMessages = [];
    
    // Get form data
    $batchId = $_POST['batchid'];
    $dateOfArrival = $_POST['dateofarrival'];
    $dateEncoded = $_POST['dateencoded'];
    $encoder = $_POST['encoder'];
    
    // First, insert into stockactivitylog
    $logSql = "INSERT INTO stockactivitylog (
        batchid, dateofarrival, dateencoded, encoder, 
        totalNumberOfBoxes, overalltotalweight, activity_type
    ) VALUES (?, ?, ?, ?, ?, ?, 'IN')";
    
    $totalBoxes = array_sum($_POST['numberofbox']);
    $totalWeight = array_sum($_POST['totalweight']);
    
    $logStmt = $conn->prepare($logSql);
    $logStmt->bind_param("ssssdd", 
        $batchId, 
        $dateOfArrival, 
        $dateEncoded, 
        $encoder,
        $totalBoxes,
        $totalWeight
    );
    $logStmt->execute();

    // Process each product
    $productCodes = $_POST['productcode'];
    $ibdIds = $_POST['ibdid'];
    $numberOfBoxes = $_POST['numberofbox'];
    $totalPacks = $_POST['totalpieces']; // Changed from totalpieces to match totalpacks column
    $totalWeights = $_POST['totalweight'];

    for ($i = 0; $i < count($productCodes); $i++) {
        // Insert into stockmovement
        $movementSql = "INSERT INTO stockmovement (
            batchid, productcode, numberofbox, 
            totalpacks, totalweight, dateencoded, 
            encoder, movement_type
        ) VALUES (?, ?, ?, ?, ?, ?, ?, 'IN')";
        
        $movementStmt = $conn->prepare($movementSql);
        $movementStmt->bind_param(
            "ssiidss",
            $batchId,
            $productCodes[$i],
            $numberOfBoxes[$i],
            $totalPacks[$i],
            $totalWeights[$i],
            $dateEncoded,
            $encoder
        );
        $movementStmt->execute();

        /* 
        // REDUNDANT: Commented out as the 'after_stockmovement_insert' trigger handles this automatically.
        // Update inventory
        $updateSql = "UPDATE inventory 
                     SET availablequantity = availablequantity + ?,
                         onhandquantity = onhandquantity + ?
                     WHERE productcode = ?";
        
        $updateStmt = $conn->prepare($updateSql);
        $updateStmt->bind_param(
            "iis", 
            $totalPacks[$i],
            $totalPacks[$i],
            $productCodes[$i]
        );
        $updateStmt->execute();
        */
    }

    $processMessages[] = [
        'title' => 'Stock Movement',
        'text' => 'Stock records created successfully',
        'icon' => 'success'
    ];

    // Store session data
    $_SESSION['stockmovement_data'] = $_POST;
    $_SESSION['process_messages'] = $processMessages;

    /*
    // REMOVED: These seem redundant as the main script handles logs
    // and triggers handle inventory updates.
    // Process other files silently
    ob_start();
    include 'add.stockactivitylog.process.php';
    include 'update.inventory.process.php';
    ob_end_clean();
    */

    $conn->commit();

    // Final response
    echo json_encode([
        'status' => 'success',
        'messages' => $_SESSION['process_messages'],
        'redirect' => 'stockactivitylog.php'
    ]);

    // Clean up
    unset($_SESSION['process_messages']);

} catch (Exception $e) {
    $conn->rollback();
    error_log("Stock movement error: " . $e->getMessage());
    
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'message' => 'Error adding stock movement: ' . $e->getMessage()
    ]);
}
exit;

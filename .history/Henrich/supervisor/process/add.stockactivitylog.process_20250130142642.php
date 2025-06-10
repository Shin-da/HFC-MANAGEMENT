<?php
if (!isset($conn)) {
    require_once '../../includes/config.php';
    require_once '../../includes/session.php';
}

require_once '../../includes/notification_functions.php';  // Include this first
require_once '../../includes/helper_functions.php';        // Then include helper functions

// Remove all HTML/styling
// Remove ob_clean() as it's already called in the main process
header('Content-Type: application/json');

try {
    // Get stock data from session
    $stockData = $_SESSION['stockmovement_data'] ?? null;
    
    if (!$stockData) {
        throw new Exception('No stock data found in session');
    }

    $conn->begin_transaction();

    // Build description string
    $description = '';
    foreach ($stockData['productcode'] as $key => $productcode) {
        $description .= $stockData['productname'][$key] . ' ' . 
                       $productcode . ' (' . 
                       (int)$stockData['totalpieces'][$key] . ' pcs)' . PHP_EOL;
    }

    // Calculate totals
    $totalNumberOfBoxes = array_sum($stockData['numberofbox']);
    $overallTotalWeight = array_sum($stockData['totalweight']);

    // Insert stock activity log
    $stmt = $conn->prepare("
        INSERT INTO stockactivitylog 
        (batchid, dateofarrival, encoder, totalNumberOfBoxes, dateencoded, description, overalltotalweight) 
        VALUES (?, ?, ?, ?, NOW(), ?, ?)
    ");

    $stmt->bind_param("ississ",
        $stockData['batchid'],
        $stockData['dateofarrival'],
        $stockData['encoder'],
        $totalNumberOfBoxes,
        $description,
        $overallTotalWeight
    );

    if (!$stmt->execute()) {
        throw new Exception("Error updating stock activity log: " . $stmt->error);
    }

    $conn->commit();

    // Clean up session data
    unset($_SESSION['stockmovement_data']);

    echo json_encode([
        'status' => 'success',
        'message' => 'Stock activity log updated successfully',
        'redirect' => 'inventory.php'
    ]);

    // Call inventory update process
    require_once 'update.inventory.process.php';

} catch (Exception $e) {
    $conn->rollback();
    error_log("Error in stock activity log: " . $e->getMessage());
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
    exit;
}

exit;
?>


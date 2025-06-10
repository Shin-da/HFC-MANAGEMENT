<?php
// This file should not output anything
if (!isset($conn)) {
    require_once '../../includes/config.php';
    require_once '../../includes/session.php';
}

try {

require_once '../../includes/notification_functions.php';  // Include this first
require_once '../../includes/helper_functions.php';        // Then include helper functions

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

    // Call inventory update process
    require_once 'update.inventory.process.php';

    // Don't output JSON here, just return true/false
    return true;

} catch (Exception $e) {
    $conn->rollback();
    error_log("Error in stock activity log: " . $e->getMessage());
    throw $e; // Propagate error to main process
}

exit;
?>


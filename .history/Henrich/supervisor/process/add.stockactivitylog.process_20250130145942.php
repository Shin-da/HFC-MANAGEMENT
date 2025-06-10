<?php
// This file should not output anything
if (!isset($conn)) {
    require_once '../../includes/config.php';
    require_once '../../includes/session.php';
}

try {
    $stockData = $_SESSION['stockmovement_data'] ?? null;
    
    if (!$stockData) {
        throw new Exception('No stock data found in session');
    }

    // Build description and calculate totals
    $description = '';
    foreach ($stockData['productcode'] as $key => $productcode) {
        $description .= "{$stockData['productname'][$key]} ({$productcode}): {$stockData['totalpieces'][$key]} pcs\n";
    }

    $totalBoxes = array_sum($stockData['numberofbox']);
    $totalWeight = array_sum($stockData['totalweight']);

    $stmt = $conn->prepare("
        INSERT INTO stockactivitylog 
        (batchid, dateofarrival, encoder, totalNumberOfBoxes, dateencoded, description, overalltotalweight) 
        VALUES (?, ?, ?, ?, NOW(), ?, ?)
    ");

    $stmt->bind_param("ississ",
        $stockData['batchid'],
        $stockData['dateofarrival'],
        $stockData['encoder'],
        $totalBoxes,
        $description,
        $totalWeight
    );

    if (!$stmt->execute()) {
        throw new Exception("Error inserting stock activity log: " . $stmt->error);
    }

    return true;

} catch (Exception $e) {
    throw new Exception("Stock activity log error: " . $e->getMessage());
}

exit;
?>


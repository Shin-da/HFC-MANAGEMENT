<?php
if (!isset($conn)) {
    require_once '../../includes/config.php';
    require_once '../../includes/session.php';
}

try {
    $stockData = $_SESSION['stockmovement_data'] ?? null;
    
    if (!$stockData) {
        throw new Exception('No stock data found in session');
    }

    // Insert log without any metric_value references
    $stmt = $conn->prepare("
        INSERT INTO stockactivitylog (
            batchid, 
            dateofarrival, 
            encoder, 
            totalNumberOfBoxes, 
            dateencoded, 
            description, 
            overalltotalweight
        ) VALUES (?, ?, ?, ?, NOW(), ?, ?)
    ");

    // Calculate totals
    $totalBoxes = array_sum($stockData['numberofbox']);
    $totalWeight = array_sum($stockData['totalweight']);
    
    // Build description
    $description = '';
    foreach ($stockData['productcode'] as $key => $code) {
        $description .= "{$stockData['productname'][$key]} ($code): {$stockData['totalpieces'][$key]} pcs\n";
    }

    $stmt->bind_param("ississ",
        $stockData['batchid'],
        $stockData['dateofarrival'],
        $stockData['encoder'],
        $totalBoxes,
        $description,
        $totalWeight
    );

    if (!$stmt->execute()) {
        throw new Exception("Error inserting activity log: " . $stmt->error);
    }

    return true;

} catch (Exception $e) {
    throw new Exception("Activity log error: " . $e->getMessage());
}

exit;
?>


// ... existing code ...

function insertStockMovements($conn, $data) {
    $stmt = $conn->prepare("INSERT INTO stockmovement (ibdid, batchid, productcode, productname, numberofbox, totalpieces, totalweight, dateencoded, encoder) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    
    foreach ($data['productcode'] as $key => $code) {
        $stmt->bind_param("iissiiiss",
            $data['ibdid'][$key],
            $data['batchid'],
            $code,
            $data['productname'][$key],
            $data['numberofbox'][$key],
            $data['totalpieces'][$key],
            $data['totalweight'][$key],
            $data['dateencoded'],
            $data['encoder']
        );
        
        if (!$stmt->execute()) {
            throw new Exception("Error inserting stock movement: " . $stmt->error);
        }
    }
    return true;
}

function insertStockActivityLog($conn, $data) {
    $stmt = $conn->prepare("
        INSERT INTO stockactivitylog 
        (batchid, dateofarrival, encoder, totalNumberOfBoxes, dateencoded, description, overalltotalweight) 
        VALUES (?, ?, ?, ?, NOW(), ?, ?)
    ");

    $totalBoxes = array_sum($data['numberofbox']);
    $totalWeight = array_sum($data['totalweight']);
    $description = '';
    
    foreach ($data['productcode'] as $key => $code) {
        $description .= "{$data['productname'][$key]} ($code) - {$data['totalpieces'][$key]} pcs\n";
    }

    $stmt->bind_param("ississ",
        $data['batchid'],
        $data['dateofarrival'],
        $data['encoder'],
        $totalBoxes,
        $description,
        $totalWeight
    );

    if (!$stmt->execute()) {
        throw new Exception("Error inserting stock activity log: " . $stmt->error);
    }
    return true;
}

function updateInventory($conn, $data) {
    foreach ($data['productcode'] as $key => $code) {
        $quantity = $data['totalpieces'][$key];
        
        // Try update first
        $stmt = $conn->prepare("
            UPDATE inventory 
            SET availablequantity = availablequantity + ?,
                onhandquantity = onhandquantity + ?,
                dateupdated = CURRENT_TIMESTAMP
            WHERE productcode = ?
        ");
        
        $stmt->bind_param("iis", $quantity, $quantity, $code);
        $stmt->execute();
        
        // If no rows updated, insert new record
        if ($stmt->affected_rows === 0) {
            $stmt = $conn->prepare("
                INSERT INTO inventory 
                (productcode, productname, availablequantity, onhandquantity) 
                VALUES (?, ?, ?, ?)
            ");
            
            $stmt->bind_param("ssii",
                $code,
                $data['productname'][$key],
                $quantity,
                $quantity
            );
            
            if (!$stmt->execute()) {
                throw new Exception("Error inserting inventory record: " . $stmt->error);
            }
        }
    }
    return true;
}

// ... rest of existing code ...

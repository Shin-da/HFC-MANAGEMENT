<?php

// indicator that add.stockmovement.process.php is running
echo "<div style='background-color: #dff0d8; padding: 10px; border-radius: 5px'>Running add.stockmovement.process.php</div>";
// Establish a connection to the database
require '/xampp/htdocs/HenrichProto/database/dbconnect.php';

// Check if the form data is being submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve the data from the form
    $ibdids = $_POST['ibdid'] ?? [];
    $batchid = $_POST['batchid'] ?? '';
    $productcodes = $_POST['productcode'] ?? [];
    $productnames = $_POST['productname'] ?? [];
    $productcategories = $_POST['productcategory'] ?? [];
    $numberofboxes = $_POST['numberofbox'] ?? [];
    $totalpiecess = $_POST['totalpieces'] ?? [];
    $totalweightss = $_POST['totalweight'] ?? [];

    $weightperpieces = $_POST['weightperpiece'] ?? []; // individual product weight
    $dateencodeds = $_POST['dateencoded'] ?? [];
    $encoders = $_POST['encoder'] ?? [];
    // Print out the values of the form fields
    echo "<div style='background-color: #dff0d8; padding: 10px; border-radius: 5px'>";
    echo "ibdids: ";
    print_r($ibdids);
    echo "<br>";
    echo "batchids: ";
    echo $batchid;
    echo "<br>";
    echo "productcodes: ";
    print_r($productcodes);
    echo "<br>";
    echo "productnames: ";
    print_r($productnames);
    echo "<br>";
    echo "productcategories: ";
    print_r($productcategories);
    echo "<br>";
    echo "numberofboxes: ";
    print_r($numberofboxes);
    echo "<br>";
    echo "totalpiecess: ";
    print_r($totalpiecess);
    echo "<br>";
    echo "totalweightss: ";
    print_r($totalweightss);
    echo "<br>";
    echo "</div>";
    // Check if any of the form fields are empty
    $hasEmptyValue = false;
    foreach ($productcodes as $key => $productcode) {
        if (empty($productcode) || empty($productnames[$key]) || empty($numberofboxes[$key]) || empty($totalpiecess[$key]) || empty($totalweightss[$key])) {
            $hasEmptyValue = true;
            break;
        }
    }

    if ($hasEmptyValue) {
        echo "<div style='background-color: #f2dede; padding: 10px; border-radius: 5px'>Error: Invalid form data</div>";
        exit;
    }

    // Insert the data into the database
    foreach ($productcodes as $key => $productcode) {
        $ibdid = $ibdids[$key] ?? '';
        $productname = $productnames[$key] ?? '';
        $numberofbox = $numberofboxes[$key] ?? '';
        $totalpieces = $totalpiecess[$key] ?? '';
        $totalweight = $totalweightss[$key] ?? '';
      
        $encoder = $encoders[$key] ?? '';  
        $dateencoded = $dateencodeds[$key] ?? '';

        // Prepare the insert statementx
        $stmt = $conn->prepare("INSERT INTO stockmovement (ibdid, batchid, productcode, productname, numberofbox, totalpieces, totalweight, encoder, dateencoded) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("iiisiiiss", $ibdid, $batchid, $productcode, $productname, $numberofbox, $totalpieces, $totalweight, $encoder, $dateencoded);
        // Execute the insert statement
        if (!$stmt->execute()) {
            echo "<div style='background-color: #f2dede; padding: 10px; border-radius: 5px'>Error: " . $stmt->error . "</div>";
        }
    }

    require 'add.stockactivitylog.process.php';
}


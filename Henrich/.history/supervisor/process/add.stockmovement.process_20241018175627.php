<?php

// indicator that add.stockmovement.process.php is running
echo "Running add.stockmovement.process.php";

// Establish a connection to the database
require '/xampp/htdocs/HenrichProto/database/dbconnect.php';

// Check if the form data is being submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve the data from the form
    $ibdids = $_POST['ibdid'] ?? [];
    $batchid = $_POST['batchid'] ?? '';
    $productcodes = $_POST['productcode'] ?? [];
    $productnames = $_POST['productname'] ?? [];
    $quantitiesofboxs = $_POST['quantityofbox'] ?? [];
    $totalpiecess = $_POST['totalpieces'] ?? [];
    $totalweightss = $_POST['totalweight'] ?? [];

    $weightperpieces = $_POST['weightperpiece'] ?? []; // individual product weight
    $quantitiesperboxs = $_POST['quantityperbox'] ?? []; // quantity per box
    $dateencodeds = $_POST['dateencoded'] ?? [];
    $encoders = $_POST['encoder'] ?? [];
    // Print out the values of the form fields
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
    echo "quantitiesofboxs: ";
    // Check if any of the form fields are empty
    $hasEmptyValue = false;
    foreach ($productcodes as $key => $productcode) {
        if (empty($productcode) || empty($productnames[$key]) || empty($productweights[$key]) || empty($quantitiesperboxs[$key]) || empty($totalpiecess[$key]) || empty($totalweightss[$key])) {
            $hasEmptyValue = true;
            break;
        }
    }

    if ($hasEmptyValue) {
        echo "Error: Invalid form data";
        exit;
    }

    // Insert the data into the database
    foreach ($productcodes as $key => $productcode) {
        $ibdid = $ibdids[$key] ?? '';
        $productname = $productnames[$key] ?? '';
        $productweight = $productweights[$key] ?? '';
        $quantityperbox = $quantitiesperboxs[$key] ?? '';
        $totalpieces = $totalpiecess[$key] ?? '';
        $totalweight = $totalweightss[$key] ?? '';
      
        $encoder = $encoders[$key] ?? '';  
        $dateencoded = date('Y-m-d'); // Default dateencoded to today's date

        // Prepare the insert statementx
        $stmt = $conn->prepare("INSERT INTO stockmovement (i
        // Execute the insert statement
        if (!$stmt->execute()) {
            echo "Error: " . $stmt->error;
        }
    }

    require 'add.stockactivitylog.process.php';
}




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
    $productweights = $_POST['productweight'] ?? []; // individual product weight
    $quantitiesperboxs = $_POST['quantityperbox'] ?? []; // quantity per box
    $totalpiecess = $_POST['totalpieces'] ?? [];
    $totalweightss = $_POST['totalweight'] ?? [];
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
    echo "productweights: ";
    print_r($productweights);
    echo "<br>";
    echo "quantitiesperboxs: ";
    print_r($quantitiesperboxs);
    echo "<br>";
    echo "totalpiecess: ";
    print_r($totalpiecess);
    echo "<br>";
    echo "totalweightss: ";
    print_r($totalweightss);
    echo "<br>";
    echo "encoders: ";
    print_r($encoders);
    echo "<br>";
    echo "dateencodeds: ";
    print_r($dateencodeds);
    echo "<br>";

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
        $encoder = $encoders[$key] ?? '';
        $quantityperbox = $quantitiesperboxs[$key] ?? 0;
        $weightperpiece = $productweights[$key] ?? 0;
        $totalpieces = $totalpiecess[$key] ?? 0;
        $totalweight = $totalweightss[$key] ?? 0;
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



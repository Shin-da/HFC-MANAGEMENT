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
    $encoders = $_POST['encoder'] ;

    $weightsperpiece = $_POST['weightperpiece'] ?? [];
    $piecesperboxs = $_POST['piecesperbox'] ?? [];
    $quantitiesperboxs = $_POST['quantityperbox'] ?? [];
    $totalpiecess = $_POST['totalpieces'] ?? [];
    $totalweightss = $_POST['totalweight'] ?? [];
    // Print out the values of the form fields
    echo "ibdids: ";
    print_r($ibdids);
    echo "<br>";
    echo "batchids: ";
    echo $batchid;
    echo "<br>";
    echo "encoders: ";
    print_r($encoders);
    echo "<br>";
    echo "productcodes: ";
    print_r($productcodes);
    echo "<br>";
    echo "quantitiesperboxs: ";
    print_r($quantitiesperboxs);
    echo "<br>";
    echo "piecesperboxs: ";
    print_r($piecesperboxs);
    echo "<br>";
    echo "weightsperpiece: ";
    print_r($weightsperpiece);
    echo "<br>";
    echo "totalpiecess: ";
    print_r($totalpiecess);
    echo "<br>";
    echo "totalweightss: ";
    print_r($totalweightss);
    echo "<br>";
/*************  ✨ Codeium Command 🌟  *************/

    // Check if any of the form fields are empty
    $hasEmptyValue = false;
    foreach ($productcodes as $key => $productcode) {
        echo "Key: $key, Productcode: $productcode, Encoder: $encoders[$key] ?? '', Quantity: $quantitiesperboxs[$key], Pieces perbox: $piecesperboxs[$key], Weight: $weightsperpiece[$key], Total Pieces: $totalpiecess[$key], Total Weight: $totalweightss[$key] ?? ''<br>";
        if (empty($productcode) || empty($encoders[$key] ?? '') || empty($quantitiesperboxs[$key]) || empty($piecesperboxs[$key]) || empty($weightsperpiece[$key]) || empty($totalpiecess[$key]) || empty($totalweightss[$key] ?? '')) {
        echo "Key: $key, Productcode: $productcode, Encoder: $encoders[$key] ?? '', Quantity: $quantities[$key], Weight: $weights[$key], Date Encoded: $dateencodeds[$key] ?? ''<br>";
        if (empty($productcode) || empty($encoders[$key] ?? '') || empty($quantities[$key]) || empty($weights[$key]) || empty($dateencodeds[$key] ?? '')) {
            $hasEmptyValue = true;
            break;
        }
    }
/******  812b47fe-63f0-42ab-8261-a02e35d7381f  *******/

    if ($hasEmptyValue) {
        echo "Error: Invalid form data";
        header('Location: ../../add.stockmovement.php');
        exit;
    }

    // Insert the data into the database
    foreach ($productcodes as $key => $productcode) {
        $ibdid = $ibdids[$key] ?? '';
        $encoder = $encoders[$key] ?? '';
        $quantity = $quantities[$key] ?? 0;
        $weight = $weights[$key] ?? 0;
        $dateencoded = $dateencodeds[$key] ?? ''; // Corrected variable name

        // Prepare the insert statement
        $stmt = $conn->prepare("INSERT INTO stockmovement (ibdid, batchid, encoder, productcode, quantity, weight, dateencoded) VALUES (?, ?, ?, ?, ?, ?, ?)");

        // Bind the parameters
        $stmt->bind_param("ssssisi", $ibdid, $batchid, $encoder, $productcode, $quantity, $weight, $dateencoded);

        // Execute the insert statement
        if (!$stmt->execute()) {
            echo "Error: " . $stmt->error;
        }
    }

require 'add.stockactivitylog.process.php';
}


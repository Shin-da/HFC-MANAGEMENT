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

    $weights = $_POST['weight'] ?? [];
    $productcodes = $_POST['productcode'] ?? [];
    $quantities = $_POST['quantity'] ?? [];
    $dateencodeds = $_POST['dateencoded'] ?? '';

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
    echo "quantities: ";
    print_r($quantities);
    echo "<br>";
    echo "weights: ";
    print_r($weights);
    echo "<br>";
    echo "dateencoded: ";
    echo implode(', ', $dateencodeds);
    echo "<br>";

    // Check if any of the form fields are empty
    $hasEmptyValue = false;
    foreach ($productcodes as $key => $productcode) {
        echo "Key: $key, Productcode: $productcode, Encoder: $encoders[$key] ?? '', Quantity: $quantities[$key], Weight: $weights[$key], Date Encoded: $dateencodeds[$key] ?? ''<br>";
        if (empty($productcode) || empty($encoders[$key] ?? '') || empty($quantities[$key]) || empty($weights[$key]) || empty($dateencodeds[$key] ?? '')) {
            $hasEmptyValue = true;
            break;
        }
    }

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


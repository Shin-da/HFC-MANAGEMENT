/*************  ✨ Codeium Command 🌟  *************/
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
    $encoders = $_POST['encoder'] ?? [];
    $productcodes = $_POST['productcode'] ?? [];
    $productweights = $_POST['productweight'] ?? [];
    $quantityperpieces = $_POST['quantityperpiece'] ?? [];
    $quantitiesperboxs = $_POST['quantityperbox'] ?? [];
    $totalpiecess = $_POST['totalpieces'] ?? [];
    $totalweightss = $_POST['totalweight'] ?? [];
    $dateencodeds = $_POST['dateencoded'] ?? [];
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
    echo "productweights: ";
    print_r($productweights);
    echo "<br>";
    echo "quantityperpieces: ";
    print_r($quantityperpieces);
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
    echo "dateencodeds: ";
    print_r($dateencodeds);
    echo "<br>";

    // Check if any of the form fields are empty
    $hasEmptyValue = false;
    foreach ($productcodes as $key => $productcode) {
        if (empty($productcode) || empty($encoders[$key] ?? '') || empty($quantitiesperboxs[$key]) || empty($quantityperpieces[$key]) || empty($productweights[$key]) || empty($totalpiecess[$key]) || empty($totalweightss[$key] ?? '')) {
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
        $quantity = $quantitiesperboxs[$key] ?? 0;
        $weightperpiece = $productweights[$key] ?? 0;
        $piecesperbox = $quantityperpieces[$key] ?? 0;
        $totalpieces = $totalpiecess[$key] ?? 0;
        $totalweight = $totalweightss[$key] ?? 0;
        $dateencoded = date('Y-m-d'); // Default dateencoded to today's date

        // Prepare the insert statement
        $stmt = $conn->prepare("INSERT INTO stockmovement (ibdid, batchid, encoder, productcode, quantity, totalpieces, totalweight, dateencoded) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt = $conn->prepare("INSERT INTO stockmovement (ibdid, batchid, encoder, productcode, quantity, piecesperbox, totalpieces, totalweight, dateencoded) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");

        // Bind the parameters
        $stmt->bind_param("ssssiiiiis", $ibdid, $batchid, $encoder, $productcode, $quantity, $totalpieces, $totalweight, $dateencoded);
        $stmt->bind_param("ssssiiiiis", $ibdid, $batchid, $encoder, $productcode, $quantity, $piecesperbox, $totalpieces, $totalweight, $dateencoded);

        // Execute the insert statement
        if (!$stmt->execute()) {
            echo "Error: " . $stmt->error;
        }
    }

    require 'add.stockactivitylog.process.php';
}


/******  47cdfce5-ac95-41e1-9d20-b169a79f483b  *******/
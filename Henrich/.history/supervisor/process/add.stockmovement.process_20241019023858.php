/*************  ✨ Codeium Command 🌟  *************/
<style>
    .alert {
        background-color: #dff0d8;
        padding: 10px;
        border-radius: 5px;
        color: #3c763d;
        border: 1px solid #3c763d;
    }
    .alert-danger {
        background-color: #f2dede;
        border-color: #ebccd1;
        color: #a94442;
    }
    .alert-success {
        background-color: #dff0d8;
        border-color: #d6e9c6;
        color: #3c763d;
    }
</style>

<?php

// indicator that add.stockmovement.process.php is running
echo "<div class='alert alert-success'>Running add.stockmovement.process.php</div>";
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
    echo "<div class='alert alert-success'>";
    echo "ibdids: " . (empty($ibdids) ? 'Array ( )' : 'Array ( ' . implode(', ', $ibdids) . ' )') . "<br>";
    echo "batchids: " . $batchid . "<br>";
    echo "productcodes: " . (empty($productcodes) ? 'Array ( )' : 'Array ( ' . implode(', ', $productcodes) . ' )') . "<br>";
    echo "productnames: " . (empty($productnames) ? 'Array ( )' : 'Array ( ' . implode(', ', $productnames) . ' )') . "<br>";
    echo "productcategories: " . (empty($productcategories) ? 'Array ( )' : 'Array ( ' . implode(', ', $productcategories) . ' )') . "<br>";
    echo "numberofboxes: " . (empty($numberofboxes) ? 'Array ( )' : 'Array ( ' . implode(', ', $numberofboxes) . ' )') . "<br>";
    echo "totalpiecess: " . (empty($totalpiecess) ? 'Array ( )' : 'Array ( ' . implode(', ', $totalpiecess) . ' )') . "<br>";
    echo "totalweightss: " . (empty($totalweightss) ? 'Array ( )' : 'Array ( ' . implode(', ', $totalweightss) . ' )') . "<br>";
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
        echo "<div class='alert alert-danger'>Error: Invalid form data</div>";
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
            echo "<div class='alert alert-danger'>Error: " . $stmt->error . "</div>";
        }
    }

    require 'add.stockactivitylog.process.php';
}


/******  30d65ad5-e17f-4faa-8cf1-06bf6b277d7a  *******/
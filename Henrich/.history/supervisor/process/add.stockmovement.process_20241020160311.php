<style>
    .body {
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100vh;
        background-color: #f2f2f2;
        margin: 10px;
        padding: 0;
    }
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
    .output-table {
        border-collapse: collapse;
        width: 100%;
    }
    .output-table td, .output-table th {
        border: 1px solid #ddd;
        padding: 8px;
    }
    .output-table tr:nth-child(even) {
        background-color: #f2f2f2;
    }
    .output-table th {
        padding-top: 12px;
        padding-bottom: 12px;
        text-align: left;
        background-color: #4CAF50;
        color: white;
    }
</style>


<?= "<div class='body'>"; ?>
<?php
// indicator that add.stockmovement.process.php is running
echo "<div class='alert alert-success'>Running add.stockmovement.process.php</div>";
// Establish a connection to the database
require '/xampp/htdocs/HenrichProto/database/dbconnect.php';

    // Check if the form data is being submitted
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Retrieve the data from the form
        $ibdid = $_POST['ibdid'] ?? [];
        $batchid = $_POST['batchid'] ?? [];
        $productcodes = $_POST['productcode'] ?? [];
        $productnames = $_POST['productname'] ?? [];
        $productcategories = $_POST['productcategory'] ?? [];
        $numberofboxes = $_POST['numberofbox'] ?? [];
        $totalpiecess = $_POST['totalpieces'] ?? [];
        $totalweightss = $_POST['totalweight'] ?? [];

        $weightperpieces = $_POST['weightperpiece'] ?? []; // individual product weight
        $dateencodeds = $_POST['dateencoded'] ?? [];
        $encoders = $_POST['encoder'] ?? [];

        $totalNumberOfBoxes = $_POST['totalNumberOfBoxes'] ?? [];
        $overalltotalpieces = $_POST['overalltotalpieces'] ?? [];
        $overalltotalweight = $_POST['overalltotalweight'] ?? [];
        // Print out the values of the form fields
        echo "<table class='output-table'>";
        echo "<tr><th>IBD ID</th><th>Batch ID</th><th>Product Code</th><th>Product Name</th><th>Number of Box</th><th>Total Pieces</th><th>Total Weight</th><th>Encoder</th><th>Date Encoded</th></tr>";
        foreach ($productcodes as $key => $productcode) {
            echo "<tr>";
            echo "<td>" . (isset($ibdid[$key]) ? $ibdid[$key] : '') . "</td>";
        echo "<td>" . (is_array($batchid) ? implode(', ', $batchid) : $batchid) . "</td>";
        echo "<td>" . $productcode . "</td>";
        echo "<td>" . (isset($productnames[$key]) ? $productnames[$key] : '') . "</td>";
        echo "<td>" . (isset($numberofboxes[$key]) ? $numberofboxes[$key] : '') . "</td>";
        echo "<td>" . (isset($totalpiecess[$key]) ? $totalpiecess[$key] : '') . "</td>";
        echo "<td>" . (isset($totalweightss[$key]) ? $totalweightss[$key] : '') . "</td>";
        echo "<td>" . (isset($encoders[$key]) ? $encoders[$key] : '') . "</td>";
        echo "<td>" . (isset($dateencodeds[$key]) ? $dateencodeds[$key] : '') . "</td>";
        echo "</tr>";
    }
    echo "</table>";
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
?>

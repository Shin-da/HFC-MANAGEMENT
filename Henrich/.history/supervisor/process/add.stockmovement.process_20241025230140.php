<style>
    html {
        background-color: #3c763d;
    }

    .body {
        background-color: #f2f2f2;
        margin: 10px;
        margin-bottom: 20px;
        padding: 10px;
        border-radius: 5px;
        border: 1px solid #a94442;
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

    .output-table td,
    .output-table th {
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

require '/xampp/htdocs/HenrichProto/database/dbconnect.php';
require '/xampp/htdocs/HenrichProto/session/session.php';

$role = $_SESSION['role'];

// Check if the form data is being submitted
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Retrieve the data from the form
        $ibdids =  isset($_POST['ibdid']) ? $_POST['ibdid'] : [];
        $batchids = $_POST['batchid'] ?? [];
        $productcodes = $_POST['productcode'] ?? [];
        $productnames = $_POST['productname'] ?? [];
        $productcategories = $_POST['productcategory'] ?? [];
        $numberofboxes = $_POST['numberofbox'] ?? [];
        $totalpiecess = $_POST['totalpieces'] ?? [];
        $totalweightss = $_POST['totalweight'] ?? [];

        $weightperpieces = $_POST['weightperpiece'] ?? []; // individual product weight
        $dateencodeds = array_fill(0, count($productcodes), date('Y-m-d'));
        $encoder = array_fill(0, count($productcodes), isset($_SESSION['role']) ? $_SESSION['role'] : '');

        $totalNumberOfBoxes = $_POST['totalNumberOfBoxes'] ?? [];
        $overalltotalpieces = $_POST['overalltotalpieces'] ?? [];
        $overalltotalweight = $_POST['overalltotalweight'] ?? [];
        // Print out the values of the form fields
        echo "<table class='output-table'>";
        echo "<tr>
              <th>IBD ID</th>
               <th>Batch ID</th>
               <th>Product Code</th>
               <th>Product Name</th>
               <th>Number of Box</th>
               <th>Total Pieces</th>
               <th>Total Weight</th>
               <th>Encoder</th>
               <th>Date Encoded</th>
               </tr>";
        foreach ($productcodes as $key => $productcode) {
            echo "<tr>";
            echo "<td>" . (isset($ibdids[$key]) ? $ibdids[$key] : '') . "</td>";
            echo "<td>" . (is_array($batchids) ? implode(', ', $batchids) : $batchids) . "</td>";
            echo "<td>" . $productcode . "</td>";
            echo "<td>" . (isset($productnames[$key]) ? $productnames[$key] : '') . "</td>";
            echo "<td>" . (isset($numberofboxes[$key]) ? $numberofboxes[$key] : '') . " </td>";
            echo "<td>" . (isset($totalpiecess[$key]) ? $totalpiecess[$key] : '') . "</td>";
            echo "<td>" . (isset($totalweightss[$key]) ? $totalweightss[$key] : '') . "</td>";
            echo "<td>" . $encoder[$key] . "</td>";
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
        $batchid = implode(', ', $batchids);
        $productname = $productnames[$key] ?? '';
        $numberofbox = $numberofboxes[$key] ?? '';
        $totalpieces = $totalpiecess[$key] ?? '';
        $totalweight = $totalweightss[$key] ?? '';

        $encoder = $encoders[$key] ?? '';
        $dateencoded = $dateencodeds[$key] ?? '';

        // Prepare the insert statement
        $stmt = $conn->prepare("INSERT INTO stockmovement (ibdid, batchid, productcode, productname, numberofbox, totalpieces, totalweight, encoder, dateencoded) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("iissiiiss", $ibdid, $batchid, $productcode, $productname, $numberofbox, $totalpieces, $totalweight, $encoder, $dateencoded);
        // Execute the insert statement
        if (!$stmt->execute()) {
            echo "<div class='alert alert-danger'>Error: " . $stmt->error . "</div>";
        } else {
            echo "<div class='alert alert-success'>Data inserted successfully!</div>";
        }
    }
}
?>
<?= '</div>'; ?>
<?php require 'add.stockactivitylog.process.php'; ?>
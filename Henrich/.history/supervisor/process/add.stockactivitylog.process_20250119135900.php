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

<?= " <div class='body'>"; ?>
<?php
require '/xampp/htdocs/HenrichProto/database/dbconnect.php';
// require '/xampp/htdocs/HenrichProto/session/session.php';

$role = $_SESSION['role'];
// indicator that add.stockmovement.process.php is running
echo "<div class='alert alert-success'>Running add.stockactivitylog.process.php</div>";
echo "<br>";

// Retrieve the data from the form
$batchid = isset($_POST['batchid'][0]) ? $_POST['batchid'][0] : '';
$description = isset($_POST['description']) ? $_POST['description'] : '';

$productcodes = isset($_POST['productcode']) ? $_POST['productcode'] : [];

$dateencodeds = array_fill(0, count($productcodes), date('Y-m-d'));
$encoder = $_SESSION['role']; // Get encoder from session directly
$numberofboxes = $_POST['numberofbox'] ?? [];
$totalpiecess = $_POST['totalpieces'] ?? [];
$totalweightss = $_POST['totalweight'] ?? [];

// Sum all the totalpieces of the products and display it
$overalltotalpieces = array_sum($totalpiecess);
echo "<div class='alert alert-success'>Total pieces: " . $overalltotalpieces . "</div>";

// Sum all the totalweight of the products and display it
$overalltotalweight = array_sum($totalweightss);
echo "<div class='alert alert-success'>Total weight: " . $overalltotalweight . "</div>";

// Sum all the numberofboxes of the products and display it
$totalNumberOfBoxes = array_sum($numberofboxes);
echo "<div class='alert alert-success'>Total number of boxes: " . $totalNumberOfBoxes . "</div>";

$productname = isset($_POST['productname']) ? $_POST['productname'] : [];
$productcategory = isset($_POST['productcategory']) ? $_POST['productcategory'] : [];
$dateofarrival = isset($_POST['dateofarrival']) ? $_POST['dateofarrival'] : '';

// Check if batchid already exists in stockmovement table
$stmt = $conn->prepare("SELECT COUNT(*) as count FROM stockactivitylog WHERE batchid = ?");
$stmt->bind_param("s", $batchid);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

if ($row['count'] > 0) {
    // Batchid already exists, update the existing row
    $stmt = $conn->prepare("UPDATE stockactivitylog SET totalNumberOfBoxes = ?, overalltotalweight = ?, dateencoded = NOW() WHERE batchid = ?");
    $stmt->bind_param("iis", $totalNumberOfBoxes, $overalltotalweight, $batchid);
    if (!$stmt->execute()) {
        $_SESSION['sweetalert'] = [
            'icon' => 'error',
            'title' => 'Error!',
            'text' => 'Error updating data in stockactivitylog table: ' . $stmt->error
        ];
    } else {
        $_SESSION['sweetalert'] = [
            'icon' => 'success',
            'title' => 'Success!',
            'text' => 'Data updated successfully in stockactivitylog table'
        ];
    }
} else {
    // Batchid does not exist, insert a new row

    // Initialize an empty string to store the product descriptions
    $description = '';

    // Loop through each product code and its corresponding quantity
    foreach ($_POST['productcode'] as $key => $productcode) {
        // Append the product code and quantity to the description string
        // in the format "Product Code (Quantity packs), "
        $description .= $productname[$key] .  '    ' . $productcode . ' (' . (int)$_POST['totalpieces'][$key] . ' packs), ' . PHP_EOL;
    }

    // Remove the trailing comma and space from the description string
    $description = rtrim($description, ', ');

    // Example output: "001 (2 packs), 002 (3 packs), 003 (5 packs)"

    $stmt = $conn->prepare("INSERT INTO stockactivitylog (batchid, dateofarrival, encoder, totalNumberOfBoxes, dateencoded, description, overalltotalweight) VALUES (?, ?, ?, ?, NOW(), ?, ?)");
    $stmt->bind_param("ississ", $batchid, $dateofarrival, $encoder, $totalNumberOfBoxes, $description, $overalltotalweight);
    if (!$stmt->execute()) {
        $_SESSION['sweetalert'] = [
            'icon' => 'error',
            'title' => 'Error!',
            'text' => 'Error inserting data into stockactivitylog table: ' . $stmt->error
        ];
    } else {
        $_SESSION['sweetalert'] = [
            'icon' => 'success',
            'title' => 'Success!',
            'text' => 'Data inserted successfully into stockactivitylog table'
        ];
    }
    echo "<table class='output-table'>";
    echo "<tr><th>Batch ID</th><th>Date of Arrival</th><th>Encoder</th><th>Total Number Of Boxes</th><th>Date Encoded</th><th>Description</th></tr>";    echo "<tr><td>" . $batchid . "</td><td>" . $dateofarrival . "</td><td>" . $encoder . "</td><td>" . $totalNumberOfBoxes . "</td><td>" . $dateencoded . "</td><td>" . $description . "</td></tr>";
    echo "</table>";
}
?>
<?= "</div>" ?>
<?php require 'update.inventory.process.php'; // call update.inventory.process.php?>


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

ini_set('display_errors', 1);
error_reporting(E_ALL);

require '/xampp/htdocs/HenrichProto/database/dbconnect.php';
require '/xampp/htdocs/HenrichProto/session/session.php';

// Ensure we only output JSON
header('Content-Type: application/json');
ob_clean(); // Clear any previous output

// Start transaction
$conn->begin_transaction();

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Invalid request method');
    }

    $role = $_SESSION['role'];
    
    // Validate inputs
    $requiredFields = ['ibdid', 'batchid', 'productcode', 'productname', 'numberofbox', 'totalpieces', 'totalweight'];
    $missingFields = array();
    foreach ($requiredFields as $field) {
        if (!isset($_POST[$field]) || empty($_POST[$field])) {
            $missingFields[] = $field;
        }
    }
    
    if (!empty($missingFields)) {
        throw new Exception("Missing required fields: " . implode(', ', $missingFields));
    }

    // Get form data
    $ibdids = $_POST['ibdid'];
    $batchids = $_POST['batchid'];
    $productcodes = $_POST['productcode'] ?? [];
    $productnames = $_POST['productname'] ?? [];
    $numberofboxes = $_POST['numberofbox'] ?? [];
    $totalpiecess = $_POST['totalpieces'] ?? [];
    $totalweightss = $_POST['totalweight'] ?? [];
    $encoder = $_SESSION['role'];

    // Insert records
    foreach ($productcodes as $key => $productcode) {
        $stmt = $conn->prepare("INSERT INTO stockmovement (
            ibdid, batchid, productcode, productname, 
            numberofbox, totalpieces, totalweight, 
            encoder, dateencoded
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())");

        if (!$stmt) {
            throw new Exception("Prepare failed: " . $conn->error);
        }

        $ibdid = $ibdids[$key];
        $batchid = is_array($batchids) ? $batchids[0] : $batchids;
        $productname = $productnames[$key];
        $numberofbox = $numberofboxes[$key];
        $totalpieces = $totalpiecess[$key];
        $totalweight = $totalweightss[$key];

        $stmt->bind_param("iissiids", 
            $ibdid, $batchid, $productcode, $productname,
            $numberofbox, $totalpieces, $totalweight, $encoder
        );

        if (!$stmt->execute()) {
            throw new Exception("Error inserting record: " . $stmt->error);
        }
    }

    // If we get here, everything worked
    $conn->commit();

    echo json_encode([
        'status' => 'success',
        'message' => 'Stock movement recorded successfully',
        'redirect' => 'stocklevel.php'
    ]);

} catch (Exception $e) {
    // Roll back transaction and return error
    $conn->rollback();
    error_log("Error in stock movement: " . $e->getMessage());
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
    exit;
}
?>
<?= '</div>'; ?>
<?php require 'add.stockactivitylog.process.php'; ?>

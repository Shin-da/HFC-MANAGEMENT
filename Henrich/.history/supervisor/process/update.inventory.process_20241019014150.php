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
    .container {
        width: 80%;
        margin: 0 auto;
        padding: 10px;
        background-color: #dff0d8;
        border-radius: 10px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1), 0 8px 16px rgba(0, 0, 0, 0.05);
    }
    .container h2 {
        text-align: center;
        padding: 10px;
        background-color: #3c763d;
        color: white;
        border-top-left-radius: 10px;
        border-top-right-radius: 10px;
    }
    .container table {
        width: 100%;
        border-collapse: collapse;
        font-size: 14px;
    }
    .container th, td {
        padding: 10px;
        text-align: left;
        border-bottom: 1px solid #ddd;
    }
    .container th {
        background-color: #3c763d;
        color: white;
    }
    .container tr:nth-child(even) {
        background-color: #f2f2f2;
    }
</style>

<?php
//fetch data from form add.stockactivitylog.process.php

if (isset($_POST['totalboxes'])) {
    $totalboxes = $_POST['totalboxes'];
} else {
    $totalboxes = '';
}

if (isset($_POST['totalpieces'])) {
    $onhand = $_POST['totalpieces'];
} else {
    $onhand = '';
}

if (isset($_POST['productname'])) {
    $productname = $_POST['productname'];
} else {
    $productname = '';
}

// print out the values of the form fields

echo "<div class='alert alert-success'>Total Boxes: " . $totalboxes . "<br>On Hand: " . $onhand . "<br>Product Name: " . $productname . "</div>";
echo "<div class='container'>";
echo "<h2>Stock Levels Updated</h2>";
echo "<table>";
echo "<tr><th>Total Boxes</th><th>On Hand</th><th>Product Name</th></tr>";
echo "<tr><td>" . $totalboxes . "</td><td>" . $onhand . "</td><td>" . $productname . "</td></tr>";
echo "</table>";
echo "</div>";
echo "<br>";
// Update the stock levels in the inventory table
$stmt = $conn->prepare("UPDATE inventory SET onhand = onhand + ?, productname = ?, productcategory = ?, dateupdated = NOW() WHERE productcode = ?");
$stmt->bind_param("issi", $quantity, $productname, $productcategory, $productcode);
$stmt2 = $conn->prepare("INSERT INTO inventory (productcode, onhand, productname, productcategory, dateupdated) VALUES (?, ?, ?, ?, NOW())");
$stmt2->bind_param("isss", $productcode, $quantity, $productname, $productcategory);

$productcodes = $_POST['productcode'] ?? [];
$quantities = $_POST['totalpieces'] ?? [];
foreach ($productcodes as $key => $productcode) {
    $quantity = $quantities[$key] ?? 0;

    // Get productname and productcategory from productlist table
    $stmt4 = $conn->prepare("SELECT productname, productcategory FROM productlist WHERE productcode = ?");
    $stmt4->bind_param("s", $productcode);
    $stmt4->execute();
    $result4 = $stmt4->get_result();
    $row = $result4->fetch_assoc();
    $productname = $row['productname'] ?? '';
    $productcategory = $row['productcategory'] ?? '';

    // Check if productcode exists in stock table
    $stmt3 = $conn->prepare("SELECT productcode FROM inventory WHERE productcode = ?");
    $stmt3->bind_param("s", $productcode);
    $stmt3->execute();
    $result = $stmt3->get_result();
    $row = $result->fetch_assoc();

if (!empty($row)) {
    // Productcode exists, update record
    $stmt->bind_param("issi", $quantity, $productname, $productcategory, $productcode);
    $stmt->execute();
} else {
    // Productcode does not exist, insert new record
    $stmt2->bind_param("isss", $productcode, $quantity, $productname, $productcategory);
    $stmt2->execute();
}
}

echo "<div class='alert alert-success'>Stock levels updated successfully!</div>";


/******  caedd2e6-b54a-42c5-9c1a-1d397b61a03b  *******/
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
    .btn {
        background-color: #4CAF50;
        color: white;
        padding: 10px 20px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
    }
</style>

<div class="body">

<?php
require '/xampp/htdocs/HenrichProto/database/dbconnect.php';

//fetch data from form add.stockactivitylog.process.php

if (isset($_POST['totalboxes'])) {
    $totalboxes = $_POST['totalboxes'];
} else {
    $totalboxes = '';
}

if (isset($_POST['totalpieces'])) {
    $availablequantity = $_POST['totalpieces'];
} else {
    $availablequantity = '';
}

if (isset($_POST['productname'])) {
    $productname = $_POST['productname'];
} else {
    $productname = '';
}

// print out the values of the form fields

echo "<div class='alert alert-success'>Total Boxes: ";
if(is_array($totalboxes)) {
    echo implode(", ", $totalboxes);
} else {
    echo $totalboxes;
}
echo "<br>On Hand: ";
if(is_array($availablequantity)) {
    echo implode(", ", $availablequantity);
} else {
    echo $availablequantity;
}
echo "<br>Product Name: ";
if(is_array($productname)) {
    echo implode(", ", $productname);
} else {
    echo $productname;
}
echo "</div>";
echo "<br>";
// Update the inventory table with the new stock
$stmt = $conn->prepare("
    UPDATE inventory i 
    SET availablequantity = availablequantity + ?,
        onhandquantity = availablequantity + ?,
        unit_price = (SELECT productprice FROM products WHERE productcode = ?),
        dateupdated = CURRENT_TIMESTAMP
    WHERE productcode = ?
");

$stmt2 = $conn->prepare("
    INSERT INTO inventory (
        productcode, 
        productname, 
        productcategory, 
        availablequantity,
        onhandquantity,
        unit_price
    ) 
    SELECT 
        ?,
        productname,
        productcategory,
        ?,
        ?,
        productprice
    FROM productlist
    WHERE productcode = ?
");

$productcodes = $_POST['productcode'] ?? [];
$quantities = $_POST['totalpieces'] ?? [];

foreach ($productcodes as $key => $productcode) {
    $quantity = $quantities[$key] ?? 0;
    
    // First try to update
    $stmt->bind_param("iiis", $quantity, $quantity, $productcode, $productcode);
    $result = $stmt->execute();
    
    if ($stmt->affected_rows === 0) {
        // If no rows were updated, insert new record
        $stmt2->bind_param("siii", $productcode, $quantity, $quantity, $productcode);
        $stmt2->execute();
    }
}

echo "<div class='alert alert-success'>Stock levels updated successfully!</div>";
echo "<br><a href='../stocklevel.php'><button type='button' class='btn btn-primary'>Back to Stock Levels</button></a>";
?>
</div>


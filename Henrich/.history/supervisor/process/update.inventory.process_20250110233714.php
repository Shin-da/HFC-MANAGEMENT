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
//fetch data from form add.stockactivitylog.process.php

if (isset($_POST['totalboxes'])) {
    $totalboxes = $_POST['totalboxes'];
} else {
    $totalboxes = '';
}

if (isset($_POST['totalpieces'])) {
    $onhandquantity = $_POST['totalpieces'];
} else {
    $onhandquantity = '';
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
if(is_array($onhandquantity)) {
    echo implode(", ", $onhandquantity);
} else {
    echo $onhandquantity;
}
echo "<br>Product Name: ";
if(is_array($productname)) {
    echo implode(", ", $productname);
} else {
    echo $productname;
}
echo "</div>";
echo "<br>";
// Update the stock levels in the inventory table
$stmt = $conn->prepare("UPDATE inventory SET onhandquantity = onhandquantity + ?, productname = ?, productcategory = ?, dateupdated = NOW() WHERE productcode = ?");
$stmt->bind_param("issi", $quantity, $productname, $productcategory, $productcode);
$stmt2 = $conn->prepare("INSERT INTO inventory (productcode, onhandquantity, productname, productcategory, dateupdated) VALUES (?, ?, ?, ?, NOW())");
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

$stmt = $conn->prepare("INSERT INTO inventory (productcode, onhandquantity, productname, productcategory, dateupdated) VALUES (?, ?, ?, ?, NOW()) ON DUPLICATE KEY UPDATE onhandquantity = onhandquantity + VALUES(onhandquantity)");
$stmt->bind_param("isss", $productcode, $quantity, $productname, $productcategory);
$stmt->execute();


}
echo "<div class='alert alert-success'>Stock levels updated successfully!</div>";

echo "<br><a href='../stocklevel.php'><button type='button' class='btn btn-primary'>Back to Stock Levels</button></a>";
?>
</div>

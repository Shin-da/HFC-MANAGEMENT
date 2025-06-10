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
// Update the stock levels in the inventory table
$stmt = $conn->prepare("
    UPDATE inventory i 
    INNER JOIN products p ON i.productcode = p.productcode
    SET 
        i.availablequantity = i.availablequantity + ?,
        i.onhandquantity = i.availablequantity,
        i.last_restock_date = CURRENT_TIMESTAMP,
        i.unit_price = p.price
    WHERE i.productcode = ?
");
$stmt->bind_param("is", $quantity, $productcode);

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
        p.productcode,
        p.productname,
        p.productcategory,
        ?,
        ?,
        p.price
    FROM products p
    WHERE p.productcode = ?

}
echo "<div class='alert alert-success'>Stock levels updated successfully!</div>";

echo "<br><a href='../stocklevel.php'><button type='button' class='btn btn-primary'>Back to Stock Levels</button></a>";
?>
</div>


/*************  ✨ Codeium Command 🌟  *************/
<style>
    body {
        background-color: #f5f5f5;
        font-family: Arial, sans-serif;
    }
<?php
//fetch data from form add.stockactivitylog.process.php

    .container {
        width: 80%;
        margin: auto;
        padding: 20px;
        background-color: #fff;
        border: 1px solid #ddd;
        border-radius: 5px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }
if (isset($_POST['totalboxes'])) {
    $totalboxes = $_POST['totalboxes'];
} else {
    $totalboxes = '';
}

    .alert {
        background-color: #dff0d8;
        padding: 10px;
        border-radius: 5px;
        color: #3c763d;
        border: 1px solid #3c763d;
    }
if (isset($_POST['totalpieces'])) {
    $onhand = $_POST['totalpieces'];
} else {
    $onhand = '';
}

    .alert-danger {
        background-color: #f2dede;
        border-color: #ebccd1;
        color: #a94442;
    }
if (isset($_POST['productname'])) {
    $productname = $_POST['productname'];
} else {
    $productname = '';
}

    .alert-success {
        background-color: #dff0d8;
        border-color: #d6e9c6;
        color: #3c763d;
    }
</style>
// print out the values of the form fields

<div class="container">
    <h2>Update Inventory</h2>
echo "totalboxes: ";
echo $totalboxes;
echo "<br>";
echo "onhand: ";
echo $onhand;
echo "<br>";
echo "productname: ";
echo $productname;
echo "<br>";

    <?php
    //fetch data from form add.stockactivitylog.process.php
// Update the stock levels in the inventory table
$stmt = $conn->prepare("UPDATE inventory SET onhand = onhand + ?, productname = ?, productcategory = ?, dateupdated = NOW() WHERE productcode = ?");
$stmt->bind_param("issi", $quantity, $productname, $productcategory, $productcode);
$stmt2 = $conn->prepare("INSERT INTO inventory (productcode, onhand, productname, productcategory, dateupdated) VALUES (?, ?, ?, ?, NOW())");
$stmt2->bind_param("isss", $productcode, $quantity, $productname, $productcategory);

    if (isset($_POST['totalboxes'])) {
        $totalboxes = $_POST['totalboxes'];
    } else {
        $totalboxes = '';
    }
$productcodes = $_POST['productcode'] ?? [];
$quantities = $_POST['totalpieces'] ?? [];
foreach ($productcodes as $key => $productcode) {
    $quantity = $quantities[$key] ?? 0;

    if (isset($_POST['totalpieces'])) {
        $onhand = $_POST['totalpieces'];
    } else {
        $onhand = '';
    }
    // Get productname and productcategory from productlist table
    $stmt4 = $conn->prepare("SELECT productname, productcategory FROM productlist WHERE productcode = ?");
    $stmt4->bind_param("s", $productcode);
    $stmt4->execute();
    $result4 = $stmt4->get_result();
    $row = $result4->fetch_assoc();
    $productname = $row['productname'] ?? '';
    $productcategory = $row['productcategory'] ?? '';

    if (isset($_POST['productname'])) {
        $productname = $_POST['productname'];
    } else {
        $productname = '';
    }
    // Check if productcode exists in stock table
    $stmt3 = $conn->prepare("SELECT productcode FROM inventory WHERE productcode = ?");
    $stmt3->bind_param("s", $productcode);
    $stmt3->execute();
    $result = $stmt3->get_result();
    $row = $result->fetch_assoc();

    // print out the values of the form fields

    echo "<p>totalboxes: " . $totalboxes . "</p>";
    echo "<p>onhand: " . $onhand . "</p>";
    echo "<p>productname: " . $productname . "</p>";


    // Update the stock levels in the inventory table
    $stmt = $conn->prepare("UPDATE inventory SET onhand = onhand + ?, productname = ?, productcategory = ?, dateupdated = NOW() WHERE productcode = ?");
if (!empty($row)) {
    // Productcode exists, update record
    $stmt->bind_param("issi", $quantity, $productname, $productcategory, $productcode);
    $stmt2 = $conn->prepare("INSERT INTO inventory (productcode, onhand, productname, productcategory, dateupdated) VALUES (?, ?, ?, ?, NOW())");
    $stmt->execute();
} else {
    // Productcode does not exist, insert new record
    $stmt2->bind_param("isss", $productcode, $quantity, $productname, $productcategory);
    $stmt2->execute();
}
}

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

    echo "<p class='alert alert-success'>Stock levels updated successfully!</p>";
    ?>
</div>
echo "Stock levels updated successfully!";
/******  ca35d5dc-9dd7-4a7d-b09c-ea1a0e070ff8  *******/
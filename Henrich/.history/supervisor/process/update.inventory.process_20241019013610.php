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
    }
    .container h1 {
        text-align: center;
    }
    .container table {
        width: 100%;
        border-collapse: collapse;
        text-align: center;
    }
    .container th, .container td {
        border: 1px solid #ddd;
        padding: 8px;
    }
    .container tr:nth-child(even) {
        background-color: #f2f2f2;
    }
    .container th {
        background-color: #4CAF50;
        color: white;
    }
</style>
<?php
//fetch data from form add.stockactivitylog.process.php

<div class="container">
    <h1>Update Inventory</h1>
    <table>
        <thead>
            <tr>
                <th>Product Code</th>
                <th>Product Name</th>
                <th>Quantity</th>
                <th>Category</th>
            </tr>
        </thead>
        <tbody>
            <?php
            foreach ($productcodes as $key => $productcode) {
                $quantity = $quantities[$key] ?? 0;
if (isset($_POST['totalboxes'])) {
    $totalboxes = $_POST['totalboxes'];
} else {
    $totalboxes = '';
}

                // Get productname and productcategory from productlist table
                $stmt4 = $conn->prepare("SELECT productname, productcategory FROM productlist WHERE productcode = ?");
                $stmt4->bind_param("s", $productcode);
                $stmt4->execute();
                $result4 = $stmt4->get_result();
                $row = $result4->fetch_assoc();
                $productname = $row['productname'] ?? '';
                $productcategory = $row['productcategory'] ?? '';
            ?>
            <tr>
                <td><?= $productcode ?></td>
                <td><?= $productname ?></td>
                <td><?= $quantity ?></td>
                <td><?= $productcategory ?></td>
            </tr>
            <?php
            }
            ?>
        </tbody>
    </table>
if (isset($_POST['totalpieces'])) {
    $onhand = $_POST['totalpieces'];
} else {
    $onhand = '';
}

    <?php
    if (isset($success)) {
        echo "<div class='alert alert-success'>$success</div>";
    } else if (isset($error)) {
        echo "<div class='alert alert-danger'>$error</div>";
    }
    ?>
if (isset($_POST['productname'])) {
    $productname = $_POST['productname'];
} else {
    $productname = '';
}

</div>
// print out the values of the form fields

echo "totalboxes: ";
echo $totalboxes;
echo "<br>";
echo "onhand: ";
echo $onhand;
echo "<br>";
echo "productname: ";
echo $productname;
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

echo "Stock levels updated successfully!";
/******  383f1747-9524-4824-881f-8588ae46ea75  *******/
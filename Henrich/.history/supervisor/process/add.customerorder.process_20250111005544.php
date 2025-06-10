<style>
    html {
        background-color: #96CEB4;
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
        background-color: #2196F3;
        color: white;
    }
</style>
<?php

echo "<div class='body'>";
require '/xampp/htdocs/HenrichProto/database/dbconnect.php';
require '/xampp/htdocs/HenrichProto/session/session.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $hid = rand(100000000, 999999999);
    $customername = isset($_POST['customername']) ? $_POST['customername'] : '';
    $customeraddress = isset($_POST['customeraddress']) ? $_POST['customeraddress'] : '';
    $customerphonenumber = isset($_POST['customerphonenumber']) ? $_POST['customerphonenumber'] : '';
    $ordertotal = isset($_POST['ordertotal']) ? $_POST['ordertotal'] : '';
    $orderdate = date('Y-m-d');
    $salesperson = isset($_POST['salesperson']) ? $_POST['salesperson'] : '';
    $status = $_POST['status'] ?? 'Pending';

    $productcode = $_POST['productcode'] ?? array();
    $productname = $_POST['productname'] ?? array();
    $productweight = $_POST['productweight'] ?? array();
    $quantity = $_POST['quantity'] ?? array();
    $quantityType = $_POST['quantityType'] ?? array();
    $productprice = $_POST['productprice'] ?? array();

    // Display received data in table
    echo "<table class='output-table'>";
    echo "<tr>
        <th>Product Code</th>
        <th>Product Name</th>
        <th>Product Weight</th>
        <th>Quantity</th>
        <th>Quantity Type</th>
        <th>Product Price</th>
    </tr>";

    foreach ($productname as $key => $value) { 
        echo "<tr>";
        echo "<td>" . htmlspecialchars($productcode[$key]) . "</td>";
        echo "<td>" . htmlspecialchars($value) . "</td>";
        echo "<td>" . htmlspecialchars($productweight[$key]) . "</td>";
        echo "<td>" . htmlspecialchars($quantity[$key]) . "</td>";
        echo "<td>" . htmlspecialchars($quantityType[$key]) . "</td>";
        echo "<td>" . htmlspecialchars($productprice[$key]) . "</td>";
        echo "</tr>";
    }

    echo "</table>";

    // Insert individual ordered products into orderedproduct table
    $sql = "INSERT INTO orderedproduct (orderid, productcode, productname, productweight, productprice, quantity, orderdate) VALUES (?, ?, ?, ?, ?, ?, ?)";

    if ($stmt = $conn->prepare($sql)) {
        foreach ($productname as $key => $value) {
            $orderIDValue = $hid;
            $productCodeValue = isset($productcode[$key]) ? $productcode[$key] : 'N/A';
            $productWeightValue = isset($productweight[$key]) ? $productweight[$key] : 'N/A';
            $productPriceValue = isset($productprice[$key]) ? $productprice[$key] : 'N/A';
            $quantityValue = isset($quantity[$key]) ? $quantity[$key] : 'N/A';
            $orderDateValue = $orderdate;

            $stmt->bind_param("iisiiis", $orderIDValue, $productCodeValue, $value, $productWeightValue, $productPriceValue, $quantityValue, $orderDateValue);
            $stmt->execute();
        }
        $stmt->close();
    } else {
        error_log("add.customerorder.process.php: Error: " . $sql . "<br>" . $conn->error);
        echo "<div class='alert alert-danger'>Error: " . $sql . "<br>" . $conn->error . "</div>";
    }

    // Display success message
    echo "<div class='alert alert-success'>Data inserted successfully!</div>";

    // Insert rows in orderedproducts to orderhistory in one row
    $orderdescription = '';
    foreach ($productname as $key => $value) { 
        $orderdescription .= htmlspecialchars($value . ", " . (isset($quantity[$key]) ? $quantity[$key] :
            'N/A') . " " . (isset($quantityType[$key]) ? $quantityType[$key] : 'N/A')) . "<br>";
    }

    $sql = "INSERT INTO orderhistory (orderdescription, orderdate, customername, customeraddress, customerphonenumber, ordertotal, salesperson, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("ssssssss", $orderdescription, $orderdate, $customername, $customeraddress, $customerphonenumber, $ordertotal, $salesperson, $status);
        $stmt->execute();
        $stmt->close();
    } else {
        error_log("add.customerorder.process.php: Error: " . $sql . "<br>" . $conn->error);
        echo "<div class='alert alert-danger'>Error: " . $sql . "<br>" . $conn->error . "</div>";
    }
}

echo "</div>";

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


    $oid = rand(100000000, 999999999);
    $customername = isset($_POST['customername']) ? $_POST['customername'] : '';
    $customeraddress = isset($_POST['customeraddress']) ? $_POST['customeraddress'] : '';
    $customerphonenumber = isset($_POST['customerphonenumber']) ? $_POST['customerphonenumber'] : '';
    $ordertotal = isset($_POST['ordertotal']) ? $_POST['ordertotal'] : '';
    $orderdate = date('Y-m-d');
    $salesperson = isset($_POST['salesperson']) ? $_POST['salesperson'] : '';
    $status = $_POST['status'] ?? 'Pending';

    $productname = $_POST['productname'] ?? array();
    $quantity = $_POST['quantity'] ?? array();
    $quantityType = $_POST['quantityType'] ?? array();
    $productprice = $_POST['productprice'] ?? array();
    $ordertotal = $_POST['ordertotal'] ?? array();
    $orderdescription = '';

    if (!empty($productname)) {
        foreach ($productname as $key => $value) {
            $quantityValue = isset($quantity[$key]) ? $quantity[$key] : 'N/A';
            $quantityTypeValue = isset($quantityType[$key]) ? $quantityType[$key] : 'N/A';
            $productPriceValue = isset($productprice[$key]) ? $productprice[$key] : 'N/A';
            $orderdescription .= "Product: $value, Quantity: $quantityValue $quantityTypeValue, Price: $productPriceValue, 
                                                Total: $orderTotalValue\n";
        }
    }

    echo "<table class='output-table'>
            <tr>
                <th>OID</th>
                <th>Customer Name</th>
                <th>Customer Address</th>
                <th>Customer Phone Number</th>
                <th>Order Description</th>
                <th>Order Total</th>
                <th>Order Date</th>
                <th>Salesperson</th>
                <th>Status</th>
            </tr>
            <tr>
                <td>" . htmlspecialchars($oid) . "</td>
                <td>" . htmlspecialchars($customername) . "</td>
                <td>" . htmlspecialchars($customeraddress) . "</td>
                <td>" . htmlspecialchars($customerphonenumber) . "</td>
                <td>" . htmlspecialchars($orderdescription) . "</td>
                <td>" . htmlspecialchars(is_array($ordertotal) ? implode(', ', $ordertotal) : $ordertotal) . "</td>
                <td>" . htmlspecialchars($orderdate) . "</td>
                <td>" . htmlspecialchars($salesperson) . "</td>
                <td>" . htmlspecialchars($status) . "</td>
             </tr>
        </table>";

    error_log("add.customerorder.process.php: POST request received with data: " . json_encode($_POST));

    $sql = "INSERT INTO orders (oid, customername, customeraddress, customerphonenumber, orderdescription, ordertotal, orderdate, salesperson, status)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("issisisss", $oid, $customername, $customeraddress, $customerphonenumber, $orderdescription, $ordertotal, $orderdate, $salesperson, $status);
        $stmt->execute();

        error_log("add.customerorder.process.php: Data inserted successfully.");
        echo "<div class='alert alert-success'>Data inserted successfully!</div>";
        $stmt->close();
    } else {
        error_log("add.customerorder.process.php: Error: " . $sql . "<br>" . $conn->error);
        echo "<div class='alert alert-danger'>Error: " . $sql . "<br>" . $conn->error . "</div>";
    }
}

echo "</div>";

?>

<div style="text-align: center;">
    <a href="orders.php" class="btn btn-primary">Back to Orders</a>
</div>

<?php
$conn->close();
?>


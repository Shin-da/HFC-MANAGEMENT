/*************  ✨ Codeium Command 🌟  *************/
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
echo "<p>Running <code>add.customerorder.process.php</code></p>";
require '/xampp/htdocs/HenrichProto/database/dbconnect.php';
require '/xampp/htdocs/HenrichProto/session/session.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $oid = isset($_POST['oid']) ? $_POST['oid'] : '';
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
    $totalprice = $_POST['totalprice'] ?? array();

    $orderdescription = '';

    if (count($productname) > 0) {
        foreach ($productname as $key => $value) {
            $orderdescription .= "Product: $value, Quantity: $quantity[$key] $quantityType[$key], Price: $productprice[$key], Total: $totalprice[$key]\n";
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
                <td>{$oid}</td>
                <td>{$customername}</td>
                <td>{$customeraddress}</td>
                <td>{$customerphonenumber}</td>
                <td>{$orderdescription}</td>
                <td>{$ordertotal}</td>
                <td>{$orderdate}</td>
                <td>{$salesperson}</td>
                <td>{$status}</td>
            </tr>
        </table>";
    error_log("add.customerorder.process.php: POST request received with data: " . json_encode($_POST));

    error_log("add.customerorder.process.php: POST request received with data: " . json_encode($_POST));
    $sql = "INSERT INTO orders (oid, customername, customeraddress, customerphonenumber, orderdescription, ordertotal, orderdate, salesperson, status)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $sql = "INSERT INTO orders (oid, customername, customeraddress, customerphonenumber, orderdescription, ordertotal, orderdate, salesperson, status)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("issisisss", $oid, $customername, $customeraddress, $customerphonenumber, $orderdescription, $ordertotal, $orderdate, $salesperson, $status);
        $stmt->execute();

    if ($stmt = $conn->prepare($sql)) {
        error_log("add.customerorder.process.php: Data inserted successfully.");
        echo "<div class='alert alert-success'>Data inserted successfully!</div>";
        $stmt->close();
    } else {
        error_log("add.customerorder.process.php: Error: " . $sql . "<br>" . $conn->error);
        echo "<div class='alert alert-danger'>Error: " . $sql . "<br>" . $conn->error . "</div>";
    }
}
?>
       

        <div style="text-align: center;">
            <a href="orders.php" class="btn btn-primary">Back to Orders</a>
        </div>
        <?php

        $conn->close();
        ?>


/******  2a507553-114a-4718-a0d0-6f8b3e6658e9  *******/
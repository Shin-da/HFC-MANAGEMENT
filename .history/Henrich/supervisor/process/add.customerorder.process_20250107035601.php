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

<?= "<div class='body'>"; ?>
<?php

require '/xampp/htdocs/HenrichProto/database/dbconnect.php';
require '/xampp/htdocs/HenrichProto/session/session.php';

        echo "<p>Running <code>add.customerorder.process.php</code></p>";
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

/*************  ✨ Codeium Command 🌟  *************/
            $oid = $_POST['oid'] ?? '';
/******  2821c376-10f7-4527-986b-7b2621051a65  *******/
            $customername = isset($_POST['customername']) ? $_POST['customername'] : '';
            $customeraddress = isset($_POST['customeraddress']) ? $_POST['customeraddress'] : '';
            $customerphonenumber = isset($_POST['customerphonenumber']) ? $_POST['customerphonenumber'] : '';
            $ordertotal = isset($_POST['ordertotal']) ? $_POST['ordertotal'] : '';
            $orderdate = isset($_POST['orderdate']) ? $_POST['orderdate'] : '';
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

            $sql = "INSERT INTO orders (oid, customername, customeraddress, customerphonenumber, orderdescription, ordertotal, orderdate, salesperson, status)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

            if ($stmt = $conn->prepare($sql)) {
                $stmt->bind_param("sssssssss", $oid, $customername, $customeraddress, $customerphonenumber, $orderdescription, $ordertotal, $orderdate, $salesperson, $status);
                $stmt->execute();

                echo "<div class='alert alert-success'>Data inserted successfully!</div>";
                $stmt->close();
            } else {
                echo "<div class='alert alert-danger'>Error: " . $sql . "<br>" . $conn->error . "</div>";
            }
        }
        ?>

        <table class='output-table'>
            <tr>
                <th>OID</th>
                <th>Customer Name</th>
                <th>Customer Address</th>
                <th>Customer Phone Number</th>
                <th>Order Description</th>
                <th>ordertotal</th>
                <th>Order Date</th>
                <th>Salesperson</th>
                <th>Status</th>
            </tr>
            <tr>
                <td><?= htmlspecialchars((string)$oid) ?></td>
                <td><?= htmlspecialchars((string)$customername) ?></td>
                <td><?= htmlspecialchars((string)$customeraddress) ?></td>
                <td><?= htmlspecialchars((string)$customerphonenumber) ?></td>
                <td><?= htmlspecialchars((string)$orderdescription) ?></td>
                <td><?= htmlspecialchars((string)$ordertotal) ?></td>
                <td><?= htmlspecialchars((string)$orderdate) ?></td>
                <td><?= htmlspecialchars((string)$salesperson) ?></td>
                <td><?= htmlspecialchars((string)$status) ?></td>
            </tr>
        </table>

        <?php

        $conn->close();
        ?>


<style>
    html {
        background-color: rgb(181, 195, 189);
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
    $orderid = isset($_POST['orderid']) ? $_POST['orderid'] : '';
    $customername = isset($_POST['customername']) ? $_POST['customername'] : '';
    $customeraddress = isset($_POST['customeraddress']) ? $_POST['customeraddress'] : '';
    $customerphonenumber = isset($_POST['customerphonenumber']) ? $_POST['customerphonenumber'] : '';
    $ordertotal = isset($_POST['ordertotal']) ? $_POST['ordertotal'] : '';
    $orderdate = date('Y-m-d');
    $salesperson = isset($_POST['salesperson']) ? $_POST['salesperson'] : '';
    $status = $_POST['status'] ?? 'Pending';
    $ordertype = $_POST['ordertype'] ?? 'N/A';

    // Get current time in Philippine time
    date_default_timezone_set('Asia/Manila');
    $timeoforder = date('H:i:s');

    $productcode = $_POST['productcode'] ?? array();
    $productname = $_POST['productname'] ?? array();
    $productweight = $_POST['productweight'] ?? array();
    $quantity = $_POST['quantity'] ?? array();
    $productprice = $_POST['productprice'] ?? array();
    $ordertotal = array_sum(array_map(function ($a, $b) {
        return $a * $b;
    }, $productprice, $quantity));

    // Insert individual ordered products into orderedproduct table
    $sql = "INSERT INTO orderedproduct (orderid, productcode, productname, productweight, productprice, quantity, orderdate, timeoforder) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    if ($stmt = $conn->prepare($sql)) {
        foreach ($productname as $key => $value) {
            $orderIDValue = $hid;
            $productCodeValue = isset($productcode[$key]) ? $productcode[$key] : 'N/A';
            $productWeightValue = isset($productweight[$key]) ? $productweight[$key] : 'N/A';
            $productPriceValue = isset($productprice[$key]) ? $productprice[$key] : 'N/A';
            $quantityValue = isset($quantity[$key]) ? $quantity[$key] : 'N/A';

            $stmt->bind_param("issddsss", $orderIDValue, $productCodeValue, $value, $productWeightValue, $productPriceValue, $quantityValue, $orderdate, $timeoforder);

            $stmt->execute();
        }
        $stmt->close();
    } else {
        error_log("add.customerorder.process.php: Error: " . $sql . "<br>" . $conn->error);
        echo "<div class='alert alert-danger'>Error: " . $sql . "<br>" . $conn->error . "</div>";
    }

    // Insert rows in orderedproducts to orderhistory in one row
    $orderdescription = '';
    foreach ($productname as $key => $value) {
        $orderdescription .= htmlspecialchars($value . ", " . (isset($productweight[$key]) ? $productweight[$key] : 'N/A') . " kg, " . (isset($quantity[$key]) ? $quantity[$key] : 'N/A') . " " . (isset($quantityType[$key]) ? $quantityType[$key] : 'N/A')) . "<br>";
    }
    $sql = "INSERT INTO orderhistory (orderdescription, orderdate, customername, customeraddress, customerphonenumber, ordertotal, salesperson, status, timeoforder, ordertype) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("sssssdssss", $orderdescription, $orderdate, $customername, $customeraddress, $customerphonenumber, $ordertotal, $salesperson, $status, $timeoforder, $ordertype);
        $stmt->execute();
        $stmt->close();
    } else {
        error_log("add.customerorder.process.php: Error: " . $sql . "<br>" . $conn->error);
        echo "<div class='alert alert-danger'>Error: " . $sql . "<br>" . $conn->error . "</div>";
    }

    echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>";
    echo "<script>
        Swal.fire({
            title: 'Success!',
            text: 'Data has been successfully added to both tables!',
            icon: 'success',
            confirmButtonText: 'OK'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = '../orderhistory.php'
            }
        });
    </script>";

    // Display the received data in a table
    echo "<table class='output-table'>";
    echo "<tr><th>Product Code</th><th>Product Name</th><th>Weight</th><th>Quantity</th><th>Price</th></tr>";
    foreach ($productname as $key => $value) {
        echo "<tr>
                <td>" . htmlspecialchars($productcode[$key] ?? 'N/A') . "</td>
                <td>" . htmlspecialchars($value) . "</td>
                <td>" . htmlspecialchars($productweight[$key] ?? 'N/A') . "</td>
                <td>" . htmlspecialchars($quantity[$key] ?? 'N/A') . "</td>
                <td>" . htmlspecialchars($productprice[$key] ?? 'N/A') . "</td>
              </tr>";
    }
    echo "</table>";
}

echo "</div>";


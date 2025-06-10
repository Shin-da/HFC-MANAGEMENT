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
    // Validate stock availability first
    $productcode = $_POST['productcode'] ?? array();
    $quantity = $_POST['quantity'] ?? array();
    $insufficientStock = false;
    $outOfStockProducts = array();

    // Check stock availability for all products
    foreach ($productcode as $key => $code) {
        $checkStockSql = "SELECT availablequantity, productname FROM inventory WHERE productcode = ?";
        if ($checkStmt = $conn->prepare($checkStockSql)) {
            $checkStmt->bind_param("s", $code);
            $checkStmt->execute();
            $result = $checkStmt->get_result();
            if ($row = $result->fetch_assoc()) {
                if ($row['availablequantity'] < $quantity[$key] || $row['availablequantity'] <= 0) {
                    $insufficientStock = true;
                    $outOfStockProducts[] = array(
                        'productname' => $row['productname'],
                        'available' => $row['availablequantity'],
                        'requested' => $quantity[$key]
                    );
                }
            }
            $checkStmt->close();
        }
    }

    if ($insufficientStock) {
        $errorMessage = "Cannot process order due to insufficient stock:\n";
        foreach ($outOfStockProducts as $product) {
            $errorMessage .= "- {$product['productname']}: Available: {$product['available']}, Requested: {$product['requested']}\n";
        }
        
        echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>";
        echo "<script>
            Swal.fire({
                icon: 'error',
                title: 'Insufficient Stock',
                html: `" . nl2br(htmlspecialchars($errorMessage)) . "`,
                confirmButtonColor: '#3085d6'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.history.back();
                }
            });
        </script>";
        exit;
    }

    // If stock validation passes, continue with the original process
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
        return (float)$a * (int)$b;
    }, $productprice, $quantity));

    // Insert individual ordered products into orderlog table
    $sql = "INSERT INTO orderlog (orderid, productcode, productname, productweight, productprice, quantity, orderdate, timeoforder) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    if ($stmt = $conn->prepare($sql)) {
        foreach ($productname as $key => $value) {
            $orderIDValue = $orderid; // Use the passed orderid
            $productCodeValue = isset($productcode[$key]) ? $productcode[$key] : 'N/A';
            $productCodeValue = isset($productcode[$key]) ? $productcode[$key] : 'N/A';
            $productWeightValue = isset($productweight[$key]) ? $productweight[$key] : 'N/A';
            $productPriceValue = isset($productprice[$key]) ? $productprice[$key] : 'N/A';
            $quantityValue = isset($quantity[$key]) ? $quantity[$key] : 'N/A';

            $stmt->bind_param("issddsss", $orderIDValue, $productCodeValue, $value, $productWeightValue, $productPriceValue, $quantityValue, $orderdate, $timeoforder);

            $stmt->execute();

            // Deduct available quantity in inventory table
            $updateSql = "UPDATE inventory SET availablequantity = availablequantity - ? WHERE productcode = ?";
            if ($updateStmt = $conn->prepare($updateSql)) {
                $updateStmt->bind_param("is", $quantityValue, $productCodeValue);
                $updateStmt->execute();
                $updateStmt->close();
            } else {
                error_log("add.customerorder.process.php: Error: " . $updateSql . "<br>" . $conn->error);
            }
        }
        $stmt->close();
    } else {
        error_log("add.customerorder.process.php: Error: " . $sql . "<br>" . $conn->error);
        echo "<div class='alert alert-danger'>Error: " . $sql . "<br>" . $conn->error . "</div>";
    }

    // Insert rows in orderedproducts to customerorder in one row
    $orderdescription = '';
    foreach ($productname as $key => $value) {
        $orderdescription .= htmlspecialchars($value . ", " . (isset($productweight[$key]) ? $productweight[$key] : 'N/A') . " kg, " . (isset($quantity[$key]) ? $quantity[$key] : 'N/A') . " " . (isset($quantityType[$key]) ? $quantityType[$key] : 'N/A')) . "<br>";
    }
    $sql = "INSERT INTO customerorder (orderdescription, orderdate, customername, customeraddress, customerphonenumber, ordertotal, salesperson, status, timeoforder, ordertype) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

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
                window.location.href = '../customerorder.php'
            }
        });
    </script>";

    // Display the received data in a table
   echo "<h2>Order Summary</h2>";
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

    echo "<h2>Inventory Updates</h2>";
    echo "<table class='output-table'>";

    // Display the updated inventory table
    echo "<tr><th>Product Code</th><th>Product Name</th><th>Available Quantity</th></tr>";
    foreach ($productcode as $key => $productCodeValue) {
        $sql = "SELECT productcode, productname, availablequantity FROM inventory WHERE productcode = ?";
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("s", $productCodeValue);
            $stmt->execute();
            $stmt->bind_result($productcode, $productname, $availablequantity);
            $stmt->fetch();
            echo "<tr>
                    <td>" . htmlspecialchars($productcode) . "</td>
                    <td>" . htmlspecialchars($productname) . "</td>
                    <td>" . htmlspecialchars($availablequantity) . "</td>
                  </tr>";
            $stmt->close();
        } else {
            error_log("add.customerorder.process.php: Error: " . $sql . "<br>" . $conn->error);
            echo "<div class='alert alert-danger'>Error: " . $sql . "<br>" . $conn->error . "</div>";
        }
    }
    echo "</table>";
}

echo "</div>";



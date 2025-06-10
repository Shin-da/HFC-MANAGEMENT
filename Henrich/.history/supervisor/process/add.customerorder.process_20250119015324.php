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
    // Get the order ID from the form
    $orderid = isset($_POST['orderid']) ? $_POST['orderid'] : '';
    
    // Validate that orderid exists and is properly formatted
    if (empty($orderid) || strlen($orderid) < 10) {
        error_log("Invalid order ID: " . $orderid);
        echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>";
        echo "<script>
            Swal.fire({
                icon: 'error',
                title: 'Invalid Order ID',
                text: 'Please try again with a valid order ID.',
                confirmButtonColor: '#3085d6'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.history.back();
                }
            });
        </script>";
        exit;
    }

    // Check if orderid already exists
    $checkSql = "SELECT orderid FROM customerorder WHERE orderid = ?";
    if ($checkStmt = $conn->prepare($checkSql)) {
        $checkStmt->bind_param("s", $orderid);
        $checkStmt->execute();
        $result = $checkStmt->get_result();
        if ($result->num_rows > 0) {
            error_log("Duplicate order ID attempt: " . $orderid);
            echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>";
            echo "<script>
                Swal.fire({
                    icon: 'error',
                    title: 'Duplicate Order ID',
                    text: 'Please try again. A new order ID will be generated.',
                    confirmButtonColor: '#3085d6'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.history.back();
                    }
                });
            </script>";
            exit;
        }
        $checkStmt->close();
    }

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
    $customername = isset($_POST['customername']) ? $_POST['customername'] : '';
            $status, 
            $timeoforder, 
            $ordertype
        );
        
        if (!$stmt->execute()) {
            error_log("Error inserting into customerorder: " . $stmt->error);
            echo "<div class='alert alert-danger'>Error creating order</div>";
            exit;
        }
        $stmt->close();
    }

    // Then insert order items into orderlog
    $sql = "INSERT INTO orderlog (orderid, productcode, productname, productweight, productprice, quantity, orderdate, timeoforder) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    
    if ($stmt = $conn->prepare($sql)) {
        foreach ($productname as $key => $value) {
            $stmt->bind_param("issddsss", 
                $orderid,
                $productcode[$key],
                $value,
                $productweight[$key],
                $productprice[$key],
                $quantity[$key],
                $orderdate,
                $timeoforder
            );
            
            if (!$stmt->execute()) {
                error_log("Error inserting into orderlog: " . $stmt->error);
            }
            
            // Update inventory
            updateInventory($conn, $productcode[$key], $quantity[$key]);
        }
        $stmt->close();
    }

    // Success message and redirect
    echo "<script>
        Swal.fire({
            title: 'Success!',
            text: 'Order #$orderid has been created successfully!',
            icon: 'success',
            confirmButtonText: 'OK'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = '../customerorderdetail.php?orderid=$orderid'
            }
        });
    </script>";
}

// Helper function to update inventory
function updateInventory($conn, $productCode, $quantity) {
    $sql = "UPDATE inventory 
            SET availablequantity = availablequantity - ? 
            WHERE productcode = ?";
    
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("is", $quantity, $productCode);
        $stmt->execute();
        $stmt->close();
    }
}

echo "</div>";



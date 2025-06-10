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
require_once '../../database/dbconnect.php';
session_start();

if (isset($_POST['addCustomerOrder'])) {
    try {
        $conn->begin_transaction();

        // Get form data
        $orderid = $_POST['orderid'];
        $orderdate = $_POST['orderdate'];
        $salesperson = $_POST['salesperson'];
        $ordertype = $_POST['ordertype'];
        $customername = $_POST['customername'];
        $customeraddress = $_POST['customeraddress'];
        $customerphonenumber = $_POST['customerphonenumber'];
        
        // Arrays from form
        $productcodes = $_POST['productcode'];
        $quantities = $_POST['quantity'];
        $totalprices = $_POST['totalprice'];
        
        // Validate stock availability
        $stockErrors = [];
        foreach ($productcodes as $index => $productcode) {
            // Get current available stock
            $stockQuery = "SELECT 
                COALESCE(i.availablequantity, i.onhandquantity) as available_qty,
                p.productname
                FROM inventory i 
                JOIN products p ON p.productcode = i.productcode 
                WHERE i.productcode = ?";
            
            $stmt = $conn->prepare($stockQuery);
            $stmt->bind_param("s", $productcode);
            $stmt->execute();
            $result = $stmt->get_result();
            $stock = $result->fetch_assoc();
            
            $requestedQty = $quantities[$index];
            $availableQty = $stock['available_qty'];
            
            if ($requestedQty > $availableQty) {
                $stockErrors[] = "{$stock['productname']}: Available: {$availableQty}, Requested: {$requestedQty}";
            }
        }
        
        // If there are stock errors, throw exception
        if (!empty($stockErrors)) {
            throw new Exception("Cannot process order due to insufficient stock:\n- " . implode("\n- ", $stockErrors));
        }

        // Calculate order total
        $ordertotal = array_sum($totalprices);
        
        // Create order description
        $orderDescription = [];
        foreach ($productcodes as $index => $code) {
            $quantity = $quantities[$index];
            $orderDescription[] = "$code x$quantity";
        }
        $orderdescription = implode(", ", $orderDescription);

        // Insert order
        $orderSql = "INSERT INTO customerorder (orderid, orderdate, timeoforder, salesperson, ordertype, 
                    customername, customeraddress, customerphonenumber, orderdescription, ordertotal, status) 
                    VALUES (?, ?, CURRENT_TIME(), ?, ?, ?, ?, ?, ?, ?, 'Processing')";
        
        $stmt = $conn->prepare($orderSql);
        $stmt->bind_param("ssssssssd", 
            $orderid, $orderdate, $salesperson, $ordertype, 
            $customername, $customeraddress, $customerphonenumber, 
            $orderdescription, $ordertotal
        );
        $stmt->execute();

        // Update inventory
        foreach ($productcodes as $index => $productcode) {
            $quantity = $quantities[$index];
            
            // Update both onhandquantity and availablequantity
            $updateSql = "UPDATE inventory 
                         SET onhandquantity = onhandquantity - ?,
                             availablequantity = CASE 
                                 WHEN availablequantity IS NULL THEN onhandquantity - ?
                                 ELSE availablequantity - ?
                             END
                         WHERE productcode = ?";
            
            $stmt = $conn->prepare($updateSql);
            $stmt->bind_param("iiis", $quantity, $quantity, $quantity, $productcode);
            $stmt->execute();
        }

        $conn->commit();
        
        $_SESSION['success'] = "Order successfully created!";
        header("Location: ../customerorder.list.php");
        exit();

    } catch (Exception $e) {
        $conn->rollback();
        $_SESSION['error'] = $e->getMessage();
        header("Location: ../add.customerorder.php");
        exit();
    }
}
?>



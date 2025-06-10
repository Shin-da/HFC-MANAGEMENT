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
    $customeraddress = isset($_POST['customeraddress']) ? $_POST['customeraddress'] : '';
    $customerphonenumber = isset($_POST['customerphonenumber']) ? $_POST['customerphonenumber'] : '';
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

    // Generate order description
    $orderdescription = '';
    foreach ($productname as $key => $name) {
        $orderdescription .= sprintf(
            "%s (%s kg) x %d, ", 
            $name,
            $productweight[$key],
            $quantity[$key]
        );
    }
    // Remove trailing comma and space
    $orderdescription = rtrim($orderdescription, ", ");

    // First insert into customerorder table
    $sql = "INSERT INTO customerorder (orderid, orderdescription, orderdate, customername, customeraddress, customerphonenumber, ordertotal, salesperson, status, timeoforder, ordertype) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("ssssssdssss", 
            $orderid,
            $orderdescription, 
            $orderdate, 
            $customername, 
            $customeraddress, 
            $customerphonenumber, 
            $ordertotal, 
            $salesperson, 
            $status, 
            $timeoforder, 
            $ordertype
        );
        
        if ($stmt->execute()) {
            // Only proceed with orderlog insertion if customerorder insertion was successful
            $orderLogSql = "INSERT INTO orderlog (orderid, productcode, productname, productweight, productprice, quantity, orderdate, timeoforder) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            
            if ($orderLogStmt = $conn->prepare($orderLogSql)) {
                $insertError = false;
                
                foreach ($productcode as $key => $code) {
                    $productWeightValue = floatval($productweight[$key]);
                    $productPriceValue = floatval($productprice[$key]);
                    $quantityValue = intval($quantity[$key]);
                    
                    // Verify orderid before binding
                    error_log("Inserting orderlog with orderid: " . $orderid);
                    
                    $orderLogStmt->bind_param("sssdddss", 
                        $orderid,
                        $code,
                        $productname[$key],
                        $productWeightValue,
                        $productPriceValue,
                        $quantityValue,
                        $orderdate,
                        $timeoforder
                    );
                    
                    if (!$orderLogStmt->execute()) {
                        $insertError = true;
                        error_log("Error inserting into orderlog: " . $orderLogStmt->error);
                        error_log("Failed values: orderid=$orderid, code=$code, name={$productname[$key]}, weight=$productWeightValue, price=$productPriceValue, qty=$quantityValue");
                    } else {
                        updateInventory($conn, $code, $quantityValue);
                    }
                }
                $orderLogStmt->close();
                
                if ($insertError) {
                    echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>";
                    echo "<script>
                        Swal.fire({
                            icon: 'warning',
                            title: 'Partial Success',
                            text: 'Order created but some items may not have been properly logged.',
                            confirmButtonColor: '#3085d6'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                window.location.href = '../customerorderdetail.php?orderid=$orderid';
                            }
                        });
                    </script>";
                } else {
                    echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>";
                    echo "<script>
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: 'Order #$orderid has been created successfully!',
                            confirmButtonText: 'OK'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                window.location.href = '../customerorderdetail.php?orderid=$orderid';
                            }
                        });
                    </script>";
                }
            }
        } else {
            error_log("Error inserting into customerorder: " . $stmt->error);
            echo "<div class='alert alert-danger'>Error creating order</div>";
        }
        $stmt->close();
    }
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



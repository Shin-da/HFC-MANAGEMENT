<?php
require '../reusable/redirect404.php';
require '../session/session.php';
require '../database/dbconnect.php';
$current_page = basename($_SERVER['PHP_SELF'], '.php');

// Update order status
if (isset($_POST['updateStatus'])) {
    $oid = $_POST['oid'];
    $status = $_POST['status'] ?? '';
    if ($status != '') {
        $sql = "UPDATE orders SET status = '$status' WHERE oid = '$oid'";
        if ($conn->query($sql) === TRUE) {
            echo "<script>alert('Order status has been updated!');</script>";
            header("Location: orderdetail.php?oid=$oid");
            exit;
        } else {
            echo "Error updating record: " . $conn->error;
        }
    }
}

// Delete order
if (isset($_POST['deleteOrder'])) {
    $oid = $_POST['oid'];
    $sql = "DELETE FROM orders WHERE oid = '$oid'";
    if ($conn->query($sql) === TRUE) {
        echo "<script>alert('Order has been deleted!');</script>";
        header("Location: transactions.php");
        exit;
    } else {
        echo "Error deleting record: " . $conn->error;
    }
}
?>
<!DOCTYPE html>
<html>

<head>
    <title>Order Detail</title>
    <?php require '../reusable/header.php'; ?>
    <style>
        .order-detail {
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 20px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1), 0 8px 16px rgba(0, 0, 0, 0.05);
            border-radius: 5px;
            margin-bottom: 20px;
        }

        .order-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }

        .order-header h2 {
            font-size: 18px;
            font-weight: 600;
            margin: 0;
        }

        .order-body {
            display: flex;
            flex-direction: column;
        }

        .order-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }

        .order-item span:first-child {
            font-weight: 600;
        }

        .order-item span:last-child {
            font-size: 16px;
        }

        .btn {
            margin-top: 10px;
        }

        .btn-danger {
            background-color: #dc3545;
            border-color: #dc3545;
        }

        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
        }

        @media only screen and (max-width: 600px) {
            .order-detail {
                width: 100%;
            }

            .order-header {
                flex-direction: column;
            }

            .order-header h2 {
                margin-bottom: 10px;
            }

            .order-item {
                flex-direction: column;
            }

            .order-item span:first-child {
                margin-bottom: 5px;
            }
        }
    </style>
</head>

<body>
    <?php include '../reusable/sidebar.php';   // Sidebar    
    ?>
    <div class="panel">
        <?php include '../reusable/navbarNoSearch.html';   // TOP NAVBAR    
        ?>
        <div class="container">
            <div class="btn">
                                <button class="btn btn-secondary" onclick="window.history.back();">Back to Previous Page</button>
                            </div>
            <div class="order-header">
                <h2 class="header-title">Order Detail</h2>
                <div class="header-subtitle">View order details</div>
            </div>
            <div class="order-detail">
                <div class="order">
                    <?php
                    if (isset($_GET['oid'])) {
                        $oid = $_GET['oid'];
                        $sql = "SELECT * FROM orders WHERE oid = '$oid'";
                        $result = $conn->query($sql);
                        if ($result->num_rows > 0) {
                            $row = $result->fetch_assoc();
                            $customername = $row["customername"];
                            $customeraddress = $row["customeraddress"];
                            $customerphonenumber = $row["customerphonenumber"];
                            $orderdescription = $row["orderdescription"];
                            $ordertotal = $row["ordertotal"];
                            $orderdate = $row["orderdate"];
                            $salesperson = $row["salesperson"];
                            $status = $row["status"];

                            echo "
                            <div class='order-container'>
                                <div class='order-header'>
                                    <h3>Order # $oid</h3>
                                </div>
                                <div class='order-body'>
                                    <div class='order-item'>
                                        <span>Customer Name:</span>
                                        <span>$customername</span>
                                    </div>
                                    <div class='order-item'>
                                        <span>Customer Address:</span>
                                        <span>$customeraddress</span>
                                    </div>
                                    <div class='order-item'>
                                        <span>Customer Phone Number:</span>
                                        <span>$customerphonenumber</span>
                                    </div>
                                    <div class='order-item'>
                                        <span>Order Description:</span>
                                        <span>";
                            $orderDescription = explode(", ", $orderdescription ?? '');
                            foreach ($orderDescription as $desc) {
                                echo "- " . $desc . "<br>";
                            }
                            echo "</span>
                                    </div>
                                    <div class='order-item'>
                                        <span>Order Total:</span>
                                        <span>₱ $ordertotal</span>
                                    </div>
                                    <div class='order-item'>
                                        <span>Order Date:</span>
                                        <span>$orderdate</span>
                                    </div>
                                    <div class='order-item'>
                                        <span>Salesperson:</span>
                                        <span>$salesperson</span>
                                    </div>
                                    <div class='order-item'>
                                        <span>Status:</span>
                                        <span class='$status'>$status</span>
                                        <form action='' method='post'>
                                            <input type='hidden' name='oid' value='$oid'>
                                            <select name='status' class='form-control'>
                                                <option value='Pending'>Pending</option>
                                                <option value='Completed'>Completed</option>
                                                <option value='Cancelled'>Cancelled</option>
                                            </select>
                                            <button type='submit' name='updateStatus' class='btn btn-primary' onclick=\"return confirm('Are you sure you want to update the status of this order?')\">Update Status</button>
                                        </form>
                                    </div>
                                    ";
                                    if (isset($_SESSION['userlevel']) && $_SESSION['userlevel'] == 'admin') {
                                        echo "
                                        <div class='order-item'>
                                            <form action='' method='post'>
                                                <input type='hidden' name='oid' value='$oid'>
                                                <button type='submit' name='deleteOrder' class='btn btn-danger' onclick=\"return confirm('Are you sure you want to delete this order?')\">Delete Order</button>
                                            </form>
                                        </div>
                                        ";
                                    }
                                    echo "
                                </div>
                            </div>
                            ";
                        } else {
                            echo "<p>0 results</p>";
                        }
                    } else {
                        echo "<p>Invalid request</p>";
                    }
                    ?>
               
                </div>
            </div>
        </div>
    </div>
    <?php require '../reusable/footer.php'; ?>
</body>

</html>


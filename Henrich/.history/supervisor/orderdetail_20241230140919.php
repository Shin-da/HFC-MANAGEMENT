/*************  ✨ Codeium Command 🌟  *************/
<?php
require '../reusable/redirect404.php';
require '../session/session.php';
require '../database/dbconnect.php';
$conn = db_connect();
$conn = connect_to_db();
$current_page = basename($_SERVER['PHP_SELF'], '.php');

// Function to update order status
function updateOrderStatus($oid, $status) {
    global $conn;
    $sql = "UPDATE orders SET status = '$status' WHERE oid = '$oid'";
    if ($conn->query($sql) === TRUE) {
        echo "<script>alert('Order status has been updated!');</script>";
        header("Location: orderdetail.php?oid=$oid");
        exit;
    } else {
        echo "Error updating record: " . $conn->error;
    }
}

// Function to delete order
function deleteOrder($oid) {
    global $conn;
    $sql = "DELETE FROM orders WHERE oid = '$oid'";
    if ($conn->query($sql) === TRUE) {
        echo "<script>alert('Order has been deleted!');</script>";
        header("Location: transactions.php");
        exit;
    } else {
        echo "Error deleting record: " . $conn->error;
    }
}

// Function to print receipt
function printReceipt($oid) {
    global $conn;
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
        echo "
        <div class='receipt-content'>
            <div class='receipt-container'>
                <div class='receipt-header'>
                    <h2>Receipt</h2>
                    <p>Transaction Date: " . htmlspecialchars($orderdate) . "</p>
                    <p>Receipt Date: " . htmlspecialchars(date('Y-m-d')) . "</p>
                </div>
                <hr>
                <div class='receipt-body'>
                    <div class='receipt-info'>
                        <div class='receipt-item'>
                            <span>Order ID:</span>
                            <span>" . htmlspecialchars($oid) . "</span>
                        </div>
                        <div class='receipt-item'>
                            <span>Customer Name:</span>
                            <span>" . htmlspecialchars($customername) . "</span>
                        </div>
                    </div>
                    <hr>
                    <div class='receipt-item'>
                        <span>Payment Method:</span>
                        <span>Cash</span>
                    </div>
                    <hr>
                    <div class='receipt-item'>
                        <span>Payment Status:</span>
                        <span> Paid</span>
                    </div>
                    <hr>
                    <div class='receipt-item'>
                        <span>Order Total:</span>
                        <span>₱ " . htmlspecialchars($ordertotal) . "</span>
                    </div>
                    <hr>
                    <div class='receipt-item'>
                        <span>Salesperson:</span>
                        <span>" . htmlspecialchars($salesperson) . "</span>
                    </div>
                    <hr>
                    <div class='receipt-item'>
                        <span>Order Description:</span>
                        <div class='receipt-description'>
                            <ul style='list-style: none; padding: 0; margin: 0'>
                                ";
        $orderDescription = explode(", ", $orderdescription ?? '');
        foreach ($orderDescription as $desc) {
            echo "<li>" . htmlspecialchars($desc) . "</li>";
        }
        echo "</ul>
                            </div>
                        </div>
                    </div>
                </div>
                <div class='receipt-footer'>
                    <p>Thank you for your business!</p>
                </div>
            </div>
        </div>
        ";
    }
}

// Function to print invoice
function printInvoice($oid) {
    global $conn;
    $sql = "SELECT * FROM orders WHERE oid = '$oid'";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $customername = $row['customername'];
        $customeraddress = $row['customeraddress'];
        $customerphonenumber = $row['customerphonenumber'];
        $orderdate = $row['orderdate'];
        $orderdescription = $row['orderdescription'];
        $ordertotal = $row['ordertotal'];
        $salesperson = $row['salesperson'];
        echo "
        <div class='invoice-content'>
            <div class='invoice-container'>
                <div class='invoice-header'>
                    <div class='invoice-logo'>
                        <img src='../images/logo.png' alt='Company Logo'>
                    </div>
                    <div class='invoice-info'>
                        <h2>Invoice</h2>
                        <p>Invoice ID: " . htmlspecialchars($oid) . "</p>
                        <p>Invoice Date: " . htmlspecialchars(date('Y-m-d')) . "</p>
                        <p>Due Date: " . htmlspecialchars(date('Y-m-d', strtotime('+7 days'))) . "</p>
                        <p>Issued by: " . htmlspecialchars($_SESSION['username']) . "</p>
                    </div>
                </div>
                <hr>
                <div class='invoice-bill-to'>
                    <p>Bill to:</p>
                    <ul style='list-style: none; padding: 0; margin: 0'>
                        <li>Name: " . htmlspecialchars($customername) . "</li>
                        <li>Address: " . htmlspecialchars($customeraddress) . "</li>
                        <li>Phone: " . htmlspecialchars($customerphonenumber) . "</li>
                    </ul>
                </div>
                <hr>
                <div class='invoice-salesperson'>
                    <p>Salesperson: " . htmlspecialchars($salesperson) . "</p>
                </div>
                <div class='invoice-items'>
                    <p>Order Total: ₱ " . htmlspecialchars($ordertotal) . "</p>
                </div>
                <hr>
                <div class='invoice-description'>
                    <p>Order Description:</p>
                    <ul style='list-style: none; padding: 0; margin: 0'>
                        ";
        $orderDescription = explode(", ", $orderdescription ?? '');
        foreach ($orderDescription as $desc) {
            echo "<li>" . htmlspecialchars($desc) . "</li>";
        }
        echo "</ul>
                    </div>
                    <hr>
                    <div class='invoice-footer'>
                        <p>Payment is due within 30 days</p>
                    </div>
                </div>
            </div>
        </div>
        ";
    }
}

// Update order status
if (isset($_POST['updateStatus'])) {
    $oid = $_POST['oid'];
    $status = $_POST['status'] ?? '';
    if ($status != '') {
        updateOrderStatus($oid, $status);
    }
}

// Delete order
if (isset($_POST['deleteOrder'])) {
    $oid = $_POST['oid'];
    deleteOrder($oid);
}

// Print receipt
if (isset($_GET['oid'])) {
    $oid = $_GET['oid'];
    printReceipt($oid);
}

// Print invoice
if (isset($_GET['oid'])) {
    $oid = $_GET['oid'];
    printInvoice($oid);
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Detail</title>
    <?php require '../reusable/header.php'; ?>
    <style>
        #back-toprev {
            /* display: flex;
            align-items: center;
            margin-bottom: 20px; */
        }

        #back-toprev .i {
            font-size: 24px;
            cursor: pointer;
            margin-right: 10px;
            color: var(--accent-color);
        }

        #back-toprev .i:hover {
            color: var(--accent-color);
        }

        .order-detail {
            background-color: #fff;
            max-width: 600px;
            margin: 20px auto;
            padding: 20px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1), 0 8px 16px rgba(0, 0, 0, 0.05);
            border-radius: 5px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .order-header {
            display: flex;
            text-align: center;
            align-items: center;
            margin-bottom: 20px;
            width: 100%;
            /* background-color: var(--yellow-color); */
            color: var(--black);
            padding: 10px;
            border-radius: 5px 5px 0 0;
        }

        .order-header h2 {
            font-size: 24px;
            font-weight: 600;
            margin: 0;
        }

        .order-body {
            display: flex;
            flex-direction: column;
            gap: 10px;
            width: 100%;
        }

        .order-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <?php include '../reusable/sidebar.php';   // Sidebar   
    ?>
    <section class="panel">
        <?php include '../reusable/navbarNoSearch.html'; // TOP NAVBAR         
        ?>
        <div class="order-detail">  
            <div class="order-header">
                <h2>Order Detail</h2>
            </div>
            <div class="order-body">
                <?php
                $oid = $_GET['oid'];
                $sql = "SELECT * FROM orders WHERE oid = '$oid'";
                $result = $conn->query($sql);
                if ($result->num_rows > 0) {
                    $row = $result->fetch_assoc();
                    $customername = $row['customername'];
                    $customeraddress = $row['customeraddress'];
                    $customerphonenumber = $row['customerphonenumber'];
                    $orderdate = $row['orderdate'];
                    $orderdescription = $row['orderdescription'];
                    $ordertotal = $row['ordertotal'];
                    $salesperson = $row['salesperson'];
                    printOrderDetail($oid, $customername, $customeraddress, $customerphonenumber, $orderdate, $orderdescription, $ordertotal, $salesperson);
                } else {
                    echo "<p>No order found.</p>";
                }
                ?>
            </div>
        </div>
    </section>
</body>
/******  13c2e8ba-fd6e-41d2-9071-9f4784b80c7c  *******/
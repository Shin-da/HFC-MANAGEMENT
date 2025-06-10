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
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Detail</title>
    <?php require '../reusable/header.php'; ?>
    <style>
        .order-detail {
            max-width: 600px;
            margin: 20px auto;
            padding: 20px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1), 0 8px 16px rgba(0, 0, 0, 0.05);
            border-radius: 5px;
        }

        .order-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
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
        }

        .order-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .order-item span:first-child {
            font-weight: 600;
        }

        .btn {
            display: flex;
            justify-content: center;
            margin-top: 20px;
        }

        .btn > button {
            margin: 0 5px;
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
            .order-item {
                flex-direction: column;
                align-items: flex-start;
            }

            .order-item span:first-child {
                margin-bottom: 5px;
            }
        }

        .receipt-content {
            display: none;
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 20px;
        }
        
        .receipt-header, .invoice-header {
            background-color: #f0f0f0;
            padding: 10px;
            border-bottom: 1px solid #ccc;
        }
        
        .logo {
            width: 100px;
            height: 100px;
            margin: 10px;
        }
        
        .receipt-body, .invoice-body {
            padding: 20px;
        }
        
        .order-summary, .payment-method, .salesperson-info, .payment-terms {
            margin-bottom: 20px;
        }
        
        .order-summary table {
            border-collapse: collapse;
            width: 100%;
        }
        
        .order-summary th, .order-summary td {
            border: 1px solid #ccc;
            padding: 10px;
        }
        
        .receipt-footer, .invoice-footer {
            background-color: #f0f0f0;
            padding: 10px;
            border-top: 1px solid #ccc;
        }
        
        @media print {
            .receipt-content {
                display: block;
            }
        }

    </style>
</head>

<body>
    <?php include '../reusable/sidebar.php'; ?>
    <div class="panel">
        <?php include '../reusable/navbarNoSearch.html'; ?>
        <div class="order-detail">
            <div class="order-header">
                <a href="javascript:window.history.back()" id="back-toprev"> <i class='bx bx-chevron-left'></i> </a>
                <h2>Order Detail</h2>
                <p>Viewing Order ID # <?= htmlspecialchars(isset($_GET['oid']) ? $_GET['oid'] : 'N/A') ?></p>
            </div>
            <div class="order-body">
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
                            echo "- " . htmlspecialchars($desc) . "<br>";
                        }
                        echo "</span>
                        </div>
                        <div class='order-item'>
                            <span>Order Total:</span>
                            <span>₱ " . htmlspecialchars($ordertotal) . "</span>
                        </div>
                        <div class='order-item'>
                            <span>Order Date:</span>
                            <span>" . htmlspecialchars($orderdate) . "</span>
                        </div>
                        <div class='order-item'>
                            <span>Salesperson:</span>
                            <span>" . htmlspecialchars($salesperson) . "</span>
                        </div>
                        <div class='order-item'>
                            <span>Status:</span>
                            <span class='$status'>" . htmlspecialchars($status) . "</span>
                            <form action='' method='post'>
                                <input type='hidden' name='oid' value='$oid'>
                                <select name='status' class='form-control'>
                                    <option value='Pending'>Pending</option>
                                    <option value='Completed'>Completed</option>
                                    <option value='Cancelled'>Cancelled</option>
                                </select>
                                <button type='submit' name='updateStatus' class='btn btn-primary' onclick=\"return confirm('Are you sure you want to update the status of this order?')\">Update Status</button>
                            </form>";
                        if ($status === 'Completed') {
                            echo "
                            <button type='button' class='btn btn-success' onclick=\"printReceipt()\">Print Receipt</button>
                            ";
                        }
                        echo "
                            <button type='button' class='btn btn-info' onclick=\"printInvoice()\">Print Invoice</button>
                            <script>
                                function printReceipt() {
                                    var printContents = document.getElementById('receipt').innerHTML;
                                    var originalContents = document.body.innerHTML;
                                    document.body.innerHTML = printContents;
                                    window.print();
                                    document.body.innerHTML = originalContents;
                                }
                                function printInvoice() {
                                    var printContents = document.getElementById('invoice').innerHTML;
                                    var originalContents = document.body.innerHTML;
                                    document.body.innerHTML = printContents;
                                    window.print();
                                    document.body.innerHTML = originalContents;
                                }
                            </script>
/*************  ✨ Codeium Command 🌟  *************/
                          <div id='receipt' class='receipt-content' style='display:none;'>
                          <div id='receipt' class='receipt-content'>
    <div class='receipt-header'>
        <img src='logo.png' alt='Company Logo' class='logo'>
        <h2>Receipt</h2>
        <p>Transaction Date: <?= htmlspecialchars($orderdate) ?></p>
    </div>
    <div class='receipt-body'>
        <div class='order-summary'>
            <h3>Order Summary</h3>
            <table>
                <tr>
                    <th>Order ID:</th>
                    <td><?= htmlspecialchars($oid) ?></td>
                </tr>
                <tr>
                    <th>Customer Name:</th>
                    <td><?= htmlspecialchars($customername) ?></td>
                </tr>
                <tr>
                    <th>Order Total:</th>
                    <td>₱ <?= htmlspecialchars($ordertotal) ?></td>
                </tr>
            </table>
        </div>
        <div class='payment-method'>
            <h3>Payment Method</h3>
            <p>Cash</p>
        </div>
        <div class='salesperson-info'>
            <h3>Salesperson Information</h3>
            <p><?= htmlspecialchars($salesperson) ?></p>
        </div>
    </div>
    <div class='receipt-footer'>
        <p>Thank you for your business!</p>
    </div>
</div>

<div id='invoice' class='receipt-content' style='display:none;'>
<div id='invoice' class='receipt-content'>
    <div class='invoice-header'>
        <img src='logo.png' alt='Company Logo' class='logo'>
        <h2>Invoice</h2>
        <p>Transaction Date: <?= htmlspecialchars($orderdate) ?></p>
    </div>
    <div class='invoice-body'>
        <div class='order-summary'>
            <h3>Order Summary</h3>
            <table>
                <tr>
                    <th>Order ID:</th>
                    <td><?= htmlspecialchars($oid) ?></td>
                </tr>
                <tr>
                    <th>Customer Name:</th>
                    <td><?= htmlspecialchars($customername) ?></td>
                </tr>
                <tr>
                    <th>Order Total:</th>
                    <td>₱ <?= htmlspecialchars($ordertotal) ?></td>
                </tr>
            </table>
        </div>
        <div class='payment-terms'>
            <h3>Payment Terms</h3>
            <p>Due upon receipt.</p>
        </div>
        <div class='salesperson-info'>
            <h3>Salesperson Information</h3>
            <p><?= htmlspecialchars($salesperson) ?></p>
        </div>
    </div>
    <div class='invoice-footer'>
        <p>Payment is due within 30 days.</p>
    </div>
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
    <?php require '../reusable/footer.php'; ?>
</body>

</html>



/******  a4a79da1-1f7d-45d4-841e-e870f4f4f9d1  *******/
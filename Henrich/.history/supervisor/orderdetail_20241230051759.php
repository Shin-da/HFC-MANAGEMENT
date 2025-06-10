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
            .receipt-content {
                display: none;
            }

            .receipt-header, .invoice-header {
                background-color: #333;
                color: #fff;
                padding: 10px;
                border-bottom: 1px solid #ccc;
            }

            .receipt-body, .invoice-body {
                padding: 20px;
            }

            .receipt-item, .invoice-item {
                display: flex;
                justify-content: space-between;
                align-items: center;
                margin-bottom: 10px;
            }

            .receipt-item span:first-child, .invoice-item span:first-child {
                font-weight: 600;
            }

            .receipt-item span:last-child, .invoice-item span:last-child {
                font-size: 16px;
            }

            .invoice-table {
                width: 100%;
                border-collapse: collapse;
            }

            .invoice-table tr td {
                padding: 5px;
                border: 1px solid #ccc;
            }

            .invoice-table tr td:first-child {
                font-weight: 600;
            }

            .invoice-table tr td:last-child {
                text-align: right;
            }

            .invoice-table tr:nth-child(even) {
                background-color: #f2f2f2;
            }

            .invoice-table tr:first-child {
                background-color: #333;
                color: #fff;
            }

            @media print {
                .receipt-content {
                    display: block;
                }
                .order-detail, .btn, .panel, .navbar {
                    display: none;
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
                                        var originalContents = document.body.innerHTML;
                                        document.getElementById('receipt').style.display = 'block';
                                        var printContents = document.getElementById('receipt').innerHTML;
                                        document.body.innerHTML = printContents;
                                        window.print();
                                        document.body.innerHTML = originalContents;
                                        document.getElementById('receipt').style.display = 'none';
                                    }
                                    function printInvoice() {
                                        var originalContents = document.body.innerHTML;
                                        document.getElementById('invoice').style.display = 'block';
                                        var printContents = document.getElementById('invoice').innerHTML;
                                        document.body.innerHTML = printContents;
                                        window.print();
                                        document.body.innerHTML = originalContents;
                                        document.getElementById('invoice').style.display = 'none';
                                    }
                                </script>
                                <div id='receipt' class='receipt-content'>
                                    <div class='receipt-header'>
                                        <h2>Receipt</h2>
                                        <p>Transaction Date: " . htmlspecialchars($orderdate) . "</p>
                                        <p>Receipt Date: " . htmlspecialchars(date('Y-m-d')) . "</p>
                                    </div>
                                    <div class='receipt-body'>
                                        <div class='receipt-item'>
                                            <span>Order ID:</span>
                                            <span>" . htmlspecialchars($oid) . "</span>
                                        </div>
                                        <div class='receipt-item'>
                                            <span>Customer Name:</span>
                                            <span>" . htmlspecialchars($customername) . "</span>
                                        </div>
                                        <p>Payment Method: Cash</p>
                                        <p>Payment Status: Paid</p>
                                        <p>Order Total: ₱ " . htmlspecialchars($ordertotal) . "</p>
                                        <p>Salesperson: " . htmlspecialchars($salesperson) . "</p>
                                        <p>Order Description:</p>
                                        <ul style='list-style: none; padding: 0; margin: 0'>";
                            $orderDescription = explode(", ", $orderdescription ?? '');
                            foreach ($orderDescription as $desc) {
                                echo "<li>" . htmlspecialchars($desc) . "</li>";
                            }
                            echo "</ul>
                                </div>
                                <div id='invoice' class='receipt-content'>
                                    <div class='invoice-header'>
                                        <h2>Invoice</h2>
                                        <p>Invoice ID: " . htmlspecialchars($oid) . "</p>
                                        <p>Invoice Date: " . htmlspecialchars(date('Y-m-d')) . "</p>
                                        <p>Due Date: " . htmlspecialchars(date('Y-m-d', strtotime('+7 days'))) . "</p>
                                    </div>
                                    <div class='invoice-body'>
                                        <p>Bill to:</p>
                                        <ul style='list-style: none; padding: 0; margin: 0'>
                                            <li>Name: " . htmlspecialchars($customername) . "</li>
                                            <li>Address: " . htmlspecialchars($customeraddress) . "</li>
                                            <li>Phone: " . htmlspecialchars($customerphonenumber) . "</li>
                                        </ul>
                                        <p>Order Total: ₱ " . htmlspecialchars($ordertotal) . "</p>
                                        <p>Salesperson: " . htmlspecialchars($salesperson) . "</p>
                                        <table class='invoice-table'>
                                            <tr>
                                                <th>Item</th>
                                                <th>Quantity</th>
                                                <th>Price</th>
                                            </tr>";
                            $orderDescription = explode(", ", $orderdescription ?? '');
                            foreach ($orderDescription as $desc) {
                                echo "<tr>
                                        <td>" . htmlspecialchars($desc) . "</td>
                                        <td>1</td>
                                        <td>₱ " . htmlspecialchars($ordertotal) . "</td>
                                    </tr>";
                            }
                            echo "</table>
                                    </div>
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



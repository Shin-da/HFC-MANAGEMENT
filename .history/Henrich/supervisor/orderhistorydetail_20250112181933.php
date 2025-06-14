<?php
require '../reusable/redirect404.php';
require '../session/session.php';
require '../database/dbconnect.php';
$current_page = basename($_SERVER['PHP_SELF'], '.php');

// Update order status
if (isset($_POST['updateStatus'])) {
    $hid = $_POST['hid'];
    $status = $_POST['status'] ?? '';
    if ($status != '') {
        $sql = "UPDATE orderhistory SET status = '$status', datecompleted = NOW() WHERE hid = '$hid'";
        if ($conn->query($sql) === TRUE) {
            echo "<script>alert('Order status has been updated!');</script>";
            header("Location: orderhistorydetail.php?hid=$hid");
            exit;
        } else {
            echo "Error updating record: " . $conn->error;
        }
    }
}

// Delete order
if (isset($_POST['deleteOrder'])) {
    $hid = $_POST['hid'];
    // Transfer order to archivedorder table
    $sql = "INSERT INTO archivedorder (hid, oid, customername, customeraddress, customerphonenumber, orderdescription, ordertotal, orderdate, timeoforder, salesperson, status, ordertype) SELECT hid, oid, customername, customeraddress, customerphonenumber, orderdescription, ordertotal, orderdate, timeoforder, salesperson, status, ordertype FROM orderhistory WHERE hid = '$hid'";
    if ($conn->query($sql) === TRUE) {
        // Delete order from orderhistory table
        $sql = "DELETE FROM orderhistory WHERE hid = '$hid'";
        if ($conn->query($sql) === TRUE) {
            echo "<script>alert('Order has been deleted and transferred to archived order!');</script>";
            header("Location: orderhistory.php");
            exit;
        } else {
            echo "Error deleting record: " . $conn->error;
        }
    } else {
        echo "Error transferring record: " . $conn->error;
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
            border-bottom: 1px solid #ccc;
            /* background-color: #f5f5f5; */
            border-radius: 5px;
        }

        .order-item span:first-child {
            font-weight: bold;
        }

        .btn {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin-top: 20px;
            flex-wrap: wrap;
        }

        .btn>button {
            margin: 5px;
            padding: 12px 24px;
            border: none;
            border-radius: 5px;
            color: white;
            cursor: pointer;
            transition: background-color 0.3s, transform 0.3s;
        }

        .btn>button:hover {
            transform: scale(1.05);
        }

        .btn-danger {
            background-color: #dc3545;
        }

        .btn-primary {
            background-color: #007bff;
        }

        @media (max-width: 600px) {
            .btn>button {
                width: 100%;
                padding: 14px 0;
            }
        }

        #receipt,
        #invoice {
            display: none;
            width: 100%;
            padding: 20px;
            font-size: 14px;
            border: 1px solid #ccc;
            border-radius: 5px;
            background-color: #fff;
        }

        #receipt h2,
        #invoice h2 {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 10px;
        }

        #receipt p,
        #invoice p {
            margin-bottom: 10px;
        }

        #receipt ul,
        #invoice ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        #receipt li,
        #invoice li {
            margin-bottom: 10px;
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

        @media print {
            .receipt-content {
                display: block;
            }

            .order-detail,
            .btn,
            .panel,
            .navbar {
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
                <a href="javascript:window.history.back()" id="back-toprev" style="font-size: 3rem;">
                    <i class='bx bx-chevron-left'></i>
                </a>
                <h2>Order Detail</h2>
            </div>
            <div class="order-header">
                <?php
                if (isset($_GET['hid'])) {
                    $hid = $_GET['hid'];
                    $sql = "SELECT timeoforder FROM orderhistory WHERE hid = '$hid'";
                    $result = $conn->query($sql);
                    if ($result->num_rows > 0) {
                        $row = $result->fetch_assoc();
                        $timeoforder = $row["timeoforder"];
                        echo "<p>Viewing Order ID # $hid Created at: " . date('g:i A', strtotime($timeoforder)) . "</p>";
                    } else {
                        echo "<p>Viewing Order ID # $hid</p>";
                    }
                }
                ?> 
            
                <span>" . htmlspecialchars($ordertype) . "</span>
         
            </div>
            <div class="order-body">
                <?php
                if (isset($_GET['hid'])) {
                    $hid = $_GET['hid'];
                    $sql = "SELECT * FROM orderhistory WHERE hid = '$hid'";
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
                        $ordertype = $row["ordertype"];

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
                            <span>Items:</span>
                            <span>";
                        $orderDescription = explode("<br>", $orderdescription ?? '');
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
                                <input type='hidden' name='hid' value='$hid'>
                                <select name='status' class='form-control'>
                                    <option value='Pending'>Pending</option>
                                    <option value='Ongoing'>Ongoing</option>
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
                                                <span>" . htmlspecialchars($hid) . "</span>
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
                                            <span>Items:</span>
                                            <div class='receipt-description'>
                                                <ul style='list-style: none; padding: 0; margin: 0'>";
                        $orderDescription = explode(", ", $orderdescription ?? '');
                        foreach ($orderDescription as $desc) {
                            echo "<li>" . htmlspecialchars($desc) . "</li>";
                        }
                        echo "</ul>
                                            </div>
                                        </div>
                                    </div>
                                    <div class='receipt-footer'>
                                        <p>Thank you for your business!</p>
                                    </div>
                                </div>
                            </div>
                            <div id='invoice' class='receipt-content'>
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
                                        <p>Items:</p>
                                        <ul style='list-style: none; padding: 0; margin: 0'>";
                        $orderDescription = explode(", ", $orderdescription ?? '');
                        foreach ($orderDescription as $desc) {
                            echo "<li>" . htmlspecialchars($desc) . "</li>";
                        }
                        echo "</ul>
                                    </div>
                                    <hr>
                                    <div class='invoice-footer'>
                                       <p>Payment is due within 30days</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        ";
                        if (isset($_SESSION['userlevel']) && $_SESSION['userlevel'] == 'admin') {
                            echo "
                            <div class='order-item'>
                                <form action='' method='post'>
                                    <input type='hidden' name='hid' value='$hid'>
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

<!-- <style>
    .receipt-content,
    .invoice-content {
        font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
        font-size: 14px;
        line-height: 1.5;
        color: #333;
        -webkit-font-smoothing: antialiased;
        -moz-osx-font-smoothing: grayscale;
    }

    .receipt-header,
    .invoice-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        background-color: #f0f0f0;
        padding: 10px;
        border-bottom: 1px solid #ccc;
    }

    .receipt-header h2,
    .invoice-header h2 {
        font-size: 24px;
        font-weight: 600;
        margin: 0;
    }

    .receipt-header p,
    .invoice-header p {
        font-size: 12px;
        margin: 0;
    }

    .receipt-body,
    .invoice-body {
        padding-top: 20px;
    }

    .receipt-body table,
    .invoice-body table {
        width: 100%;
        border-collapse: collapse;
    }

    .receipt-body table tr td,
    .invoice-body table tr td {
        padding: 10px;
        border-bottom: 1px solid #ccc;
    }

    .receipt-body table tr:last-child td,
    .invoice-body table tr:last-child td {
        border-bottom: none;
    }

    .receipt-body table tr td:first-child,
    .invoice-body table tr td:first-child {
        font-weight: 600;
    }

    .receipt-footer,
    .invoice-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        background-color: #f0f0f0;
        padding: 10px;
        margin-top: 20px;
    }

    .receipt-logo,
    .invoice-logo {
        width: 100px;
        height: 100px;
        object-fit: cover;
        object-position: center;
    }

    .receipt-info,
    .invoice-info {
        margin-left: 20px;
    }

    .receipt-bill-to,
    .invoice-bill-to {
        margin-top: 20px;
    }

    .receipt-items,
    .invoice-items {
        margin-top: 20px;
    }

    .receipt-salesperson,
    .invoice-salesperson {
        margin-top: 20px;
    }

    .receipt-description,
    .invoice-description {
        margin-top: 20px;
    }

    .receipt-footer-text,
    .invoice-footer-text {
        font-size: 12px;
        margin: 0;
    }
</style> -->

</html>



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
        $sql = "UPDATE customerorder SET status = '$status', datecompleted = NOW() WHERE hid = '$hid'";
        if ($conn->query($sql) === TRUE) {
            echo "<script>alert('Order status has been updated!');</script>";
            header("Location: customerorderdetail.php?hid=$hid");
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
    $sql = "INSERT INTO archivedorder (hid, oid, customername, customeraddress, customerphonenumber, orderdescription, ordertotal, orderdate, timeoforder, salesperson, status, ordertype) SELECT hid, oid, customername, customeraddress, customerphonenumber, orderdescription, ordertotal, orderdate, timeoforder, salesperson, status, ordertype FROM customerorder WHERE hid = '$hid'";
    if ($conn->query($sql) === TRUE) {
        // Delete order from customerorder table
        $sql = "DELETE FROM customerorder WHERE hid = '$hid'";
        if ($conn->query($sql) === TRUE) {
            echo "<script>alert('Order has been deleted and transferred to archived order!');</script>";
            header("Location: customerorder.php");
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
        .order-detail {
            background-color: #fff;
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
            padding: 10px;
            background-color: #fff;
            border-bottom: 1px solid #ccc;
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
            padding: 20px;
        }

        .order-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px;
            border-bottom: 1px solid #ccc;
            cursor: pointer;
        }

        .order-item span:first-child {
            font-weight: bold;
        }

        .order-item:hover {
            background-color: #f0f0f0;
        }

        .btn {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin-top: 20px;
        }

        .btn>button {
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

        .order-footer {
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 10px;
            border-top: 1px solid #ccc;
            border-radius: 0 0 5px 5px;
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
    <script>
        function copyToClipboard(text) {
            navigator.clipboard.writeText(text).then(function() {
                alert('Order ID ' + text + ' copied to clipboard');
            }, function(err) {
                alert('Could not copy text: ', err);
            });
        }
    </script>
</head>

<body>
    <?php include '../reusable/sidebar.php'; ?>
    <div class="panel">
        <?php include '../reusable/navbarNoSearch.html'; ?>

        <div class="order-detail">
            <div class="order-header">
                <?php
                if (isset($_GET['hid'])) {
                    $hid = $_GET['hid'];
                    $sql = "SELECT *  FROM customerorder WHERE hid = '$hid'";
                    $result = $conn->query($sql);
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            $customername = $row['customername'];
                            $customeraddress = $row['customeraddress'];
                            $customerphonenumber = $row['customerphonenumber'];
                            $orderdescription = $row['orderdescription'];
                            $ordertotal = $row['ordertotal'];
                            $orderdate = $row['orderdate'];
                            $timeoforder = $row['timeoforder'];
                            $salesperson = $row['salesperson'];
                            $status = $row['status'];
                            $ordertype = $row['ordertype'];
                            $datecompleted = $row['datecompleted'];
                        }
                    }
                }
                ?>
                <div class="order-header-left">
                    <a href="javascript:window.history.back()" id="back-toprev" style="font-size: 3rem;">
                        <i class='bx bx-chevron-left'></i>
                    </a>
                </div>
                <div class="order-header-right">
                    <h2>Order Detail</h2>
                </div>
            </div>
            <div class="order-header">
                <div class="order-header-left">
                    <div>
                        <span>Order ID:</span>
                        <span><?= isset($hid) ? htmlspecialchars($hid) : ''; ?></span>

                    </div>

                    <span>Order Type:</span>
                    <span><?= isset($ordertype) ? htmlspecialchars($ordertype) : ''; ?></span>
                </div>
                <div class="order-header-right">
                    <div>
                        <span>Order Date:</span>
                        <span><?= isset($orderdate) ? htmlspecialchars($orderdate) : ''; ?></span>
                    </div>
                    <div>
                        <span>Salesperson:</span>
                        <span><?= isset($salesperson) ? htmlspecialchars($salesperson) : ''; ?></span>
                    </div>
                </div>
            </div>
            <div class="order-body">
                <?php
                if (isset($_GET['hid'])) {
                    $hid = $_GET['hid'];
                    $sql = "SELECT * FROM customerorder WHERE hid = '$hid'";
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
                            <span>Status:</span>
                            <span class='$status'>" . htmlspecialchars($status) . "</span>
                            <div class='btn-group' role='group'>
                                <button type='button' class='btn btn-info' name='updateStatus' value='Processing' onclick=\"return confirm('Are you sure you want to update the status of this order to Processing?')\">Process</button>
                                <button type='button' class='btn btn-danger' name='updateStatus' value='Cancelled' onclick=\"return confirm('Are you sure you want to update the status of this order to Cancelled?')\">Cancel</button>
                                <button type='button' class='btn btn-success' name='updateStatus' value='Completed' onclick=\"return confirm('Are you sure you want to update the status of this order to Completed?')\">Complete</button>
                            </div>";
                        echo "
                            
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
                <div class="order-footer">
                    <button type='button' class='btn btn-info' onclick=\"printInvoice\">Print Invoice</button>
                </div>
            </div>
        </div>
    </div>
    <?php require '../reusable/footer.php'; ?>
</body>

</html>
<!DOCTYPE html>
<html>

<head>
    <title>Order Details</title>
    <?php require '../reusable/header.php'; ?>
    <link rel="stylesheet" type="text/css" href="../resources/css/customer-pages.css">
</head>

<body>
    <?php include '../reusable/sidebar.php'; ?>
    <section class="panel">
        <?php include '../reusable/navbarNoSearch.html'; ?>

        <div class="order-detail-card animate-fade-in">
            <div class="order-detail-header">
                <a href="javascript:history.back()" class="btn-action">
                    <i class="bx bx-arrow-back"></i> Back
                </a>
                <h2>Order Details</h2>
                <div class="header-actions">
                    <button class="btn-action" onclick="printInvoice()">
                        <i class="bx bx-printer"></i> Print Invoice
                    </button>
                </div>
            </div>

            <div class="order-detail-body">
                <div class="order-info-grid">
                    <div class="info-group">
                        <h4>Order Information</h4>
                        <p>Order ID: #<?= $hid ?></p>
                        <p>Date: <?= $orderdate ?></p>
                        <p>Time: <?= $timeoforder ?></p>
                        <p>Status: <span class="status-pill status-<?= strtolower($status) ?>"><?= $status ?></span></p>
                    </div>
                    <div class="info-group">
                        <h4>Customer Details</h4>
                        <p>Name: <?= $customername ?></p>
                        <p>Address: <?= $customeraddress ?></p>
                        <p>Phone: <?= $customerphonenumber ?></p>
                    </div>
                    <div class="info-group">
                        <h4>Order Summary</h4>
                        <p>Total Amount: ₱<?= number_format($ordertotal, 2) ?></p>
                        <p>Order Type: <?= $ordertype ?></p>
                        <p>Salesperson: <?= $salesperson ?></p>
                    </div>
                </div>

                <!-- Enhanced Status Updates -->
                <div class="action-group">
                    <?php if ($status !== 'Completed' && $status !== 'Cancelled'): ?>
                        <button class="btn-action status-processing" onclick="updateOrderStatus('Processing')">
                            <i class="bx bx-time"></i> Process Order
                        </button>
                        <button class="btn-action status-completed" onclick="updateOrderStatus('Completed')">
                            <i class="bx bx-check"></i> Complete Order
                        </button>
                        <button class="btn-action status-cancelled" onclick="updateOrderStatus('Cancelled')">
                            <i class="bx bx-x"></i> Cancel Order
                        </button>
                    <?php endif; ?>
                </div>

                <!-- Rest of the existing order details code -->
                /* ...existing order details code... */
            </div>
        </div>
    </section>

    <script>
        function updateOrderStatus(status) {
            Swal.fire({
                title: 'Update Order Status',
                text: `Are you sure you want to mark this order as ${status}?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, update it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.querySelector(`form[action*="updateStatus"][value="${status}"]`).submit();
                }
            });
        }
    </script>
</body>

</html>
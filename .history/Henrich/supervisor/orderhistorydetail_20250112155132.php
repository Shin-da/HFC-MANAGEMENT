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
    $sql = "INSERT INTO archivedorder (hid, oid, customername, customeraddress, customerphonenumber, orderdescription, ordertotal, orderdate, timeoforder, salesperson, status) SELECT hid, oid, customername, customeraddress, customerphonenumber, orderdescription, ordertotal, orderdate, timeoforder, salesperson, status FROM orderhistory WHERE hid = '$hid'";
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
    .order-item {
        padding: 10px;
        border-bottom: 1px solid #ccc;
        border-radius: 5px;
        background-color: #f5f5f5;
    }

    .order-item span:first-child {
        font-weight: bold;
        color: var(--black);
    }

    .order-item span:last-child {
        font-style: italic;
        color: var(--gray);
    }

    .order-item.status-Pending {
        background-color: #ffc107;
    }

    .order-item.status-Ongoing {
        background-color: #007bff;
    }

    .order-item.status-Completed {
        background-color: #28a745;
    }

    .order-item.status-Cancelled {
        background-color: #dc3545;
    }

    .order-item.status-Pending>i {
        color: #ffc107;
    }

    .order-item.status-Ongoing>i {
        color: #007bff;
    }

    .order-item.status-Completed>i {
        color: #28a745;
    }

    .order-item.status-Cancelled>i {
        color: #dc3545;
    }
</style>

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
                echo "<p>Viewing Order ID # $hid Created at: $timeoforder</p>";
            } else {
                echo "<p>Viewing Order ID # $hid</p>";
            }
        }
        ?>
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
                <div class='order-item status-$status'>
                    <span>Status:</span>
                    <span><i class='bx bx-$status'></i> " . htmlspecialchars($status) . "</span>
                    <form action='' method='post'>
                        <input type='hidden' name='hid' value='$hid'>
                        <select name='status' class='form-control'>
                            <option value='Pending'>Pending</option>
                            <option value='Ongoing'>Ongoing</option>
                            <option value='Completed'>Completed</option>
                            <option value='Cancelled'>Cancelled</option>
                        </select>
                        <button type='submit' name='updateStatus' class='btn btn-primary' onclick=\"return confirm('Are you sure you want to update the status of this order?')\">Update Status</button>
                    </form>
                </div>
                ";
                if ($status === 'Completed') {
                    echo "
                    <button type='button' class='btn btn-success' onclick=\"printReceipt()\">Print Receipt</button>
                    ";
                }
                echo "
                    <button type='button' class='btn btn-info' onclick=\"printInvoice()\">Print Invoice</button>
                    <script>
                        function printReceipt() {
                            window.print();
                        }

<style>
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
</style>

</html>



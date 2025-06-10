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
                <div class="order-header">
                    <?php
                    if (isset($_GET['hid'])) {
                        $hid = $_GET['hid'];
                        $sql = "SELECT timeoforder, orderdate, ordertype FROM orderhistory WHERE hid = '$hid'";
                        $result = $conn->query($sql);
                        if ($result->num_rows > 0) {
                            $row = $result->fetch_assoc();
                            $orderdate = $row["orderdate"];
                            $ordertype = $row["ordertype"];
                            echo "<p>Viewing Order ID # $hid Created at: " . date('g:i A', strtotime($row["timeoforder"])) . " on $orderdate ($ordertype)</p>";
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
                            $orderdescription = explode("<br>", $orderdescription ?? '');
                            foreach ($orderdescription as $desc) {
                                echo "- " . htmlspecialchars($desc) . "<br>";
                            }
                            echo "</span>
                        </div>
                        <div class='order-item'>
                            <span>Order Total:</span>
                            <span>₱ " . htmlspecialchars($ordertotal) . "</span>
                        </div>
                        <div class='order-item'>
                            <span>Salesperson:</span>

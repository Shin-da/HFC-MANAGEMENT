<?php
include_once '../database/dbconnect.php';
require '../reusable/header.php';
require '../session/session.php';

if (isset($_GET['hid'])) {
    $hid = $_GET['hid'];
    $stmt = $conn->prepare("SELECT * FROM orderhistory WHERE hid = ?");
    $stmt->bind_param("i", $hid);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $stmt->close();
}

if (!isset($row)) {
    header("Location: orderhistory.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Order History Details</title>
    <link rel="stylesheet" type="text/css" href="../resources/css/styles.css">
    <style>
        
        .container {
            padding: 20px;
            max-width: 800px;
            margin: auto;
        }

        .order-detail {
            background-color: #f9f9f9;
            padding: 15px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .order-item {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #e0e0e0;
        }

        .order-item:last-child {
            border-bottom: none;
        }

        .order-item span {
            flex: 1;
        }

        .order-item span:first-child {
            font-weight: bold;
        }

        @media (max-width: 768px) {
            .order-item {
                flex-direction: column;
                align-items: flex-start;
            }
        }

    </style>
</head>

<body>
    <?php include '../reusable/sidebar.php'; ?>
    <div class="panel">
        <?php include '../reusable/navbarNoSearch.html'; ?>
        <div class="container">
            <div class="content">
                <div class="order-detail">
                    <a href="javascript:window.history.back()" id="back-toprev" style="font-size: 3rem;">
                        <i class='bx bx-chevron-left'></i>
                    </a>
                    <h1>Order History #<?= htmlspecialchars($row['hid']); ?></h1>
                    <div class="order-item">
                        <span>Date Completed:</span>
                        <span><?= htmlspecialchars($row['datecompleted']); ?></span>
                    </div>
                    <div class="order-item">
                        <span>Order ID:</span>
                        <span><?= htmlspecialchars($row['oid']); ?></span>
                    </div>
                    <div class="order-item">
                        <span>Customer Name:</span>
                        <span><?= htmlspecialchars($row['customername']); ?></span>
                    </div>
                    <div class="order-item">
                        <span>Customer Address:</span>
                        <span><?= htmlspecialchars($row['customeraddress']); ?></span>
                    </div>
                    <div class="order-item">
                        <span>Customer Phone Number:</span>
                        <span><?= htmlspecialchars($row['customerphonenumber']); ?></span>
                    </div>
                    <div class="order-item">
                        <span>Order Description:</span>
                        <span><?= htmlspecialchars($row['orderdescription']); ?></span>
                    </div>
                    <div class="order-item">
                        <span>Order Total:</span>
                        <span>₱ <?= htmlspecialchars($row['ordertotal']); ?></span>
                    </div>
                    <div class="order-item">
                        <span>Order Date:</span>
                        <span><?= htmlspecialchars($row['orderdate']); ?></span>
                    </div>
                    <div class="order-item">
                        <span>Salesperson:</span>
                        <span><?= htmlspecialchars($row['salesperson']); ?></span>
                    </div>
                    <div class="order-item">
                        <span>Status:</span>
                        <span><?= htmlspecialchars($row['status']); ?></span>
                    </div>
                    <div class="order-item">
                        <span>Customer ID:</span>
                        <span><?= htmlspecialchars($row['customerid']); ?></span>
                    </div>
                </div>
            </div>
        </div>
        <?php require '../reusable/footer.php'; ?>
    </div>
</body>

</html>


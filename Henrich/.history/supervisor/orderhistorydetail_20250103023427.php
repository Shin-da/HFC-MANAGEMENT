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
    <title>Order History Details</title>
    <link rel="stylesheet" type="text/css" href="../resources/css/styles.css">
    <style>
        .order-detail {
            background-color: #f5f5f5;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1), 0 8px 16px rgba(0, 0, 0, 0.05);
        }

        .order-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px;
            border-bottom: 1px solid #ccc;
        }

        .order-item span:first-child {
            font-weight: bold;
        }
    </style>
</head>

<body>
    <?php   include '../reusable/sidebar.php';  // Sidebar       ?>
    <div class="panel">
        <?php include '../reusable/navbarNoSearch.html';  // Navbar       ?>
        <div class="container">
            <div class="content">
                <div class="order-detail">
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
    </div>
    <?php require '../reusable/footer.php'; ?>
</div>
</body>

</html>


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
            margin-top: 20px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
            padding: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
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
    </style>
</head>

<body>
    <?php include '../reusable/sidebar.php'; // Sidebar       ?>
    <div class="panel">
        <?php include '../reusable/navbarNoSearch.html'; // Navbar       ?>
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
                        <span>
                            <?php if ($row['status'] == 'Pending') { ?>
                                <span class="Pending"><?= htmlspecialchars($row['status']); ?></span>
                            <?php } else if ($row['status'] == 'Completed') { ?>
                                <span class="Completed"><?= htmlspecialchars($row['status']); ?></span>
                            <?php } else { ?>
                                <span class="Cancelled"><?= htmlspecialchars($row['status']); ?></span>
                            <?php } ?>
                        </span>
                    </div>
                    <div class="order-item">
                        <span>Customer ID:</span>
                        <span><?= htmlspecialchars($row['customerid']); ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php require '../reusable/footer.php'; // Footer       ?>
</div>
</body>

</html>



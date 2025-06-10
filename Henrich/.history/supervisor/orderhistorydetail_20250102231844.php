/*************  ✨ Codeium Command 🌟  *************/
<?php
include_once '../database/dbconnect.php';
require '../reusable/header.php';
require '../reusable/sidebar.php';

if (isset($_GET['hid'])) {
if(isset($_GET['hid'])) {
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
<html>
<head>
    <title>Order History Details</title>
</head>
<body>
    <h1>Order History #<?php echo $row['hid']; ?></h1>
    <p>Date Completed: <?php echo $row['datecompleted']; ?></p>
    <p>Order ID: <?php echo $row['oid']; ?></p>
    <p>Customer Name: <?php echo $row['customername']; ?></p>
    <p>Customer Address: <?php echo $row['customeraddress']; ?></p>
    <p>Customer Phone Number: <?php echo $row['customerphonenumber']; ?></p>
    <p>Order Description: <?php echo $row['orderdescription']; ?></p>
    <p>Order Total: <?php echo $row['ordertotal']; ?></p>
    <p>Order Date: <?php echo $row['orderdate']; ?></p>
    <p>Salesperson: <?php echo $row['salesperson']; ?></p>
    <p>Status: <?php echo $row['status']; ?></p>
    <p>Customer ID: <?php echo $row['customerid']; ?></p>
</body>
</html>

<head>
    <title>Order History Details</title>
    <link rel="stylesheet" type="text/css" href="../resources/css/styles.css">
</head>

<body>
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
    <?php require '../reusable/footer.php'; ?>
</body>

</html>

/******  088a6930-19c1-41e9-ae09-66c516c96582  *******/
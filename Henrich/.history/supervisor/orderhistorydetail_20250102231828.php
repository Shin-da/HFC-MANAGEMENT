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


/******  088a6930-19c1-41e9-ae09-66c516c96582  *******/
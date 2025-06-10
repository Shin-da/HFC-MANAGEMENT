<?php
require '../reusable/redirect404.php';
require '../session/session.php';
require '../database/dbconnect.php';
$current_page = basename($_SERVER['PHP_SELF'], '.php');

function getStatusClass($status) {
    switch ($status) {
        case 'Pending':
            return 'Pending';
        case 'Completed':
            return 'Completed';
        case 'Cancelled':
            return 'Cancelled';
        default:
            return '';
    }
}
?>
<!DOCTYPE html>
<html>

<head>
    <title>Order Detail</title>
    <?php require '../reusable/header.php'; ?>
</head>

<body>
    <?php include '../reusable/sidebar.php';   // Sidebar    ?>
    <div class="main-content">
        <div class="page-header">
            <h2 class="header-title">Order Detail</h2>
            <div class="header-subtitle">View order details</div>
        </div>
        <div class="panel">
            <div class="panel-body">
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
                        
                        if(isset($_POST['updateStatus'])) {
                            $newStatus = $_POST['status'];
                            $updateSql = "UPDATE orders SET status = '$newStatus' WHERE oid = '$oid'";
                            $conn->query($updateSql);
                            header("Location: orderdetail.php?oid=$oid");
                            exit();
                        }

                        echo "
                        <div class='detail-container'>
                            <div class='detail-header'>
                                <h3>Order # $oid</h3>
                            </div>
                            <div class='detail-body'>
                                <div class='detail-item'>
                                    <span>Customer Name:</span>
                                    <span>$customername</span>
                                </div>
                                <div class='detail-item'>
                                    <span>Customer Address:</span>
                                    <span>$customeraddress</span>
                                </div>
                                <div class='detail-item'>
                                    <span>Customer Phone Number:</span>
                                    <span>$customerphonenumber</span>
                                </div>
                                <div class='detail-item'>
                                    <span>Order Description:</span>
                                    <span>";
                                    $orderDescription = explode(", ", $orderdescription ?? '');
                                    foreach ($orderDescription as $desc) {
                                        echo "- " . $desc . "<br>";
                                    }
                                    echo "</span>
                                </div>
                                <div class='detail-item'>
                                    <span>Order Total:</span>
                                    <span>₱ $ordertotal</span>
                                </div>
                                <div class='detail-item'>
                                    <span>Order Date:</span>
                                    <span>$orderdate</span>
                                </div>
                                <div class='detail-item'>
                                    <span>Salesperson:</span>
                                    <span>$salesperson</span>
                                </div>
                                <div class='detail-item'>
                                    <span>Status:</span>
                                    <span class='" . getStatusClass($status) . "'>";
                                    echo "<form method='POST'>";
                                    echo "<select name='status' onchange='this.form.submit()' style='margin-left: 10px'>";
                                    echo "<option value='Pending' " . ($status == 'Pending' ? 'selected' : '') . ">Pending</option>";
                                    echo "<option value='Completed' " . ($status == 'Completed' ? 'selected' : '') . ">Completed</option>";
                                    echo "<option value='Cancelled' " . ($status == 'Cancelled' ? 'selected' : '') . ">Cancelled</option>";
                                    echo "</select>";
                                    echo "</form>";
                                    echo "</span>
                                </div>
                            </div>
                        </div>
                        ";
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

</html>




/*************  ✨ Codeium Command 🌟  *************/
<?php
require '../reusable/redirect404.php';
require '../session/session.php';
require '../database/dbconnect.php';
$current_page = basename($_SERVER['PHP_SELF'], '.php');
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
                                    <span class='$status'>$status</span>
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


/******  7c99a26c-2778-490d-8501-5f2a3e4edb33  *******/
<?php
require '../reusable/redirect404.php';
require '../session/session.php';
require '../database/dbconnect.php';
?>
<!DOCTYPE html>
<html>

<head>
    <title>View Order</title>
    <?php require '../reusable/header.php'; ?>
</head>

<body>
    <?php  include '../reusable/sidebar.php';

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $OrderID = $_GET['OrderID'];
    $sql = "SELECT * FROM orderdetails WHERE OrderID = '$OrderID'";
    $result = $conn->query($sql);
    ?>

    <!-- ===  === -->
    <section class=" panel">
        <?php include '../reusable/navbarNoSearch.html';// TOP NAVBAR ?>

        <div class="overview container ">
            <div class="title">
                <a href="orders.php">
                    <i class='bx bx-arrow-back'></i>
                </a>

                <span class="text">Order #
                    <a href="view_order.php?OrderID=<?php echo $OrderID; ?>"> <?php echo $OrderID; ?></a></span>
            </div>
        </div>

        <div class="panel-content">
            <div class="blank-content">

                <!-- Order Details -->
                <div class="order-details">

                    <div class="customer-info">
                        <div class="name">
                            <span class="text">Name:</span>
                            <span class="value">---</span>
                        </div>

                        <div class="address">
                            <span class="text">Address:</span>
                            <span class="value">---</span>
                        </div>
                    </div>

                    <div class="product-info">
                        <div class="product-code">
                            <span class="text">Product Code:</span>
                            <span class="value">---</span>
                        </div>

                        <div class="product-name">
                            <span class="text">Product Name:</span>
                            <span class="value">---</span>
                        </div>

                        <div class="price">
                            <span class="text">Price:</span>
                            <span class="value">---</span>
                        </div>
                    </div>

                    <div class="datetime">
                        <span class="text">Datetime:</span>
                        <span class="value">---</span>
                    </div>

                </div>

            </div>
        </div>

    </section>

</body>
<?php include '../reusable/footer.php'; ?>
</html>
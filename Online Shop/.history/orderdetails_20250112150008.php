<?php
require "./database/dbconnect.php";
require "header.php";
require "./session/session.php";

if (isset($_SESSION['accountid'])) {
    $accountId = $_SESSION['accountid'];
    $query = "SELECT customerid FROM customeraccount WHERE accountid = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $accountId);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $customerId = $row['customerid'];
    $orderId = $_GET['orderid'] ?? null;

    if ($orderId) {
        $query = "SELECT * FROM orders WHERE oid = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $orderId);
        $stmt->execute();
        $result = $stmt->get_result();
        $order = $result->fetch_assoc();

        if ($order) {
            $orderDescription = explode(", ", $order['orderdescription']);
            $customerName = $order['customername'];
            $customerAddress = $order['customeraddress'];
            $customerPhone = $order['customerphonenumber'];
            $products = array();
            foreach ($orderDescription as $item) {
                $productDetails = explode(" ", $item, 4);
                $productName = $productDetails[0];
                $productQuantity = isset($productDetails[3]) ? $productDetails[3] : '0';
                $query = "SELECT * FROM productlist WHERE productname LIKE ?";
                $stmt = $conn->prepare($query);
                $productNameLike = $productName . '%';
                $stmt->bind_param("s", $productNameLike);
                $stmt->execute();
                $result = $stmt->get_result();
                $product = $result->fetch_assoc();
                $product['quantity'] = $productQuantity;
                $products[] = $product;
            }
        } else {
            echo "<p style='text-align: center; color: red;'>Order not found</p>";
        }
    } else {
        echo "<p style='text-align: center; color: red;'>Invalid order ID</p>";
    }
} else {
    header("Location: login.php");
    exit;
}
?>
<style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        font-family: 'Poppins', sans-serif;
    }

    body {
        min-height: 100vh;
        background-color: #f4f4f9;
        color: #333;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
    }

    .panel {
        height:calc(100vh - 70px);
        width: 100%;
        padding: 20px;
        background-color: #fff;
        border-radius: 10px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .container {
        overflow-y: scroll;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        height: auto;
        width: 100%;
        padding: 20px;
    }

    .content {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        width: 100%;
        padding: 10px;
    }

    .title {
        display: flex;
        justify-content: center;
        align-items: center;
        margin-bottom: 20px;
        width: 100%;
    }

    .title h1 {
        font-size: 1.5rem;
        display: flex;
        align-items: center;
        margin-left: 10px;
    }

    .order-details {
        width: 100%;
        max-width: 800px;
        padding: 20px;
        background-color: #fff;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        position: relative;
        margin-bottom: 20px;
    }

    .order-id {
        font-weight: bold;
        font-size: 1.1rem;
        color: #333;
    }

    h2 {
        color: #333;
        margin-bottom: 10px;
    }

    ul {
        padding-left: 20px;
        list-style-type: none;
    }

    ul li {
        margin: 5px 0;
    }

    .order-description {
        margin: 20px 0;
        max-height: 250px;
        overflow-y: auto;
    }

    .customer-details {
        margin-bottom: 20px;
        width: 100%;
        border-bottom: 1px solid #ddd;
        padding-bottom: 10px;
    }

    .product {
        display: flex;
        align-items: center;
        margin: 10px 0;
    }

    .product img {
        width: 75px;
        height: 75px;
        object-fit: cover;
        border-radius: 50%;
        margin-right: 10px;
    }

    .order-total {
        position: absolute;
        right: 20px;
        bottom: 20px;
        font-weight: bold;
        font-size: 1.1rem;
        color: orange;
    }

    .order-date {
        position: absolute;
        right: 20px;
        top: 20px;
        font-size: 1rem;
        color: #333;
    }

    @media (max-width: 768px) {
        .order-details {
            padding: 10px;
        }

        .order-id,
        .order-total,
        .order-date {
            font-size: 1rem;
        }

        .product img {
            width: 50px;
            height: 50px;
        }
    }

    @media (max-width: 425px) {
        .order-details {
            width: 100%;
            padding: 10px;
        }

        .order-id,
        .order-total,
        .order-date {
            font-size: 0.8rem;
        }

        .product img {
            width: 25px;
            height: 25px;
        }
    }
</style>

<body>
    <?php include "navbar.php"; ?>
    <div class="panel">
        
        <div class="container">
            <div class="content">
                <div class="title">
                    <a href="javascript:window.history.back()" class="back" style="margin-right: 10px;">
                        <i class="bx bx-arrow-back" style="display: inline-block;"></i>
                    </a>
                    <h1>Order Details</h1>
                </div>
                <?php if (!empty($order)): ?>
                    <div class="order-details">
                        <div class="order-id">Order #<?php echo htmlspecialchars($order['oid']); ?>
                            <p style="color: <?php if ($order['status'] == 'Pending') {
                                                    echo 'orange';
                                                } elseif ($order['status'] == 'Completed') {
                                                    echo 'green';
                                                } elseif ($order['status'] == 'Ongoing') {
                                                    echo 'blue';
                                                } else {
                                                    echo 'red';
                                                } ?>"><?php echo htmlspecialchars($order['status']); ?></p>
                        </div>

                        <div class="order-date"><?php echo date('d-m-Y', strtotime(htmlspecialchars($order['orderdate']))); ?></div>

                        <div class="customer-details">
                            <p> Name: <?php echo htmlspecialchars($customerName); ?></p>
                            <p> Address: <?php echo htmlspecialchars($customerAddress); ?></p>
                            <p> Phone: <?php echo htmlspecialchars($customerPhone); ?></p>
                        </div>

                        <div class="order-description" style="max-height: 450px; overflow-y: auto;">
                            <p>Items:</p>
                            <ul>
                                <?php foreach ($products as $product) { ?>
                                    <li class="product">
                                        <img class="product-image" src="<?php echo htmlspecialchars(!empty($product['productimage']) ? $product['productimage'] : 'placeholder-image.png'); ?>" alt="" onerror="this.src='placeholder-image.png'">
                                        <span><?php echo htmlspecialchars($product['productname']); ?> - <?php echo htmlspecialchars($product['quantity']); ?></span>
                                    </li>
                                <?php } ?>
                            </ul>
                        </div>

                        <div class="order-total">Order Total: <span style="color: orange; font-weight: bold;">&#8369;<?php echo htmlspecialchars($order['ordertotal']); ?></span></div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <script src="app.js"></script>
    <script src="script.js"></script>
</body>


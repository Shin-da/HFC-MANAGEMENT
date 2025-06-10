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
    $customerId = $row['customerid'] ?? null;

    $query = "SELECT o.oid, o.customerid, o.orderdescription, o.ordertotal, o.orderdate, o.status 
              FROM orders o 
              JOIN customeraccount c ON o.customerid = c.customerid 
              WHERE c.accountid = ? 
              ORDER BY o.orderdate DESC";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $accountId);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    header("Location: login.php");
    exit;
}
?>

<style>
    * {
        padding: 0;
        margin: 0;
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
        height: calc(100vh - 70px);
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

    .order-history {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        width: 100%;
        padding: 20px;
    }

    .search-bar {
        display: flex;
        align-items: center;
        margin-bottom: 20px;
    }

    .search-bar input {
        padding: 10px;
        border-radius: 5px;
        border: 1px solid #ccc;
        width: 300px;
    }

    .search-bar button {
        padding: 10px 20px;
        margin-left: 10px;
        border-radius: 5px;
        border: none;
        cursor: pointer;
        background-color: #007BFF;
        color: #fff;
        transition: background-color 0.2s;
    }

    .search-bar button:hover {
        background-color: #0056b3;
    }

    .filter-tabs {
        display: flex;
        justify-content: center;
        margin-bottom: 20px;
    }

    .filter-tabs button {
        color: #fff;
        border: none;
        padding: 10px 20px;
        margin-right: 10px;
        border-radius: 5px;
        cursor: pointer;
        transition: background-color 0.2s;
    }

    .filter-tabs button:hover {
        background-color: #555;
    }

    .order-list {
        height: 60vh;
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
    }

    .order-item {
        position: relative;
        flex-basis: 47%;
        margin: 10px;
        padding: 15px;
        background-color: #fff;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        cursor: pointer;
        transition: transform 0.2s;
        border-color: #ff9900;
        min-width: 600px;
        max-width: 900px;
        border: 1px solid #ccc;
        border-bottom: 2px solid #555;
        border-top: 2px solid #555;
    }

    .order-item.Pending {
        border-color: #ff9900;
    }

    .order-item.Completed {
        border-color: #2ecc71;
    }

    .order-item.Cancelled {
        border-color: #ff5151;
    }

    .order-item .order-date {
        position: absolute;
        top: 10px;
        right: 10px;
        color: #777;
        font-size: 14px;
    }

    .order-item:hover {
        transform: scale(1.02);
        border: 1px solid #007bff;
    }

    .order-item h3 {
        margin: 0 0 10px;
        color: #007BFF;
    }

    .order-item p {
        margin: 5px 0;
        color: #555;
    }

    .order-item p span {
        color: #ffc107;
    }

    .order-item .order-total {
        position: relative;
        font-size: 18px;
        text-align: right;
        font-weight: bold;
        margin-top: 10px;
        color: #333;
    }

    .order-item .product img {
        width: auto;
        height: 10vh;
        object-fit: cover;
        border-radius: 10px 10px 0 0;
    }

    .order-description {
        margin-top: 20px;
        padding: 20px;
        background-color: #f9f9f9;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        position: relative;
        max-height: 300px;
        overflow-y: auto;
    }

    .order-item li.product {
        display: flex;
        align-items: center;
        margin-bottom: 10px;
    }

    @media (max-width: 768px) {
        .order-item {
            flex-basis: 100%;
        }
    }
</style>
<body>
    <?php include "navbar.php"; ?>
    <div class="panel">
        <div class="container">
            <div class="order-history">
                <h1 style="text-align: center; color: #333;">Order History</h1>
                <div class="search-bar">
                    <input type="text" id="search-input" placeholder="Search orders...">
                    <button onclick="searchOrders()">Search</button>
                </div>
                <div class="filter-tabs">
                    <button class="active" onclick="filterOrders(event, 'all')">All</button>
                    <button class="Pending" onclick="filterOrders(event, 'Pending')">Pending</button>
                    <button class="Completed" onclick="filterOrders(event, 'Completed')">Completed</button>
                    <button class="Cancelled" onclick="filterOrders(event, 'Cancelled')">Cancelled</button>
                </div>
                <div class="order-list" id="order-list">
                    <?php
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            $orderId = $row['oid'] ?? null;
                            $orderDesc = $row['orderdescription'] ?? null;
                            $orderTotal = $row['ordertotal'] ?? null;
                            $orderDate = date('d-m-Y', strtotime($row['orderdate'] ?? ''));
                            $orderStatus = $row['status'] ?? null;
                            $orderDescription = explode(", ", $orderDesc ?? '');
                            $products = array();
                            foreach ($orderDescription as $item) {
                                $productDetails = explode(" ", $item, 5);
                                $productName = $productDetails[0];
                                $productQuantity = isset($productDetails[3]) ? $productDetails[3] : '0';
                                $productWeight = isset($productDetails[4]) ? $productDetails[4] : '0';
                                $query = "SELECT * FROM productlist WHERE productname LIKE ?";
                                $stmt = $conn->prepare($query);
                                $productNameLike = $productName . '%'; 
                                $stmt->bind_param("s", $productNameLike);
                                $stmt->execute();
                                $productResult = $stmt->get_result();
                                $product = $productResult->fetch_assoc();
                                $product['quantity'] = $productQuantity;
                                $product['weight'] = $productWeight;
                                $products[] = $product;
                            }
                            echo "<div class='order-item {$orderStatus}' data-status='{$orderStatus}' data-id='{$orderId}' onclick=\"window.location.href='orderdetails.php?orderid={$orderId}'\" style='border-color: ";
                            echo match ($orderStatus) {
                                'Pending' => '#ffc107',
                                'Completed' => '#2ecc71',
                                'Cancelled' => '#ff5151',
                            };
                            echo ";'>";
                            echo "<h3>Order #{$orderId}</h3>";
                            echo "<h3 style='color: ";
                            echo match ($orderStatus) {
                                'Pending' => '#ffc107',
                                'Completed' => '#2ecc71',
                                'Cancelled' => '#e74c3c',
                            };
                            echo "'>" . htmlspecialchars($orderStatus) . "</h3>";
                            echo "<p class='order-date'>Date of Order: " . htmlspecialchars($orderDate) . "</p>";
                            echo "<div class='order-description'> <p>Description:</p><ul>";
                            foreach ($products as $product) {
                    ?>
                                <li class="product">
                                    <img class="product-image" src="./resources/images/<?php echo htmlspecialchars(!empty($product['productimage']) ? $product['productimage'] : 'placeholder-image.png'); ?>" alt="" onerror="this.src='placeholder-image.png'">
                                    <span><?php echo htmlspecialchars($product['productname']); ?> - <?php echo htmlspecialchars($product['quantity']); ?></span>
                                </li>
                    <?php
                            }
                            echo "</ul></div>";
                            echo "<div class='order-total'>Total: " . htmlspecialchars($orderTotal) . "</div>";
                            echo " </div>";
                        }
                    } else {
                        echo "<p style='text-align: center; color: #777;'>No orders found</p>";
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Cart tab -->
    <?php include "cart.php"; ?>

    
    <script>
        function filterOrders(event, status) {
            if (event) {
                event.preventDefault();
            }
            const orders = document.querySelectorAll('.order-item');
            let hasResults = false;
            orders.forEach(order => {
                const orderStatus = order.getAttribute('data-status');
                if (status === 'all' || orderStatus === status) {
                    order.style.display = 'block';
                    hasResults = true;
                } else {
                    order.style.display = 'none';
                }
            });
            displayNoResultsMessage(hasResults);
        }

        function searchOrders() {
            const searchInput = document.getElementById('search-input').value.toLowerCase();
            const orders = document.querySelectorAll('.order-item');
            let hasResults = false;
            orders.forEach(order => {
                const orderDesc = order.querySelector('.order-description').textContent.toLowerCase();
                const orderId = order.querySelector('h3').textContent.toLowerCase();
                const orderDate = order.querySelector('.order-date').textContent.toLowerCase();
                const orderStatus = order.querySelector('h3:nth-of-type(2)').textContent.toLowerCase();
                if (orderDesc.includes(searchInput) || orderId.includes(searchInput) || orderDate.includes(searchInput) || orderStatus.includes(searchInput)) {
                    order.style.display = 'block';
                    hasResults = true;
                } else {
                    order.style.display = 'none';
                }
            });
            displayNoResultsMessage(hasResults);
        }

        function displayNoResultsMessage(hasResults) {
            let noResults = document.getElementById('no-results');
            if (!noResults) {
                noResults = document.createElement('p');
                noResults.id = 'no-results';
                noResults.textContent = 'No orders found';
                noResults.style.textAlign = 'center';
                noResults.style.color = '#777';
                document.querySelector('.order-list').appendChild(noResults);
            }
            noResults.style.display = hasResults ? 'none' : 'block';
        }

        filterOrders(null, 'all');
    </script>
    <script src="transferOrdersToHistory.js"></script>
    <script src="app.js"></script>
    <script src="script.js"></script>
</body>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="app.js"></script>
</html>





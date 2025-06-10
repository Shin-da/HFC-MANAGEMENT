<?php
include "header.php";
include "./database/dbconnect.php";
include "./session/session.php";

if (isset($_SESSION['accountid'])) {
    $accountId = $_SESSION['accountid'];
    $query = "SELECT * FROM customeraccount WHERE accountid = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $accountId);
    $stmt->execute();
    $result = $stmt->get_result();
    $customerAccount = $result->fetch_assoc();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Shopping Cart">
    <style>
        /* ======== GLOBAL ======== */
        :root {
            /* ===== Colors ===== */
            --body-color: #E4E9F7;
            --border-color: #d7d7d7;
            --panel-color: #E7E6E1;
            --container-color: #fff;
            --text-color-white: #faf9f5;
            --text-color: #3a3a3a;
            --toggle-color: #e9ecef;

            --grey-active: #717171;
            --grey-inactive: #3a3b3c;
            --grey-hover-color: #a1a1a1;

            --accent-color: #933d24;
            --accent-color-dark: #6d1c27;

            --white: #fff;
            --orange-color: #FFAD60;
            --yellow-color: #FFEEAD;
            --blue-color: #96CEB4;
            --green-color: #385a41;
            --blue-color-dark: #2D5B6B;
            --vandyke-color: #362C28;

            --black: #313638;

            /* ====== Transition ====== */
            --tran-03: all 0.2s ease;
            --tran-04: all 0.3s ease;
            --tran-05: all 0.3s ease;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Lato', sans-serif;
            background-color: #f9f9f9;
        }

        /* panel */
        .panel {
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 20px;
            margin-top: 20px;
            position: relative;
        }

        .panel .top-container {
            gap: 20px;
            width: 100%;
            height: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            margin-bottom: 20px;
            padding: 20px;
            background-color: #fff;
            border-bottom: 1px solid #ddd;
            border-radius: 5px;
        }

        .panel .top-container .search-bar {
            display: flex;
            align-items: center;
            width: 60vw;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            background-color: #fff;
            margin-bottom: 10px;
            position: sticky;
            top: 10px;
            z-index: 1;
        }

        .panel .top-container .search-bar input {
            flex: 1;
            padding: 10px;
            border: none;
            outline: none;
        }

        .panel .top-container .search-bar button {
            padding: 10px;
            border: none;
            background-color: var(--accent-color);
            color: #fff;
            cursor: pointer;
        }

        .panel .top-container .search-bar button:hover {
            background-color: var(--accent-color-dark);
        }

        /* categories */
        .categories {
            background-color: #df5c36;
            border: 1px solid var(--accent-color);
            color: #fff;
            margin-top: 20px;
            position: sticky;
            padding: 20px;
            border-radius: 5px;
            top: 50px;
            width: 100%;
            max-height: 300px;
            overflow-y: auto;
        }

        .categories h2 {
            margin-bottom: 10px;
        }

        .categories ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .categories li {
            margin-bottom: 10px;
            cursor: pointer;
        }

        .categories a {
            text-decoration: none;
            color: #333;
        }

        .categories a:hover {
            color: #4CAF50;
        }

        /* products */
        .product-list {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            grid-gap: 10px;
            margin-top: 20px;
        }

        .product-list .product {
            background-color: #fff;
            padding: 10px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 300px;
        }

        .product-list .product img {
            width: 100%;
            height: 200px;
            object-fit: cover;
            border-radius: 5px 5px 0 0;
        }

        .product-list .product h3 {
            margin: 0;
            font-size: 1.2rem;
            color: #333;
        }

        .product-list .product p {
            margin: 0;
            font-size: 1rem;
            color: #666;
        }

        .product-list .product button {
            padding: 10px;
            font-size: 16px;
            border: none;
            background-color: #df5c36;
            color: #fff;
            cursor: pointer;
            border-radius: 5px;
        }

        .product-list .product button:hover {
            background-color: #c0392b;
        }

        /* cart */
        .cartTab {
            z-index: 2;
            position: fixed;
            bottom: 0;
            right: 0;
            height: calc(100vh - 80px);
            width: 100%;
            max-width: 500px;
            background-color: #fff;
            border-left: 1px solid #ddd;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            display: flex;
            flex-direction: column;
        }

        .cartTab .close {
            position: absolute;
            top: 10px;
            right: 10px;
            cursor: pointer;
            margin-top: 10px;
            background-color: transparent;
            border: none;
        }

        .cartTab .close i {
            font-size: 24px;
            color: var(--accent-color);
        }

        .cartTab .close:hover i {
            background-color: var(--accent-color);
            color: var(--white);
            border-radius: 100%;
        }

        .cartTab h1 {
            margin-bottom: 20px;
        }

        .listCart {
            max-height: 60vh;
            overflow-y: auto;
            margin-bottom: 20px;
        }

        .listCart::-webkit-scrollbar {
            width: 10px;
            background-color: #fff;
        }

        .listCart::-webkit-scrollbar-thumb {
            background-color: var(--accent-color);
            border-radius: 10px;
        }

        .cartTab .listCart .item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px;
            border-bottom: 1px solid #ddd;
            transition: all 0.5s ease;
        }

        .cartTab .listCart .item:last-child {
            border-bottom: none;
        }

        .cartTab .listCart .item:hover {
            background-color: #f9f9f9;
        }

        .cartTab .listCart .item img {
            width: 100%;
            max-width: 100px;
            border-radius: 5px;
        }

        .cartTab .listCart .item .quantity {
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .cartTab .listCart .item .quantity button {
            padding: 5px;
            background-color: transparent;
            border: none;
            cursor: pointer;
        }

        .cartTab .listCart .item .quantity button:hover {
            background-color: #4CAF50;
            color: #fff;
        }

        .cartTab .listCart .item .quantity span {
            display: inline-block;
            width: 25px;
            height: 25px;
            background-color: var(--white);
            color: var(--text-color);
            border-radius: 5px;
            margin: 0 5px;
            font-size: 16px;
            text-align: center;
            line-height: 25px;
        }

        .cartTab .listCart .item .remove {
            padding: 10px;
            font-size: 16px;
            border: none;
            background-color: var(--danger-color);
            color: #fff;
            cursor: pointer;
            border-radius: 5px;
        }

        .cartTab .listCart .item .remove:hover {
            background-color: var(--white);
        }

        .cartTab .cart-footer {
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            background-color: #fff;
            padding: 10px;
            text-align: center;
            padding: 20px;
            border-top: 1px solid #ddd;
        }

        @media only screen and (min-width: 880px) {
            .categories {
                position: fixed;
                top: 30%;
                left: 10px;
                width: 20%;
            }

            .product-list {
                grid-template-columns: repeat(3, minmax(300px, 1fr));
            }

            .product {
                max-width: 300px;
            }

            .product-list .product img {
                height: 300px;
            }

        }

        @media only screen and (max-width: 880px) {
            .panel {
                flex-direction: column;
            }

            .panel .top-container .search-bar { 
                width: 100%;
                margin: 0;
            }

            .categories {
                margin-top: 0;
                min-height: 200px;
            }

            .product-list {
                margin-top: 0;
                display: flex;
                flex-wrap: wrap;
                justify-content: space-between;
            }

            .product {
                flex: 1 0 40%;
                margin: 10px;
            }
        }

        /* Back to Top Button */
        #back-to-top {
            display: none;
            position: fixed;
            bottom: 20px;
            right: 20px;
            background-color: #4CAF50;
            color: #fff;
            border: none;
            border-radius: 50%;
            width: 50px;
            height: 50px;
            font-size: 24px;
            cursor: pointer;
            z-index: 999999999;
            transition: background-color 0.3s;
        }

        #back-to-top:hover {
            background-color: #45a049;
        }

        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.4);
        }

        .modal-content {
            background-color: #fefefe;
            margin: 15% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-bottom: 10px;
            border-bottom: 1px solid #ccc;
        }

        .modal-body {
            padding: 20px;
        }

        .customer-details {
            margin-top: 20px;
        }

        .customer-info {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
        }

        .customer-info .detail {
            display: flex;
            flex-direction: column;
            margin-bottom: 10px;
        }

        .customer-info .detail span:first-child {
            font-weight: bold;
        }

        #edit-info {
            margin-top: 10px;
        }

        .order-details,
        .payment-method {
            margin-top: 20px;
        }

        .order-details>div,
        .payment-method>div {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
        }

        .order-details>div>span:first-child,
        .payment-method>div>span:first-child {
            font-weight: bold;
        }

        .modal-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-top: 10px;
            border-top: 1px solid #ccc;
        }
    </style>

</head>

<body>
    <?php include "navbar.php"; ?>

    <button id="back-to-top" onclick="scrollToTop()"><i class="fas fa-arrow-up"></i></button>
    <div class="error-message">
        <div class="panel">
            <div class="container top-container">
                <div class="search-bar">
                    <input type="text" id="search-input" placeholder="Search products...">
                    <button onclick="searchProducts()">Search</button>
                </div>

                <div class="categories">
                    <h2>Categories</h2>
                    <ul>
                        <?php
                        $sql = "SELECT DISTINCT productcategory FROM productlist";
                        $stmt = $conn->prepare($sql);
                        $stmt->execute();
                        $result = $stmt->get_result();
                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                echo '<li onclick="filterProducts(\'' . $row['productcategory'] . '\')">' . $row['productcategory'] . '</li>';
                            }
                        }
                        ?>
                    </ul>
                </div>
            </div>
            <div class="container">
                <div class="product-list" id="product-list">
                    <!-- Dynamically populated products go here -->
                </div>
            </div>
        </div>
        <div class="cartTab">
            <button class="close" type="button" id="close-modal"><i class='bx bx-x-circle'></i></button>
            <h1>Shopping Cart</h1>
            <div class="listCart">
                <!-- Dynamically populated cart items go here -->
            </div>

            <div class="cart-footer">
                <form action="checkout.php" method="post" id="checkout-form" style="width: 100%;">
                    <button type="submit" name="confirm" style="width: 100%; padding: 15px; background-color: var(--orange-color); color: #fff; border: none; border-radius: 5px; font-size: 16px;">Checkout</button>
                </form>
            </div>
        </div>

        <!-- Checkout Modal Form -->
        <div id="order-summary-modal" class="modal" style="display:none;">
            <div class="modal-content">
                <div class="modal-header">
                    <h2><i class="fas fa-shopping-cart"></i> Checkout</h2>
                </div>
                <div class="modal-body">
                    <div class="customer-details">
                        <h3>Customer Details</h3>
                        <?php if (isset($_SESSION['accountid'])) { ?>
                            <ul class="customer-info">
                                <li><span>Name:</span> <span><?php echo htmlspecialchars($customerName); ?></span></li>
                                <li><span>Address:</span> <span><?php echo htmlspecialchars($customerAddress); ?></span></li>
                                <li><span>Phone Number:</span> <span><?php echo htmlspecialchars($customerphonenumber); ?></span></li>
                            </ul>
                            <button type="button" id="edit-info" class="btn btn-secondary"><i class="fas fa-edit"></i> Edit Information</button>
                        <?php } else { ?>
                            <p>Please login to proceed.</p>
                        <?php } ?>
                    </div>

                    <form id="order-summary-form">
                        <h3 style="margin-top: 20px;"> <i class="fas fa-list-ol"></i> Order Details</h3>
                        <div class="order-details">
                            <!-- Order details will be populated here, see script.js -->
                        </div>
                        <h3 style="margin-top: 20px;"> <i class="fas fa-money-bill-wave"></i> Mode of Payment</h3>
                        <div class="payment-method">
                            <div class="detail">
                                <span>Payment Method:</span>
                                <span>Cash on Delivery</span>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" id="back-to-cart" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Back to Cart</button>
                            <button type="button" id="confirm-order" class="btn btn-primary" data-toggle="modal" data-target="#confirmation-modal">
                                <i class="fas fa-check-circle"></i> Confirm Order
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- Confirmation Modal -->
        <div class="modal fade" id="confirmation-modal" tabindex="-1" role="dialog" aria-labelledby="confirmation-modal-label" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="confirmation-modal-label">Confirm Order</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        Are you sure you want to confirm this order?
                    </div>
                    <div class="modal-footer">
                        <button id="cancel-confirmation" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="button" id="confirm-purchase" class="btn btn-primary">Confirm Purchase</button>
                    </div>
                </div>
            </div>
        </div>
        <script>
            // Back to Top Button Script
            window.onscroll = function() {
                const backToTopButton = document.getElementById("back-to-top");
                const container = document.querySelector(".panel");
                if (container.scrollTop > 200) {
                    backToTopButton.style.display = "block";
                } else {
                    backToTopButton.style.display = "none";
                }
            };

            function scrollToTop() {
                window.scrollTo({
                    top: 0,
                    behavior: 'smooth'
                });
            }
        </script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
        <script>
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer)
                    toast.addEventListener('mouseleave', Swal.resumeTimer)
                }
            })
        </script>
        <script src="app.js"></script>
        <script src="script.js"></script>
</body>

</html>
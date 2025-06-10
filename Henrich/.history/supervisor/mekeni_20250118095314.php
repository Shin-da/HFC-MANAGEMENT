<?php
require '../reusable/redirect404.php';
require '../session/session.php';
require '../database/dbconnect.php';
$current_page = basename($_SERVER['PHP_SELF'], '.php');

// Add queries for analytics
$total_orders = $conn->query("SELECT COUNT(*) as total FROM mekeni_orders")->fetch_assoc()['total'];
$pending_orders = $conn->query("SELECT COUNT(*) as pending FROM mekeni_orders WHERE status='pending'")->fetch_assoc()['pending'];
$low_stock_items = $conn->query("SELECT COUNT(*) as low_stock FROM inventory WHERE auantity <= reorder_point")->fetch_assoc()['low_stock'];
?>
<!DOCTYPE html>
<html>
<head>
    <title>Mekeni Analytics & Orders</title>
    <?php require '../reusable/header.php'; ?>
    <link rel="stylesheet" type="text/css" href="../resources/css/table.css">
</head>

<style>
    button:focus,
    input:focus,
    textarea:focus,
    select:focus {
        outline: none;
    }

    .tabs {
        display: block;
        display: -webkit-flex;
        display: -moz-flex;
        display: flex;
        -webkit-flex-wrap: wrap;
        -moz-flex-wrap: wrap;
        flex-wrap: wrap;
        margin: 0;
        overflow: hidden;
    }

    .tabs [class^="tab"] label,
    .tabs [class*=" tab"] label {
        color: #191212;
        cursor: pointer;
        display: block;
        line-height: 1em;
        padding: 2rem 0;
        text-align: center;
    }

    .tabs [class^="tab"] [type="radio"],
    .tabs [class*=" tab"] [type="radio"] {
        border-bottom: 1px solid rgba(239, 237, 239, 0.5);
        cursor: pointer;
        -webkit-appearance: none;
        -moz-appearance: none;
        appearance: none;
        display: block;
        width: 100%;
        -webkit-transition: all 0.3s ease-in-out;
        -moz-transition: all 0.3s ease-in-out;
        -o-transition: all 0.3s ease-in-out;
        transition: all 0.3s ease-in-out;
    }

    .tabs [class^="tab"] [type="radio"]:hover,
    .tabs [class^="tab"] [type="radio"]:focus,
    .tabs [class*=" tab"] [type="radio"]:hover,
    .tabs [class*=" tab"] [type="radio"]:focus {
        border-bottom: 1px solid var(--accent-color-dark);
    }

    .tabs [class^="tab"] [type="radio"]:checked,
    .tabs [class*=" tab"] [type="radio"]:checked {
        border-bottom: 2px solid var(--accent-color-dark);
    }

    .tabs [class^="tab"] [type="radio"]:checked+div,
    .tabs [class*=" tab"] [type="radio"]:checked+div {
        opacity: 1;
    }

    .tabs [class^="tab"] [type="radio"]+div,
    .tabs [class*=" tab"] [type="radio"]+div {
        display: block;
        opacity: 0;
        padding: 2rem 0;
        width: 90%;
        -webkit-transition: opacity 0.3s ease-in-out;
        -moz-transition: opacity 0.3s ease-in-out;
        -o-transition: opacity 0.3s ease-in-out;
        transition: opacity 0.3s ease-in-out;
    }

    .tabs .tab-2 {
        width: 50%;
    }

    .tabs .tab-2 [type="radio"]+div {
        width: 200%;
        margin-left: 200%;
    }

    .tabs .tab-2 [type="radio"]:checked+div {
        margin-left: 0;
    }

    .tabs .tab-2:last-child [type="radio"]+div {
        margin-left: 100%;
    }

    .tabs .tab-2:last-child [type="radio"]:checked+div {
        margin-left: -100%;
    }
</style>

<body>
    <?php include '../reusable/sidebar.php';   // Sidebar   
    ?>

    <!-- === Orders === -->
    <section class=" panel">
        <?php include '../reusable/navbarNoSearch.html'; // TOP NAVBAR         
        ?>

        <div class="container-fluid"> <!-- Stock Management -->
            <div class="table-header" style="justify-content: center">
                <div class="title" style="color:var(--accent-color-dark)">
                    <span>
                       <img src="../resources/images/Mekeni-Ph-Logo.svg" alt="" style="width: 100px; height: 100px;">
                    </span>
                    <h2>Mekeni Order Management & Analytics</h2>
                </div>
            </div>

            <!-- Analytics Dashboard -->
            <div class="row mt-4">
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body">
                            <h5>Total Orders</h5>
                            <h2><?php echo $total_orders; ?></h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body">
                            <h5>Pending Orders</h5>
                            <h2><?php echo $pending_orders; ?></h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body">
                            <h5>Low Stock Items</h5>
                            <h2><?php echo $low_stock_items; ?></h2>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <div class="container-fluid">
            <div class="tabs">
                <div class="tab-2">
                    <label for="tab2-1">Order Management</label>
                    <input id="tab2-1" name="tabs-two" type="radio" checked="checked">
                   <div>
                        <!-- Suggested Products Section -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h4>Suggested Products to Order</h4>
                            </div>
                            <div class="card-body">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Product</th>
                                            <th>Current Stock</th>
                                            <th>Average Sales</th>
                                            <th>Suggested Order</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        // Query for suggested products based on analytics
                                        $suggested_products = $conn->query("
                                            SELECT 
                                                p.product_name,
                                                i.quantity as current_stock,
                                                ROUND(AVG(s.quantity), 0) as avg_sales,
                                                GREATEST(0, (i.reorder_point - i.quantity)) as suggested_order
                                            FROM inventory i
                                            JOIN products p ON i.product_id = p.id
                                            LEFT JOIN sales s ON p.id = s.product_id
                                            WHERE i.quantity <= i.reorder_point
                                            GROUP BY p.id
                                        ");

                                        while($row = $suggested_products->fetch_assoc()) {
                                            echo "<tr>";
                                            echo "<td>{$row['product_name']}</td>";
                                            echo "<td>{$row['current_stock']}</td>";
                                            echo "<td>{$row['avg_sales']}</td>";
                                            echo "<td>{$row['suggested_order']}</td>";
                                            echo "<td><button class='btn btn-primary btn-sm'>Add to Order</button></td>";
                                            echo "</tr>";
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- New Order Form -->
                        <div class="card">
                            <div class="card-header">
                                <h4>Create New Order</h4>
                            </div>
                            <div class="card-body">
                                <form action="process_order.php" method="POST">
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label>Product</label>
                                            <select class="form-control" name="product_id" required>
                                                <!-- Populate with products -->
                                            </select>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label>Quantity</label>
                                            <input type="number" class="form-control" name="quantity" required>
                                        </div>
                                    </div>
                                    <button type="submit" class="btn btn-primary">Submit Order</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="tab-2">
                    <label for="tab2-2"><i class="bx bx-history"></i>Order History</label>
                    <input id="tab2-2" name="tabs-two" type="radio">
                    <div>
                        <div class="card">
                            <div class="card-header">
                                <h4>Order History</h4>
                            </div>
                            <div class="card-body">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Order ID</th>
                                            <th>Date</th>
                                            <th>Products</th>
                                            <th>Total Amount</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        // Query for order history
                                        $orders = $conn->query("SELECT * FROM mekeni_orders ORDER BY order_date DESC");
                                        while($order = $orders->fetch_assoc()) {
                                            echo "<tr>";
                                            echo "<td>{$order['order_id']}</td>";
                                            echo "<td>{$order['order_date']}</td>";
                                            echo "<td>{$order['products']}</td>";
                                            echo "<td>{$order['total_amount']}</td>";
                                            echo "<td>{$order['status']}</td>";
                                            echo "</tr>";
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    
                </div>
            </div>
        </div>


    </section>




</body>
<?php require '../reusable/footer.php'; ?>

</html>
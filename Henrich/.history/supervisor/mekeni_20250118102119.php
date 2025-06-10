<?php
require '../reusable/redirect404.php';
require '../session/session.php';
require '../database/dbconnect.php';
$current_page = basename($_SERVER['PHP_SELF'], '.php');

// Add error handling and use correct column names
try {
    // Add queries for analytics
    $total_orders = $conn->query("SELECT COUNT(*) as total FROM mekeni_orders")->fetch_assoc()['total'];
    $pending_orders = $conn->query("SELECT COUNT(*) as pending FROM mekeni_orders WHERE status='pending'")->fetch_assoc()['pending'];
    
    // Modified query to check if reorder_point exists
    $low_stock_query = "SELECT COUNT(*) as low_stock 
                       FROM inventory 
                       WHERE availablequantity <= 
                       CASE 
                           WHEN reorder_point IS NOT NULL THEN reorder_point 
                           ELSE 10 
                       END";
    $low_stock_items = $conn->query($low_stock_query)->fetch_assoc()['low_stock'];
} catch (mysqli_sql_exception $e) {
    // Handle the error gracefully
    $total_orders = 0;
    $pending_orders = 0;
    $low_stock_items = 0;
    error_log("Database Error: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Mekeni Analytics & Orders</title>
    <?php require '../reusable/header.php'; ?>
    <link rel="stylesheet" type="text/css" href="../resources/css/table.css">
    <!-- Add Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- Add loading overlay -->
    <style>
        .card {
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s;
        }
        .card:hover {
            transform: translateY(-5px);
        }
        .metric-card {
            background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
            padding: 20px;
        }
        .metric-card h2 {
            font-size: 2.5rem;
            color: var(--accent-color-dark);
        }
        .loading {
            position: relative;
            opacity: 0.7;
            pointer-events: none;
        }
        .loading::after {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255,255,255,0.7) url('../resources/images/loading.gif') center no-repeat;
        }
        .btn-add-order {
            background: var(--accent-color-dark);
            color: white;
            border-radius: 20px;
            padding: 8px 20px;
            transition: all 0.3s;
        }
        .btn-add-order:hover {
            transform: scale(1.05);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        .status-badge {
            padding: 5px 10px;
            border-radius: 12px;
            font-size: 0.85rem;
        }
        .status-pending { background-color: #ffeeba; color: #856404; }
        .status-approved { background-color: #d4edda; color: #155724; }
        .status-delivered { background-color: #c3e6cb; color: #1e7e34; }
        .status-cancelled { background-color: #f8d7da; color: #721c24; }
        #cart-items {
            max-height: 300px;
            overflow-y: auto;
        }
        .cart-item {
            display: flex;
            justify-content: space-between;
            padding: 10px;
            border-bottom: 1px solid #eee;
        }
        .floating-cart {
            position: fixed;
            bottom: 20px;
            right: 20px;
            z-index: 1000;
        }
        .cart-badge {
            position: absolute;
            top: -8px;
            right: -8px;
            background: red;
            color: white;
            border-radius: 50%;
            padding: 4px 8px;
            font-size: 12px;
        }
        .export-btn {
            margin-left: 10px;
        }
    </style>
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

            <!-- Enhanced Analytics Dashboard -->
            <div class="row mt-4">
                <div class="col-md-3">
                    <div class="card metric-card">
                        <div class="card-body text-center">
                            <i class="bx bx-shopping-bag mb-2" style="font-size: 2rem;"></i>
                            <h5>Total Orders</h5>
                            <h2><?php echo $total_orders; ?></h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card metric-card">
                        <div class="card-body text-center">
                            <i class="bx bx-time mb-2" style="font-size: 2rem;"></i>
                            <h5>Pending Orders</h5>
                            <h2><?php echo $pending_orders; ?></h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card metric-card">
                        <div class="card-body text-center">
                            <i class="bx bx-error-circle mb-2" style="font-size: 2rem;"></i>
                            <h5>Low Stock Items</h5>
                            <h2><?php echo $low_stock_items; ?></h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card metric-card">
                        <div class="card-body text-center">
                            <i class="bx bx-chart mb-2" style="font-size: 2rem;"></i>
                            <h5>Monthly Orders</h5>
                            <canvas id="monthlyOrdersChart"></canvas>
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
                        <!-- Enhanced Suggested Products Section -->
                        <div class="card mb-4">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h4>Suggested Products to Order</h4>
                                <div class="form-inline">
                                    <input type="text" class="form-control mr-2" id="productSearch" placeholder="Search products...">
                                    <select class="form-control" id="categoryFilter">
                                        <option value="">All Categories</option>
                                        <!-- Add category options here -->
                                    </select>
                                </div>
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
                                        // Update the suggested products query
                                        $suggested_products_query = "
                                            SELECT 
                                                p.product_name,
                                                i.availablequantity as current_stock,
                                                ROUND(AVG(s.quantity), 0) as avg_sales,
                                                GREATEST(0, (COALESCE(i.reorder_point, 10) - i.availablequantity)) as suggested_order
                                            FROM inventory i
                                            JOIN products p ON i.product_id = p.id
                                            LEFT JOIN sales s ON p.id = s.product_id
                                            WHERE i.availablequantity <= COALESCE(i.reorder_point, 10)
                                            GROUP BY p.id";

                                        try {
                                            $suggested_products = $conn->query($suggested_products_query);
                                        } catch (mysqli_sql_exception $e) {
                                            error_log("Query Error: " . $e->getMessage());
                                            $suggested_products = false;
                                        }

                                        // Update the suggested products table section
                                        if ($suggested_products && $suggested_products->num_rows > 0) {
                                            while($row = $suggested_products->fetch_assoc()) {
                                                echo "<tr>";
                                                echo "<td>{$row['product_name']}</td>";
                                                echo "<td>{$row['current_stock']}</td>";
                                                echo "<td>{$row['avg_sales']}</td>";
                                                echo "<td>{$row['suggested_order']}</td>";
                                                echo "<td><button class='btn btn-primary btn-sm'>Add to Order</button></td>";
                                                echo "</tr>";
                                            }
                                        } else {
                                            echo "<tr><td colspan='5'>No products need reordering at this time.</td></tr>";
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Enhanced Order Form -->
                        <div class="card">
                            <div class="card-header">
                                <h4>Create New Order</h4>
                            </div>
                            <div class="card-body">
                                <form id="orderForm" action="process_order.php" method="POST">
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label>Product</label>
                                            <select class="form-control select2" name="product_id" required>
                                                <?php
                                                $products = $conn->query("SELECT id, product_name FROM products WHERE status = 'active'");
                                                while($product = $products->fetch_assoc()) {
                                                    echo "<option value='{$product['id']}'>{$product['product_name']}</option>";
                                                }
                                                ?>
                                            </select>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label>Quantity</label>
                                            <input type="number" class="form-control" name="quantity" required>
                                        </div>
                                    </div>
                                    <button type="submit" class="btn btn-primary btn-add-order">Submit Order</button>
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
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h4>Order History</h4>
                                <div>
                                    <button class="btn btn-secondary export-btn" onclick="exportToExcel()">
                                        <i class="bx bx-export"></i> Export
                                    </button>
                                    <select class="form-control d-inline-block w-auto ml-2" id="statusFilter">
                                        <option value="">All Status</option>
                                        <option value="pending">Pending</option>

    </section>

    <!-- Add JavaScript for enhanced functionality -->
    <script>
        // Initialize Chart.js
        const ctx = document.getElementById('monthlyOrdersChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                datasets: [{
                    label: 'Monthly Orders',
                    data: [12, 19, 3, 5, 2, 3],
                    borderColor: 'rgb(75, 192, 192)',
                    tension: 0.1
                }]
            }
        });

        // Add loading state to form submission
        document.getElementById('orderForm').addEventListener('submit', function(e) {
            this.classList.add('loading');
        });

        // Add search functionality
        document.getElementById('productSearch').addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase();
            const rows = document.querySelectorAll('tbody tr');
            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(searchTerm) ? '' : 'none';
            });
        });

        // Initialize select2 for better dropdown experience
        $(document).ready(function() {
            $('.select2').select2();
        });
    </script>

</body>
<?php require '../reusable/footer.php'; ?>

</html>
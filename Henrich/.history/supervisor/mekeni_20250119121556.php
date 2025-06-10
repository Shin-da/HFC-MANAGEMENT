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

    // Get categories first
    $category_query = "SELECT DISTINCT productcategory 
                      FROM products 
                      WHERE supplier = 'Mekeni' 
                      AND status = 'active' 
                      ORDER BY productcategory";
    $categories_result = $conn->query($category_query);
    $categories = [];
    if ($categories_result) {
        while ($row = $categories_result->fetch_assoc()) {
            $categories[] = $row['productcategory'];
        }
    }

    // Get all available products from Mekeni
    $products_query = "
        SELECT p.id, p.product_name, p.price, p.productcategory, i.availablequantity 
        FROM products p 
        LEFT JOIN inventory i ON p.id = i.product_id 
        WHERE p.supplier = 'Mekeni' 
        AND p.status = 'active'
        ORDER BY p.productcategory, p.product_name";

    $available_products = $conn->query($products_query);
} catch (mysqli_sql_exception $e) {
    error_log("Database Error: " . $e->getMessage());
    $total_orders = 0;
    $pending_orders = 0;
    $low_stock_items = 0;
    $available_products = false;
    $categories = [];
}
?>
<!DOCTYPE html>
<html>

<head>
    <title>Mekeni Analytics & Orders</title>
    <?php require '../reusable/header.php'; ?>
    <link rel="stylesheet" type="text/css" href="../resources/css/table.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- Enhanced UI Styles -->
    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #6366F1 0%, #4338CA 100%);
            --shadow-sm: 0 1px 3px rgba(0, 0, 0, 0.12);
            --shadow-md: 0 4px 6px rgba(0, 0, 0, 0.1);
            --shadow-lg: 0 10px 15px rgba(0, 0, 0, 0.1);
            --radius-md: 10px;
            --radius-lg: 15px;
        }

        .dashboard-container {
            padding: 2rem;
            background: #f8fafc;
        }

        .metric-card {
            background: white;
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-md);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            overflow: hidden;
        }

        .metric-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-lg);
        }

        .metric-card .card-body {
            padding: 1.5rem;
        }

        .metric-icon {
            width: 48px;
            height: 48px;
            background: var(--primary-gradient);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1rem;
        }

        .metric-icon i {
            color: white;
            font-size: 1.5rem;
        }

        .stat-number {
            font-size: 2rem;
            font-weight: 700;
            color: #1e293b;
            margin: 0.5rem 0;
        }

        .metric-label {
            color: #64748b;
            font-size: 0.875rem;
            font-weight: 500;
        }

        .table-container {
            background: white;
            border-radius: var(--radius-md);
            box-shadow: var(--shadow-sm);
            padding: 1.5rem;
            margin-top: 2rem;
        }

        .table thead th {
            background: #f1f5f9;
            color: #475569;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.05em;
            padding: 1rem;
        }

        .table tbody td {
            padding: 1rem;
            vertical-align: middle;
        }

        .status-badge {
            padding: 0.375rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 500;
            text-transform: capitalize;
        }

        .btn-modern {
            padding: 0.5rem 1rem;
            border-radius: 9999px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-modern:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
        }

        .search-box {
            position: relative;
            margin-bottom: 1.5rem;
        }

        .search-box input {
            width: 100%;
            padding: 0.75rem 1rem 0.75rem 2.5rem;
            border: 1px solid #e2e8f0;
            border-radius: var(--radius-md);
            font-size: 0.875rem;
        }

        .search-box i {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
        }

        .chart-container {
            height: 300px;
            margin-top: 2rem;
        }

        /* Enhanced Modal Styles */
        .modal-content {
            border-radius: var(--radius-lg);
            border: none;
            box-shadow: var(--shadow-lg);
        }

        .modal-header {
            background: var(--primary-gradient);
            color: white;
            border-radius: var(--radius-lg) var(--radius-lg) 0 0;
            padding: 1.5rem;
        }

        .modal-body {
            padding: 2rem;
        }

        /* Enhanced Form Controls */
        .form-control {
            border-radius: var(--radius-md);
            border: 1px solid #e2e8f0;
            padding: 0.75rem 1rem;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            border-color: #6366F1;
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
        }

        /* Animations */
        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-slide-in {
            animation: slideIn 0.5s ease forwards;
        }
    </style>
</head>

<body>
    <div class="loading-overlay" id="loadingOverlay" style="display: none;">
        <div class="loading-spinner"></div>
    </div>

    <div class="toast-container"></div>
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
                <div class="col-md-3 animate-slide-in" style="animation-delay: 0.1s">
                    <div class="metric-card">
                        <div class="card-body">
                            <div class="metric-icon">
                                <i class="bx bx-shopping-bag"></i>
                            </div>
                            <h5 class="metric-label">Total Orders</h5>
                            <div class="stat-number"><?php echo $total_orders; ?></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card metric-card">
                        <div class="card-body text-center">
                            <i class="bx bx-time mb-2" style="font-size: 2rem;"></i>
                            <h5>Pending Orders</h5>
                            <h2 class="stat-number"><?php echo $pending_orders; ?></h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card metric-card">
                        <div class="card-body text-center">
                            <i class="bx bx-error-circle mb-2" style="font-size: 2rem;"></i>
                            <h5>Low Stock Items</h5>
                            <h2 class="stat-number"><?php echo $low_stock_items; ?></h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card metric-card">
                        <div class="card-body text-center">
                            <i class="bx bx-chart mb-2" style="font-size: 2rem;"></i>
                            <h5>Monthly Orders</h5>
                            <div class="chart-container">
                                <canvas id="monthlyOrdersChart"></canvas>
                            </div>
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
                                            while ($row = $suggested_products->fetch_assoc()) {
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
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h4>Create New Order</h4>
                                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#productSelectionModal">
                                    <i class="bx bx-plus"></i> Add Products
                                </button>
                            </div>
                            <div class="card-body">
                                <div class="selected-products mb-3">
                                    <h5>Selected Products</h5>
                                    <div class="table-responsive">
                                        <table class="table" id="selectedProductsTable">
                                            <thead>
                                                <tr>
                                                    <th>Product</th>
                                                    <th>Category</th>
                                                    <th>Current Stock</th>
                                                    <th>Quantity to Order</th>
                                                    <th>Unit Price</th>
                                                    <th>Total</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <!-- Selected products will be added here dynamically -->
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <td colspan="5" class="text-right"><strong>Grand Total:</strong></td>
                                                    <td colspan="2"><strong id="grandTotal">₱0.00</strong></td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                                <button type="button" class="btn btn-primary btn-add-order" onclick="submitOrder()" id="submitOrderBtn" disabled>
                                    Submit Order
                                </button>
                            </div>
                        </div>

                        <!-- Add Product Selection Modal -->
                        <div class="modal fade" id="productSelectionModal" tabindex="-1">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Select Products to Order</h5>
                                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="form-row mb-3">
                                            <div class="col">
                                                <input type="text" class="form-control" id="modalProductSearch" placeholder="Search products...">
                                            </div>
                                            <div class="col">
                                                <select class="form-control" id="modalCategoryFilter">
                                                    <option value="">All Categories</option>
                                                    <?php
                                                    foreach ($categories as $category) {
                                                        echo "<option value='" . htmlspecialchars($category) . "'>" . htmlspecialchars($category) . "</option>";
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="table-responsive">
                                            <table class="table" id="availableProductsTable">
                                                <thead>
                                                    <tr>
                                                        <th>Product</th>
                                                        <th>Category</th>
                                                        <th>Current Stock</th>
                                                        <th>Price</th>
                                                        <th>Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                    if ($available_products && $available_products->num_rows > 0) {
                                                        mysqli_data_seek($available_products, 0); // Reset pointer
                                                        while ($product = $available_products->fetch_assoc()) {
                                                            echo "<tr>";
                                                            echo "<td>" . htmlspecialchars($product['product_name']) . "</td>";
                                                            echo "<td>" . htmlspecialchars($product['productcategory']) . "</td>";
                                                            echo "<td>" . htmlspecialchars($product['availablequantity']) . "</td>";
                                                            echo "<td>₱" . htmlspecialchars($product['price']) . "</td>";
                                                            echo "<td><button class='btn btn-sm btn-primary' onclick='addToSelection({
                                                                id: " . $product['id'] . ",
                                                                name: \"" . addslashes($product['product_name']) . "\",
                                                                category: \"" . addslashes($product['productcategory']) . "\",
                                                                stock: " . ($product['availablequantity'] ?? 0) . ",
                                                                price: " . $product['price'] . "
                                                            })'>Select</button></td>";
                                                            echo "</tr>";
                                                        }
                                                    } else {
                                                        echo "<tr><td colspan='5' class='text-center'>No products available</td></tr>";
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
                                        <option value="approved">Approved</option>
                                        <option value="delivered">Delivered</option>
                                        <option value="cancelled">Cancelled</option>
                                    </select>
                                </div>
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
                                        while ($order = $orders->fetch_assoc()) {
                                            echo "<tr>";
                                            echo "<td>{$order['order_id']}</td>";
                                            echo "<td>{$order['order_date']}</td>";
                                            echo "<td>{$order['products']}</td>";
                                            echo "<td>{$order['total_amount']}</td>";
                                            echo "<td><span class='status-badge status-{$order['status']}'>{$order['status']}</span></td>";
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

        <?php include_once("../reusable/footer.php"); ?>
    </section>

    <!-- Floating Cart -->
    <div class="floating-cart">
        <button class="btn btn-primary rounded-circle p-3" data-toggle="modal" data-target="#cartModal">
            <i class="bx bx-cart" style="font-size: 24px;"></i>
            <span class="cart-badge" id="cartCount">0</span>
        </button>
    </div>

    <!-- Cart Modal -->
    <div class="modal fade" id="cartModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Order Cart</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div id="cart-items"></div>
                    <div class="text-right mt-3">
                        <h5>Total: ₱<span id="cartTotal">0.00</span></h5>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" onclick="submitOrder()">Place Order</button>
                </div>
            </div>
        </div>
    </div>

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
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    },
                    title: {
                        display: true,
                        text: 'Monthly Order Trends'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });

        // Show loading overlay
        function showLoading() {
            document.getElementById('loadingOverlay').style.display = 'flex';
        }

        // Hide loading overlay
        function hideLoading() {
            document.getElementById('loadingOverlay').style.display = 'none';
        }

        // Show toast notification
        function showToast(message, type = 'success') {
            const toast = document.createElement('div');
            toast.className = `toast ${type}`;
            toast.innerHTML = `
        <div class="toast-header">
            <strong class="mr-auto">${type === 'success' ? 'Success' : 'Error'}</strong>
            <button type="button" class="ml-2 mb-1 close" data-dismiss="toast">&times;</button>
        </div>
        <div class="toast-body">${message}</div>
    `;
            document.querySelector('.toast-container').appendChild(toast);
            $(toast).toast({
                delay: 3000
            }).toast('show');
        }

        // Enhance form submission
        async function submitOrder() {
            if (selectedProducts.length === 0) {
                showToast('Please select products to order', 'error');
                return;
            }

            try {
                showLoading();
                const formData = new FormData();
                selectedProducts.forEach(product => {
                    formData.append('product_id[]', product.id);
                    formData.append('quantity[]', product.quantity);
                });

                const response = await fetch('process_order.php', {
                    method: 'POST',
                    body: formData
                });
                const data = await response.json();

                if (data.status === 'success') {
                    showToast('Order placed successfully!');
                    selectedProducts = [];
                    updateSelectedProductsTable();
                    setTimeout(() => location.reload(), 2000);
                } else {
                    throw new Error(data.message);
                }
            } catch (error) {
                showToast(error.message, 'error');
            } finally {
                hideLoading();
            }
        }

        // Add animation to statistics
        function animateNumbers() {
            const stats = document.querySelectorAll('.stat-number');
            stats.forEach(stat => {
                const target = parseInt(stat.textContent);
                let current = 0;
                const increment = target / 20;
                const interval = setInterval(() => {
                    current += increment;
                    if (current >= target) {
                        stat.textContent = target;
                        clearInterval(interval);
                    } else {
                        stat.textContent = Math.round(current);
                    }
                }, 50);
            });
        }

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            animateNumbers();

            // Enhanced tooltips
            $('[data-toggle="tooltip"]').tooltip({
                template: '<div class="tooltip" role="tooltip"><div class="arrow"></div><div class="tooltip-inner"></div></div>',
                animation: true
            });

            // Enhanced select2
            $('.select2').select2({
                theme: 'bootstrap4',
                placeholder: 'Select a product',
                allowClear: true
            });
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

        let cart = [];

        function addToCart(productId, productName, price) {
            const existingItem = cart.find(item => item.productId === productId);
            if (existingItem) {
                existingItem.quantity++;
            } else {
                cart.push({
                    productId,
                    productName,
                    price,
                    quantity: 1
                });
            }
            updateCartUI();
        }

        function updateCartUI() {
            const cartItems = document.getElementById('cart-items');
            const cartCount = document.getElementById('cartCount');
            const cartTotal = document.getElementById('cartTotal');

            cartItems.innerHTML = '';
            let total = 0;

            cart.forEach((item, index) => {
                const itemTotal = item.price * item.quantity;
                total += itemTotal;

                cartItems.innerHTML += `
                    <div class="cart-item">
                        <div>
                            ${item.productName} x ${item.quantity}
                            <br>
                            <small>₱${item.price.toFixed(2)} each</small>
                        </div>
                        <div>
                            ₱${itemTotal.toFixed(2)}
                            <button class="btn btn-sm btn-danger ml-2" onclick="removeFromCart(${index})">
                                <i class="bx bx-trash"></i>
                            </button>
                        </div>
                    </div>
                `;
            });

            cartCount.textContent = cart.length;
            cartTotal.textContent = total.toFixed(2);
        }

        function removeFromCart(index) {
            cart.splice(index, 1);
            updateCartUI();
        }

        function submitOrder() {
            if (cart.length === 0) {
                alert('Cart is empty!');
                return;
            }

            const formData = new FormData();
            cart.forEach(item => {
                formData.append('product_id[]', item.productId);
                formData.append('quantity[]', item.quantity);
            });

            fetch('process_order.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        alert('Order placed successfully!');
                        cart = [];
                        updateCartUI();
                        $('#cartModal').modal('hide');
                        location.reload();
                    } else {
                        alert('Error: ' + data.message);
                    }
                })
                .catch(error => alert('Error: ' + error));
        }

        function exportToExcel() {
            window.location.href = 'export_orders.php';
        }

        // Add status filter functionality
        document.getElementById('statusFilter').addEventListener('change', function(e) {
            const status = e.target.value.toLowerCase();
            const rows = document.querySelectorAll('.order-history tbody tr');
            rows.forEach(row => {
                const rowStatus = row.querySelector('.status-badge').textContent.toLowerCase();
                row.style.display = status === '' || rowStatus === status ? '' : 'none';
            });
        });

        // Initialize tooltips
        $(function() {
            $('[data-toggle="tooltip"]').tooltip();
        });

        let selectedProducts = [];

        function addToSelection(product) {
            if (!selectedProducts.find(p => p.id === product.id)) {
                selectedProducts.push({
                    ...product,
                    quantity: 1
                });
                updateSelectedProductsTable();
                $('#productSelectionModal').modal('hide');
            } else {
                alert('Product already added to selection');
            }
        }

        function updateSelectedProductsTable() {
            const tbody = document.querySelector('#selectedProductsTable tbody');
            tbody.innerHTML = '';
            let grandTotal = 0;

            selectedProducts.forEach((product, index) => {
                const total = product.price * product.quantity;
                grandTotal += total;

                tbody.innerHTML += `
                    <tr>
                        <td>${product.name}</td>
                        <td>${product.category}</td>
                        <td>${product.stock}</td>
                        <td>
                            <input type="number" class="form-control form-control-sm" 
                                   value="${product.quantity}" min="1" 
                                   onchange="updateQuantity(${index}, this.value)">
                        </td>
                        <td>₱${product.price.toFixed(2)}</td>
                        <td>₱${total.toFixed(2)}</td>
                        <td>
                            <button class="btn btn-sm btn-danger" onclick="removeProduct(${index})">
                                <i class="bx bx-trash"></i>
                            </button>
                        </td>
                    </tr>
                `;
            });

            document.getElementById('grandTotal').textContent = `₱${grandTotal.toFixed(2)}`;
            document.getElementById('submitOrderBtn').disabled = selectedProducts.length === 0;
        }

        function updateQuantity(index, quantity) {
            selectedProducts[index].quantity = parseInt(quantity);
            updateSelectedProductsTable();
        }

        function removeProduct(index) {
            selectedProducts.splice(index, 1);
            updateSelectedProductsTable();
        }

        function submitOrder() {
            if (selectedProducts.length === 0) {
                alert('Please select products to order');
                return;
            }

            const formData = new FormData();
            selectedProducts.forEach(product => {
                formData.append('product_id[]', product.id);
                formData.append('quantity[]', product.quantity);
            });

            fetch('process_order.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        alert('Order placed successfully!');
                        selectedProducts = [];
                        updateSelectedProductsTable();
                        location.reload();
                    } else {
                        alert('Error: ' + data.message);
                    }
                })
                .catch(error => alert('Error: ' + error));
        }

        // Modal search and filter functionality
        document.getElementById('modalProductSearch').addEventListener('input', function(e) {
            filterProducts();
        });

        document.getElementById('modalCategoryFilter').addEventListener('change', function(e) {
            filterProducts();
        });

        function filterProducts() {
            const searchTerm = document.getElementById('modalProductSearch').value.toLowerCase();
            const category = document.getElementById('modalCategoryFilter').value.toLowerCase();
            const rows = document.querySelectorAll('#availableProductsTable tbody tr');

            rows.forEach(row => {
                const productName = row.cells[0].textContent.toLowerCase();
                const productCategory = row.cells[1].textContent.toLowerCase();
                const matchesSearch = productName.includes(searchTerm);
                const matchesCategory = category === '' || productCategory === category;
                row.style.display = matchesSearch && matchesCategory ? '' : 'none';
            });
        }
    </script>

</body>


</html>
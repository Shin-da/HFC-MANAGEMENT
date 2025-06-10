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

// Add this after the existing queries
try {
    // Get all available products from Mekeni
    $available_products = $conn->query("
        SELECT p.id, p.product_name, p.price, p.productcategory, i.availablequantity 
        FROM products p 
        LEFT JOIN inventory i ON p.id = i.product_id 
        WHERE p.supplier = 'Mekeni' 
        AND p.status = 'active'
        ORDER BY p.productcategory, p.product_name
    ");
} catch (mysqli_sql_exception $e) {
    error_log("Product Query Error: " . $e->getMessage());
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
            background: rgba(255, 255, 255, 0.7) url('../resources/images/loading.gif') center no-repeat;
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
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .status-badge {
            padding: 5px 10px;
            border-radius: 12px;
            font-size: 0.85rem;
        }

        .status-pending {
            background-color: #ffeeba;
            color: #856404;
        }

        .status-approved {
            background-color: #d4edda;
            color: #155724;
        }

        .status-delivered {
            background-color: #c3e6cb;
            color: #1e7e34;
        }

        .status-cancelled {
            background-color: #f8d7da;
            color: #721c24;
        }

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

            textarea:focus,
            select:focus {
                outline: none;
            }
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
    <style>
    /* Enhanced Card Styles */
    .dashboard-card {
        border: none;
        border-radius: 15px;
        transition: all 0.3s ease;
        background: linear-gradient(145deg, #ffffff, #f0f0f0);
        box-shadow: 5px 5px 15px #d1d1d1, -5px -5px 15px #ffffff;
    }

    .dashboard-card:hover {
        transform: translateY(-5px);
        box-shadow: 8px 8px 20px #d1d1d1, -8px -8px 20px #ffffff;
    }

    /* Animated Statistics */
    .stat-number {
        font-size: 2.5rem;
        font-weight: bold;
        color: var(--accent-color-dark);
        opacity: 0;
        transform: translateY(20px);
        animation: fadeInUp 0.5s ease forwards;
    }

    @keyframes fadeInUp {
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* Table Enhancements */
    .table {
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 0 20px rgba(0,0,0,0.05);
    }

    .table thead th {
        background: var(--accent-color-dark);
        color: white;
        font-weight: 500;
        text-transform: uppercase;
        font-size: 0.85rem;
        letter-spacing: 0.5px;
        padding: 15px;
    }

    .table tbody tr {
        transition: all 0.2s ease;
    }

    .table tbody tr:hover {
        background-color: rgba(0,0,0,0.02);
        transform: scale(1.01);
    }

    /* Modal Enhancements */
    .modal-content {
        border-radius: 15px;
        border: none;
        box-shadow: 0 0 30px rgba(0,0,0,0.1);
    }

    .modal-header {
        background: linear-gradient(135deg, var(--accent-color-dark), var(--accent-color));
        color: white;
        border-radius: 15px 15px 0 0;
    }

    /* Button Styles */
    .btn-modern {
        border-radius: 25px;
        padding: 8px 20px;
        text-transform: uppercase;
        font-size: 0.85rem;
        letter-spacing: 0.5px;
        font-weight: 500;
        transition: all 0.3s ease;
    }

    .btn-modern:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }

    /* Loading Animation */
    .loading-overlay {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(255,255,255,0.8);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 9999;
    }

    .loading-spinner {
        width: 50px;
        height: 50px;
        border: 5px solid #f3f3f3;
        border-top: 5px solid var(--accent-color-dark);
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

    /* Toast Notifications */
    .toast-container {
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 9999;
    }

    .toast {
        min-width: 300px;
        border-radius: 10px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }
</style>

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
                <div class="col-md-3">
                    <div class="card metric-card">
                        <div class="card-body text-center">
                            <i class="bx bx-shopping-bag mb-2" style="font-size: 2rem;"></i>
                            <h5>Total Orders</h5>
                            <h2 class="stat-number"><?php echo $total_orders; ?></h2>
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
                                                    if ($available_products) {
                                                        $categories = [];
                                                        while ($product = $available_products->fetch_assoc()) {
                                                            if (!in_array($product['productcategory'], $categories)) {
                                                                $categories[] = $product['productcategory'];
                                                                echo "<option value='{$product['productcategory']}'>{$product['productcategory']}</option>";
                                                            }
                                                        }
                                                        mysqli_data_seek($available_products, 0); // Reset pointer
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
                                                    if ($available_products) {
                                                        while ($product = $available_products->fetch_assoc()) {
                                                            echo "<tr>";
                                                            echo "<td>{$product['product_name']}</td>";
                                                            echo "<td>{$product['productcategory']}</td>";
                                                            echo "<td>{$product['availablequantity']}</td>";
                                                            echo "<td>₱{$product['price']}</td>";
                                                            echo "<td><button class='btn btn-sm btn-primary' onclick='addToSelection({
                                                                id: {$product['id']},
                                                                name: \"{$product['product_name']}\",
                                                                category: \"{$product['productcategory']}\",
                                                                stock: {$product['availablequantity']},
                                                                price: {$product['price']}
                                                            })'>Select</button></td>";
                                                            echo "</tr>";
                                                        }
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
<?php
require_once '../includes/config.php';
require_once '../includes/session.php';
require_once '../includes/Page.php';

$current_page = basename($_SERVER['PHP_SELF'], '.php');
Page::setCurrentPage($current_page);

try {
    // Initialize variables
    $analytics = [
        'total_orders' => 0,
        'pending_orders' => 0,
        'total_revenue' => 0,
        'average_order_value' => 0
    ];
    $inventory = [
        'total_products' => 0,
        'low_stock' => 0,
        'out_of_stock' => 0
    ];
    $predictions = null;

    // Verify database connection
    if (!$conn) {
        throw new Exception("Database connection failed");
    }

    // Analytics query using correct table structure
    $analytics_query = "
        SELECT 
            COUNT(DISTINCT o.orderid) as total_orders,
            COUNT(DISTINCT CASE WHEN o.status = 'pending' THEN o.orderid END) as pending_orders,
            SUM(o.ordertotal) as total_revenue,
            AVG(o.ordertotal) as average_order_value
        FROM customerorder o
        WHERE o.orderdate >= DATE_SUB(CURRENT_DATE, INTERVAL 30 DAY)
        AND o.orderdescription LIKE '%Mekeni%'";

    // Inventory status query
    $inventory_query = "
        SELECT 
            COUNT(*) as total_products,
            COUNT(CASE WHEN i.availablequantity <= 10 THEN 1 END) as low_stock,
            COUNT(CASE WHEN i.availablequantity = 0 THEN 1 END) as out_of_stock
        FROM inventory i
        JOIN products p ON i.productcode = p.productcode
        WHERE p.productcategory LIKE '%Mekeni%'";

    // Enhanced prediction query using available fields
    $prediction_query = "
        SELECT 
            p.productcode as id,
            p.productname as product_name,
            p.unit_price as price,
            i.availablequantity as current_stock,
            i.productcategory,
            p.packsperbox,
            (
                SELECT AVG(co2.ordertotal / NULLIF(co2.quantity, 0))
                FROM customerorder co2
                WHERE co2.productcode = p.productcode
                AND co2.orderdate >= DATE_SUB(CURRENT_DATE, INTERVAL 30 DAY)
            ) as avg_daily_sales,
            (
                SELECT MAX(co3.ordertotal / NULLIF(co3.quantity, 0))
                FROM customerorder co3
                WHERE co3.productcode = p.productcode
                AND co3.orderdate >= DATE_SUB(CURRENT_DATE, INTERVAL 30 DAY)
            ) as max_daily_sales,
            DATEDIFF(
                CURRENT_DATE,
                (
                    SELECT MAX(co4.orderdate)
                    FROM customerorder co4
                    WHERE co4.productcode = p.productcode
                )
            ) as days_since_last_order,
            CASE 
                WHEN (
                    SELECT AVG(co5.ordertotal / NULLIF(co5.quantity, 0))
                    FROM customerorder co5
                    WHERE co5.productcode = p.productcode
                    AND co5.orderdate >= DATE_SUB(CURRENT_DATE, INTERVAL 30 DAY)
                ) > 0 
                THEN i.availablequantity / (
                    SELECT AVG(co6.ordertotal / NULLIF(co6.quantity, 0))
                    FROM customerorder co6
                    WHERE co6.productcode = p.productcode
                    AND co6.orderdate >= DATE_SUB(CURRENT_DATE, INTERVAL 30 DAY)
                )
                ELSE 999
            END as days_until_stockout
        FROM products p
        JOIN inventory i ON p.productcode = i.productcode
        WHERE p.productcategory LIKE '%Mekeni%'
        AND p.productstatus = 'active'
        GROUP BY p.productcode
        HAVING days_until_stockout < 14 OR current_stock <= 10
        ORDER BY days_until_stockout ASC";

    // Execute queries with error handling
    $analytics = $conn->query($analytics_query)->fetch_assoc() ?? [
        'total_orders' => 0,
        'pending_orders' => 0,
        'total_revenue' => 0,
        'average_order_value' => 0
    ];

    $inventory = $conn->query($inventory_query)->fetch_assoc() ?? [
        'total_products' => 0,
        'low_stock' => 0,
        'out_of_stock' => 0
    ];

    $predictions = $conn->query($prediction_query);
    if (!$predictions) {
        throw new Exception("Failed to fetch prediction data: " . $conn->error);
    }

    // Get categories query
    $category_query = "
        SELECT DISTINCT productcategory 
        FROM inventory 
        WHERE productcategory LIKE '%Mekeni%' 
        ORDER BY productcategory";

    $categories_result = $conn->query($category_query);
    $categories = [];
    if ($categories_result) {
        while ($row = $categories_result->fetch_assoc()) {
            $categories[] = $row['productcategory'];
        }
    }

    // Update the available products query
    $available_products_query = "
        SELECT 
            p.productcode as id,
            p.productname as product_name,
            p.unit_price as price,
            p.productcategory,
            p.packsperbox,
            i.availablequantity as stock
        FROM products p
        JOIN inventory i ON p.productcode = i.productcode
        WHERE p.productcategory LIKE '%Mekeni%'
        AND p.productstatus = 'active'
        ORDER BY p.productcategory, p.productname";

    $available_products = $conn->query($available_products_query);
} catch (Exception $e) {
    error_log("Error in mekeni.php: " . $e->getMessage());
    // Set default values
    $predictions = null;
}

// Configure page
Page::setTitle('Mekeni | Supervisor');
Page::setBodyClass('supervisor-body');
Page::set('current_page', 'mekeni');
Page::addScript('https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js');
Page::addScript('https://cdn.jsdelivr.net/npm/chart.js'); // Change from charts.js to chart.js

// Add styles and scripts
Page::addStyle('../assets/css/mekeni.css');
Page::addStyle('../assets/css/metrics-dashboard.css');
Page::addScript('../assets/js/mekeni-dashboard.js');

ob_start();
?>

<div class="dashboard-wrapper">
    <!-- Enhanced Header with Order Status -->
    <div class="page-header">
        <div class="header-content">
            <h1>Mekeni Products Management</h1>
            <div class="header-actions">
                <button class="btn btn-primary" data-toggle="modal" data-target="#productSelectionModal">
                    <i class="bx bx-plus"></i> New Order
                </button>
                <div class="date-filter">
                    <select id="dateRange" class="form-control">
                        <option value="today">Today</option>
                        <option value="week">This Week</option>
                        <option value="month" selected>This Month</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="order-status-bar">
            <div class="status-item">
                <span class="label">Pending Orders</span>
                <span class="value"><?php echo $analytics['pending_orders']; ?></span>
            </div>
            <div class="status-item">
                <span class="label">Monthly Revenue</span>
                <span class="value">₱<?php echo number_format($analytics['total_revenue'] ?? 0, 2); ?></span>
            </div>
            <div class="status-item">
                <span class="label">Avg Order Value</span>
                <span class="value">₱<?php echo number_format($analytics['average_order_value'] ?? 0, 2); ?></span>
            </div>
        </div>
    </div>

    <!-- Metrics Dashboard -->
    <div class="metrics-grid">
        <!-- Enhanced metric cards with animations -->
        <div class="metric-card animate__animated animate__fadeIn">
            <div class="metric-icon orders">
                <i class="bx bx-shopping-bag"></i>
            </div>
            <div class="metric-content">
                <h3>Total Orders</h3>
                <div class="metric-value counter"><?php echo $analytics['total_orders']; ?></div>
                <div class="metric-trend positive">
                    <i class="bx bx-up-arrow-alt"></i>
                    <span>12% vs last month</span>
                </div>
            </div>
        </div>

        <!-- Add remaining metric cards similarly -->
    </div>

    <!-- Main Content Tabs -->
    <div class="content-tabs">
        <div class="tab-header">
            <button class="tab-btn active" data-tab="order-management">
                <i class="bx bx-package"></i> Order Management
            </button>
            <button class="tab-btn" data-tab="order-history">
                <i class="bx bx-history"></i> Order History
            </button>
        </div>

        <!-- Order Management Tab -->
        <div class="tab-content active" id="order-management">
            <!-- Predictive Ordering Section -->
            <div class="card mb-4">
                <div class="card-header">
                    <h4>Suggested Orders</h4>
                    <div class="header-actions">
                        <button class="btn btn-primary" onclick="processAllSuggestions()">
                            Process All Suggestions
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Current Stock</th>
                                    <th>Avg Daily Sales</th>
                                    <th>Days Until Stockout</th>
                                    <th>Suggested Order</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if ($predictions && $predictions->num_rows > 0):
                                    while ($product = $predictions->fetch_assoc()):
                                        $suggested_order = calculateSuggestedOrder(
                                            $product['current_stock'],
                                            $product['avg_daily_sales'],
                                            $product['packsperbox']
                                        );
                                ?>
                                        <tr class="<?php echo $product['days_until_stockout'] < 7 ? 'urgent' : ''; ?>">
                                            <td><?php echo htmlspecialchars($product['product_name']); ?></td>
                                            <td><?php echo $product['current_stock']; ?></td>
                                            <td><?php echo round($product['avg_daily_sales'], 1); ?></td>
                                            <td><?php echo round($product['days_until_stockout']); ?> days</td>
                                            <td><?php echo $suggested_order; ?></td>
                                            <td>
                                                <button class="btn btn-sm btn-primary"
                                                    onclick="addToOrder(<?php echo json_encode($product); ?>, <?php echo $suggested_order; ?>)">
                                                    Add to Order
                                                </button>
                                            </td>
                                        </tr>
                                    <?php
                                    endwhile;
                                else:
                                    ?>
                                    <tr>
                                        <td colspan="6" class="text-center">
                                            <?php echo $predictions === null ? 'Error loading prediction data' : 'No products need reordering at this time'; ?>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Order Form Section -->
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
        </div>

        <!-- Order History Tab -->
        <div class="tab-content" id="order-history">
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

<!-- Enhanced Modal Designs -->
<div class="modal fade" id="productSelectionModal">
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

<!-- Floating Action Button for Cart -->
<div class="floating-action-button" id="cartFab"></div>
<button class="fab-button" data-toggle="modal" data-target="#cartModal">
    <i class="bx bx-cart"></i>
    <span class="fab-badge" id="cartCount">0</span>
</button>
</div>

<script>
    // Enhanced JavaScript functionality
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize animations
        initializeAnimations();

        // Setup event listeners
        setupEventListeners();

        // Initialize charts
        initializeCharts();
    });

    // Add predictive ordering functionality
    function processAllSuggestions() {
        const suggestions = document.querySelectorAll('.urgent');
        suggestions.forEach(row => {
            const addButton = row.querySelector('.btn-primary');
            if (addButton) addButton.click();
        });
    }

    // Enhanced addToOrder function
    function addToOrder(product, suggestedQuantity) {
        const orderData = {
            product_id: product.id,
            quantity: suggestedQuantity,
            current_stock: product.current_stock,
            avg_daily_sales: product.avg_daily_sales
        };

        // Show confirmation with order details
        Swal.fire({
            title: 'Confirm Order',
            html: `
            <div class="order-confirmation">
                <p><strong>${product.product_name}</strong></p>
                <p>Suggested Quantity: ${suggestedQuantity}</p>
                <p>Current Stock: ${product.current_stock}</p>
                <p>Average Daily Sales: ${Math.round(product.avg_daily_sales * 10) / 10}</p>
            </div>
        `,
            showCancelButton: true,
            confirmButtonText: 'Add to Order'
        }).then((result) => {
            if (result.isConfirmed) {
                addToSelection(product, suggestedQuantity);
            }
        });
    }

    // ... rest of the JavaScript functions ...
</script>

<?php
function calculateSuggestedOrder($current_stock, $avg_daily_sales, $packs_per_box)
{
    $days_to_cover = 30; // Order enough for 30 days
    $safety_stock = max($avg_daily_sales * 7, 10); // Higher of 7 days peak sales or minimum 10 units
    $projected_need = ceil(($avg_daily_sales * $days_to_cover) + $safety_stock);
    $suggested_order = max(0, $projected_need - $current_stock);

    // Round up to nearest box quantity if packs_per_box is set
    if ($packs_per_box > 0) {
        $suggested_order = ceil($suggested_order / $packs_per_box) * $packs_per_box;
    }

    return $suggested_order;
}

$content = ob_get_clean();
Page::render($content);
?>
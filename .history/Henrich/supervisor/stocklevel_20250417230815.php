<?php
require_once '../includes/session.php';
require_once '../includes/config.php';
require_once '../includes/Page.php';
require_once './access_control.php';

$current_page = basename($_SERVER['PHP_SELF'], '.php');
$_SESSION['current_page'] = $current_page;

// Initialize page
Page::setTitle('Stock Levels Management');
Page::setBodyClass('supervisor-body');
Page::setCurrentPage('stocklevel');

// Add required styles and scripts
Page::addStyle('../assets/css/inventory-master.css');

// Add jQuery before other scripts
Page::addScript('https://code.jquery.com/jquery-3.7.0.min.js');
Page::addStyle('https://cdn.datatables.net/1.13.6/css/jquery.dataTables.css');
Page::addScript('https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js');
Page::addScript('https://cdn.jsdelivr.net/npm/chart.js');
Page::addScript('../assets/js/stocklevel.js');

// Add theme-related styles and scripts
Page::addStyle('/assets/css/themes.css');
Page::addStyle('/assets/css/theme-toggle.css');
Page::addScript('/assets/js/theme.js');

// Add after other script includes
Page::addScript('../assets/js/debug-loader.js');

// Add new required scripts
Page::addScript('https://cdn.jsdelivr.net/npm/sweetalert2@11');
Page::addScript('https://cdn.jsdelivr.net/npm/apexcharts');
Page::addScript('../assets/js/stocklevel-enhanced.js');

// Cache setup - using simple file caching with 5 minute expiry
$cache_dir = __DIR__ . '/../cache/';
$cache_file = $cache_dir . 'stocklevel_data_' . md5($_SESSION['user_id']) . '.cache';
$cache_ttl = 300; // 5 minutes cache

// Initialize empty data structure
$initial_data = [
    'stats' => [
        'total_products' => 0,
        'low_stock' => 0,
        'out_of_stock' => 0,
        'total_value' => 0
    ],
    'trends' => ['dates' => [], 'values' => []]
];

// Initialize stock data
$stock_data = [
    'stats' => [],
    'trends' => ['dates' => [], 'values' => []],
    'alerts' => [],
    'top_products' => [],
    'categories' => [],
    'recent_movements' => []
];

// Check if we should bypass cache (refresh requested)
$refresh_requested = isset($_GET['refresh']) && $_GET['refresh'] == 1;

// Function to get data from DB or cache
function getStockData($conn, $cache_file, $cache_ttl, $refresh_requested) {
    // Check if cache exists and is valid
    if (!$refresh_requested && file_exists($cache_file) && (time() - filemtime($cache_file) < $cache_ttl)) {
        // Use cached data
        $cached_data = file_get_contents($cache_file);
        if ($cached_data) {
            return json_decode($cached_data, true);
        }
    }

    // If no cache or refresh requested, get data from database
    try {
        // Initialize all variables
        $stats = [];
        $inventory_data = [];
        $alerts_data = [];
        $top_products = [];
        $categories = [];
        $trends_data = ['dates' => [], 'values' => []];
        $recent_movements = [];
        
        // Use separate queries instead of prepared statements to avoid sync issues
        
        // Get basic statistics in one query
        $stats_query = "SELECT 
            COUNT(*) as total_products,
            SUM(CASE WHEN availablequantity <= 10 AND availablequantity > 0 THEN 1 ELSE 0 END) as low_stock,
            SUM(CASE WHEN availablequantity = 0 THEN 1 ELSE 0 END) as out_of_stock,
            SUM(availablequantity * unit_price) as total_value
        FROM inventory";
        $stats_result = $conn->query($stats_query);
        if ($stats_result) {
            $stats = $stats_result->fetch_assoc();
            $stats_result->free_result();
        }

        // Get inventory data
        $inventory_query = "SELECT 
            i.productcode,
            i.productname,
            i.productcategory,
            i.availablequantity,
            i.onhandquantity,
            i.unit_price,
            i.dateupdated,
            CASE 
                WHEN i.availablequantity = 0 THEN 'Out of Stock'
                WHEN i.availablequantity <= 10 THEN 'Low Stock'
                ELSE 'In Stock'
            END as stock_status
        FROM inventory i
        ORDER BY i.availablequantity ASC";
        $inventory_result = $conn->query($inventory_query);
        if ($inventory_result) {
            while ($row = $inventory_result->fetch_assoc()) {
                $inventory_data[] = $row;
            }
            $inventory_result->free_result();
        }

        // Get alerts data
        $alerts_query = "SELECT 
            productcode,
            productname,
            availablequantity,
            onhandquantity,
            unit_price,
            dateupdated,
            CASE 
                WHEN availablequantity = 0 THEN 'out_of_stock'
                WHEN availablequantity <= 10 THEN 'low_stock'
                ELSE 'normal'
            END as alert_type
        FROM inventory 
        WHERE availablequantity <= 10
        ORDER BY availablequantity ASC, dateupdated DESC
        LIMIT 10";
        $alerts_result = $conn->query($alerts_query);
        if ($alerts_result) {
            while ($row = $alerts_result->fetch_assoc()) {
                $alerts_data[] = $row;
            }
            $alerts_result->free_result();
        }

        // Get top products
        $top_products_query = "SELECT 
            i.productcode, 
            p.productname, 
            i.availablequantity, 
            i.unit_price, 
            (i.availablequantity * i.unit_price) as total_value
        FROM inventory i
        JOIN products p ON i.productcode = p.productcode
        ORDER BY total_value DESC
        LIMIT 5";
        $top_products_result = $conn->query($top_products_query);
        if ($top_products_result) {
            while ($row = $top_products_result->fetch_assoc()) {
                $top_products[] = $row;
            }
            $top_products_result->free_result();
        }

        // Get category data
        $category_query = "SELECT 
            productcategory, 
            COUNT(*) as count, 
            SUM(availablequantity) as total_quantity,
            SUM(availablequantity * unit_price) as total_value
        FROM inventory
        GROUP BY productcategory
        ORDER BY total_value DESC";
        $category_result = $conn->query($category_query);
        if ($category_result) {
            while ($row = $category_result->fetch_assoc()) {
                $categories[] = $row;
            }
            $category_result->free_result();
        }

        // Get trends data
        $trends_query = "SELECT 
            DATE(dateupdated) as date,
            SUM(availablequantity * unit_price) as total_value
        FROM inventory
        WHERE dateupdated >= DATE_SUB(CURRENT_DATE, INTERVAL 30 DAY)
        GROUP BY DATE(dateupdated)
        ORDER BY date ASC";
        $trends_result = $conn->query($trends_query);
        if ($trends_result) {
            while ($row = $trends_result->fetch_assoc()) {
                $trends_data['dates'][] = $row['date'];
                $trends_data['values'][] = (float)$row['total_value'];
            }
            $trends_result->free_result();
        }

        // Get recent stock movements
        $recent_movements_query = "SELECT sm.*, p.productname 
                                 FROM stockmovement sm
                                 JOIN products p ON sm.productcode = p.productcode
                                 ORDER BY sm.dateencoded DESC LIMIT 10";
        $recent_movements_result = $conn->query($recent_movements_query);
        if ($recent_movements_result) {
            while ($row = $recent_movements_result->fetch_assoc()) {
                $recent_movements[] = $row;
            }
            $recent_movements_result->free_result();
        }

        // All queries complete, compile data
        $stock_data = [
            'stats' => $stats,
            'inventory' => $inventory_data,
            'alerts' => $alerts_data,
            'top_products' => $top_products,
            'categories' => $categories,
            'trends' => $trends_data,
            'recent_movements' => $recent_movements,
            'last_updated' => date('Y-m-d H:i:s')
        ];

        // Save to cache
        if (!is_dir(dirname($cache_file))) {
            mkdir(dirname($cache_file), 0755, true);
        }
        file_put_contents($cache_file, json_encode($stock_data));

        return $stock_data;

    } catch (Exception $e) {
        error_log("Error fetching stock data: " . $e->getMessage());
        return ['error' => $e->getMessage()];
    }
}

// Get data from cache or database
$stock_data = getStockData($conn, $cache_file, $cache_ttl, $refresh_requested);

// Make sure we have stats data
if (!isset($stock_data['stats']) || !is_array($stock_data['stats'])) {
    $stock_data['stats'] = $initial_data['stats'];
}

// Extract commonly used data
$stats = $stock_data['stats'];
$inventory_data = $stock_data['inventory'] ?? [];
$alerts_data = $stock_data['alerts'] ?? [];
$top_products = $stock_data['top_products'] ?? [];
$categories = $stock_data['categories'] ?? [];
$trends_data = $stock_data['trends'] ?? ['dates' => [], 'values' => []];
$recent_movements = $stock_data['recent_movements'] ?? [];
$last_updated = $stock_data['last_updated'] ?? date('Y-m-d H:i:s');

ob_start();
?>

<div class="stock-management-wrapper theme-aware">
    <div class="dashboard-header">
        <div class="welcome-section">
            <div class="title-section">
                <h1>Stock Level</h1>
                <p class="subtitle">Real-time inventory tracking and management</p>
            </div>
            <div class="header-actions">
                <div class="refresh-control">
                    <button class="btn-refresh" onclick="refreshData()">
                        <i class='bx bx-refresh'></i>
                        <span class="last-updated">Last updated: <time id="lastUpdate"><?= $last_updated ?></time></span>
                    </button>
                    <div class="auto-refresh">
                        <label class="switch">
                            <input type="checkbox" id="autoRefresh">
                            <span class="slider"></span>
                        </label>
                        <span>Auto-refresh</span>
                        <select id="refreshInterval">
                            <option value="30">30s</option>
                            <option value="60">1m</option>
                            <option value="300">5m</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Interactive Stats Grid -->
    <div class="quick-stats">
        <div class="stats-grid">
            <div class="stat-card" onclick="showProductsList('all')">
                <div class="stat-icon"><i class='bx bx-package'></i></div>
                <h3>Total Products</h3>
                <div class="value" id="totalProducts"><?= number_format($stats['total_products']) ?></div>
            </div>
            <div class="stat-card warning" onclick="showProductsList('low')">
                <div class="stat-icon"><i class='bx bx-error'></i></div>
                <h3>Low Stock Items</h3>
                <div class="value" id="lowStock"><?= number_format($stats['low_stock']) ?></div>
            </div>
            <div class="stat-card danger" onclick="showProductsList('out')">
                <div class="stat-icon"><i class='bx bx-x-circle'></i></div>
                <h3>Out of Stock</h3>
                <div class="value" id="outOfStock"><?= number_format($stats['out_of_stock']) ?></div>
            </div>
            <div class="stat-card success">
                <div class="stat-icon"><i class='bx bx-money'></i></div>
                <h3>Total Stock Value</h3>
                <div class="value" id="totalValue">₱<?= number_format($stats['total_value'], 2) ?></div>
            </div>
        </div>
    </div>

    <!-- Dashboard first row -->
    <div class="dashboard-row">
        <!-- Real-time Alerts Section -->
        <div class="stock-alerts-container">
            <div class="alerts-header">
                <h3>Stock Alerts</h3>
                <div class="alert-controls">
                    <button class="btn-filter active" data-filter-type="all" onclick="window.filterAlerts('all', event)">All</button>
                    <button class="btn-filter" data-filter-type="critical" onclick="window.filterAlerts('critical', event)">Critical</button>
                </div>
            </div>
            <div id="stockAlerts" class="alerts-list">
                <?php if (!empty($alerts_data)): ?>
                    <?php foreach ($alerts_data as $alert): ?>
                        <div class="alert-item <?= $alert['alert_type'] ?>">
                            <div class="alert-content">
                                <div class="alert-header">
                                    <h4><?= htmlspecialchars($alert['productname']) ?></h4>
                                    <span class="alert-time">
                                        <?= date('M d, H:i', strtotime($alert['dateupdated'])) ?>
                                    </span>
                                </div>
                                <div class="alert-details">
                                    <span class="quantity">
                                        Qty: <?= number_format($alert['availablequantity']) ?>
                                    </span>
                                    <span class="status">
                                        <?= ucfirst(str_replace('_', ' ', $alert['alert_type'])) ?>
                                    </span>
                                </div>
                            </div>
                            <button class="btn-action" onclick="adjustStock('<?= $alert['productcode'] ?>')">
                                Take Action
                            </button>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="no-alerts">
                        <i class='bx bx-check-circle'></i>
                        <p>No stock alerts at this time</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Recent Stock Movements -->
        <div class="recent-movements-container insight-card">
            <div class="card-header">
                <h3>Recent Stock Movements</h3>
                <a href="stockactivitylog.php" class="view-all-link">View All</a>
            </div>
            <div class="card-content">
                <?php if (empty($recent_movements)): ?>
                    <p class="no-data">No recent movements</p>
                <?php else: ?>
                    <table class="insight-table movements-table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Product</th>
                                <th>Qty</th>
                                <th>Type</th>
                                <th>Reference</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recent_movements as $movement): ?>
                                <tr>
                                    <td><?= date('M d, H:i', strtotime($movement['dateencoded'])) ?></td>
                                    <td>
                                        <div class="product-info">
                                            <span class="product-code"><?= htmlspecialchars($movement['productcode']) ?></span>
                                            <span class="product-name"><?= htmlspecialchars($movement['productname']) ?></span>
                                        </div>
                                    </td>
                                    <td><?= number_format($movement['totalpacks']) // Assuming totalpacks is quantity ?></td>
                                    <td>
                                        <?php 
                                        $movement_type = $movement['movement_type'] ?? 'ADJUSTMENT';
                                        $badge_class = strtolower($movement_type);
                                        ?>
                                        <span class="movement-badge <?= $badge_class ?>"><?= $movement_type ?></span>
                                    </td>
                                    <td><?= htmlspecialchars($movement['reference_number'] ?? 'N/A') ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </div>

        <!-- Stock Value Trend Chart -->
        <div class="trend-chart-container">
            <div class="chart-header">
                <h3>Stock Value Trends</h3>
                <div class="chart-controls">
                    <select id="chartPeriod">
                        <option value="7">7 Days</option>
                        <option value="30" selected>30 Days</option>
                        <option value="90">90 Days</option>
                    </select>
                </div>
            </div>
            <div id="stockTrendsChart" class="trend-chart"></div>
        </div>
    </div>

    <!-- New Section: Additional Insights -->
    <div class="insights-grid">
        <!-- Top Products by Value -->
        <div class="insight-card">
            <div class="card-header">
                <h3>Top Products by Value</h3>
            </div>
            <div class="card-content">
                <?php if (empty($top_products)): ?>
                    <p class="no-data">No products available</p>
                <?php else: ?>
                    <table class="insight-table">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Quantity</th>
                                <th>Value</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($top_products as $product): ?>
                                <tr>
                                    <td>
                                        <div class="product-info">
                                            <span class="product-code"><?= $product['productcode'] ?></span>
                                            <span class="product-name"><?= $product['productname'] ?></span>
                                        </div>
                                    </td>
                                    <td><?= number_format($product['availablequantity']) ?></td>
                                    <td>₱<?= number_format($product['total_value'], 2) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </div>

        <!-- Category Distribution -->
        <div class="insight-card">
            <div class="card-header">
                <h3>Category Distribution</h3>
            </div>
            <div class="card-content">
                <?php if (empty($categories)): ?>
                    <p class="no-data">No category data available</p>
                <?php else: ?>
                    <div class="chart-container">
                        <canvas id="categoryChart" height="200"></canvas>
                    </div>
                    <div class="category-legend">
                        <?php foreach ($categories as $category): ?>
                            <div class="legend-item">
                                <span class="category-name"><?= $category['productcategory'] ?></span>
                                <span class="category-value">₱<?= number_format($category['total_value'], 2) ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Enhanced Inventory Section -->
    <div class="inventory-section theme-container">
        <div class="table-header">
            <div class="title-section">
                <h2>Inventory Management</h2>
                <div class="filters">
                    <select id="categoryFilter">
                        <option value="">All Categories</option>
                    </select>
                    <select id="stockStatus">
                        <option value="">All Status</option>
                        <option value="low">Low Stock</option>
                        <option value="out">Out of Stock</option>
                    </select>
                </div>
            </div>
            <div class="action-buttons">
                <!-- Export buttons -->
                <button class="btn export-btn" onclick="exportInventory('excel')">
                    <i class='bx bx-export'></i> Export to Excel
                </button>
                <button class="btn export-btn" onclick="exportInventory('pdf')">
                    <i class='bx bx-file'></i> Export to PDF
                </button>
                <a href="add.stockmovement.php" class="btn add-btn">
                    <i class='bx bx-plus'></i> New Stock Movement
                </a>
            </div>
        </div>

        <div class="table-responsive">
            <table id="inventoryTable" class="display" style="width:100%">
                <thead>
                    <tr>
                        <th>Product Code</th>
                        <th>Product Name</th>
                        <th>Category</th>
                        <th>Available Qty</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($inventory_data as $item): ?>
                        <tr class="<?= strtolower(str_replace(' ', '-', $item['stock_status'])) ?>">
                            <td><?= htmlspecialchars($item['productcode']) ?></td>
                            <td><?= htmlspecialchars($item['productname']) ?></td>
                            <td><?= htmlspecialchars($item['productcategory']) ?></td>
                            <td><?= number_format($item['availablequantity']) ?></td>
                            <td>
                                <span class="status-badge <?= strtolower(str_replace(' ', '-', $item['stock_status'])) ?>">
                                    <?= $item['stock_status'] ?>
                                </span>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <button class="btn-icon" onclick="viewDetails('<?= $item['productcode'] ?>')">
                                        <i class='bx bx-show'></i>
                                    </button>
                                    <button class="btn-icon" onclick="adjustStock('<?= $item['productcode'] ?>')">
                                        <i class='bx bx-edit'></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add these debug elements -->
<div class="debug-info" style="display: none;">
    <pre><?php echo json_encode($stock_data, JSON_PRETTY_PRINT); ?></pre>
</div>

<script>
    // Make functions globally available
    window.filterAlerts = function(type, event) {
        const alertsList = document.querySelector('.alerts-list');
        if (!alertsList) {
            console.error('Alerts list element not found');
            return;
        }

        const buttons = document.querySelectorAll('.btn-filter');
        buttons.forEach(btn => btn.classList.remove('active'));
        
        if (event?.target) {
            event.target.classList.add('active');
        }
        
        const alertItems = alertsList.querySelectorAll('.alert-item');
        alertItems.forEach(item => {
            if (type === 'critical') {
                item.style.display = item.classList.contains('out-of-stock') ? 'flex' : 'none';
            } else {
                item.style.display = 'flex';
            }
        });
    };

    // Make stock data available globally
    window.stockData = <?php 
        echo json_encode([
            'stats' => $stats,
            'trends' => $trends_data,
            'categories' => $categories,
            'alerts' => $alerts_data
        ]); 
    ?>;

    // Initialize when DOM is ready
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof initializeEnhancedStockPage === 'function') {
            initializeEnhancedStockPage();
        } else {
            console.error('Enhanced stock page initialization function not found');
        }
    });
</script>

<style>
/* Additional styles for new sections */
.insights-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 20px;
    margin: 20px 0;
}

/* New dashboard row layout */
.dashboard-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
    margin: 20px 0;
}

.insight-card {
    background-color: var(--card-bg);
    border-radius: 10px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    overflow: hidden;
}

.insight-card .card-header {
    padding: 15px;
    border-bottom: 1px solid var(--border-color);
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.insight-card .card-header h3 {
    margin: 0;
    font-size: 16px;
    font-weight: 600;
}

.insight-card .card-content {
    padding: 15px;
}

.chart-container {
    height: 250px;
    width: 100%;
    position: relative;
}

/* Fix for trend chart container */
.trend-chart-container {
    background-color: var(--card-bg);
    border-radius: 10px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    overflow: hidden;
    height: 100%;
}

.trend-chart-container .chart-header {
    padding: 15px;
    border-bottom: 1px solid var(--border-color);
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.trend-chart-container .chart-header h3 {
    margin: 0;
    font-size: 16px;
    font-weight: 600;
}

.trend-chart {
    height: 300px;
    width: 100%;
    padding: 10px 15px;
}

.stock-overview {
    display: flex;
    flex-direction: column;
    margin-bottom: 0;
}

.stock-alerts-container {
    background-color: var(--card-bg);
    border-radius: 10px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    overflow: hidden;
    height: 100%;
}

.insight-table {
    width: 100%;
    border-collapse: collapse;
}

.insight-table th, .insight-table td {
    padding: 10px;
    text-align: left;
    border-bottom: 1px solid var(--border-color);
}

.insight-table th {
    font-size: 13px;
    font-weight: 600;
    color: var(--muted-text);
}

.product-info {
    display: flex;
    flex-direction: column;
}

.product-code {
    font-weight: 600;
    color: var(--primary-color);
    font-size: 13px;
}

.product-name {
    font-size: 12px;
    color: var(--text-color);
}

.category-legend {
    display: flex;
    flex-wrap: wrap;
    margin-top: 15px;
    gap: 10px;
}

.legend-item {
    display: flex;
    flex-direction: column;
    font-size: 12px;
    padding: 5px 10px;
    border-radius: 4px;
    background-color: var(--light-bg);
    min-width: 80px;
}

.category-name {
    font-weight: 600;
}

.category-value {
    color: var(--success-color);
}

.no-data {
    text-align: center;
    padding: 20px;
    color: var(--muted-text);
    font-style: italic;
}

/* Loading overlay */
#loadingOverlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    z-index: 9999;
    display: none;
    justify-content: center;
    align-items: center;
    flex-direction: column;
}

#loadingOverlay .spinner {
    width: 50px;
    height: 50px;
    border: 5px solid #f3f3f3;
    border-top: 5px solid var(--primary-color);
    border-radius: 50%;
    animation: spin 1s linear infinite;
    margin-bottom: 15px;
}

#loadingOverlay p {
    color: white;
    font-size: 16px;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

@media (max-width: 992px) {
    .insights-grid, .dashboard-row {
        grid-template-columns: 1fr;
    }
}
</style>

<?php
// Create get_stock_trends.php endpoint inline
if (isset($_GET['refresh']) && $_GET['refresh'] == 1) {
    $days = isset($_GET['days']) ? intval($_GET['days']) : 30;
    
    // Get trends data
    $trends_query = "SELECT 
        DATE(dateupdated) as date,
        SUM(availablequantity * unit_price) as total_value
    FROM inventory
    WHERE dateupdated >= DATE_SUB(CURRENT_DATE, INTERVAL ? DAY)
    GROUP BY DATE(dateupdated)
    ORDER BY date ASC";
    
    try {
        $stmt = $conn->prepare($trends_query);
        $stmt->bind_param('i', $days);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $trends_data = [
            'dates' => [],
            'values' => []
        ];
        
        while ($row = $result->fetch_assoc()) {
            $trends_data['dates'][] = $row['date'];
            $trends_data['values'][] = (float)$row['total_value'];
        }
        
        header('Content-Type: application/json');
        echo json_encode($trends_data);
        exit;
    } catch (Exception $e) {
        error_log("Error fetching trends data: " . $e->getMessage());
        http_response_code(500);
        echo json_encode(['error' => 'Failed to fetch trends data']);
        exit;
    }
}

Page::render(ob_get_clean());
?>
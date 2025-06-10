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
    'alerts' => []
];

// Get data using transactions
try {
    $conn->begin_transaction();

    // Get stock statistics with more detailed queries
    $stats_query = "SELECT 
        COUNT(*) as total_products,
        SUM(CASE WHEN availablequantity <= 10 AND availablequantity > 0 THEN 1 ELSE 0 END) as low_stock,
        SUM(CASE WHEN availablequantity = 0 THEN 1 ELSE 0 END) as out_of_stock,
        SUM(availablequantity * unit_price) as total_value
    FROM inventory";

    // Get inventory data for table
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
    ORDER BY availablequantity ASC, dateupdated DESC";

    // New query for top products by value
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

    // New query for category distribution
    $category_query = "SELECT 
        productcategory, 
        COUNT(*) as count, 
        SUM(availablequantity) as total_quantity,
        SUM(availablequantity * unit_price) as total_value
    FROM inventory
    GROUP BY productcategory
    ORDER BY total_value DESC";

    $stats_result = $conn->query($stats_query);
    $inventory_result = $conn->query($inventory_query);
    $alerts_result = $conn->query($alerts_query);
    $top_products_result = $conn->query($top_products_query);
    $category_result = $conn->query($category_query);

    // Process results
    $stats = $stats_result->fetch_assoc();
    $inventory_data = [];
    $alerts_data = [];
    $top_products = [];
    $categories = [];

    while ($row = $inventory_result->fetch_assoc()) {
        $inventory_data[] = $row;
    }

    while ($row = $alerts_result->fetch_assoc()) {
        $alerts_data[] = $row;
    }

    while ($row = $top_products_result->fetch_assoc()) {
        $top_products[] = $row;
    }

    while ($row = $category_result->fetch_assoc()) {
        $categories[] = $row;
    }

    // Updated stock trends query
    $trends_query = "SELECT 
        DATE(dateupdated) as date,
        SUM(availablequantity * unit_price) as total_value
    FROM inventory
    WHERE dateupdated >= DATE_SUB(CURRENT_DATE, INTERVAL 30 DAY)
    GROUP BY DATE(dateupdated)
    ORDER BY date ASC";

    $trends_result = $conn->query($trends_query);
    $trends_data = [
        'dates' => [],
        'values' => []
    ];

    while ($row = $trends_result->fetch_assoc()) {
        $trends_data['dates'][] = $row['date'];
        $trends_data['values'][] = (float)$row['total_value'];
    }

    $stock_data = [
        'stats' => $stats,
        'inventory' => $inventory_data,
        'alerts' => $alerts_data,
        'top_products' => $top_products,
        'categories' => $categories,
        'trends' => $trends_data
    ];

    $conn->commit();
} catch (Exception $e) {
    $conn->rollback();
    error_log("Error fetching stock data: " . $e->getMessage());
    $stock_data = ['error' => $e->getMessage()];
}

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
                        <span class="last-updated">Last updated: <time id="lastUpdate"></time></span>
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

    <!-- Enhanced Stock Overview -->
    <div class="stock-overview">
        <!-- Real-time Alerts Section -->
        <div class="stock-alerts-container">
            <div class="alerts-header">
                <h3>Stock Alerts</h3>
                <div class="alert-controls">
                    <button class="btn-filter active" onclick="filterAlerts('all')">All</button>
                    <button class="btn-filter" onclick="filterAlerts('critical')">Critical</button>
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

        <!-- New: Stock Value Trend Chart -->
        <div class="trends-chart-container">
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
            <div id="stockTrendsChart"></div>
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
                    <div class="category-chart-container">
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
                <button class="btn export-btn" onclick="exportToExcel()">
                    <i class='bx bx-export'></i> Export to Excel
                </button>
                <button class="btn export-btn" onclick="exportToPDF()">
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
    // Make stock data available globally
    window.stockData = <?php echo json_encode($stock_data); ?>;
    window.initialData = <?php echo json_encode($initial_data); ?>;

    // Enhanced initialization
    document.addEventListener('DOMContentLoaded', function() {
        initializeEnhancedStockPage();
        updateLastRefreshTime();
        setupAutoRefresh();
        
        // Initialize stock trends chart
        initializeStockTrendsChart();
        
        // Initialize category distribution chart
        initializeCategoryChart();
    });
    
    function initializeStockTrendsChart() {
        if (!window.stockData?.trends?.dates?.length) {
            console.log('No stock trends data available');
            return;
        }
        
        const chartOptions = {
            series: [{
                name: 'Total Stock Value',
                data: window.stockData.trends.values
            }],
            chart: {
                type: 'area',
                height: 350,
                width: '100%',
                zoom: {
                    enabled: true
                },
                toolbar: {
                    show: true
                }
            },
            dataLabels: {
                enabled: false
            },
            stroke: {
                curve: 'smooth',
                width: 2
            },
            fill: {
                type: 'gradient',
                gradient: {
                    shadeIntensity: 1,
                    opacityFrom: 0.7,
                    opacityTo: 0.3,
                    stops: [0, 90, 100]
                }
            },
            xaxis: {
                categories: window.stockData.trends.dates,
                type: 'datetime',
                labels: {
                    datetimeFormatter: {
                        year: 'yyyy',
                        month: 'MMM \'yy',
                        day: 'dd MMM',
                        hour: 'HH:mm'
                    }
                }
            },
            yaxis: {
                labels: {
                    formatter: function(val) {
                        return '₱' + val.toLocaleString('en-PH', {
                            minimumFractionDigits: 2,
                            maximumFractionDigits: 2
                        });
                    }
                }
            },
            tooltip: {
                x: {
                    format: 'dd MMM yyyy'
                },
                y: {
                    formatter: function(val) {
                        return '₱' + val.toLocaleString('en-PH', {
                            minimumFractionDigits: 2,
                            maximumFractionDigits: 2
                        });
                    }
                }
            },
            theme: {
                mode: document.body.getAttribute('data-theme') === 'dark' ? 'dark' : 'light'
            }
        };

        const chart = new ApexCharts(document.querySelector("#stockTrendsChart"), chartOptions);
        chart.render();
    }
    
    function initializeCategoryChart() {
        const categoryData = <?php echo json_encode($categories); ?>;
        if (categoryData.length > 0) {
            const ctx = document.getElementById('categoryChart').getContext('2d');
            new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: categoryData.map(item => item.productcategory),
                    datasets: [{
                        data: categoryData.map(item => item.total_value),
                        backgroundColor: [
                            '#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b',
                            '#fd7e14', '#6f42c1', '#20c9a6', '#5a5c69', '#858796'
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false // Hide default legend, we'll use our custom one
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    let label = context.label || '';
                                    let value = context.raw || 0;
                                    return label + ': ₱' + Number(value).toLocaleString('en-US', {
                                        minimumFractionDigits: 2,
                                        maximumFractionDigits: 2
                                    });
                                }
                            }
                        }
                    }
                }
            });
        }
    }
</script>

<style>
/* Additional styles for new sections */
.insights-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
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
    height: 100%;
}

/* Fixed chart containers */
.trends-chart-container {
    flex: 1;
    height: 400px;
    background-color: var(--card-bg);
    border-radius: 10px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    padding: 15px;
    margin-left: 20px;
    overflow: hidden;
}

.trends-chart-container .chart-header {
    padding-bottom: 15px;
    border-bottom: 1px solid var(--border-color);
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 15px;
}

.trends-chart-container .chart-header h3 {
    margin: 0;
    font-size: 16px;
    font-weight: 600;
}

#stockTrendsChart {
    height: 300px;
    width: 100%;
}

.category-chart-container {
    height: 200px;
    width: 100%;
    position: relative;
    margin-bottom: 20px;
}

.stock-overview {
    display: flex;
    margin-bottom: 20px;
}

.stock-alerts-container {
    flex: 0 0 300px;
    background-color: var(--card-bg);
    border-radius: 10px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    padding: 0;
    overflow: hidden;
}

.insight-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 0;
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

@media (max-width: 992px) {
    .insights-grid {
        grid-template-columns: 1fr;
    }
    
    .stock-overview {
        flex-direction: column;
    }
    
    .trends-chart-container {
        margin-left: 0;
        margin-top: 20px;
    }
}
</style>

<?php
Page::render(ob_get_clean());
?>
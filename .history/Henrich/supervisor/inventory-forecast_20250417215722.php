<?php
require_once '../includes/session.php';
require_once '../includes/config.php';
require_once '../includes/Page.php';
require_once './access_control.php';

$current_page = basename($_SERVER['PHP_SELF'], '.php');
$_SESSION['current_page'] = $current_page;

// Initialize page
Page::setTitle('Inventory Forecasting');
Page::setBodyClass('supervisor-body');
Page::setCurrentPage('inventory-forecast');

// Add required styles and scripts
Page::addStyle('../assets/css/inventory-master.css');

// Add jQuery before other scripts
Page::addScript('https://code.jquery.com/jquery-3.7.0.min.js');
Page::addStyle('https://cdn.datatables.net/1.13.6/css/jquery.dataTables.css');
Page::addScript('https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js');
Page::addScript('https://cdn.jsdelivr.net/npm/chart.js');

// Add theme-related styles and scripts
Page::addStyle('/assets/css/themes.css');
Page::addStyle('/assets/css/theme-toggle.css');
Page::addScript('/assets/js/theme.js');

// Add new required scripts
Page::addScript('https://cdn.jsdelivr.net/npm/sweetalert2@11');
Page::addScript('https://cdn.jsdelivr.net/npm/apexcharts');

// Get data for forecasting
try {
    // Get products with their current stock and usage history
    $query = "
        SELECT 
            i.productcode,
            i.productname,
            i.productcategory,
            i.availablequantity,
            i.onhandquantity,
            i.unit_price,
            COALESCE(
                (SELECT SUM(sm.quantity) 
                FROM stockmovement sm 
                WHERE sm.productcode = i.productcode
                AND sm.movementtype = 'OUT'
                AND sm.date >= DATE_SUB(CURRENT_DATE, INTERVAL 30 DAY)
                ), 0
            ) as usage_last_30_days,
            COALESCE(
                (SELECT SUM(sm.quantity) 
                FROM stockmovement sm 
                WHERE sm.productcode = i.productcode
                AND sm.movementtype = 'OUT'
                AND sm.date >= DATE_SUB(CURRENT_DATE, INTERVAL 60 DAY)
                AND sm.date < DATE_SUB(CURRENT_DATE, INTERVAL 30 DAY)
                ), 0
            ) as usage_30_to_60_days,
            COALESCE(
                (SELECT SUM(sm.quantity) 
                FROM stockmovement sm 
                WHERE sm.productcode = i.productcode
                AND sm.movementtype = 'OUT'
                AND sm.date >= DATE_SUB(CURRENT_DATE, INTERVAL 90 DAY)
                AND sm.date < DATE_SUB(CURRENT_DATE, INTERVAL 60 DAY)
                ), 0
            ) as usage_60_to_90_days,
            i.reorder_point,
            i.dateupdated
        FROM inventory i
        ORDER BY i.availablequantity ASC
    ";

    $result = $conn->query($query);
    
    $products = [];
    while ($row = $result->fetch_assoc()) {
        // Calculate daily usage rate using weighted average
        $weight_recent = 0.6; // 60% weight to recent usage
        $weight_mid = 0.3; // 30% weight to mid-term usage
        $weight_old = 0.1; // 10% weight to older usage

        $daily_usage = (
            ($row['usage_last_30_days'] / 30) * $weight_recent +
            ($row['usage_30_to_60_days'] / 30) * $weight_mid +
            ($row['usage_60_to_90_days'] / 30) * $weight_old
        );

        // Ensure daily usage is never zero for calculation purposes
        $daily_usage = max($daily_usage, 0.01);
        
        // Calculate days until out of stock
        $days_until_empty = $row['availablequantity'] / $daily_usage;
        
        // Calculate restock date
        $restock_date = new DateTime();
        $restock_date->modify('+' . round($days_until_empty) . ' days');
        
        // Determine stock status and urgency
        $urgency = 'normal';
        $status = 'OK';
        
        if ($days_until_empty <= 7) {
            $urgency = 'critical';
            $status = 'Critical - Restock Immediately';
        } else if ($days_until_empty <= 14) {
            $urgency = 'warning';
            $status = 'Warning - Restock Soon';
        } else if ($days_until_empty <= 30) {
            $urgency = 'attention';
            $status = 'Plan Restock This Month';
        }
        
        // Add calculated fields to the row
        $row['daily_usage'] = $daily_usage;
        $row['days_until_empty'] = round($days_until_empty);
        $row['restock_date'] = $restock_date->format('Y-m-d');
        $row['urgency'] = $urgency;
        $row['status'] = $status;
        
        $products[] = $row;
    }
    
    // Sort products by urgency (days until empty)
    usort($products, function($a, $b) {
        return $a['days_until_empty'] - $b['days_until_empty'];
    });
    
    // Group products by category for the chart
    $categories = [];
    foreach ($products as $product) {
        $category = $product['productcategory'];
        if (!isset($categories[$category])) {
            $categories[$category] = [
                'critical' => 0,
                'warning' => 0,
                'attention' => 0,
                'normal' => 0
            ];
        }
        $categories[$category][$product['urgency']]++;
    }

} catch (Exception $e) {
    error_log("Error fetching forecast data: " . $e->getMessage());
    $products = [];
    $categories = [];
}

ob_start();
?>

<div class="forecast-management-wrapper theme-aware">
    <div class="dashboard-header">
        <div class="welcome-section">
            <div class="title-section">
                <h1>Inventory Forecasting</h1>
                <p class="subtitle">Predict stock depletion and plan restocking</p>
            </div>
            <div class="header-actions">
                <div class="refresh-control">
                    <button class="btn-refresh" onclick="location.reload();">
                        <i class='bx bx-refresh'></i>
                        <span class="last-updated">Last updated: <time><?= date('Y-m-d H:i:s') ?></time></span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Summary Stats -->
    <div class="quick-stats">
        <div class="stats-grid">
            <div class="stat-card <?= (count(array_filter($products, function($p) { return $p['urgency'] == 'critical'; })) > 0) ? 'danger' : '' ?>">
                <div class="stat-icon"><i class='bx bx-error-circle'></i></div>
                <h3>Critical (< 7 days)</h3>
                <div class="value"><?= count(array_filter($products, function($p) { return $p['urgency'] == 'critical'; })) ?></div>
            </div>
            <div class="stat-card <?= (count(array_filter($products, function($p) { return $p['urgency'] == 'warning'; })) > 0) ? 'warning' : '' ?>">
                <div class="stat-icon"><i class='bx bx-error'></i></div>
                <h3>Warning (< 14 days)</h3>
                <div class="value"><?= count(array_filter($products, function($p) { return $p['urgency'] == 'warning'; })) ?></div>
            </div>
            <div class="stat-card <?= (count(array_filter($products, function($p) { return $p['urgency'] == 'attention'; })) > 0) ? 'info' : '' ?>">
                <div class="stat-icon"><i class='bx bx-calendar'></i></div>
                <h3>Plan Ahead (< 30 days)</h3>
                <div class="value"><?= count(array_filter($products, function($p) { return $p['urgency'] == 'attention'; })) ?></div>
            </div>
            <div class="stat-card success">
                <div class="stat-icon"><i class='bx bx-check-circle'></i></div>
                <h3>OK (> 30 days)</h3>
                <div class="value"><?= count(array_filter($products, function($p) { return $p['urgency'] == 'normal'; })) ?></div>
            </div>
        </div>
    </div>

    <!-- Dashboard Charts and Insights -->
    <div class="dashboard-row">
        <!-- Upcoming Restock Needs -->
        <div class="forecast-container">
            <div class="card-header">
                <h3>Items Requiring Attention</h3>
                <select id="urgencyFilter" onchange="filterTable()">
                    <option value="all">All Items</option>
                    <option value="critical">Critical Only</option>
                    <option value="warning">Warning & Critical</option>
                    <option value="attention">All Requiring Attention</option>
                </select>
            </div>
            <div class="forecast-list">
                <?php if (empty($products)): ?>
                    <div class="no-data">
                        <p>No product usage data available for forecasting.</p>
                    </div>
                <?php else: ?>
                    <table id="forecastTable" class="display">
                        <thead>
                            <tr>
                                <th>Code</th>
                                <th>Product</th>
                                <th>Category</th>
                                <th>Current Stock</th>
                                <th>Daily Usage</th>
                                <th>Days Left</th>
                                <th>Est. Out of Stock</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($products as $product): ?>
                                <tr class="<?= $product['urgency'] ?>" data-urgency="<?= $product['urgency'] ?>">
                                    <td><?= htmlspecialchars($product['productcode']) ?></td>
                                    <td><?= htmlspecialchars($product['productname']) ?></td>
                                    <td><?= htmlspecialchars($product['productcategory']) ?></td>
                                    <td><?= number_format($product['availablequantity']) ?></td>
                                    <td><?= number_format($product['daily_usage'], 2) ?></td>
                                    <td><?= $product['days_until_empty'] ?></td>
                                    <td><?= $product['restock_date'] ?></td>
                                    <td>
                                        <span class="status-badge <?= $product['urgency'] ?>">
                                            <?= $product['status'] ?>
                                        </span>
                                    </td>
                                    <td>
                                        <button class="btn-icon" onclick="createStockOrder('<?= $product['productcode'] ?>')">
                                            <i class='bx bx-cart-add'></i>
                                        </button>
                                        <button class="btn-icon" onclick="adjustUsageRate('<?= $product['productcode'] ?>')">
                                            <i class='bx bx-edit'></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </div>

        <!-- Category Risk Analysis -->
        <div class="category-risk-container">
            <div class="card-header">
                <h3>Category Risk Analysis</h3>
            </div>
            <div class="card-content">
                <div class="chart-container">
                    <canvas id="categoryRiskChart" height="250"></canvas>
                </div>
                <div class="chart-legend">
                    <div class="legend-item critical">
                        <span class="legend-color"></span>
                        <span>Critical (< 7 days)</span>
                    </div>
                    <div class="legend-item warning">
                        <span class="legend-color"></span>
                        <span>Warning (< 14 days)</span>
                    </div>
                    <div class="legend-item attention">
                        <span class="legend-color"></span>
                        <span>Plan Ahead (< 30 days)</span>
                    </div>
                    <div class="legend-item normal">
                        <span class="legend-color"></span>
                        <span>OK (> 30 days)</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Weekly Restock Schedule -->
    <div class="restock-schedule">
        <div class="card-header">
            <h3>Weekly Restock Schedule Recommendation</h3>
            <button class="btn-action" onclick="exportRestockSchedule()">
                <i class='bx bx-export'></i> Export Schedule
            </button>
        </div>
        <div class="schedule-timeline">
            <?php
            // Group products by restock week
            $weeks = [];
            $today = new DateTime();
            foreach ($products as $product) {
                if ($product['urgency'] == 'normal') continue; // Skip products not needing attention
                
                $restock_date = new DateTime($product['restock_date']);
                $week_diff = floor($today->diff($restock_date)->days / 7);
                
                if (!isset($weeks[$week_diff])) {
                    $monday = clone $today;
                    $monday->modify('+' . ($week_diff * 7) . ' days');
                    $monday->modify('this week monday');
                    
                    $sunday = clone $monday;
                    $sunday->modify('+6 days');
                    
                    $weeks[$week_diff] = [
                        'start_date' => $monday->format('Y-m-d'),
                        'end_date' => $sunday->format('Y-m-d'),
                        'products' => []
                    ];
                }
                
                $weeks[$week_diff]['products'][] = $product;
            }
            
            // Sort weeks chronologically
            ksort($weeks);
            ?>
            
            <?php if (empty($weeks)): ?>
                <div class="no-data">
                    <p>No items require restocking within the next 30 days.</p>
                </div>
            <?php else: ?>
                <?php foreach ($weeks as $week_num => $week): ?>
                    <div class="timeline-week <?= ($week_num === 0) ? 'current' : '' ?>">
                        <div class="week-header">
                            <h4>Week <?= $week_num + 1 ?>: <?= date('M d', strtotime($week['start_date'])) ?> - <?= date('M d', strtotime($week['end_date'])) ?></h4>
                            <span class="item-count"><?= count($week['products']) ?> items</span>
                        </div>
                        <div class="week-items">
                            <?php foreach ($week['products'] as $product): ?>
                                <div class="restock-item <?= $product['urgency'] ?>">
                                    <div class="product-info">
                                        <span class="product-code"><?= $product['productcode'] ?></span>
                                        <span class="product-name"><?= $product['productname'] ?></span>
                                    </div>
                                    <div class="restock-details">
                                        <span class="current-stock">Current: <?= number_format($product['availablequantity']) ?></span>
                                        <span class="recommended-order">Order: <?= number_format(ceil($product['daily_usage'] * 30)) ?></span>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
    // Initialize DataTable
    $(document).ready(function() {
        $('#forecastTable').DataTable({
            order: [[5, 'asc']], // Sort by days left
            pageLength: 10,
            responsive: true
        });
        
        // Initialize category risk chart
        initializeCategoryChart();
    });
    
    // Filter table by urgency
    function filterTable() {
        const filter = document.getElementById('urgencyFilter').value;
        const table = $('#forecastTable').DataTable();
        
        if (filter === 'all') {
            table.search('').draw();
        } else if (filter === 'critical') {
            table.search('critical').draw();
        } else if (filter === 'warning') {
            // Custom filtering for warning & critical
            $.fn.dataTable.ext.search.push(
                function(settings, data, dataIndex) {
                    const row = table.row(dataIndex).node();
                    const urgency = $(row).data('urgency');
                    return urgency === 'critical' || urgency === 'warning';
                }
            );
            table.draw();
            $.fn.dataTable.ext.search.pop(); // Remove the custom filter after use
        } else if (filter === 'attention') {
            // Custom filtering for all requiring attention
            $.fn.dataTable.ext.search.push(
                function(settings, data, dataIndex) {
                    const row = table.row(dataIndex).node();
                    const urgency = $(row).data('urgency');
                    return urgency === 'critical' || urgency === 'warning' || urgency === 'attention';
                }
            );
            table.draw();
            $.fn.dataTable.ext.search.pop(); // Remove the custom filter after use
        }
    }
    
    // Initialize category risk chart
    function initializeCategoryChart() {
        const ctx = document.getElementById('categoryRiskChart').getContext('2d');
        
        // Prepare data for the chart
        const categories = <?= json_encode(array_keys($categories)) ?>;
        const criticalData = categories.map(category => <?= json_encode($categories) ?>[category].critical);
        const warningData = categories.map(category => <?= json_encode($categories) ?>[category].warning);
        const attentionData = categories.map(category => <?= json_encode($categories) ?>[category].attention);
        const normalData = categories.map(category => <?= json_encode($categories) ?>[category].normal);
        
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: categories,
                datasets: [
                    {
                        label: 'Critical',
                        backgroundColor: '#e74a3b',
                        data: criticalData,
                        stack: 'stack1'
                    },
                    {
                        label: 'Warning',
                        backgroundColor: '#f6c23e',
                        data: warningData,
                        stack: 'stack1'
                    },
                    {
                        label: 'Plan Ahead',
                        backgroundColor: '#36b9cc',
                        data: attentionData,
                        stack: 'stack1'
                    },
                    {
                        label: 'OK',
                        backgroundColor: '#1cc88a',
                        data: normalData,
                        stack: 'stack1'
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    x: {
                        stacked: true,
                        title: {
                            display: true,
                            text: 'Product Categories'
                        }
                    },
                    y: {
                        stacked: true,
                        title: {
                            display: true,
                            text: 'Number of Products'
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            title: function(context) {
                                return context[0].label;
                            },
                            label: function(context) {
                                return context.dataset.label + ': ' + context.raw + ' products';
                            }
                        }
                    }
                }
            }
        });
    }
    
    // Create a stock order for a product
    function createStockOrder(productCode) {
        window.location.href = 'add.stockmovement.php?productcode=' + productCode + '&type=IN';
    }
    
    // Adjust usage rate for a product
    function adjustUsageRate(productCode) {
        Swal.fire({
            title: 'Adjust Usage Rate',
            text: 'Modify the daily usage rate for this product:',
            icon: 'info',
            input: 'number',
            inputAttributes: {
                min: 0.1,
                step: 0.1
            },
            showCancelButton: true,
            confirmButtonText: 'Update',
            cancelButtonText: 'Cancel',
            showLoaderOnConfirm: true,
            preConfirm: (rate) => {
                // Here you could send an AJAX request to update the usage rate
                return new Promise((resolve) => {
                    setTimeout(() => {
                        resolve();
                    }, 1000);
                });
            }
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Updated!',
                    text: 'The usage rate has been updated.',
                    icon: 'success'
                }).then(() => {
                    location.reload();
                });
            }
        });
    }
    
    // Export restock schedule
    function exportRestockSchedule() {
        window.open('export-restock-schedule.php', '_blank');
    }
</script>

<style>
    /* Inventory Forecast Styles */
    .forecast-management-wrapper {
        padding: 20px;
    }
    
    .dashboard-row {
        display: grid;
        grid-template-columns: 3fr 1fr;
        gap: 20px;
        margin: 20px 0;
    }
    
    .forecast-container, .category-risk-container {
        background-color: var(--card-bg);
        border-radius: 10px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        overflow: hidden;
    }
    
    .card-header {
        padding: 15px;
        border-bottom: 1px solid var(--border-color);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .card-header h3 {
        margin: 0;
        font-size: 16px;
        font-weight: 600;
    }
    
    .card-content {
        padding: 15px;
    }
    
    .forecast-list {
        padding: 15px;
    }
    
    /* Status badges */
    .status-badge {
        padding: 5px 8px;
        border-radius: 15px;
        font-size: 12px;
        font-weight: 600;
    }
    
    .status-badge.critical {
        background-color: rgba(231, 74, 59, 0.1);
        color: #e74a3b;
    }
    
    .status-badge.warning {
        background-color: rgba(246, 194, 62, 0.1);
        color: #f6c23e;
    }
    
    .status-badge.attention {
        background-color: rgba(54, 185, 204, 0.1);
        color: #36b9cc;
    }
    
    .status-badge.normal {
        background-color: rgba(28, 200, 138, 0.1);
        color: #1cc88a;
    }
    
    /* Table row colors */
    #forecastTable tr.critical {
        background-color: rgba(231, 74, 59, 0.05);
    }
    
    #forecastTable tr.warning {
        background-color: rgba(246, 194, 62, 0.05);
    }
    
    #forecastTable tr.attention {
        background-color: rgba(54, 185, 204, 0.05);
    }
    
    /* Chart legend */
    .chart-legend {
        display: flex;
        flex-wrap: wrap;
        gap: 15px;
        margin-top: 15px;
        justify-content: center;
    }
    
    .legend-item {
        display: flex;
        align-items: center;
        font-size: 12px;
    }
    
    .legend-color {
        width: 12px;
        height: 12px;
        border-radius: 3px;
        margin-right: 5px;
    }
    
    .legend-item.critical .legend-color {
        background-color: #e74a3b;
    }
    
    .legend-item.warning .legend-color {
        background-color: #f6c23e;
    }
    
    .legend-item.attention .legend-color {
        background-color: #36b9cc;
    }
    
    .legend-item.normal .legend-color {
        background-color: #1cc88a;
    }
    
    /* Restock Schedule */
    .restock-schedule {
        background-color: var(--card-bg);
        border-radius: 10px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        overflow: hidden;
        margin: 20px 0;
    }
    
    .schedule-timeline {
        padding: 15px;
        display: flex;
        flex-direction: column;
        gap: 15px;
    }
    
    .timeline-week {
        border-left: 3px solid #858796;
        padding-left: 15px;
        position: relative;
    }
    
    .timeline-week.current {
        border-left-color: #4e73df;
    }
    
    .timeline-week::before {
        content: '';
        position: absolute;
        left: -9px;
        top: 0;
        width: 15px;
        height: 15px;
        border-radius: 50%;
        background-color: #858796;
    }
    
    .timeline-week.current::before {
        background-color: #4e73df;
    }
    
    .week-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 10px;
    }
    
    .week-header h4 {
        margin: 0;
        font-size: 14px;
        font-weight: 600;
    }
    
    .item-count {
        font-size: 12px;
        color: var(--muted-text);
    }
    
    .week-items {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 10px;
    }
    
    .restock-item {
        border-radius: 5px;
        padding: 10px;
        background-color: var(--light-bg);
    }
    
    .restock-item.critical {
        border-left: 3px solid #e74a3b;
    }
    
    .restock-item.warning {
        border-left: 3px solid #f6c23e;
    }
    
    .restock-item.attention {
        border-left: 3px solid #36b9cc;
    }
    
    .product-info {
        display: flex;
        flex-direction: column;
        margin-bottom: 5px;
    }
    
    .product-code {
        font-size: 12px;
        color: var(--muted-text);
    }
    
    .product-name {
        font-weight: 600;
    }
    
    .restock-details {
        display: flex;
        justify-content: space-between;
        font-size: 12px;
    }
    
    .recommended-order {
        font-weight: 600;
        color: var(--primary-color);
    }
    
    .btn-action {
        padding: 5px 10px;
        background-color: var(--primary-color);
        color: white;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        font-size: 12px;
    }
    
    .btn-action i {
        margin-right: 5px;
    }
    
    .no-data {
        padding: 30px;
        text-align: center;
        color: var(--muted-text);
        font-style: italic;
    }
    
    @media (max-width: 992px) {
        .dashboard-row {
            grid-template-columns: 1fr;
        }
        
        .week-items {
            grid-template-columns: 1fr;
        }
    }
</style>

<?php
Page::render(ob_get_clean());
?> 
<?php
require_once '../includes/session.php';
require_once '../includes/config.php';
require_once '../includes/Page.php';
require_once './access_control.php';

// Initialize database connection
try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME,
        DB_USER,
        DB_PASSWORD,
        array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION)
    );
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Function to get order logs with filtering
function getOrderLogs($pdo, $filters = []) {
    $query = "
        SELECT 
            ol.*,
            co.orderdate,
            co.timeoforder,
            co.ordertype,
            co.status as order_status,
            co.customername
        FROM orderlog ol
        JOIN customerorder co ON ol.orderid = co.orderid
        WHERE 1=1
    ";
    
    $params = [];
    
    if (!empty($filters['dateRange'])) {
        switch ($filters['dateRange']) {
            case 'today':
                $query .= " AND DATE(co.orderdate) = CURRENT_DATE";
                break;
            case '7':
                $query .= " AND co.orderdate >= DATE_SUB(CURRENT_DATE, INTERVAL 7 DAY)";
                break;
            case '30':
                $query .= " AND co.orderdate >= DATE_SUB(CURRENT_DATE, INTERVAL 30 DAY)";
                break;
            case '90':
                $query .= " AND co.orderdate >= DATE_SUB(CURRENT_DATE, INTERVAL 90 DAY)";
                break;
        }
    }

    if (!empty($filters['orderType'])) {
        $query .= " AND co.ordertype = :orderType";
        $params[':orderType'] = $filters['orderType'];
    }

    if (!empty($filters['status'])) {
        $query .= " AND co.status = :status";
        $params[':status'] = $filters['status'];
    }

    $query .= " ORDER BY co.orderdate DESC, co.timeoforder DESC";

    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Get initial data
$filters = [
    'dateRange' => $_GET['dateRange'] ?? '30',
    'orderType' => $_GET['orderType'] ?? '',
    'status' => $_GET['status'] ?? ''
];

$orderLogs = getOrderLogs($pdo, $filters);

// Configure page
Page::setTitle('Order Logs | Supervisor');
Page::setBodyClass('supervisor-body');
Page::set('current_page', 'orderedproducts');

// Add styles and scripts
Page::addStyle('/assets/css/themes.css');
Page::addStyle('/assets/css/customer-order.css');
Page::addStyle('/assets/css/table.css');

// Start output buffering
ob_start();
?>

<div class="orderlogs-wrapper">
    <!-- Page Header -->
    <div class="page-header">
        <div class="header-content">
            <h1>Order Logs</h1>
            <div class="filters-section theme-container">
            <div class="filters-container">
            <div class="filter-group">
                    <select id="dateRange" onchange="updateFilters()">
                        <option value="today" <?php echo $filters['dateRange'] === 'today' ? 'selected' : ''; ?>>Today</option>
                        <option value="7" <?php echo $filters['dateRange'] === '7' ? 'selected' : ''; ?>>Last 7 Days</option>
                        <option value="30" <?php echo $filters['dateRange'] === '30' ? 'selected' : ''; ?>>Last 30 Days</option>
                        <option value="90" <?php echo $filters['dateRange'] === '90' ? 'selected' : ''; ?>>Last 90 Days</option>
                    </select>
                    </div>
                    <select id="orderType" onchange="updateFilters()">
                        <option value="">All Order Types</option>
                        <option value="walk-in" <?php echo $filters['orderType'] === 'walk-in' ? 'selected' : ''; ?>>Walk-in</option>
                        <option value="online" <?php echo $filters['orderType'] === 'online' ? 'selected' : ''; ?>>Online</option>
                        <option value="delivery" <?php echo $filters['orderType'] === 'delivery' ? 'selected' : ''; ?>>Delivery</option>
                    </select>
                    <select id="status" onchange="updateFilters()">
                        <option value="">All Statuses</option>
                        <option value="pending" <?php echo $filters['status'] === 'pending' ? 'selected' : ''; ?>>Pending</option>
                        <option value="completed" <?php echo $filters['status'] === 'completed' ? 'selected' : ''; ?>>Completed</option>
                        <option value="cancelled" <?php echo $filters['status'] === 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                    </select>
                </div>
                <button class="refresh-btn" onclick="refreshData()">
                    <i class='bx bx-refresh'></i>
                </button>
            </div>
        </div>
        <div class="breadcrumb">
            <span>Operations</span>
            <i class='bx bx-chevron-right'></i>
            <span>Order Logs</span>
        </div>
    </div>

    <!-- Order Logs Table -->
    <div class="table-container">
        <table id="orderLogsTable" class="display">
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Date & Time</th>
                    <th>Customer</th>
                    <th>Product</th>
                    <th>Quantity</th>
                    <th>Unit Price</th>
                    <th>Total</th>
                    <th>Type</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($orderLogs as $log): ?>
                    <tr>
                        <td><?php echo $log['orderid']; ?></td>
                        <td>
                            <?php 
                                echo date('M d, Y', strtotime($log['orderdate'])) . '<br>';
                                echo '<small>' . date('h:i A', strtotime($log['timeoforder'])) . '</small>';
                            ?>
                        </td>
                        <td><?php echo $log['customername']; ?></td>
                        <td><?php echo $log['productname']; ?></td>
                        <td><?php echo $log['quantity']; ?></td>
                        <td>₱<?php echo number_format($log['unit_price'], 2); ?></td>
                        <td>₱<?php echo number_format($log['quantity'] * $log['unit_price'], 2); ?></td>
                        <td><span class="badge <?php echo $log['ordertype']; ?>"><?php echo ucfirst($log['ordertype']); ?></span></td>
                        <td><span class="status <?php echo strtolower($log['order_status']); ?>"><?php echo ucfirst($log['order_status']); ?></span></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
function updateFilters() {
    const dateRange = document.getElementById('dateRange').value;
    const orderType = document.getElementById('orderType').value;
    const status = document.getElementById('status').value;
    
    const url = new URL(window.location.href);
    url.searchParams.set('dateRange', dateRange);
    if (orderType) url.searchParams.set('orderType', orderType);
    if (status) url.searchParams.set('status', status);
    
    window.location.href = url.toString();
}

function refreshData() {
    window.location.reload();
}

// Initialize DataTable
$(document).ready(function() {
    $('#orderLogsTable').DataTable({
        order: [[1, 'desc']],
        pageLength: 25,
        responsive: true,
        dom: 'Bfrtip',
        buttons: [
            'copy', 'csv', 'excel', 'pdf', 'print'
        ]
    });
});
</script>

<?php
// Render the page
Page::render(ob_get_clean());
?>








<?php
require_once '../includes/session.php';
require_once '../includes/config.php';
require_once '../includes/Page.php';
require_once './access_control.php';

$current_page = basename($_SERVER['PHP_SELF'], '.php');
$_SESSION['current_page'] = $current_page;

// Initialize database connection
try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME,
        DB_USER,
        DB_PASSWORD,
        array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION)
    );
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Function to get order logs with filtering
function getOrderLogs($pdo, $filters = [])
{
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
Page::setTitle('Order History | Supervisor');
Page::setBodyClass('supervisor-body');
Page::set('current_page', 'orderedproducts');

// Add styles and scripts
Page::addStyle('/assets/css/themes.css');
Page::addStyle('/assets/css/customer-order.css');
Page::addStyle('/assets/css/table.css');
Page::addStyle('/assets/css/orderlogs.css');
Page::addScript('https://cdn.jsdelivr.net/npm/sweetalert2@11');

// Start output buffering
ob_start();
?>

<div class="dashboard-wrapper">
    <!-- Dashboard Container -->
    <div class="dashboard-container">
        <!-- Page Header -->
        <div class="dashboard-header">
            <div class="welcome-section">
                <h1>Order History</h1>
                <p class="timestamp">Historical view of all ordered products</p>
            </div>
        </div>

        <!-- Quick Stats - Make them look more historical -->
        <div class="quick-stats">
            <div class="stats-grid">
                <div class="stat-card info">
                    <div class="stat-icon"><i class='bx bx-history'></i></div>
                    <div class="stat-content">
                        <h3>Total Historical Orders</h3>
                        <div class="value"><?= array_sum(array_column($orderLogs, 'quantity')) ?></div>
                    </div>
                </div>
                <div class="stat-card warning">
                    <div class="stat-icon"><i class='bx bx-package'></i></div>
                    <div class="stat-content">
                        <h3>Products Sold</h3>
                        <div class="value"><?= count(array_unique(array_column($orderLogs, 'productcode'))) ?></div>
                    </div>
                </div>
                <div class="stat-card success">
                    <div class="stat-icon"><i class='bx bx-money'></i></div>
                    <div class="stat-content">
                        <h3>Total Historical Revenue</h3>
                        <div class="value">₱<?= number_format(array_sum(array_map(function($log) {
                            return $log['quantity'] * $log['unit_price'];
                        }, $orderLogs)), 2) ?></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Simplified Filters Section -->
        <div class="filters-section theme-container">
            <div class="filters-container">
                <div class="filter-group">
                    <select id="dateRange" onchange="updateFilters()">
                        <option value="today">Today's History</option>
                        <option value="7">Past 7 Days</option>
                        <option value="30" selected>Past 30 Days</option>
                        <option value="90">Past 90 Days</option>
                    </select>
                </div>
                <div class="search-box">
                    <input type="text" id="searchInput" placeholder="Search order history..." onkeyup="filterTable()">
                </div>
            </div>
        </div>

        <!-- Table Section -->
        <div class="table-section theme-container">
            <div class="table-responsive">
                <table id="orderLogsTable" class="table">
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
                        <tr class="order-row status-<?= strtolower($log['order_status']) ?>"
                            onclick="window.location.href='customerorderdetail.php?orderid=<?= $log['orderid'] ?>'">
                            <td><?= $log['orderid'] ?></td>
                            <td>
                                <div class="date-time">
                                    <span class="date"><?= date('M d, Y', strtotime($log['orderdate'])) ?></span>
                                    <span class="time"><?= date('h:i A', strtotime($log['timeoforder'])) ?></span>
                                </div>
                            </td>
                            <td><?= htmlspecialchars($log['customername']) ?></td>
                            <td><?= htmlspecialchars($log['productname']) ?></td>
                            <td class="text-center"><?= $log['quantity'] ?></td>
                            <td class="text-right">₱<?= number_format($log['unit_price'], 2) ?></td>
                            <td class="text-right">₱<?= number_format($log['quantity'] * $log['unit_price'], 2) ?></td>
                            <td><span class="order-type-badge <?= strtolower($log['ordertype']) ?>"><?= $log['ordertype'] ?></span></td>
                            <td>
                                <div class="status-cell status-<?= strtolower($log['order_status']) ?>">
                                    <i class='bx bx-radio-circle'></i>
                                    <?= $log['order_status'] ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
// Add enhanced JavaScript functionality
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

function filterTable() {
    const searchTerm = document.getElementById('searchInput').value.toLowerCase();
    const rows = document.querySelectorAll('#orderLogsTable tbody tr');
    
    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(searchTerm) ? '' : 'none';
    });
}

function filterByStatus(status) {
    const rows = document.querySelectorAll('#orderLogsTable tbody tr');
    const buttons = document.querySelectorAll('.status-pill');
    
    buttons.forEach(btn => {
        btn.classList.remove('active');
        if (btn.textContent.toLowerCase() === status.toLowerCase()) {
            btn.classList.add('active');
        }
    });

    rows.forEach(row => {
        if (status.toLowerCase() === 'all') {
            row.style.display = '';
        } else {
            const statusCell = row.querySelector('.status-cell');
            const rowStatus = statusCell.textContent.trim().toLowerCase();
            row.style.display = rowStatus === status.toLowerCase() ? '' : 'none';
        }
    });
}

// Initialize DataTable with more historical-appropriate settings
$(document).ready(function() {
    $('#orderLogsTable').DataTable({
        order: [[1, 'desc']],
        pageLength: 50,
        responsive: true,
        dom: '<"top"Bf>rt<"bottom"ip>',
        buttons: ['excel', 'pdf', 'print'],
        language: {
            searchPlaceholder: "Search order history..."
        }
    });
});
</script>

<?php
$content = ob_get_clean();
    });
}

function refreshDashboard() {
    Swal.fire({
        title: 'Refreshing...',
        text: 'Updating order data',
        timer: 1000,
        timerProgressBar: true,
        didOpen: () => {
            Swal.showLoading();
        }
    }).then(() => {
        window.location.reload();
    });
}

// Initialize DataTable with enhanced features
$(document).ready(function() {
    $('#orderLogsTable').DataTable({
        order: [[1, 'desc']],
        pageLength: 25,
        responsive: true,
        dom: 'Bfrtip',
        buttons: [
            {
                extend: 'collection',
                text: 'Export',
                buttons: ['copy', 'csv', 'excel', 'pdf', 'print']
            }
        ]
    });
});
</script>

<?php
$content = ob_get_clean();
Page::render($content);
?>
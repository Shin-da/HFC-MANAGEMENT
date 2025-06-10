<?php
require_once '../includes/session.php';
require_once '../includes/config.php';
require_once '../includes/Page.php';
$current_page = basename($_SERVER['PHP_SELF'], '.php');
$_SESSION['current_page'] = $current_page;
// Initialize page
Page::setTitle('Customer Orders');
Page::setBodyClass('supervisor-body');
Page::set('current_page', 'customerorder');

// Add theme and core styles
Page::addStyle('/assets/css/themes.css');
Page::addStyle('/assets/css/customer-order.css');

Page::addStyle('/assets/css/customer-pages.css');
Page::addStyle('/assets/css/table.css');

// Update the script loading section
Page::addScript('https://cdn.jsdelivr.net/npm/chart.js'); // Add Chart.js first
Page::addScript('/assets/js/order-charts.js'); // Then add our charts script
Page::addScript('/assets/js/customer-orders.js');

// Initialize order data
$order_data = [
    'stats' => [
        'total_orders' => 0,
        'pending_orders' => 0,
        'today_orders' => 0,
        'total_revenue' => 0.00
    ],
    'orders' => []
];

function getOrderTrendsData($conn)
{
    $result = $conn->query("
        SELECT 
            DATE(orderdate) as date,
            COUNT(*) as order_count,
            SUM(ordertotal) as daily_total,
            COUNT(CASE WHEN status = 'Completed' THEN 1 END) as completed,
            COUNT(CASE WHEN status = 'Pending' THEN 1 END) as pending
        FROM customerorder 
        WHERE orderdate >= DATE_SUB(CURRENT_DATE, INTERVAL 30 DAY)
        GROUP BY DATE(orderdate)
        ORDER BY date ASC
    ");

    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }

    return [
        'labels' => array_map(fn($row) => date('M d', strtotime($row['date'])), $data),
        'orders' => array_column($data, 'order_count'),
        'revenue' => array_column($data, 'daily_total'),
        'completed' => array_column($data, 'completed'),
        'pending' => array_column($data, 'pending')
    ];
}

function getOrderTypeData($conn)
{
    $result = $conn->query("
        SELECT 
            ordertype,
            COUNT(*) as count,
            SUM(ordertotal) as total
        FROM customerorder
        WHERE orderdate >= DATE_SUB(CURRENT_DATE, INTERVAL 30 DAY)
        GROUP BY ordertype
    ");

    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }

    return [
        'labels' => array_column($data, 'ordertype'),
        'counts' => array_column($data, 'count'),
        'totals' => array_column($data, 'total')
    ];
}

try {
    $conn->begin_transaction();

    // Get order statistics
    $order_data['stats'] = $conn->query("
        SELECT 
            COUNT(*) as total_orders,
            SUM(CASE WHEN status = 'Pending' THEN 1 ELSE 0 END) as pending_orders,
            SUM(CASE WHEN DATE(orderdate) = CURDATE() THEN 1 ELSE 0 END) as today_orders,
            SUM(CASE WHEN status = 'Completed' THEN ordertotal ELSE 0 END) as total_revenue
        FROM customerorder
    ")->fetch_assoc();

    // Define pagination variables at the top of the file
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 500; // Changed default to 500
    $start = ($page - 1) * $limit;

    // Get total records for pagination
    $totalRecords = $conn->query("SELECT COUNT(*) FROM customerorder")->fetch_row()[0];
    $totalPages = ceil($totalRecords / $limit);

    // Get all orders with proper sorting
    $result = $conn->query("SELECT * FROM customerorder 
                       ORDER BY 
                       CASE status 
                           WHEN 'Pending' THEN 1
                           WHEN 'Processing' THEN 2
                           WHEN 'Completed' THEN 3
                           WHEN 'Cancelled' THEN 4
                       END,
                       orderdate DESC, 
                       timeoforder DESC,
                       orderid DESC 
                       LIMIT $start, $limit");

    // Store results in array instead of keeping result set open
    $orders = [];
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $orders[] = $row;
        }
        $result->free();
    }

    // Add to your existing order_data array
    $order_data['charts'] = [
        'trends' => getOrderTrendsData($conn),
        'types' => getOrderTypeData($conn)
    ];

    // Add debug logging
    error_log('Order Data: ' . json_encode($order_data));

    ob_start();
?>

    <div class="dashboard-wrapper" style="width: 100%;">

        <!-- Dashboard Container -->
        <div class="dashboard-container">
            <!-- Page Header -->
            <div class="dashboard-header">
                <div class="welcome-section">
                    <h1>Customer Orders</h1>
                    <p class="timestamp"><?= date('l, F j, Y') ?></p>
                </div>
                <button class="refresh-btn" onclick="refreshDashboard()">
                    <i class='bx bx-refresh'></i>
                </button>

            </div>
            <!-- Stats Container -->
            <div class="quick-stats">
                <div class="stats-grid">
                    <div class="stat-card info">
                        <div class="stat-icon"> <i class='bx bx-package'></i> </div>
                        <div class="stat-content">
                            <h3>Total Orders</h3>
                            <div class="value"><?= $order_data['stats']['total_orders'] ?></div>
                        </div>
                    </div>
                    <div class="stat-card warning">
                        <div class="stat-icon"> <i class='bx bx-time'></i> </div>
                        <div class="stat-content">
                            <h3>Pending Orders</h3>
                            <div class="value"><?= $order_data['stats']['pending_orders'] ?></div>
                        </div>
                    </div>
                    <div class="stat-card success">
                        <div class="stat-icon"> <i class='bx bx-calendar'></i> </div>
                        <div class="stat-content">
                            <h3>Today's Orders</h3>
                            <div class="value"><?= $order_data['stats']['today_orders'] ?></div>
                        </div>
                    </div>
                    <div class="stat-card success">
                        <div class="stat-icon"> <i class='bx bx-money'></i> </div>
                        <div class="stat-content">
                            <h3>Total Revenue</h3>
                            <?php
                            $revenue = (float)($order_data['stats']['total_revenue'] ?? 0);
                            ?>
                            <div class="value">₱<?= number_format($revenue, 2) ?></div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Charts Section -->
            <div class="charts-container" style="display: flex;  justify-content: space-around; flex-wrap: wrap; width: 100%; flex-direction: row;">
                <div class="dashboard-card" style="width: 49%;">
                    <div class="card-header">
                        <h3>Order Trends</h3>
                    </div>
                    <div class="chart-container">
                        <canvas id="orderTrendsChart"></canvas>
                    </div>
                </div>
                <div class="dashboard-card" style="width: 49%;">
                    <div class="card-header">
                        <h3>Order Types Distribution</h3>
                    </div>
                    <div class="chart-container">
                        <canvas id="orderTypesChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Main Content Area -->
            <div class="main-content">
                <!-- Filters Section -->
                <div class="filters-section theme-container">
                    <div class="filters-container">
                        <div class="filter-group">
                            <label>Show entries:</label>
                            <select id="limit" onchange="location.href='?page=<?= $page ?>&limit=' + this.value">
                                <option value="10" <?php echo $limit == 10 ? 'selected' : '' ?>>10</option>
                                <option value="25" <?php echo $limit == 25 ? 'selected' : '' ?>>25</option>
                                <option value="50" <?php echo $limit == 50 ? 'selected' : '' ?>>50</option>
                                <option value="100" <?php echo $limit == 100 ? 'selected' : '' ?>>100</option>
                                <option value="500" <?php echo $limit == 500 ? 'selected' : '' ?>>500</option>
                                <option value="1000" <?php echo $limit == 1000 ? 'selected' : '' ?>>1000</option>
                                <option value="<?= $totalRecords ?>" <?php echo $limit == $totalRecords ? 'selected' : '' ?>>All</option>
                            </select>
                        </div>

                        <div class="filter-group">
                            <label>Date Range:</label>
                            <input type="date" id="start-date" value="<?= date('Y-m-d') ?>" onchange="filterTable()">
                            <input type="date" id="end-date" value="<?= date('Y-m-d') ?>" onchange="filterTable()">
                        </div>

                        <div class="search-box">
                            <input type="text" id="general-search" placeholder="Search orders..." onkeyup="filterTable()">
                        </div>
                    </div>
                    <div style="display: flex; flex-wrap: wrap; gap: 10px;">
                    <div class="status-filters" style="flex: 1; min-width: 300px;">
                        <button class="status-pill status-all" onclick="filterByStatus('All')">All</button>
                        <button class="status-pill status-pending" onclick="filterByStatus('Pending')">Pending</button>
                        <button class="status-pill status-processing" onclick="filterByStatus('Processing')">Processing</button>
                        <button class="status-pill status-completed" onclick="filterByStatus('Completed')">Completed</button>
                        <button class="status-pill status-cancelled" onclick="filterByStatus('Cancelled')">Cancelled</button>
                    </div>
                    <div class="actions-section theme-container" style="flex: 1; min-width: 300px;">
                        <div class="action-group">
                            <a href="add.customerorder.php" class="btn-action">
                                <i class="bx bx-plus"></i> New Order
                            </a>
                            <?php if ($_SESSION['role'] == 'admin'): ?>
                                <a href="edit.customerorder.php" class="btn-action">
                                    <i class="bx bx-edit"></i> Edit
                                </a>
                                <button class="btn-action" onclick="confirmDelete(event)">
                                    <i class="bx bx-trash"></i> Delete
                                </button>
                            <?php endif; ?>
                            <button class="btn-action" onclick="exportOrders()">
                                <i class="bx bx-download"></i> Export
                            </button>
                            <a href="javascript:location.reload()" class="btn-action">
                                <i class="bx bx-refresh"></i> Refresh
                            </a>
                        </div>
                    </div>
                </div>
                </div>



                <!-- Table Section -->
                <div class="table-section theme-container">
                    <div class="table-responsive">
                        <table class="table" id="myTable">
                            <thead>
                                <tr>
                                    <?php if ($_SESSION['role'] === 'admin') { ?>
                                        <th><input type="checkbox" class="select-checkbox" id="select-all" onclick="selectAllRows()"></th>
                                    <?php } ?>
                                    <th>Order ID</th>
                                    <th>Reference ID</th>
                                    <th>Time</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>Order Date</th>
                                    <!-- <th>Completed Date</th> -->
                                    <th>Type</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if (count($orders) > 0) {
                                    foreach ($orders as $row) {
                                        $statusClass = strtolower($row['status']);
                                ?>
                                        <tr class="row-<?= $statusClass ?>" onclick="location.href='customerorderdetail.php?orderid=<?= $row['orderid'] ?>'">
                                            <?php if ($_SESSION['role'] === 'admin') { ?>
                                                <td onclick="event.stopPropagation()">
                                                    <input type="checkbox" class="select-checkbox" name="selected[]" value="<?= $row['orderid'] ?>">
                                                </td>
                                            <?php } ?>
                                            <td class="order-id"><?= htmlspecialchars($row['orderid']) ?></td>
                                            <td><?= isset($row['hid']) ? '#' . htmlspecialchars($row['hid']) : 'N/A' ?></td>
                                            <td><?= date('g:i A', strtotime($row['timeoforder'])) ?></td>
                                            <td class="price-column">₱<?= number_format($row['ordertotal'], 2) ?></td>
                                            <td>
                                                <div class="status-cell status-<?= $statusClass ?>">
                                                    <?php
                                                    $icon = '';
                                                    switch ($row['status']) {
                                                        case 'Pending':
                                                            $icon = 'bx-time';
                                                            break;
                                                        case 'Processing':
                                                            $icon = 'bx-hourglass';
                                                            break;
                                                        case 'Completed':
                                                            $icon = 'bx-check';
                                                            break;
                                                        case 'Cancelled':
                                                            $icon = 'bx-x';
                                                            break;
                                                    }
                                                    ?>
                                                    <i class='bx <?= $icon ?> bx-fw'></i>
                                                    <?= $row['status'] ?>
                                                </div>
                                            </td>
                                            <td><?= date('F j, Y', strtotime($row['orderdate'])) ?></td>
                                            <!-- <td><?= isset($row['datecompleted']) && $row['datecompleted'] ? date('F j, Y', strtotime($row['datecompleted'])) : '-' ?></td> -->
                                            <td><?= $row['ordertype'] ?? 'N/A' ?></td>
                                        </tr>
                                <?php
                                    }
                                } else {
                                    echo "<tr>
                                            <td colspan='8'>
                                                <div class='empty-state'>
                                                    <i class='bx bx-package'></i>
                                                    <h4>No Orders Found</h4>
                                                    <p>There are no orders to display at this time.</p>
                                                </div>
                                            </td>
                                        </tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="pagination-container">
                        <div class="dataTables_info">
                            Showing <?= $start + 1 ?> to <?= min($start + $limit, $totalRecords) ?> of <?= $totalRecords ?> entries
                        </div>
                        <ul class="pagination">
                            <li class="page-item <?php echo $page == 1 ? 'disabled' : '' ?>">
                                <a class="page-link" href="<?= $page == 1 ? '#' : "?page=" . ($page - 1) . "&limit=$limit" ?>">Previous</a>
                            </li>

                            <?php for ($i = 1; $i <= $totalPages; $i++) { ?>
                                <li class="page-item <?php echo $page == $i ? 'active' : '' ?>">
                                    <a class="page-link" href="?page=<?= $i ?>&limit=<?= $limit ?>"><?= $i ?></a>
                                </li>
                            <?php } ?>
                            <li class="page-item <?php echo $page == $totalPages ? 'disabled' : '' ?>">
                                <a class="page-link" href="<?= $page == $totalPages ? '#' : "?page=" . ($page + 1) . "&limit=$limit" ?>">Next</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <!-- Debug information -->
    <script>
        console.log('PHP Order Data:', <?= json_encode($order_data) ?>);
        const orderData = <?= json_encode($order_data) ?>;
    </script>

    <!-- Load chart-related scripts -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="../assets/js/order-charts.js"></script>

    <script>
        // Pass PHP data to JavaScript
        const orderData = <?= json_encode($order_data) ?>;

        // Update filterTable function to show all by default
        function filterTable() {
            const startDate = document.getElementById('start-date').value;
            const endDate = document.getElementById('end-date').value;
            const searchTerm = document.getElementById('general-search').value.toLowerCase();
            const rows = document.querySelectorAll('#myTable tbody tr');
            let hasRecords = false;

            rows.forEach(row => {
                if (row.classList.contains('empty-state')) return;

                const orderDate = row.querySelector('td:nth-child(6)').textContent;
                const rowText = row.textContent.toLowerCase();
                const dateMatches = (!startDate || orderDate >= startDate) &&
                    (!endDate || orderDate <= endDate);
                const textMatches = searchTerm === '' || rowText.includes(searchTerm);

                row.style.display = dateMatches && textMatches ? '' : 'none';
                if (dateMatches && textMatches) hasRecords = true;
            });

            updateNoRecordsMessage(!hasRecords);
        }

        // Update filterByStatus to show all by default
        function filterByStatus(status) {
            const rows = document.querySelectorAll('#myTable tbody tr');
            let hasRecords = false;

            // Update active status for filter buttons
            document.querySelectorAll('.status-pill').forEach(button => {
                if (button.textContent.trim() === status) {
                    button.classList.add('active');
                } else {
                    button.classList.remove('active');
                }
            });

            rows.forEach(row => {
                if (row.classList.contains('empty-state')) return;

                // Get the status from the status cell div
                const statusCell = row.querySelector('.status-cell');
                if (!statusCell) return;

                const rowStatus = statusCell.textContent.trim();
                const isVisible = status === 'All' || rowStatus === status;

                row.style.display = isVisible ? 'table-row' : 'none';
                if (isVisible) hasRecords = true;
            });

            // Update no records message
            const noRecordsMsg = document.getElementById('no-records');
            if (noRecordsMsg) {
                noRecordsMsg.style.display = hasRecords ? 'none' : 'block';
            }

            // Update showing entries info
            updateShowingEntries();
        }

        function updateShowingEntries() {
            const visibleRows = document.querySelectorAll('#myTable tbody tr[style="display: table-row"]').length;
            const totalRows = document.querySelectorAll('#myTable tbody tr').length;
            const infoElement = document.querySelector('.dataTables_info');

            if (infoElement) {
                infoElement.textContent = `Showing ${visibleRows} of ${totalRows} entries`;
            }
        }

        function updateNoRecordsMessage(show = false) {
            const noRecords = document.getElementById('no-records');
            if (noRecords) {
                noRecords.style.display = show ? 'block' : 'none';
            }
        }

        function confirmDelete(event) {
            event.preventDefault();
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Add delete logic here
                }
            });
        }

        // Initialize on page load to show all orders
        document.addEventListener('DOMContentLoaded', () => {
            filterByStatus('All');
        });
    </script>

<?php
    $content = ob_get_clean();

    // Render the page (this will include navbar with notifications)
    Page::render($content);

    // Only close the connection after everything is rendered
    $conn->close();
} catch (Exception $e) {
    error_log("Order data error: " . $e->getMessage());
    // Handle error appropriately
    echo "An error occurred while loading the page. Please try again later.";
}
?>
<?php
require '../reusable/redirect404.php';
require '../session/session.php';
require '../database/dbconnect.php';
$current_page = basename($_SERVER['PHP_SELF'], '.php');

// Define pagination variables at the top of the file
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 500; // Changed default to 500
$start = ($page - 1) * $limit;

// Get total records for pagination
$totalRecords = $conn->query("SELECT COUNT(*) FROM customerorder")->fetch_row()[0];
$totalPages = ceil($totalRecords / $limit);

// Calculate statistics
$pendingOrders = $conn->query("SELECT COUNT(*) FROM customerorder WHERE status = 'Pending'")->fetch_row()[0];

$todayOrders = $conn->query("SELECT COUNT(*) FROM customerorder 
                            WHERE DATE(orderdate) = CURDATE()")->fetch_row()[0];

$totalRevenue = $conn->query("SELECT COALESCE(SUM(ordertotal), 0) 
                             FROM customerorder 
                             WHERE status = 'Completed'")->fetch_row()[0];

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
                       timeoforder DESC 
                       LIMIT $start, $limit");

?>
<!DOCTYPE html>
<html>

<head>
    <title>Customer Orders</title>
    <?php require '../reusable/header.php'; ?>
    <link rel="stylesheet" type="text/css" href="../resources/css/shared-dashboard.css">
    <link rel="stylesheet" type="text/css" href="../resources/css/customer-pages.css">
    <?php require 'sweetalert.php'; ?>
    <!-- <style>
        .stats-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: var(--card-bg);
            border-radius: 8px;
            padding: 1.5rem;
            box-shadow: 0 2px 4px rgba(54, 48, 45, 0.1);
            border-left: 4px solid var(--accent);
        }

        .stat-card h3 {
            color: var(--text-secondary);
            font-size: 0.9rem;
            margin-bottom: 0.5rem;
        }

        .stat-card .value {
            color: var(--text-primary);
            font-size: 1.5rem;
            font-weight: 600;
        }

        .status-filters {
            display: flex;
            gap: 0.5rem;
            margin-bottom: 1rem;
            flex-wrap: wrap;
        }

        .status-pill {
            padding: 0.5rem 1rem;
            border-radius: 20px;
            border: 1px solid var(--border);
            background: var(--surface);
            color: var(--text-primary);
            cursor: pointer;
            transition: var(--tran-03);
        }

        .status-pill:hover {
            background: var(--accent);
            color: var(--light);
        }

        .status-pill.active {
            background: var(--primary);
            color: var(--light);
            border-color: var(--primary);
        }

        .status-pending { border-left-color: var(--warning); }
        .status-processing { border-left-color: var(--accent); }
        .status-completed { border-left-color: var(--success); }
        .status-cancelled { border-left-color: var(--dark); }

        .btn-action {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            border-radius: 4px;
            background: var(--surface);
            color: var(--text-primary);
            border: 1px solid var(--border);
            cursor: pointer;
            transition: var(--tran-03);
        }

        .btn-action:hover {
            background: var(--accent);
            color: var(--light);
            border-color: var(--accent);
        }

        .table tbody tr:hover {
            background: var(--surface);
            cursor: pointer;
        }

        .status-cell {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.25rem 0.75rem;
            border-radius: 15px;
            font-size: 0.875rem;
        }

        .status-cell.status-pending { 
            background: #fff3e6; 
            color: var(--warning);
        }
        .status-cell.status-processing { 
            background: #fff1ec; 
            color: var(--accent);
        }
        .status-cell.status-completed { 
            background: #f0f4eb; 
            color: var(--success);
        }
        .status-cell.status-cancelled { 
            background: #f0efee; 
            color: var(--dark);
        }

        .empty-state {
            text-align: center;
            padding: 2rem;
            color: var(--text-secondary);
        }

        .empty-state i {
            font-size: 3rem;
            margin-bottom: 1rem;
            color: var(--border);
        }
    </style>  -->

</head>

<body>
    <?php include '../reusable/sidebar.php'; ?>
    <section class="panel sales-theme">
        <?php include '../reusable/navbarNoSearch.html'; ?>

        <div class="container-fluid dashboard-container">
            <!-- Stats Container -->
            <div class="stats-container">
                <div class="stat-card">
                    <h3>Total Orders</h3>
                    <div class="value"><?= $totalRecords ?></div>
                </div>
                <div class="stat-card">
                    <h3>Pending Orders</h3>
                    <div class="value"><?= $pendingOrders ?></div>
                </div>
                <div class="stat-card">
                    <h3>Today's Orders</h3>
                    <div class="value"><?= $todayOrders ?></div>
                </div>
                <div class="stat-card">
                    <h3>Total Revenue</h3>
                    <div class="value">₱<?= number_format($totalRevenue, 2) ?></div>
                </div>
            </div>

            <!-- Filters Container -->
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

            <!-- Status Filters -->
            <div class="status-filters">
                <button class="status-pill status-all" onclick="filterByStatus('All')">All</button>
                <button class="status-pill status-pending" onclick="filterByStatus('Pending')">Pending</button>
                <button class="status-pill status-processing" onclick="filterByStatus('Processing')">Processing</button>
                <button class="status-pill status-completed" onclick="filterByStatus('Completed')">Completed</button>
                <button class="status-pill status-cancelled" onclick="filterByStatus('Cancelled')">Cancelled</button>
            </div>

            <!-- Action Buttons -->
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

            <!-- Table Content -->
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
                            <th>Completed Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                $statusClass = strtolower($row['status']);
                        ?>
                                <tr class="row-<?= $statusClass ?>" onclick="location.href='customerorderdetail.php?hid=<?= $row['hid'] ?>'">
                                    <?php if ($_SESSION['role'] === 'admin') { ?>
                                        <td onclick="event.stopPropagation()">
                                            <input type="checkbox" class="select-checkbox" name="selected[]" value="<?= $row['hid'] ?>">
                                        </td>
                                    <?php } ?>
                                    <td class="order-id">#<?= $row['hid'] ?></td>
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
                                    <td><?= $row['datecompleted'] ? date('F j, Y', strtotime($row['datecompleted'])) : '-' ?></td>
                                    <td><?= $row['ordertype'] ?></td>
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
                        $conn->close();
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
        
<?php include_once("../reusable/footer.php"); ?>
    </section>

    <script>
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
</body>

</html>
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
</head>

<body>
    <?php include '../reusable/sidebar.php'; ?>
    <section class="panel">
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
                            <th>Completed Date</th>
                            <th>Type</th>
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
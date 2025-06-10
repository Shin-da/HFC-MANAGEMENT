<?php
require '../reusable/redirect404.php';
require '../session/session.php';
require '../database/dbconnect.php';
$current_page = basename($_SERVER['PHP_SELF'], '.php');
?>
<!DOCTYPE html>
<html>
<head>
    <title>Customer Orders</title>
    <?php require '../reusable/header.php'; ?>
    <link rel="stylesheet" type="text/css" href="../resources/css/customer-pages.css">
    <?php require 'sweetalert.php'; ?>
</head>
<body>
    <?php include '../reusable/sidebar.php'; ?>
    <section class="panel">
        <?php include '../reusable/navbarNoSearch.html'; ?>
        
        <div class="order-list animate-fade-in">
            <!-- Enhanced Header -->
            <div class="order-list-header">
                <div>
                    <h2>Customer Orders</h2>
                    <span class="subtitle">Online and Walk-in Orders</span>
                </div>
                <div class="date-display">
                    <i class="bx bx-calendar"></i>
                    <span><?php echo date('l, F jS'); ?></span>
                </div>
            </div>

            <!-- Enhanced Filters -->
            <div class="filters-container">
                <div class="filter-group">
                    <label>Show entries:</label>
                    <select id="limit" onchange="location.href='?page=<?= $page ?>&limit=' + this.value">
                        <option value="10" <?php echo $limit == 10 ? 'selected' : '' ?>>10</option>
                        <option value="25" <?php echo $limit == 25 ? 'selected' : '' ?>>25</option>
                        <option value="50" <?php echo $limit == 50 ? 'selected' : '' ?>>50</option>
                        <option value="100" <?php echo $limit == 100 ? 'selected' : '' ?>>100</option>
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

                    <div class="title">
                        <i class="bx bx-calendar bx-fw" style="font-size: 24px;"></i>
                        <span><?php echo date('l, F jS'); ?></span>
                    </div>
                </div>

                <div class="table-header">
                    <?php
                    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
                    $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
                    $start = ($page - 1) * $limit;
                    $items = $conn->query("SELECT * FROM customerorder ORDER BY orderdate DESC, timeoforder DESC LIMIT $start, $limit");
                    $totalRecords = $conn->query("SELECT COUNT(*) FROM customerorder")->fetch_row()[0];
                    $totalPages = ceil($totalRecords / $limit);
                    ?>
                </div>
                <?php
                $result = $conn->query("SELECT * FROM customerorder ORDER BY orderdate DESC, timeoforder DESC LIMIT $limit OFFSET $start");
                ?>
                <div class="">
                    <div class="container filters">
                        <div style="display: flex; justify-content: center; align-items: center; gap: 10px;">
                            <div class="filter-box" style="display: flex; gap: 10px; align-items: center; justify-content: center; width: 100%; flex-direction: row;">
                                <label for="limit">Show</label>
                                <select id="limit" onchange="location.href='?page=<?= $page ?>&limit=' + this.value">
                                    <option value="10" <?php echo $limit == 10 ? 'selected' : '' ?>>10</option>
                                    <option value="25" <?php echo $limit == 25 ? 'selected' : '' ?>>25</option>
                                    <option value="50" <?php echo $limit == 50 ? 'selected' : '' ?>>50</option>
                                    <option value="100" <?php echo $limit == 100 ? 'selected' : '' ?>>100</option>
                                </select>
                                <label for="limit"></label>
                            </div>
                            <div style="display: flex; gap: 10px; align-items: center; justify-content: center; flex-direction: row; background-color: #f9f9f9; padding: 10px; border-radius: 5px; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);">
                                <div style="display: flex; flex-direction: column; align-items: center;">
                                    <label for="start-date" style="font-weight: bold; margin-bottom: 5px;">Start Date:</label>
                                    <input type="date" id="start-date" style="border: 1px solid #ddd; border-radius: 5px; padding: 5px;" value="<?= date('Y-m-d') ?>" onchange="filterTable()">
                                </div>
                                <div style="display: flex; flex-direction: column; align-items: center;">
                                    <label for="end-date" style="font-weight: bold; margin-bottom: 5px;">End Date:</label>
                                    <input type="date" id="end-date" style="border: 1px solid #ddd; border-radius: 5px; padding: 5px;" value="<?= date('Y-m-d') ?>" onchange="filterTable()">
                                </div>
                            </div>

                        </div>
                        <div style="display: flex; justify-content: center; align-items: center;">
                            <form class="form" onsubmit="filterTable(); return false;">
                                <button>
                                    <svg width="17" height="16" fill="none" xmlns="http://www.w3.org/2000/svg" role="img" aria-labelledby="search">
                                        <path d="M7.667 12.667A5.333 5.333 0 107.667 2a5.333 5.333 0 000 10.667zM14.334 14l-2.9-2.9" stroke="currentColor" stroke-width="1.333" stroke-linecap="round" stroke-linejoin="round"></path>
                                    </svg>
                                </button>
                                <input class="input" id="general-search" placeholder="Search the table..." type="text">
                                <button class="reset" type="reset" onclick="filterTable()">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </div>
                    <div class="container filters">
                        <div>
                            <button class="status-button All" onclick="filterByStatus('All')">All</button>
                            <button class="status-button Pending" onclick="filterByStatus('Pending')">Pending</button>
                            <button class="status-button Processing" onclick="filterByStatus('Processing')">Processing</button>
                            <button class="status-button Completed" onclick="filterByStatus('Completed')">Completed</button>
                            <button class="status-button Cancelled" onclick="filterByStatus('Cancelled')">Cancelled</button>
                        </div>

                        <div>
                            <div class="icons">
                                <a href="add.customerorder.php" class="icon-link add"><i class="bx bx-plus"></i> </a>


                                <?php
                                if ($_SESSION['role'] == 'admin') {  ?>

                                    <a href="edit.customerorder.php" class="icon-link"><i class="bx bx-edit"></i></a>
                                    <a href="#" class="icon-link" onclick="confirmDelete(event)"><i class="bx bx-trash"></i></a>
                                <?php } ?>
                                <a href="javascript:location.reload()" class="icon-link refresh"><i class="bx bx-refresh"></i></a>
                                <a href="#" class="icon-link export" id="export"><i class="bx bx-download"></i></a>
                            </div>
                        </div>

                    </div>
                </div>
                <script>
                    function filterTable() {
                        const startDate = document.getElementById('start-date').value || new Date().toISOString().split('T')[0];
                        const endDate = document.getElementById('end-date').value || new Date().toISOString().split('T')[0];
                        const searchValue = document.getElementById('general-search').value.toUpperCase();
                        const rows = document.querySelectorAll('#myTable tbody tr');

                        let hasRecords = false;
                        rows.forEach(row => {
                            const orderDate = row.querySelector('td:nth-child(7)').textContent; // assuming order date is in the 6th column
                            const orderStatus = row.querySelector('td:nth-child(6)').textContent; // assuming order status is in the 5th column
                            const textContent = row.textContent.toUpperCase();

                            const dateInRange = (!startDate || orderDate >= startDate) && (!endDate || orderDate <= endDate);
                            const matchesSearch = textContent.indexOf(searchValue) > -1;

                            if (dateInRange && matchesSearch) {
                                row.style.display = 'table-row';
                                hasRecords = true;
                            } else {
                                row.style.display = 'none';
                            }
                        });

                        const noRecords = document.getElementById('no-records');
                        if (!hasRecords) {
                            noRecords.style.display = 'block';
                        } else {
                            noRecords.style.display = 'none';
                        }
                    }

                    function filterByStatus(status) {
                        const rows = document.querySelectorAll('#myTable tbody tr:not(.icons)');

                        let hasRecords = false;
                        rows.forEach(row => {
                            const orderStatus = row.querySelector('td:nth-child(5)').innerText.trim(); // assuming order status is in the 5th column
                            if (status === 'All' || orderStatus === status) {
                                row.style.display = 'table-row';
                                hasRecords = true;
                            } else {
                                row.style.display = 'none';
                            }
                        });

                        const noRecords = document.getElementById('no-records');
                        if (!hasRecords) {
                            noRecords.style.display = 'block';
                        } else {
                            noRecords.style.display = 'none';
                        }
                    }
                </script>
                <?php if (isset($_SESSION['admin'])) { ?>
                    <form action="updatecustomerorder.php" method="post">
                        <h1>admin editing </h1>
                    <?php } ?>
                    <table class="table" id="myTable">
                        <thead>
                            <tr>
                                <?php if ($_SESSION['role'] === 'admin') { ?>
                                    <th><input type="checkbox" id="select-all" onclick="selectAllRows()"></th>
                                <?php } ?>
                                <th>ID</th>
                                <th>Time of Order</th>
                                <th>Order Total</th>
                                <th>Status</th>
                                <th>Order Date</th>
                                <th>Date Completed</th>
                                <th>Order Type</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if ($result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) {
                                    $hid = $row['hid'];
                                    $timeoforder = date('g:i A', strtotime($row['timeoforder']));
                                    $ordertotal = $row['ordertotal'];
                                    $status = $row['status'];
                                    $orderdate = date('F j, Y', strtotime($row['orderdate']));
                                    $datecompleted = $row['datecompleted'];
                                    $ordertype = $row['ordertype'];
                                    $symbol = '';
                                    switch ($status) {
                                        case 'Pending':
                                            $symbol = '<i class="bx bx-time bx-fw"></i>';
                                            break;
                                        case 'Completed':
                                            $symbol = '<i class="bx bx-check bx-fw"></i>';
                                            break;
                                        case 'Cancelled':
                                            $symbol = '<i class="bx bx-x bx-fw"></i>';
                                            break;
                                        case 'Processing':
                                            $symbol = '<i class="bx bx-hourglass bx-fw"></i>';
                                            break;
                                    }
                            ?>
                                    <tr
                                        <?php
                                        switch ($status) {
                                            case 'Pending':
                                                echo "style='background-color:rgb(254, 254, 193);'";
                                                break;
                                            case 'Completed':
                                                echo "style='background-color: #cfffdc;'";
                                                break;
                                            case 'Cancelled':
                                                echo "style='background-color: #ffdcdc;'";
                                                break;
                                            case 'Processing':
                                                echo "style='background-color:rgb(169, 219, 255);'";
                                                break;
                                        }

                                        ?>
                                        onclick="location.href='customerorderdetail.php?hid=<?= $hid ?>'">
                                        <?php if ($_SESSION['role'] === 'admin') { ?>
                                            <td><input type="checkbox" name="selected[]" value="<?= $hid ?>"></td>
                                        <?php } ?>
                                        <td style="display: none;"><?= $status ?></td>
                                        <td>#<?= $hid ?></td>
                                        <td><?= $timeoforder ?></td>
                                        <td>&#x20B1; <?= number_format($ordertotal, 2) ?></td>
                                        <td style="color:<?= $status ?>;"><?= $symbol . ' ' . $status ?></td>
                                        <td><?= $orderdate ?></td>
                                        <td><?= $datecompleted ?></td>
                                        <td><?= $ordertype ?></td>
                                    </tr>
                            <?php
                                }
                            } else {
                                echo "<tr>
                                        <td colspan='7' style='text-align: center;'>
                                            <div style='display: flex; justify-content: center; align-items: center;'>
                                                <i class='bx bx-question-mark bx-fw' style='font-size: 40px; color: #aaa;'></i>
                                                <div style='margin-left: 10px;'>
                                                    <h4 style='margin-bottom: 5px;'>No orders have been made yet.</h4>
                                                    <p style='font-size: 14px; color: #aaa;'>It's quiet here.</p>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>";
                            }
                            $conn->close();
                            ?>
                        </tbody>
                    </table>

                    <script>
                        function selectAllRows() {
                            const checkboxes = document.querySelectorAll('#myTable tbody tr td input[type="checkbox"]');
                            const selectAllCheckbox = document.getElementById('select-all');

                            if (selectAllCheckbox.checked) {
                                checkboxes.forEach(checkbox => {
                                    checkbox.checked = true;
                                });
                            } else {
                                checkboxes.forEach(checkbox => {
                                    checkbox.checked = false;
                                });
                            }
                        }
                    </script>
                    <div class="card-footer">
                        <p id="no-records" style="display: none; text-align: center; font-size: 16px; color: red;">No records found. Please try changing your search criteria.</p>
                        <div class="dataTables_info" id="example_info" role="status" aria-live="polite">Showing <?= $start + 1 ?> to <?= min($start + $limit, $totalRecords) ?> of <?= $totalRecords ?> entries</div>

                        <div class="pagination-box">
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

            <script>
                function confirmDelete(event) {
                    event.preventDefault();
                    const href = event.target.getAttribute('href');
                    Swal.fire({
                        title: 'Are you sure you want to delete this order?',
                        text: "You won't be able to revert this!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Yes, delete it!'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            document.location.href = href;
                        }
                    });
                }
            </script>
</body>
<?php require '../reusable/footer.php'; ?>


</html>
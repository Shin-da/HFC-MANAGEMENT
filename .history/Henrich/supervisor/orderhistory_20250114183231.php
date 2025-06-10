<?php
require '../reusable/redirect404.php';
require '../session/session.php';
require '../database/dbconnect.php';

$current_page = basename($_SERVER['PHP_SELF'], '.php');
?>
<!DOCTYPE html>
<html>

<head>
    <title>Transactions</title>
    <?php require '../reusable/header.php'; ?>
    <?php require 'sweetalert.php'; ?>
    <link rel="stylesheet" type="text/css" href="../resources/css/table.css">
    <style>
        .filters {
            display: flex;
            justify-content: space-between;
            margin-bottom: 1rem;
            padding: 0 1rem;
        }

        .filters select,
        .filters button,
        .filters input[type='date'],
        .filters input[type='text'] {
            border: 1px solid #ccc;
            border-radius: 5px;
            padding: 5px 10px;
            margin-right: 10px;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 0.9em;
        }

        .filters button:hover,
        .filters select:hover,
        .filters input[type='date']:hover,
        .filters input[type='text']:hover {
            background-color: #f8f9fa;
        }

        .filters button:focus,
        .filters select:focus,
        .filters input[type='date']:focus,
        .filters input[type='text']:focus {
            outline: none;
            border-color:rgb(92, 171, 255);
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            font-size: 1em;
            min-width: 600px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.15);
        }

        .table thead tr {
            background-color:rgb(45, 156, 133);
            color: #ffffff;
            text-align: left;
            font-weight: bold;
        }

        .table th,
        .table td {
            padding: 12px 15px;
            border: 1px solid #ddd;
        }

        .table tbody tr {
            border-bottom: 1px solid #dddddd;
        }

        .table tbody tr:nth-of-type(even) {
            background-color: #f3f3f3;
        }

        .table tbody tr:last-of-type {
            border-bottom: 2px solid #009879;
        }

        .Pending {
            background-color: #ffeb3b;
            color: black;
            font-weight: bold;
        }

        .Completed {
            background-color: #4caf50;
            color: white;
            font-weight: bold;
        }

        .Cancelled {
            background-color: #f44336;
            color: white;
            font-weight: bold;
        }

        .Ongoing {
            background-color: #2196f3;
            color: white;
            font-weight: bold;
        }

        .icons a {
            color: #007bff;
        }

        .icons a:hover {
            color: #0056b3;
        }

        .card-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px;
            background-color: #f8f9fa;
            border-top: 1px solid #e9ecef;
        }

        .pagination-box {
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .pagination li {
            display: inline-block;
            margin: 0 5px;
        }

        .pagination a {
            color: #007bff;
            text-decoration: none;
            transition: color 0.3s;
        }

        .pagination a:hover {
            color: #0056b3;
        }

        .pagination .active a {
            font-weight: bold;
            color: #333;
            pointer-events: none;
        }

        .pagination .disabled a {
            color: #ccc;
            pointer-events: none;
        }
    </style>

</head>

<body>
    <?php include '../reusable/sidebar.php'; ?>
    <section class="panel">
        <?php include '../reusable/navbarNoSearch.html'; ?>
        <div class="container-fluid">
            <div class="table-header" style="border-left: 8px solid #fa1; padding: 10px;">
                <div class="title">
                    <h2>Customer Orders</h2>
                    <span style="font-size: 12px;">Customer Orders (Online and Walk-in)</span>
                </div>
                <div class="title">
                    <span><?php echo date('l, F jS'); ?></span>
                </div>
            </div>

            <?php
            $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
            $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
            $start = ($page - 1) * $limit;
            $items = $conn->query("SELECT * FROM orderhistory ORDER BY orderdate DESC, timeoforder DESC LIMIT $start, $limit");
            $totalRecords = $conn->query("SELECT COUNT(*) FROM orderhistory")->fetch_row()[0];
            $totalPages = ceil($totalRecords / $limit);
            $result = $conn->query("SELECT * FROM orderhistory ORDER BY orderdate DESC, timeoforder DESC LIMIT $limit OFFSET $start");
            ?>

            <div class="container filters">
                <div class="filter-box">
                    <label for="limit">Show</label>
                    <select id="limit" onchange="location.href='?page=<?= $page ?>&limit=' + this.value">
                        <option value="10" <?= $limit == 10 ? 'selected' : '' ?>>10</option>
                        <option value="25" <?= $limit == 25 ? 'selected' : '' ?>>25</option>
                        <option value="50" <?= $limit == 50 ? 'selected' : '' ?>>50</option>
                        <option value="100" <?= $limit == 100 ? 'selected' : '' ?>>100</option>
                    </select>
                </div>
                <div>
                    <label for="start-date">Start Date:</label>
                    <input type="date" id="start-date" onchange="filterTable()">
                    <label for="end-date">End Date:</label>
                    <input type="date" id="end-date" onchange="filterTable()">
                </div>
                <div>
                    <form onsubmit="filterTable(); return false;">
                        <input id="general-search" placeholder="Search the table..." type="text">
                        <button>
                            <svg width="17" height="16" fill="none" xmlns="http://www.w3.org/2000/svg" role="img" aria-labelledby="search">
                                <path d="M7.667 12.667A5.333 5.333 0 107.667 2a5.333 5.333 0 000 10.667zM14.334 14l-2.9-2.9" stroke="currentColor" stroke-width="1.333" stroke-linecap="round" stroke-linejoin="round"></path>
                            </svg>
                        </button>
                        <button type="reset" onclick="filterTable()">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </form>
                </div>
                <div>
                    <button class="status-button All" onclick="filterByStatus('All')">All</button>
                    <button class="status-button Pending" onclick="filterByStatus('Pending')">Pending</button>
                    <button class="status-button Ongoing" onclick="filterByStatus('Ongoing')">Ongoing</button>
                    <button class="status-button Completed" onclick="filterByStatus('Completed')">Completed</button>
                    <button class="status-button Cancelled" onclick="filterByStatus('Cancelled')">Cancelled</button>
                </div>
            </div>

            <script>
                function filterTable() {
                    const startDate = document.getElementById('start-date').value;
                    const endDate = document.getElementById('end-date').value;
                    const searchValue = document.getElementById('general-search').value.toUpperCase();
                    const rows = document.querySelectorAll('#myTable tbody tr');

                    let hasRecords = false;
                    rows.forEach(row => {
                        const orderDate = row.querySelector('td:nth-child(6)').textContent;
                        const orderStatus = row.querySelector('td:nth-child(5)').textContent;
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
                    const rows = document.querySelectorAll('#myTable tbody tr');

                    let hasRecords = false;
                    rows.forEach(row => {
                        const orderStatus = row.querySelector('td:nth-child(5)').textContent;
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

            <table class="table" id="myTable">
                <thead>
                    <tr>
                        <th></th>
                        <th>hid</th>
                        <th>Time of Order</th>
                        <th>Order Total</th>
                        <th>Status</th>
                        <th>Order Date</th>
                        <th>Date Completed</th>
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
                            $orderdate = $row['orderdate'];
                            $datecompleted = $row['datecompleted'];
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
                                case 'Ongoing':
                                    $symbol = '<i class="bx bx-hourglass bx-fw"></i>';
                                    break;
                            }
                            echo "<tr class='{$status}' onclick=\"location.href='orderhistorydetail.php?hid={$hid}'\">
                                    <td>{$symbol}</td>
                                    <td>{$hid}</td>
                                    <td>{$timeoforder}</td>
                                    <td>&#x20B1; " . number_format($ordertotal, 2) . "</td>
                                    <td>{$status}</td>
                                    <td>{$orderdate}</td>
                                    <td>{$datecompleted}</td>
                                </tr>";
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
            <div class="card-footer">
                <p id="no-records" style="display: none; text-align: center; font-size: 16px; color: red;">No records found. Please try changing your search criteria.</p>
                <div class="dataTables_info">Showing <?= $start + 1 ?> to <?= min($start + $limit, $totalRecords) ?> of <?= $totalRecords ?> entries</div>

                <div class="pagination-box">
                    <ul class="pagination">
                        <li class="page-item <?= $page == 1 ? 'disabled' : '' ?>">
                            <a class="page-link" href="<?= $page == 1 ? '#' : '?page=' . ($page - 1) ?>">Previous</a>
                        </li>
                        <?php for ($i = 1; $i <= $totalPages; $i++) : ?>
                            <li class="page-item <?= $page == $i ? 'active' : '' ?>">
                                <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                            </li>
                        <?php endfor; ?>
                        <li class="page-item <?= $page == $totalPages ? 'disabled' : '' ?>">
                            <a class="page-link" href="<?= $page == $totalPages ? '#' : '?page=' . ($page + 1) ?>">Next</a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require '../reusable/footer.php'; ?>


</html>
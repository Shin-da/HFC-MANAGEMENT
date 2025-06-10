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
            width: 100%;
        }

        .filters>div button {
            border: none;
            border-radius: 5px;
            padding: 5px 10px;
            cursor: pointer;
            background-color: #f0f0f0;
            transition: background-color 0.3s, color 0.3s;
        }

        .filters>div button:hover {
            background-color: #e0e0e0;
        }

        .filters>div button:focus {
            outline: none;
        }

        .filters>div button:active {
            background-color: #d0d0d0;
        }

        thead {
            background-color: #007bff;
            color: white;
        }

        tbody tr:nth-child(even) {
            background-color: #f8f9fa;
        }

        .Pending {
            background-color: #ffc107;
            color: white;
            border: none;
        }

        .Completed {
            background-color: #28a745;
            color: white;
            border: none;
        }

        .Cancelled {
            background-color: #dc3545;
            color: white;
            border: none;
        }

        .Ongoing {
            background-color: #17a2b8;
            color: white;
            border: none;
        }

        .icons {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 10px;
        }

        .icons a {
            display: flex;
            align-items: center;
            font-size: 1.2em;
            color: #333;
            transition: color 0.3s;
        }

        .icons a:hover {
            color: #007bff;
        }

        .card-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-direction: column;
            background-color: #f8f9fa;
            padding: 10px;
            border-top: 1px solid #dee2e6;
            border-radius: 0 0 5px 5px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            border-radius: 5px;
        }

        th, td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #dee2e6;
        }

        th:first-child, td:first-child {
            border-left: 1px solid #dee2e6;
        }

        th:last-child, td:last-child {
            border-right: 1px solid #dee2e6;
        }

        tr:hover {
            background-color: #f1f1f1;
        }
    </style>

</head>

<body>
    <?php include '../reusable/sidebar.php'; // Sidebar   
    ?>
    <!-- === Orders History === -->
    <section class="panel">
        <?php include '../reusable/navbarNoSearch.html'; // TOP NAVBAR         
        ?>
        <div class="container-fluid">
            <!-- Stock Management -->
            <div class="table-header" style="border-left: 8px solid #fa1;">
                <div class="title">
                    <span>
                        <h2>Customer Orders</h2>
                    </span>
                    <span style="font-size: 12px;">Customer Orders (Online and Walk-in)</span>
                </div>

                <div class="title">
                    <span><?php echo date('l, F jS'); ?></span>
                </div>
            </div>

            <div class="table-header">
                <?php
                $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
                $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
                $start = ($page - 1) * $limit;
                $items = $conn->query("SELECT * FROM orderhistory ORDER BY orderdate DESC, timeoforder DESC LIMIT $start, $limit");
                $totalRecords = $conn->query("SELECT COUNT(*) FROM orderhistory")->fetch_row()[0];
                $totalPages = ceil($totalRecords / $limit);
                ?>
            </div>
            <?php
            $result = $conn->query("SELECT * FROM orderhistory ORDER BY orderdate DESC, timeoforder DESC LIMIT $limit OFFSET $start");
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
                        </div>
                        <div style="display: flex; gap: 10px; align-items: center; justify-content: center; flex-direction: row;">
                            <div>
                                <label for="start-date">Start Date:</label>
                                <input type="date" id="start-date" onchange="filterTable()">
                            </div>
                            <div>
                                <label for="end-date">End Date:</label>
                                <input type="date" id="end-date" onchange="filterTable()">
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
                        <button class="status-button Ongoing" onclick="filterByStatus('Ongoing')">Ongoing</button>
                        <button class="status-button Completed" onclick="filterByStatus('Completed')">Completed</button>
                        <button class="status-button Cancelled" onclick="filterByStatus('Cancelled')">Cancelled</button>
                    </div>

                    <div>
                        <div class="icons">
                            <a href="javascript:location.reload()" class="icon-link"><i class="bx bx-refresh"></i></a>
                            <a href="#" class="icon-link" id="export"><i class="bx bx-download"></i></a>
                            <a href="add.customerorder.php" class="icon-link"><i class="bx bx-plus"></i> </a>
                        </div>
                    </div>

                </div>

                <script>
                    function filterTable() {
                        const startDate = document.getElementById('start-date').value;
                        const endDate = document.getElementById('end-date').value;
                        const searchValue = document.getElementById('general-search').value;
                        window.location.href = `orderhistory.php?start=${startDate}&end=${endDate}&search=${searchValue}`;
                    }

                    function filterByStatus(status) {
                        window.location.href = `orderhistory.php?status=${status}`;
                    }
                </script>

                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Order Description</th>
                                    <th>Order Date</th>
                                    <th>Customer Name</th>
                                    <th>Customer Address</th>
                                    <th>Customer Phone Number</th>
                                    <th>Order Total</th>
                                    <th>Salesperson</th>
                                    <th>Status</th>
                                    <th>Time of Order</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($data['orders'] as $order) { ?>
                                    <tr>
                                        <td><?= $order['description'] ?></td>
                                        <td><?= $order['order_date'] ?></td>
                                        <td><?= $order['customer_name'] ?></td>
                                        <td><?= $order['customer_address'] ?></td>
                                        <td><?= $order['customer_phone'] ?></td>
                                        <td><?= $order['order_total'] ?></td>
                                        <td><?= $order['salesperson'] ?></td>
                                        <td><?= $order['status'] ?></td>
                                        <td><?= $order['time_of_order'] ?></td>
                                        <td>
                                            <a href="edit.customerorder.php?id=<?= $order['id'] ?>" class="btn btn-primary">Edit</a>
                                            <a href="delete.customerorder.php?id=<?= $order['id'] ?>" class="btn btn-danger">Delete</a>
                                        </td>
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
        </div>
</body> 
<?php require '../reusable/footer.php'; ?>


</html>


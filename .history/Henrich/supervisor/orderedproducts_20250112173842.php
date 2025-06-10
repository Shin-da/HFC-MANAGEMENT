/*************  ✨ Codeium Command 🌟  *************/
<?php
require '../reusable/redirect404.php';
require '../session/session.php';
require '../database/dbconnect.php';
$current_page = basename($_SERVER['PHP_SELF'], '.php');
?>
<!DOCTYPE html>
<html>

<head>
    <title>Order Log</title>
    <?php require 'sweetalert.php'; ?>
    <?php require '../reusable/header.php'; ?>
    <link rel="stylesheet" type="text/css" href="../resources/css/table.css">
    <style>
        .Pending {
            background-color: var(--orange-color);
            color: white;
        }

        .Completed {
            background-color: var(--blue-color);
            color: white;
        }

        .Cancelled {
            background-color: var(--accent-color);
            color: white;
        }

        .New {
            background-color: var(--green-color);
            color: white;
        }

        .Processing {
            background-color: var(--yellow-color);
            color: white;
        }

        .icons a {
            margin: 0 5px;
            color: var(--icon-color);
            font-size: 20px;
            transition: color 0.3s;
        }

        .icons a:hover {
            color: var(--icon-hover-color);
        }
    </style>
</head>

<body>
    <?php include '../reusable/sidebar.php'; ?>
    <!-- === Ordered Products === -->
    <section class="panel">
        <?php include '../reusable/navbarNoSearch.html'; ?>
        <div class="container-fluid">
            <div class="table-header" style="border-left: 8px solid #fa1;">
                <div class="title">
                    <span>
                        <h2>Order Log</h2>
                    </span>
                    <span style="font-size: 12px;">List of Orders</span>
                </div>
                <div class="icons">
                    <a href="javascript:location.reload()" class="icon-link"><i class="bx bx-refresh"></i></a>
                    <a href="#" class="icon-link"><i class="bx bx-download"></i></a>
                </div>
                <div class="title">
                    <span><?php echo date('l, F jS'); ?></span>
                </div>
            </div>

            <div class="table-header">
                <div style="display: flex; justify-content: space-around; align-items: center; width: 100%;">
                    <div>
                        <form class="form">
                            <button>
                                <svg width="17" height="16" fill="none" xmlns="http://www.w3.org/2000/svg" role="img" aria-labelledby="search">
                                    <path d="M7.667 12.667A5.333 5.333 0 107.667 2a5.333 5.333 0 000 10.667zM14.334 14l-2.9-2.9" stroke="currentColor" stroke-width="1.333" stroke-linecap="round" stroke-linejoin="round"></path>
                                </svg>
                            </button>
                            <input class="input" id="general-search" onkeyup="search()" placeholder="Search the table..." required="" type="text">
                            <button class="reset" type="reset">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </form>
                    </div>
                </div>
                <?php
                $page = isset($_GET['page']) ? $_GET['page'] : 1;
                $limit = isset($_GET['limit']) ? $_GET['limit'] : 10;
                $start = ($page - 1) * $limit;
                $items = $conn->query("SELECT * FROM orderedproduct ORDER BY timeoforder ASC LIMIT $start, $limit");
                $totalRecords = $conn->query("SELECT COUNT(DISTINCT orderid) FROM orderedproduct")->fetch_row()[0];
                $totalPages = ceil($totalRecords / $limit);
                ?>
                <div style="display: flex; justify-content: space-around; align-items: center; width: 100%;">
                    <div class="dataTables_info" id="example_info" role="status" aria-live="polite">Showing <?= $start + 1 ?> to <?= $start + $limit ?> of <?= $totalRecords ?> entries</div>
                    <div class="filter-box">
                        <label for="status-filter">Status:</label>
                        <select id="status-filter" onchange="filterTable()">
                            <option value="">All</option>
                            <option value="Pending">Pending</option>
                            <option value="Completed">Completed</option>
                            <option value="Cancelled">Cancelled</option>
                            <option value="New">New</option>
                            <option value="Processing">Processing</option>
                        <label for="limit">Show</label>
                        <select id="limit" onchange="location.href='?page=<?= $page ?>&limit=' + this.value">
                            <option value="10" <?php echo $limit == 10 ? 'selected' : '' ?>>10</option>
                            <option value="25" <?php echo $limit == 25 ? 'selected' : '' ?>>25</option>
                            <option value="50" <?php echo $limit == 50 ? 'selected' : '' ?>>50</option>
                            <option value="100" <?php echo $limit == 100 ? 'selected' : '' ?>>100</option>
                        </select>
                        <label for="limit">entries</label>
                    </div>
                </div>
                <?php
                $page = isset($_GET['page']) ? $_GET['page'] : 1;
                $limit = isset($_GET['limit']) ? $_GET['limit'] : 10;
                $start = ($page - 1) * $limit;
                $items = $conn->query("SELECT * FROM orderedproduct ORDER BY timeoforder ASC LIMIT $start, $limit");
                $totalRecords = $conn->query("SELECT COUNT(DISTINCT orderid) FROM orderedproduct")->fetch_row()[0];
                $totalPages = ceil($totalRecords / $limit);
                ?>
                <div style="display: flex; justify-content: space-around; align-items: center; width: 100%;">
                    <div class="dataTables_info" id="example_info" role="status" aria-live="polite">Showing <?= $start + 1 ?> to <?= $start + $limit ?> of <?= $totalRecords ?> entries</div>
                    <div class="filter-box">
                        <label for="limit">Show</label>
                        <select id="limit" onchange="location.href='?page=<?= $page ?>&limit=' + this.value">
                            <option value="10" <?php echo $limit == 10 ? 'selected' : '' ?>>10</option>
                            <option value="25" <?php echo $limit == 25 ? 'selected' : '' ?>>25</option>
                            <option value="50" <?php echo $limit == 50 ? 'selected' : '' ?>>50</option>
                            <option value="100" <?php echo $limit == 100 ? 'selected' : '' ?>>100</option>
                        </select>
                        <label for="limit">entries</label>
                    </div>
            </div>
            <?php
            $stockManagementTableSQL = "SELECT orderid, GROUP_CONCAT(productname) as productnames, GROUP_CONCAT(productweight) as productweights, GROUP_CONCAT(productprice) as productprices, GROUP_CONCAT(quantity) as quantities, orderdate, timeoforder FROM orderedproduct GROUP BY orderid ORDER BY timeoforder ASC LIMIT $start, $limit";
            $result = $conn->query($stockManagementTableSQL);
            ?>
            <div class="">
                <div class="container-fluid" style="overflow-x:Scroll;">
                    <table class="table" id="myTable">
                        <thead>
                            <tr>
                                <th>Order ID</th>
                                <th>Product Names</th>
                                <th>Weights</th>
                                <th>Prices</th>
                                <th>Quantities</th>
                                <th>Date Ordered</th>
                                <th>Time Ordered</th>
                            </tr>
                        </thead>
                        <tbody id="table-body">
                            <?php
                            if ($result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) {
                                    $orderid = $row['orderid'];
                                    $productnames = explode(',', $row['productnames']);
                                    $productweights = explode(',', $row['productweights']);
                                    $productprices = explode(',', $row['productprices']);
                                    $quantities = explode(',', $row['quantities']);
                                    $orderdate = date('d-m-Y', strtotime($row['orderdate']));
                                    $timeoforder = date('g:i A', strtotime($row['timeoforder']));
                            ?>
                                    <tr class="clickable-row" onclick="location.href='orderdetail.php?orderid=<?= $orderid ?>'">
                                        <td><?= $orderid ?></td>
                                        <td><?= implode('<br>', $productnames) ?></td>
                                        <td><?= implode('<br>', $productweights) ?></td>
                                        <td><?= implode('<br>', array_map(fn($price) => '₱ ' . $price, $productprices)) ?></td>
                                        <td><?= implode('<br>', $quantities) ?></td>
                                        <td><?= $orderdate ?></td>
                                        <td><?= $timeoforder ?></td>
                                    </tr>
                                    <?php
                                }
                            } else {
                                echo "<tr><td colspan='7'>0 results</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
                <div class="container" style="display: flex; justify-content: center; flex-direction: column; align-items: center;">
                    <ul class="pagination">
                        <li><a href="?page=<?= $page - 1 <= 1 ? 1 : $page - 1 ?>" class="prev">&laquo;</a></li>
                        <?php for ($i = 1; $i <= $totalPages; $i++) { ?>
                            <li><a href="?page=<?= $i ?>" class="page <?= $page == $i ? 'active' : '' ?>"><?= $i ?></a></li>
                        <?php } ?>
                        <li><a href="?page=<?= $page + 1 > $totalPages ? $totalPages : $page + 1 ?>" class="next">&raquo;</a></li>
                    </ul>
                </div>
            </div>
            <?php
            $stockManagementTableSQL = "SELECT orderid, GROUP_CONCAT(productname) as productnames, GROUP_CONCAT(productweight) as productweights, GROUP_CONCAT(productprice) as productprices, GROUP_CONCAT(quantity) as quantities, orderdate, timeoforder, status FROM orderedproduct GROUP BY orderid ORDER BY timeoforder ASC LIMIT $start, $limit";
            $result = $conn->query($stockManagementTableSQL);
            ?>
            <div class="">
                <div class="container-fluid" style="overflow-x:Scroll;">
                    <table class="table" id="myTable">
                        <thead>
                            <tr>
                                <th>Order ID</th>
                                <th>Product Names</th>
                                <th>Weights</th>
                                <th>Prices</th>
                                <th>Quantities</th>
                                <th>Date Ordered</th>
                                <th>Time Ordered</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody id="table-body">
                            <?php
                            if ($result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) {
                                    $orderid = $row['orderid'];
                                    $productnames = explode(',', $row['productnames']);
                                    $productweights = explode(',', $row['productweights']);
                                    $productprices = explode(',', $row['productprices']);
                                    $quantities = explode(',', $row['quantities']);
                                    $orderdate = date('d-m-Y', strtotime($row['orderdate']));
                                    $timeoforder = date('g:i A', strtotime($row['timeoforder']));
                                    $status = $row['status'];
                            ?>
                                    <tr class="clickable-row" onclick="location.href='orderdetail.php?orderid=<?= $orderid ?>'">
                                        <td><?= $orderid ?></td>
                                        <td><?= implode('<br>', $productnames) ?></td>
                                        <td><?= implode('<br>', $productweights) ?></td>
                                        <td><?= implode('<br>', array_map(fn($price) => '₱ ' . $price, $productprices)) ?></td>
                                        <td><?= implode('<br>', $quantities) ?></td>
                                        <td><?= $orderdate ?></td>
                                        <td><?= $timeoforder ?></td>
                                        <td class="<?= $status ?>"><?= $status ?></td>
                                    </tr>
                                    <?php
                                }
                            } else {
                                echo "<tr><td colspan='8'>0 results</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
                <div class="container" style="display: flex; justify-content: center; flex-direction: column; align-items: center;">
                    <ul class="pagination">
                        <li><a href="?page=<?= $page - 1 <= 1 ? 1 : $page - 1 ?>" class="prev">&laquo;</a></li>
                        <?php for ($i = 1; $i <= $totalPages; $i++) { ?>
                            <li><a href="?page=<?= $i ?>" class="page <?= $page == $i ? 'active' : '' ?>"><?= $i ?></a></li>
                        <?php } ?>
                        <li><a href="?page=<?= $page + 1 > $totalPages ? $totalPages : $page + 1 ?>" class="next">&raquo;</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </section>
    <script>
        function filterTable() {
            const statusFilter = document.getElementById('status-filter').value.toLowerCase();
            const table = document.getElementById('myTable');
            const rows = table.getElementsByTagName('tr');
            for (let i = 1; i < rows.length; i++) {
                const status = rows[i].getElementsByTagName('td')[7].textContent.toLowerCase();
                if (statusFilter && status !== statusFilter) {
                    rows[i].style.display = 'none';
                } else {
                    rows[i].style.display = '';
                }
            }
        }
    </script>
</body>
<?php require '../reusable/footer.php'; ?>


/******  8ec953d7-20a6-4766-8462-35b6ee1a967d  *******/
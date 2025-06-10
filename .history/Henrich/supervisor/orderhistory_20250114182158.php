/*************  ✨ Codeium Command 🌟  *************/
<style>
    table {
        border-collapse: collapse;
        width: 100%;
        overflow: hidden;
        border-radius: 5px;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
    }
<?php
require '../reusable/redirect404.php';
require '../session/session.php';
require '../database/dbconnect.php';

    th, td {
        padding: 15px;
        background-color: rgba(255,255,255,0.1);
        border-bottom: 1px solid #ddd;
        text-align: left;
    }
$current_page = basename($_SERVER['PHP_SELF'], '.php');
?>
<!DOCTYPE html>
<html>

    th {
        background-color: #4CAF50;
        color: white;
    }
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

    tr:nth-child(even) {
        background-color: #f2f2f2;
    }
        .filters>div button {
            border: none;
            border-radius: 5px;
            padding: 5px 10px;
            cursor: pointer;
        }

    tr:hover {
        background-color: #ddd;
    }
        .filters>div button:hover {}

    .dataTables_info {
        padding: 10px;
        font-size: 14px;
        color: #666;
        text-align: right;
    }
        .filters>div button:focus {
            outline: none;
        }

    .pagination-box {
        text-align: right;
    }
        .filters>div button:active {}

    .pagination {
        display: inline-block;
        padding-left: 0;
        margin: 20px 0;
        border-radius: 4px;
    }
        thead {
            /* Add styles for thead here */
        }

    .page-item {
        display: inline;
    }
        tbody {
            /* Add styles for tbody here */
        }

    .page-link {
        color: #337ab7;
        padding: 6px 12px;
        margin-left: -1px;
        line-height: 1.42857143;
        text-decoration: none;
        background-color: #fff;
        border: 1px solid #ddd;
    }
        .Pending {
            background-color: var(--orange-color);
            color: white;
            border: 1px solid #ebccd1;
        }

    .page-link:hover {
        background-color: #eee;
    }
        .Completed {
            background-color: var(--blue-color);
            color: white;
            border: 1px solid #d6e9c6;
        }

    .page-item.active .page-link {
        z-index: 2;
        color: #fff;
        background-color: #337ab7;
        border-color: #337ab7;
    }
        .Cancelled {
            background-color: var(--accent-color);
            color: white;
            border: 1px solid #a94442;
        }

    .page-item.disabled .page-link {
        color: #777;
        pointer-events: none;
        cursor: not-allowed;
        background-color: #fff;
        border-color: #ddd;
    }
        .Ongoing {
            background-color: #87ceeb;
            color: white;
            border: 1px solid #87ceeb;
        }

    .card-footer {
        padding: 15px;
        background-color: #f8f9fa;
        border-top: 1px solid #e9ecef;
        border-radius: 0 0 5px 5px;
    }
        .icons {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 10px;
        }

    .card-footer > p {
        margin-bottom: 0;
    }
</style>
        .icons a {
            display: flex;
            align-items: center;
            font-size: 1.2em;
            color: var(--text-color);
            transition: color 0.3s;
        }

        .icons a:hover {
            color: var(--accent-color);
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
                        <h2>Customer Orders </h2>
                    </span>
                    <span style="font-size: 12px;"> Customer Orders (Online and Walk-in)</span>
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
                        <label for="limit"></label>
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
                        const searchValue = document.getElementById('general-search').value.toUpperCase();
                        const rows = document.querySelectorAll('#myTable tbody tr');

                        let hasRecords = false;
                        rows.forEach(row => {
                            const orderDate = row.querySelector('td:nth-child(6)').textContent; // assuming order date is in the 6th column
                            const orderStatus = row.querySelector('td:nth-child(5)').textContent; // assuming order status is in the 5th column
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
                            const orderStatus = row.querySelector('td:nth-child(5)').textContent; // assuming order status is in the 5th column
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
                            <th style=" background-color:transparent;"> </th>
                            <th>hid</th>
                            <th>Time of Order</th>
                            <th>Order Total</th>
                            <th style="width: 100px;">Status</th>
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
                                        case 'Ongoing':
                                            echo "style='background-color: #e6ffe6;'";
                                            break;
                                    }
                                    ?>
                                    onclick="location.href='orderhistorydetail.php?hid=<?= $hid ?>'">
                                    <td><?= $symbol ?></td>
                                    <td><?= $hid ?></td>
                                    <td><?= $timeoforder ?></td>
                                    <td>&#x20B1; <?= number_format($ordertotal, 2) ?></td>
                                    <td><?= $status ?></td>
                                    <td><?= $orderdate ?></td>
                                    <td><?= $datecompleted ?></td>
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


/******  fe345ef9-62c6-48ea-92de-c1b7faf83522  *******/
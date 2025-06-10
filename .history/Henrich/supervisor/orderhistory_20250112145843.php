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
        }

        .filters>div button:hover {}

        .filters>div button:focus {
            outline: none;
        }

        .filters>div button:active {}

        thead {
            /* Add styles for thead here */
        }

        tbody {
            /* Add styles for tbody here */
        }

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
    </style>

        .Ongoing {
            background-color: var(--green-color);
            color: white;
        }
    </style>
</head>

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
                        <h2>Order History</h2>
                    </span>
                    <span style="font-size: 12px;"> Customer Orders (Online and Walk-in)</span>
                </div>
                <div class="icons">
                    <a href="javascript:location.reload()" class="icon-link"><i class="bx bx-refresh"></i></a>
                    <a href="#" class="icon-link"><i class="bx bx-download"></i></a>
                </div>
                <div class="title">
                    <span><?php echo date('l, F jS'); ?></span>
                </div>
            </div>

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
                        <h2>Order History</h2>
                    </span>
                    <span style="font-size: 12px;"> Customer Orders (Online and Walk-in)</span>
            <div class="table-header">
                <div style=" display: flex; justify-content: space-around; align-items: center; width: 100%;">

                    <div>
                        <a href="add.customerorder.php" class="btn add-btn"><i class="i bx bx-plus"></i> Add New Order</a>
                    </div>
                    <div>
                        <form class="form">
                            <button>
                                <svg width="17" height="16" fill="none" xmlns="http://www.w3.org/2000/svg" role="img" aria-labelledby="search">
                                    <path d="M7.667 12.667A5.333 5.333 0 107.667 2a5.333 5.333 0 000 10.667zM14.334 14l-2.9-2.9" stroke="currentColor" stroke-width="1.333" stroke-linecap="round" stroke-linejoin="round"></path>
                                </svg>
                            </button>
                            <input class="input" id="general-search" onkeyup="filterTable()" placeholder="Search the table..." required="" type="text">
                            <button class="reset" type="reset">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </form>
                    </div>

                </div>
                <div class="icons">
                    <a href="javascript:location.reload()" class="icon-link"><i class="bx bx-refresh"></i></a>
                    <a href="#" class="icon-link"><i class="bx bx-download"></i></a>
                <?php // pagination for stock management table
                $page = isset($_GET['page']) ? $_GET['page'] : 1;
                $limit = isset($_GET['limit']) ? $_GET['limit'] : 10;
                $start = ($page - 1) * $limit;
                $items = $conn->query("SELECT * FROM orderhistory ORDER BY orderdate DESC, timeoforder DESC LIMIT $start, $limit");
                $totalRecords = $conn->query("SELECT COUNT(*) FROM orders")->fetch_row()[0];
                $totalPages = ceil($totalRecords / $limit);
                ?>
                <div style=" display: flex; justify-content: space-around; align-items: center; width: 100%;"> <!--  Filter results by number of entries -->
                    <div class="dataTables_info" id="example_info" role="status" aria-live="polite">Showing <?= $start + 1 ?> to <?= $start + $limit ?> of <?= $totalRecords ?> entries</div>
                    <div class="filter-box"> <!-- Filter results by number of entries -->
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
                <div class="title">
                    <span><?php echo date('l, F jS'); ?></span>
            </div>
            <?php // pagination for stock management table
            $limit = isset($_GET['limit']) ? $_GET['limit'] : 10;
            $offset = ($page - 1) * $limit;

            $stockManagementTableSQL = "SELECT * FROM orderhistory ORDER BY orderdate DESC, timeoforder DESC LIMIT $limit OFFSET $offset";
            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }
            $sql = $stockManagementTableSQL;
            $result = $conn->query($sql);
            ?>
            <div class="">
                <div class="container filters">
                    <div style="display: flex; justify-content: center; align-items: center; gap: 10px;">
                        <label for="start-date">Start Date:</label>
                        <input type="date" id="start-date" onchange="filterByDate()">
                        <label for="end-date">End Date:</label>
                        <input type="date" id="end-date" onchange="filterByDate()">
                        <label for="status">Status:</label>
                        <select id="status" onchange="filterByStatus()">
                            <option value="">All</option>
                            <option value="Pending">Pending</option>
                            <option value="Completed">Completed</option>
                            <option value="Cancelled">Cancelled</option>
                        </select>
                    </div>
                </div>

                <script>
                    function filterByDate() {
                        const startDate = document.getElementById('start-date').value;
                        const endDate = document.getElementById('end-date').value;
                        const rows = document.querySelectorAll('#myTable tbody tr');

                        rows.forEach(row => {
                            const orderDate = row.querySelector('td:nth-child(5)').textContent; // assuming order date is in the 5th column
                            if ((startDate && orderDate < startDate) || (endDate && orderDate > endDate)) {
                                row.style.display = 'none';
                            } else {
                                row.style.display = 'table-row';
                            }
                        });
                    }

                    function filterByStatus() {
                        const status = document.getElementById('status').value;
                        const rows = document.querySelectorAll('#myTable tbody tr');

                        rows.forEach(row => {
                            const orderStatus = row.querySelector('td:nth-child(4)').textContent; // assuming order status is in the 4th column
                            if (status && orderStatus !== status) {
                                row.style.display = 'none';
                            } else {
                                row.style.display = 'table-row';
                            }
                        });
                    }
                </script>

                <table class="table" id="myTable">
                    <thead>
                        <tr>
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
                                $datecompleted = $row['datecompleted']; ?>
                                <tr onclick="location.href='orderhistorydetail.php?hid=<?= $hid ?>'">
                                    <td><?= $hid ?></td>
                                    <td><?= $timeoforder ?></td>
                                    <td><?= $ordertotal ?></td>
                                    <td><?= $status ?></td>
                                    <td><?= $orderdate ?></td>
                                    <td><?= $datecompleted ?></td>
                                </tr>
                        <?php
                            }
                        } else {
                            echo "No records found.";
                        }
                        $conn->close();
                        ?>
                        <script>
                            function filterTable() {
                                var input, filter, table, tr, td, i, txtValue;
                                input = document.getElementById("general-search");
                                filter = input.value.toUpperCase();
                                table = document.getElementById("myTable");
                                tr = table.getElementsByTagName("tr");

                                for (i = 0; i < tr.length; i++) {
                                    td = tr[i].getElementsByTagName("td")[0];
                                    if (td) {
                                        txtValue = td.textContent || td.innerText;
                                        if (txtValue.toUpperCase().indexOf(filter) > -1) {
                                            tr[i].style.display = "";
                                        } else {
                                            tr[i].style.display = "none";
                                        }
                                    }
                                }
                            }
                        </script> 
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <?php require '../reusable/footer.php'; ?>

            <div class="table-header">
                <div style=" display: flex; justify-content: space-around; align-items: center; width: 100%;">
    </body>

                    <div>
                        <a href="add.customerorder.php" class="btn add-btn"><i class="i bx bx-plus"></i> Add New Order</a>
                    </div>
                    <div>
                        <form class="form">
                            <button>
                                <svg width="17" height="16" fill="none" xmlns="http://www.w3.org/2000/svg" role="img" aria-labelledby="search">
                                    <path d="M7.667 12.667A5.333 5.333 0 107.667 2a5.333 5.333 0 000 10.667zM14.334 14l-2.9-2.9" stroke="currentColor" stroke-width="1.333" stroke-linecap="round" stroke-linejoin="round"></path>
                                </svg>
                            </button>
                            <input class="input" id="general-search" onkeyup="filterTable()" placeholder="Search the table..." required="" type="text">
                            <button class="reset" type="reset">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </form>
                    </div>

                </div>
                <?php // pagination for stock management table
                $page = isset($_GET['page']) ? $_GET['page'] : 1;
                $limit = isset($_GET['limit']) ? $_GET['limit'] : 10;
                $start = ($page - 1) * $limit;
                $items = $conn->query("SELECT * FROM orderhistory ORDER BY orderdate DESC, timeoforder DESC LIMIT $start, $limit");
                $totalRecords = $conn->query("SELECT COUNT(*) FROM orders")->fetch_row()[0];
                $totalPages = ceil($totalRecords / $limit);
                ?>
                <div style=" display: flex; justify-content: space-around; align-items: center; width: 100%;"> <!--  Filter results by number of entries -->
                    <div class="dataTables_info" id="example_info" role="status" aria-live="polite">Showing <?= $start + 1 ?> to <?= $start + $limit ?> of <?= $totalRecords ?> entries</div>
                    <div class="filter-box"> <!-- Filter results by number of entries -->
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
            </div>
            <?php // pagination for stock management table
            $limit = isset($_GET['limit']) ? $_GET['limit'] : 10;
            $offset = ($page - 1) * $limit;

            $stockManagementTableSQL = "SELECT * FROM orderhistory ORDER BY orderdate DESC, timeoforder DESC LIMIT $limit OFFSET $offset";
            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }
            $sql = $stockManagementTableSQL;
            $result = $conn->query($sql);
            ?>
            <div class="">
                <div class="container filters">
                    <div style="display: flex; justify-content: center; align-items: center; gap: 10px;">
    </html>

/******  0161514d-0ac7-4af9-bf65-bb31467c2fdb  *******/
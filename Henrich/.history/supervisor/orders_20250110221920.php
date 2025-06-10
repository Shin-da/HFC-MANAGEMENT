<?php
require '../reusable/redirect404.php';
require '../session/session.php';
require '../database/dbconnect.php';
$current_page = basename($_SERVER['PHP_SELF'], '.php');
?>
<!DOCTYPE html>
<html>

<head>
    <title>Orders</title>
    <script src="../transferOrdersToHistory.js"></script>
    
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
    <?php include '../reusable/sidebar.php';   // Sidebar   
    ?>
    <!-- === Orders === -->
    <section class=" panel">
        <?php include '../reusable/navbarNoSearch.html'; // TOP NAVBAR         
        ?>
        <div class="container-fluid"> <!-- Stock Management -->
            <div class="table-header" style="border-left: 8px solid #fa1;">
                <div class="title">
                    <span>
                        <h2>Orders</h2>
                    </span>
                    <span style="font-size: 12px;">Recent Customer Orders (Online and Walk-in)</span>
                </div>
                <div class="icons">
                    <a href="javascript:location.reload()" class="icon-link"><i class="bx bx-refresh"></i></a>
                    <a href="#" class="icon-link"><i class="bx bx-refresh"></i></a>
                    <a href="#" class="icon-link"><i class="bx bx-download"></i></a>
                </div>
                <div class="title">
                    <span><?php echo date('l, F jS'); ?></span>
                </div>
            </div>
           
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
                            <input class="input" id="general-search" onkeyup="search()" placeholder="Search the table..." required="" type="text">
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
                    $items = $conn->query("SELECT * FROM orders ORDER BY orderdate DESC LIMIT $start, $limit");
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

                $stockManagementTableSQL = "SELECT * FROM orders ORDER BY orderdate DESC LIMIT $limit OFFSET $offset";
                if ($conn->connect_error) {
                    die("Connection failed: " . $conn->connect_error);
                }
                $sql = $stockManagementTableSQL;
                $result = $conn->query($sql);
            ?>
            <div class="">
                <div class="container-fluid" style="overflow-x:Scroll;">
                    <!-- Inventory Table -->
                    <table class="table" id="myTable">
                        <thead>
                            <tr>
                                <th>Order ID</th>
                                <th>customername</th>
                                <!-- <th>customeraddress</th>
                                <th>customerphonenumber</th> -->
                                <th>orderdescription</th>
                                <th>ordertotal</th>
                                <th>Date Ordered</th>
                                <th>Salesperson</th>
                                <th>Status</th>
                            </tr>
                            <tr class="filter-row">
                                <th><input type="text" placeholder="Search OID..." id="oid-filter" onkeyup="filterTable(document.querySelector('#myTable tbody'), this.value, 0)"></th>
                                <th><input type="text" placeholder="Search customername..." id="customername-filter" onkeyup="filterTable(document.querySelector('#myTable tbody'), this.value, 1)"></th>
                                <!-- <th><input type="text" placeholder="Search customeraddress..." id="customeraddress-filter" onkeyup="filterTable(document.querySelector('#myTable tbody'), this.value, 2)"></th>
                                <th><input type="text" placeholder="Search customerphonenumber..." id="customerphonenumber-filter" onkeyup="filterTable(document.querySelector('#myTable tbody'), this.value, 3)"></th> -->
                                <th><input type="text" placeholder="Search orderdescription..." id="orderdescription-filter" onkeyup="filterTable(document.querySelector('#myTable tbody'), this.value, 4)"></th>
                                <th><input type="text" placeholder="Search ordertotal..." id="ordertotal-filter" onkeyup="filterTable(document.querySelector('#myTable tbody'), this.value, 5)"></th>
                                <th><input type="text" placeholder="Search orderdate ..." id="dateupdated-filter" onkeyup="filterTable(document.querySelector('#myTable tbody'), this.value, 6)"></th>
                                <th><input type="text" placeholder="Search salesperson..." id="salesperson-filter" onkeyup="filterTable(document.querySelector('#myTable tbody'), this.value, 7)"></th>
                                <th><input type="text" placeholder="Search Status..." id="status-filter" onkeyup="filterTable(document.querySelector('#myTable tbody'), this.value, 8)"></th>
                            </tr>
                        </thead>
                        <tbody id="table-body">
                            <?php
                            if ($result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) {
                                    $oid = $row['oid'];
                                    $customername = $row['customername'];
                                    // $customeraddress = $row['customeraddress'];
                                    // $customerphonenumber = $row['customerphonenumber'];
                                    $orderDesc = $row['orderdescription'];
                                    $ordertotal = $row['ordertotal'];
                                    $orderdate = date('d-m-Y', strtotime($row['orderdate']));
                                    $salesperson = $row['salesperson'];
                                    $status = $row['status'];
                            ?>
                                    <tr class="clickable-row" onclick="location.href='orderdetail.php?oid=<?= $oid ?>'">
                                        <td><?= $oid ?></td>
                                        <td><?= $customername ?></td>
                                        <!-- <td><?= $customeraddress ?></td>
                                        <td><?= $customerphonenumber ?></td> -->
                                        <td>
                                            <?php
                                            $orderDescription = explode(", ", $orderDesc ?? '');
                                            $description = "";
                                            foreach ($orderDescription as $desc) {
                                                if (strlen($description) + strlen($desc) + 1 > 50) {
                                                    $description .= "...";
                                                    break;
                                                }
                                                $description .= "- " . $desc . "<br>";
                                            }
                                            echo $description;
                                            ?>
                                                </td>
                                        <td>₱ <?= $ordertotal ?></td>
                                        <td><?= $orderdate ?></td>
                                        <td><?= $salesperson ?></td>
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
                <div class="container" style="display: flex; justify-content: center; flex-direction: column; align-items: center; "><!-- Pagination for stock management -->
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


</body>
<?php require '../reusable/footer.php'; ?>

</html>








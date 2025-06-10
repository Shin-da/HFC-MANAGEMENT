<?php
require '../reusable/redirect404.php';
require '../session/session.php';
require '../database/dbconnect.php';
$current_page = basename($_SERVER['PHP_SELF'], '.php');

// Fetch data from the database
function fetchOrderHistory($conn, $start, $limit) {
    $sql = "SELECT * FROM orderhistory ORDER BY oid ASC LIMIT $start, $limit";
    return $conn->query($sql);
}

$page = isset($_GET['page']) ? $_GET['page'] : 1;
$limit = isset($_GET['limit']) ? $_GET['limit'] : 10;
$start = ($page - 1) * $limit;
$totalRecords = $conn->query("SELECT COUNT(*) FROM orderhistory")->fetch_row()[0];
$totalPages = ceil($totalRecords / $limit);
$result = fetchOrderHistory($conn, $start, $limit);
?>
<!DOCTYPE html>
<html>

<head>
    <title>Transactions</title>
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
    </style>
    <script>
        function printRecords() {
            window.print();
        }
    </script>
</head>

<body>
    <?php include '../reusable/sidebar.php'; ?>
    <section class="panel">
        <?php include '../reusable/navbarNoSearch.html'; ?>
        <div class="container-fluid">
            <div class="table-header" style="border-left: 8px solid var(--blue-color);">
                <div class="title">
                    <span>
                        <h2>Order History</h2>
                    </span>
                    <span style="font-size: 12px;">Customer Orders</span>
                </div>
                <button onclick="printRecords()">Print Records</button>
            </div>

            <div class="container-fluid" style="overflow-x: scroll;">
                <table class="table" id="myTable">
                    <thead>
                        <tr>
                            <th>hid</th>
                            <th>Date Completed</th>
                            <th>Order ID</th>
                            <th>customername</th>
                            <th>customeraddress</th>
                            <th>customerphonenumber</th>
                            <th>orderdescription</th>
                            <th>order total</th>
                            <th>Date Ordered</th>
                            <th>Status</th>
                        </tr>
                    </thead>

                    <tbody id="table-body">
                        <?php
                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                $hid = $row['hid'];
                                $datecompleted = date("d-m-Y", strtotime($row['datecompleted']));
                                $oid = $row['oid'];
                                $customername = $row['customername'];
                                $customeraddress = $row['customeraddress'];
                                $customerphonenumber = $row['customerphonenumber'];
                                $orderdescription = $row['orderdescription'];
                                $ordertotal = $row['ordertotal'];
                                $orderdate = $row['orderdate'];
                                $status = $row['status'];
                        ?>
                                <tr onclick="location.href='orderhistorydetail.php?hid=<?= $hid ?>'">
                                    <td><?= $hid ?></td>
                                    <td><?= $datecompleted ?></td>
                                    <td><?= $oid ?></td>
                                    <td><?= $customername ?></td>
                                    <td><?= $customeraddress ?></td>
                                    <td><?= $customerphonenumber ?></td>
                                    <td>
                                        <?php
                                        $orderDescription = explode(", ", $orderdescription ?? '');
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
                                    <td class="<?= $status ?>"><?= $status ?></td>
                                </tr>
                        <?php
                            }
                        } else {
                            echo "<tr><td colspan='10'>0 results</td></tr>";
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
    </section>
</body>
<?php require '../reusable/footer.php'; ?>


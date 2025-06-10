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

        .tab-content {
            display: none;
        }

        .tab-content.active {
            display: block;
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

            <div class="table-header">
                <div style="display: flex; justify-content: space-around; align-items: center; width: 100%;">
                    <div>
                        <a href="add.customerorder.php" class="btn add-btn"><i class="i bx bx-plus"></i> Add New Order</a>
                    </div>
                    <div>
                        <form class="form">
                            <button>
                                <svg width="17" height="16" fill="none" xmlns="http://www.w3.org/2000/svg" role="img"
                                    aria-labelledby="search">
                                    <path
                                        d="M7.667 12.667A5.333 5.333 0 107.667 2a5.333 5.333 0 000 10.667zM14.334 14l-2.9-2.9"
                                        stroke="currentColor" stroke-width="1.333" stroke-linecap="round"
                                        stroke-linejoin="round"></path>
                                </svg>
                            </button>
                            <input class="input" id="general-search" onkeyup="search()" placeholder="Search the table..."
                                required="" type="text">
                            <button class="reset" type="reset">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12">
                                    </path>
                                </svg>
                            </button>
                        </form>
                    </div>
                </div>

                <div class="tabs">
                    <button class="tab-button" onclick="showTab('Pending')">Pending</button>
                    <button class="tab-button" onclick="showTab('Completed')">Completed</button>
                    <button class="tab-button" onclick="showTab('Cancelled')">Cancelled</button>
                </div>

                <div class="tab-content" id="Pending">
                    <?php renderOrderHistoryTable('Pending'); ?>
                </div>

                <div class="tab-content" id="Completed">
                    <?php renderOrderHistoryTable('Completed'); ?>
                </div>

                <div class="tab-content" id="Cancelled">
                    <?php renderOrderHistoryTable('Cancelled'); ?>
                </div>
            </div>
        </div>
    </section>

    <?php require '../reusable/footer.php'; ?>

    <script>
        function showTab(status) {
            var contents = document.querySelectorAll('.tab-content');
            contents.forEach(content => content.classList.remove('active'));

            var activeContent = document.getElementById(status);
            if (activeContent) {
                activeContent.classList.add('active');
            }
        }

        function filterTable(table, value, column) {
            var rows = table.getElementsByTagName("tr");
            for (var i = 0; i < rows.length; i++) {
                var cell = rows[i].getElementsByTagName("td")[column];
                if (cell) {
                    if (cell.innerHTML.toLowerCase().indexOf(value.toLowerCase()) > -1) {
                        rows[i].style.display = "";
                    } else {
                        rows[i].style.display = "none";
                    }
                }
            }
        }
    </script>
</body>

<?php
function renderOrderHistoryTable($statusFilter)
{
    global $conn;

    $page = isset($_GET['page']) ? $_GET['page'] : 1;
    $limit = isset($_GET['limit']) ? $_GET['limit'] : 10;
    $start = ($page - 1) * $limit;

    $sql = "SELECT * FROM orderhistory WHERE status='$statusFilter' ORDER BY orderdate DESC, timeoforder DESC LIMIT $start, $limit";
    $result = $conn->query($sql);

    echo '<div class="container-fluid" style="overflow-x:Scroll;">';
    echo '<table class="table" id="myTable">';
    echo '<thead>
            <tr>
                <th>hid</th>
                <th>Time of Order</th>
                <th>Date Ordered</th>
                <th>Date Completed</th>
                <th>customername</th>
                <th>customeraddress</th>
                <th>customerphonenumber</th>
                <th>orderdescription</th>
                <th>order total</th>
                <th>Status</th>
            </tr>
          </thead>';
    echo '<tbody>';

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $hid = $row['hid'];
            $timeoforder = $row['timeoforder'];
            $orderdate = date("d-m-Y", strtotime($row['orderdate']));
            $datecompleted = $row['datecompleted'] ? date("d-m-Y", strtotime($row['datecompleted'])) : null;
            $customername = $row['customername'];
            $customeraddress = $row['customeraddress'];
            $customerphonenumber = $row['customerphonenumber'];
            $orderdescription = $row['orderdescription'];
            $ordertotal = $row['ordertotal'];
            $status = $row['status'];

            echo '<tr onclick="location.href=\'orderhistorydetail.php?hid=' . $hid . '\'">';
            echo '<td>' . $hid . '</td>';
            echo '<td>' . $timeoforder . '</td>';
            echo '<td>' . $orderdate . '</td>';
            echo '<td>' . $datecompleted . '</td>';
            echo '<td>' . $customername . '</td>';
            echo '<td>' . $customeraddress . '</td>';
            echo '<td>' . $customerphonenumber . '</td>';
            echo '<td>';
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
            echo '</td>';
            echo '<td>₱ ' . $ordertotal . '</td>';
            echo '<td class="status ' . $status . '">' . $status . '</td>';
            echo '</tr>';
        }
    } else {
        echo '<tr><td colspan="10">No results</td></tr>';
    }

    echo '</tbody>';
    echo '</table>';
    echo '</div>';
}
?>


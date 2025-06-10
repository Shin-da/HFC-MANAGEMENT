<?php
require '../reusable/redirect404.php';
require '../session/session.php';
require '../database/dbconnect.php';
$current_page = basename($_SERVER['PHP_SELF'], '.php');

if (isset($_GET['download'])) {
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment;filename="order_history.xls"');
    header('Cache-Control: max-age=0');

    $sql = "SELECT * FROM orderhistory ORDER BY oid ASC";
    $result = $conn->query($sql);

    echo "hid\tDate Completed\tOrder ID\tCustomer Name\tCustomer Address\tCustomer Phone Number\tOrder Description\tOrder Total\tDate Ordered\tStatus\n";

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo $row['hid'] . "\t" . date("d-m-Y", strtotime($row['datecompleted'])) . "\t" . $row['oid'] . "\t"
                . $row['customername'] . "\t" . $row['customeraddress'] . "\t" . $row['customerphonenumber'] . "\t"
                . $row['orderdescription'] . "\t" . $row['ordertotal'] . "\t" . $row['orderdate'] . "\t"
                . $row['status'] . "\n";
        }
    }
    exit;
}
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
                <div class="icons">
                    <a href="?download=1" class="icon-link"><i class="bx bx-download"></i> Download Excel</a>
                </div>
            </div>

            <!-- Rest of the code remains unchanged -->
            <!-- Include your table structure and data retrieval as before -->

        </div>
    </section>
</body>
<?php require '../reusable/footer.php'; ?>


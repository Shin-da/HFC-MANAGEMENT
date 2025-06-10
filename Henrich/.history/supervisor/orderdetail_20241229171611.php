<?php
require '../reusable/header.php';
require '../reusable/sidebar.php';
require '../database/dbconnect.php';
?>

<div class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header card-header-primary">
                        <h4 class="card-title ">Order Details</h4>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table">
                                <thead class="text-primary">
                                    <tr>
                                        <th>Order ID</th>
                                        <th>Customer Name</th>
                                        <th>Order Date</th>
                                        <th>Product Name</th>
                                        <th>Quantity</th>
                                        <th>Price</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $oid = $_GET['oid'];
                                    $sql = "SELECT o.oid, c.customername, o.orderdate, p.productname, oi.quantity, p.price 
                                    FROM orders o 
                                    INNER JOIN customerdetails c ON o.customerid = c.customerid 
                                    INNER JOIN orders oi ON o.oid = oi.oid 
                                    INNER JOIN productlist p ON oi.productcode = p.productcode 
                                    WHERE o.oid = '$oid'";
                                    $result = $conn->query($sql);
                                    if ($result->num_rows > 0) {
                                        while ($row = $result->fetch_assoc()) {
                                            echo "<tr>";
                                            echo "<td>" . $row['oid'] . "</td>";
                                            echo "<td>" . $row['customername'] . "</td>";
                                            echo "<td>" . $row['orderdate'] . "</td>";
                                            echo "<td>" . $row['productname'] . "</td>";
                                            echo "<td>" . $row[''] . "</td>";
                                            echo "<td>" . number_format($row['price'], 2) . "</td>";
                                            echo "</tr>";
                                        }
                                    } else {
                                        echo "<tr><td colspan='6'>No records found</td></tr>";
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require '../reusable/footer.php';

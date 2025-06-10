<?php
require '../includes/session.php';
require '../includes/config.php';
?>
<!DOCTYPE html>
<html>

<head>
    <title>SUPPLIER</title>
    <?php require '../reusable/head.php'; ?>
</head>

<body>
    <?php include '../includes/sidebar.php'; ?>
    <?php include '../includes/navbar.php'; ?>
    
        <div class="card">
            <div class="table-container">
                <div class="table-header">
                    <div class="title" style="color:var(--orange-color)">
                        <h2>Transaction with Supplier</h2>
                    </div>
                    <div class="search-box">
                        <i class='bx bx-search-alt-2' style="font-size: 24px"></i>
                        <input type="text" id="myInput" onkeyup="search()"
                            placeholder="Search...">
                    </div>
                </div>
                <div class="container-fluid">
                    <!-- Inventory Tab -->
                    <table class="table" id="myTable">
                        <thead>
                            <tr>
                                <th>Batch Id</th>
                                <th>Date of Arrival</th>
                                <th>Date Encoded</th>
                                <th>Encoder</th>
                                <th>Supplier</th>
                                <th>Total Boxes</th>
                                <th>Total Cost</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php

                            $sql = "SELECT * FROM suppliertransaction";
                            $result = $conn->query($sql);

                            if ($result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) {
                                    $batchid = $row['batchid'];
                                    $dateofarrival = $row['dateofarrival'];
                                    $dateencoded = $row['dateencoded'];
                                    $encoder = $row['encoder'];
                                    $supplier = $row['supplier'];
                                    $totalboxes = $row['totalboxes'];
                                    $totalcost = $row['totalcost']

                            ?>
                                    <tr>
                                        <td><?= $batchid ?></td>
                                        <td><?= $dateofarrival ?></td>
                                        <td><?= $dateencoded ?></td>
                                        <td><?= $encoder ?></td>
                                        <td><?= $supplier ?></td>
                                        <td><?= $totalboxes ?></td>
                                        <td><?= $totalcost ?></td>

                                <?php
                                }
                            } else {
                                echo "<tr><td colspan='6'>0 results</td></tr>";
                            }
                                ?>
                        </tbody>
                    </table>

                    <ul class="pagination">
                        <li><a href="?page=<?= $page - 1 <= 0 ? 1 : $page - 1 ?>" class="prev">&laquo;</a></li>
                        <?php for ($i = 1; $i <= $totalPages = ceil($conn->query("SELECT COUNT(*) FROM suppliertransaction")->fetch_assoc()['COUNT(*)'] / 10); $i++) { ?>
                            <li><a href="?page=<?= $i ?>" class="page <?= $page == $i ? 'active' : '' ?>"><?= $i ?></a></li>
                        <?php } ?>
                        <li><a href="?page=<?= $page + 1 > $totalPages ? $totalPages : $page + 1 ?>" class="next">&raquo;</a></li>
                    </ul>

                </div>
            </div>
        </div>

    </section>

</body>
<?php  include_once("../reusable/footer.php"); ?>
</html>
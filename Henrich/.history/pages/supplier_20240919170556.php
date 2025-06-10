<?php
require '../reusable/redirect404.php';
require '../session/session.php';
require '../database/dbconnect.php';
?>
<!DOCTYPE html>
<html>

<head>
    <title>SUPPLIER</title>
    <?php require '../reusable/header.php'; ?>
</head>

<body>
    <?php

    // Sidebar 
    include '../reusable/sidebar.php';
    ?>

    <section class=" panel">

        <?php
        // TOP NAVBAR
        include '../reusable/navbar.html';
        ?>

        <div class="card">
            <div class="table-container">
                <div class="table-header">
                    <div class="title">
                        <h2>Transaction with Supplier</h2>
                    </div>
                    <div class="search-box">
                        <i class='bx bx-search-alt-2' style="font-size: 24px"></i>
                        <input type="text" id="myInput" onkeyup="search()"
                            placeholder="Search...">
                    </div>
                </div>
                <div class="container">
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
                            if ($result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) {
                                    $batchid = $row['batchid'];
                                    $dateofarrival = $row['dateofarrival'];
                                    $dateencoded = $row['dateencoded'];
                                    $encoder = $row['encoder'];
                                    $supplier = $row['supplier'];
                                    $totalboxes = $row['totalboxes'];
                                    $totalcost = $row['totalcost'];

                            ?>
                                    <tr></tr>
                                        <td><?= $batchid ?></td>
                            ?>
                                    <tr>
                                        <td><?= $iid ?></td>
                                        <td><?= $inventoryName ?></td>
                                        <td><?= $quantity ?></td>
                                        <td><?= $price ?></td>
                                        <td><?= $description ?></td>

                                        <td class="actions">
                                            <a href="inventoryedit.php?iid=<?= $iid ?>" class="btn btn-primary">Edit</a>
                                            <a href="inventorydelete.php?iid=<?= $iid ?>" class="btn btn-danger">Delete</a>
                                        </td>
                                    </tr>
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
<script src="../resources/js/script.js"></script>
<script src="../resources/js/chart.js"></script>
<!-- ======= Charts JS ====== -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.7.1/dist/chart.min.js"></script>
<script src="../resources/js/chartsJS.js"></script>

</html>
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
        include '../reusable/navbarNoSearch.html';
        ?>
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
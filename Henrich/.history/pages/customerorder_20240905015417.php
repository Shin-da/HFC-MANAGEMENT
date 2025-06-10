<?php
require '../reusable/redirect404.php';
require '../session/session.php';
require '../database/dbconnect.php';
?>

<!DOCTYPE html>
<html>

<head>
    <title>Pre Order</title>
    <?php require '../reusable/header.php'; ?>

</head>


<body>
    <?php
    // Sidebar
    include '../reusable/sidebar.php';
    ?>

    <!-- === Pre order === -->
    <section class=" panel">
        <?php
        include '../reusable/navbar.html';
        // include 'inventorynav.html';
        ?>


        <div class="content">
            <div class="container">
                <div class="content-header">
                    <div class="title ">
                        <i class='bx bx-tachometer'></i>
                        <span class="text">Pre Order</span>
                    </div>

                    <div class="dropdown">
                        <i class='bx bx-chevron-down'></i>
                        <div class="dropdown-content"> </div>
                    </div>


                </div>
            </div>
        </div>
        
    </section>
</body>


</html>
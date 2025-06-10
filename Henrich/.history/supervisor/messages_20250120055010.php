<?php
require_once '../includes/config.php';
require_once '../includes/session.php';
$current_page = basename($_SERVER['PHP_SELF'], '.php');
?>
<!DOCTYPE html>
<html>

<head>
    <title>Messages</title>
    <?php require '../reusable/header.php'; ?>
    <link rel="stylesheet" type="text/css" href="../resources/css/table.css">
</head>

<body>
<?php require '../reusable/sidebar.php'; ?>
    <?php include '../reusable/navbar.html'; ?>
    <section class="panel ">
        <div class="container-fluid"> 
            <div class="table-header">
                <div class="title">
                    <h2>Messages</h2>
                </div>
                <!-- <a class="btn add-btn" href="add.stockmovement.php"> <i class="i bx bx-plus"></i>Encode to Inventory </a> -->
            </div>

           
            <div class="">




            </div>
    </section>

</body>

<script src="../resources/js/table.js"></script>
<?php include_once("../reusable/footer.php"); ?>

</html>
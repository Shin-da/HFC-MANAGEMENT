<?php
require '../reusable/redirect404.php';
require '../session/session.php';
require '../database/dbconnect.php';
$current_page = basename($_SERVER['PHP_SELF'], '.php');
?>
<!DOCTYPE html>
<html>

<head>
    <title>Mekeni</title>
    <?php require '../reusable/header.php'; ?>
    <link rel="stylesheet" type="text/css" href="../resources/css/table.css">
</head>

<body>
    <?php include '../reusable/sidebar.php';   // Sidebar   
    ?>

    <!-- === Orders === -->
    <section class=" panel">
        <?php include '../reusable/navbarNoSearch.html'; // TOP NAVBAR         
        ?>

        <div class="container-fluid"> <!-- Stock Management -->
            <div class="table-header">
                <div class="title" style="justify-content: center">
                    <span>
                        <h2>Mekeni </h2>
                    </span>
                    <span style="font-size: 12px;"> </span>
                </div>
            </div>

            <div class="table-header">
                


            </div>
        </div>


    </section>




</body>
<?php require '../reusable/footer.php'; ?>

</html>
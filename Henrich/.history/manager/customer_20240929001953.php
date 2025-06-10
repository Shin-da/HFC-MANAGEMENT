<?php require '../reusable/redirect404.php'; require '../session/session.php';
require '../database/dbconnect.php'; ?>
<!DOCTYPE html>
<html>

<head>
     <title>HOME</title>
     <?php require '../reusable/header.php'; ?>
</head>

<body>
     <?php  include '../reusable/sidebar.php'; // Sidebar  ?>

     <section class="dashboard panel">

          <?php  include '../reusable/navbar.html'; // TOP NAVBAR ?>

          <div class="overview ">


          </div>

          <a href="./admin/admin"></a>

     </section>

</body>

<?= footer(); ?>
</html>
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

         
          <section class=" panel">

               <?php
               // TOP NAVBAR
               include '../reusable/navbar.html';
               ?>

               <div class="overview ">
                    
                   
                   
               </div>

              <a href="./admin/admin"></a>

          </section>

     </body>
     <script src="../resources/js/script.js"></script>
     <script src="../resources/js/chart.js"></script>
     <!-- ======= Charts JS ====== -->
     <script src="https://cdn.jsdelivr.net/npm/chart.js@3.7.1/dist/chart.min.js"></script>
     <script src="../resources/js/chartsJS.js"></script>
</html>
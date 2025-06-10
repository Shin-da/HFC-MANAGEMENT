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

         
          <section class="dashboard panel">

               <?php
               // TOP NAVBAR
               include '../reusable/navbar.html';
               ?>

               <div class="overview ">
                   
                   
               </div>

              <a href="./admin/admin"></a>

          </section>

     </body>

</html>
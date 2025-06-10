<?php
require '../reusable/redirect404.php';
require '../session/session.php';
?>
     <!DOCTYPE html>
     <html>

     <head> 
        <title>HOME</title>
   
        <?php require ''; ?>
       
    </head>


     <body>
          <?php
          // Alert-messages
          // include 'alerts/alert-messages.php';

          // Modals
          // include 'modals/modals.php';

          // Sidebar 
          include '../reusable/sidebar.php';
          ?>

          <!-- === Dashboard === -->
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
     <script src="../resources/js/script.js"></script>
     <script src="../resources/js/chart.js"></script>
     <!-- ======= Charts JS ====== -->
     <script src="https://cdn.jsdelivr.net/npm/chart.js@3.7.1/dist/chart.min.js"></script>
     <script src="../resources/js/chartsJS.js"></script>

     </html>

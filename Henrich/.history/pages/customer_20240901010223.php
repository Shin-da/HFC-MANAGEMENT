<?php

require '../reusable/redirect404.php';

session_start();
if (isset($_SESSION['uid']) && isset($_SESSION['role'])) {
?>
     <!DOCTYPE html>
     <html>

<<<<<<<<<<<<<<  ✨ Codeium Command 🌟  >>>>>>>>>>>>>>>>
     <head>
          <meta name="viewport" content="width=device-width, initial-scale=1.0">

          <!-- Favicon -->
          <link rel="icon" href="images/henrichlogo.png">

          <title>HOME</title>
          <link rel="stylesheet" type="text/css" href="../resources/css/style.css">
          <link rel="stylesheet" type="text/css" href="../resources/css/dashboard.css">
          <link rel="stylesheet" type="text/css" href="../resources/css/sidebar.css">
          <link rel="stylesheet" type="text/css" href="../resources/css/calendar.css">

          <!-- Boxicons CDN Link -->
          <link href='https://unpkg.com/boxicons@2.0.7/css/boxicons.min.css' rel='stylesheet'>

          <!-- datetime -->
          <script src="js/datetime.js"></script>

          <!-- JS for search -->
          <script src="js/search.js"> </script>
          <script>
               function toggleDropdown() {
                    document.getElementById("myDropdown").classList.toggle("show");
               }

               // Close the dropdown if the user clicks outside of it
               window.onclick = function(event) {
                    if (!event.target.matches('.dropbtn')) {
                         var dropdowns = document.getElementsByClassName("dropdown-content");
                         var i;
                         for (i = 0; i < dropdowns.length; i++) {
                              var openDropdown = dropdowns[i];
                              if (openDropdown.classList.contains('show')) {
                                   openDropdown.classList.remove('show');
                              }
                         }
                    }
               }
          </script>

     </head>
<<<<<<<  590e438c-2b18-4b11-aa76-8559f98f11aa  >>>>>>>

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

<?php
} else {
     header("Location:../index.php");
     exit();
}
?>
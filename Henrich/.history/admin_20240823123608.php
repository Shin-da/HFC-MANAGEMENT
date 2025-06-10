<!-- Admin Page -->
<?php
require 'redirect404.php';

session_start();
if (isset($_SESSION['uid']) && isset($_SESSION['role'])) {
?>
     <!DOCTYPE html>
     <html>

     <head>
          <meta name="viewport" content="width=device-width, initial-scale=1.0">

          <!-- Favicon -->
          <link rel="icon" href="images/henrichlogo.png">

          <title>Admin Page</title>
          <link rel="stylesheet" type="text/css" href="css/style.css">
          <link rel="stylesheet" type="text/css" href="css/dashboard.css">
          <link rel="stylesheet" type="text/css" href="css/sidebar.css">
          <link rel="stylesheet" type="text/css" href="css/calendar.css">

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

     <body>
          <?php
          // Alert-messages
          // include 'alerts/alert-messages.php';

          // Modals
          // include 'modals/modals.php';

          // Sidebar 
          include 'admin-sidebar.php';
          ?>

          <section class="panel">

               <?php
               // TOP NAVBAR
               include 'navbar.html';
               ?>

<<<<<<<<<<<<<<  ✨ Codeium Command 🌟 >>>>>>>>>>>>>>>>
               <div class="left-panel">
                    <div class="content-header">
                         <div class="title">
                              <span class="text">Manage Accounts</span>
                         </div>

                         <div class="content">
                              <table class="table table-striped table-hover table-bordered">
                                   <tr>
                                        <th>UID</th>
                                        <th>Email</th>
                                        <th>Role</th>
                                        <th>Actions</th>
                                   </tr>
                                   <?php
                                   include './database/x-dbconnect.php';
                                   $sql = "SELECT * FROM user";
                                   $result = $conn->query($sql);
                         <div class ="content">
                              <?php

                                   if ($result->num_rows > 0) {
                                        while ($row = $result->fetch_assoc()) {
                                             $uid = $row["uid"];
                                             $email = $row["email"];
                                             $role = $row["role"];
                              include './database/x-dbconnect.php';
                              // Display all users
                              $sql = "SELECT * FROM user";

                                             echo "<tr>
                                             <td>" . $uid . "</td>
                                             <td>" . $email . "</td>
                                             <td>" . $role . "</td>
                                             <td>
                                                  <a href='edituser.php?uid=" . $uid . "' class='btn btn-primary'>Edit</a>
                                                  <a href='deleteuser.php?uid=" . $uid . "' class='btn btn-danger'>Delete</a>
                                             </td>
                                             </tr>";
                                        }
                                   } else {
                                        echo "0 results";
                                   }
                                   ?>
                              </table>

                              $result = $conn->query($sql);

                              if ($result->num_rows > 0) {
                                   // output data of each row
                                   while ($row = $result->fetch_assoc()) {
                                        $uid = $row["uid"];
                                        $email = $row["email"];
                                        $role = $row["role"];

                                        echo "<tr>
                                        <td>" . $uid . "</td>
                                        <td>" . $email . "</td>
                                        <td>" . $role . "</td>
                                        </tr>";
                                   } 
                              } else {
                                   echo "0 results";
                              } 
                              ?>
                         </div>


                    </div>
               </div>
               </div>
<<<<<<<  640c53f3-4998-47ce-b4dd-db7177cf8c54  >>>>>>>





          </section>

     </body>
     <script src="js/script.js"></script>
     <script src="chart.js"></script>
     <!-- ======= Charts JS ====== -->
     <script src="https://cdn.jsdelivr.net/npm/chart.js@3.7.1/dist/chart.min.js"></script>
     <script src="js/chartsJS.js"></script>

     </html>

<?php
} else {
     header("Location: index.php");
     exit();
}
?>
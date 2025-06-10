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
<div class="container"> 
     
</div>
               <table class="table table-hover table-striped bg-white mt-3">
                    <thead>
                         <tr>
                              <th>UID</th>
                              <th>Name</th>
                              <th>Role</th>
                              <th>Actions</th>
                         </tr>
                    </thead>
                    <tbody>
                         <?php
                         include 'database/x-dbconnect.php';

                         $sql = "SELECT * FROM user";
                         $result = $conn->query($sql);

                         if ($result->num_rows > 0) {
                              // output data of each row
                              while($row = $result->fetch_assoc()) {
                                   echo "<tr>
                                             <td>" . $row["uid"]. "</td>
                                             <td>" . $row["name"]. "</td>
                                             <td>" . $row["role"]. "</td>
                                             <td>
                                                  <a href='Forms/editUserForm.php?id=" . $row["uid"]. "'>
                                                                 <i class='bx bx-edit'>
                                                                 </i>
                                                  </a>

                                                  <a href='delete.php?id=" . $row["uid"]. "'><i class='bx bx-trash'></i></a>
                                             </td>
                                        </tr>";
                              }
                         }
                         ?>
                    </tbody>
               </table>





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
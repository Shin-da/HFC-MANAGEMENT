<!-- Admin Page -->
<?php
require '../reusable/redirect404.php';

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

     <body>
          <?php
          // Alert-messages
          // include 'alerts/alert-messages.php';

          // Modals
          // include 'modals/modals.php';

          // Sidebar 
          include 'admin-sidebar.php';
          ?>



          <style>
               .table-container {
                    background-color: var(--sidebar-color);
                    border-radius: 5px;
                    padding: 60px;
                    width: 100%;
                    height: 100%;
               }

               .table {
                    width: 70%;
                    /* border-collapse: collapse; */
               }

               .table td {
                    padding: 10px;
                    text-align: center;
               }

               .table th {
                    padding: 10px;
                    text-align: center;
               }

               .table tr:nth-child(even) {
                    background-color: #f2f2f2
               }

               .table tr:hover {
                    background-color: var(--blue);
               }

               .table .clickable-row:hover {
                    background-color: var(--border-color);
                    color: var(--text-color);
                    cursor: pointer;
               }

               .table .clickable-row {
                    cursor: pointer;
               }

               .table th {
                    font-weight: bold;
               }
          </style>

          <section class="panel">

               <?php
               // TOP NAVBAR
               include '../reusable/navbar.html';
               ?>



               <div class="table-container">

                    <div class="content-header">
                         <h2>Users</h2>
                    </div>

                    <table class="table">
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
                              include '../database/x-dbconnect.php';

                              $sql = "SELECT * FROM user";
                              $result = $conn->query($sql);

                              if ($result->num_rows > 0) {
                                   // output data of each row
                                   while ($row = $result->fetch_assoc()) {
                                        echo "<tr class='clickable-row'> 
                                             <td>" . $row["uid"] . "</td>
                                             <td>" . $row["name"] . "</td>
                                             <td>" . $row["role"] . "</td>
                                            <td>
                                                  <a href='Forms/editUserForm.php?id=" . $row["uid"] . "'>
                                                                 <i class='bx bx-edit'>
                                                                 </i>
                                                  </a>

                                                  <a href='delete.php?id=" . $row["uid"] . "'><i class='bx bx-trash'></i></a>
                                             </td> 
                                        </tr>";
                                   }
                              }
                              ?>
                         </tbody>
                    </table>

               </div>





          </section>

     </body>
     <script src="js/script.js"></script>
     <script src="chart.js"></script>
     <!-- ======= Charts JS ====== -->
     <script src="https://cdn.jsdelivr.net/npm/chart.js@3.7.1/dist/chart.min.js"></script>
     <script src="../resources/js/charts.js"></script>

     </html>

<?php
} else {
     header("Location: index.php");
     exit();
}
?>
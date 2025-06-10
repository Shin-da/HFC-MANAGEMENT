<!-- Admin Page -->
<?php
require '../reusable/redirect404.php';
require '../database/dbconnect.php';
$current_page = basename($_SERVER['PHP_SELF'], '.php');

session_start();
?>
     <!DOCTYPE html>
     <html>

     <head>
          <meta name="viewport" content="width=device-width, initial-scale=1.0">
          <title>Admin Page</title>
          <?php require '../reusable/header.php'; ?>
     </head>

     <body>
          <?php
          // Sidebar 
          include 'admin-sidebar.php';
          ?>

          <section class="panel">
               <?php
               // TOP NAVBAR
               include '../reusable/navbarNoSearch.html';
               ?>

               <div class="table-container">
                    <div class="container">
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
                              $sql = "SELECT uid, name, role FROM user";
                              $result = $conn->query($sql);

                              if ($result->num_rows > 0) {
                                   // output data of each row
                                   while ($row = $result->fetch_assoc()) {
                                        echo "<tr class='clickable-row'> 
                                             <td>" . $row["uid"] . "</td>
                                             <td>" . $row["name"] . "</td>
                                             <td>" . $row["role"] . "</td>
                                            <td class='actions'>
                                                  <a href='editUser.php?uid=" . $row["uid"] . "'>
                                                                 <i class='bx bx-edit'>
                                                                 </i>
                                                  </a>

                                                  <a href='delete.php?id=" . $row["uid"] . "'>
                                                  <i class='bx bx-trash'></i></a>
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

     <script src="../resources/js/script.js"></script>

     <!-- ======= Charts JS ====== -->
     <script src="https://cdn.jsdelivr.net/npm/chart.js@3.7.1/dist/chart.min.js"></script>
     <script src="../resources/js/chart.js"></script>

     </html>

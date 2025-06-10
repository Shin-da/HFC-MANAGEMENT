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
          <title>Admin Page</title>
          <?php require '../reusable/header.php'; ?>
     </head>

     <body>
          <?php
          // Sidebar 
          include 'admin-sidebar.php';
          ?>


          <style>
               .table-container {
                    background-color: var(--sidebar-color);
                    border-radius: 5px;
                    /* padding: 60px; */
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
                              require '../database/dbconnect.php';

                              $sql = "SELECT uid, name, role FROM user";
                              $result = $conn->query($sql);

                              if ($result->num_rows > 0) {
                                   // output data of each row
                                   while ($row = $result->fetch_assoc()) {
                                        echo "<tr class='clickable-row'> 
                                             <td>" . $row["uid"] . "</td>
                                             <td>" . $row["name"] . "</td>
                                             <td>" . $row["role"] . "</td>
                                            <td>
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

<?php
} else {
     header("Location: index.php");
     exit();
}
?>

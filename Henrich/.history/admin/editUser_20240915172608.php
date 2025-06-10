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

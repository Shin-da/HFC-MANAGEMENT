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

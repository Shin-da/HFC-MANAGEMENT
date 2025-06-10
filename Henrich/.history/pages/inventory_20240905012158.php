<?php
require '../reusable/redirect404.php';
require '../session/session.php';
require '../database/dbconnect.php';
// The user is logged in; continue with the page contents
include '../database/dbconnect.php';

if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}
$sql = "SELECT * FROM Inventory";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html>

<head>
  <title>INVENTORY</title>

  <!-- CSS -->
  <link rel="stylesheet" type="text/css" href="../resources/css/style.css">
  <link rel="stylesheet" type="text/css" href="../resources/css/sidebar.css">
  <link rel="stylesheet" type="text/css" href="../resources/css/dashboard.css">
  <link rel="stylesheet" type="text/css" href="../resources/css/colors.css">
  <link rel="stylesheet" type="text/css" href="../resources/css/alerts.css">
  <!-- Boxicons CDN Link -->
  <link href='https://unpkg.com/boxicons@2.0.7/css/boxicons.min.css' rel='stylesheet'>

  <!-- For Realtime Search  -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>





<body>
  <?php
  // Alert-messages
  // include 'alerts/alert-messages.php';

  // Modals
  // include 'modals/modals.php';

  // Sidebar 
  include '../reusable/sidebar.php';
  ?>

  <!-- === Inventory === -->
  <section class=" panel">
    <?php
    // TOP NAVBAR
    include '../reusable/navbar.html';
    // include 'inventorynav.html';
    ?>


    <div class="container title">
      <i class='bx bx-tachometer'></i>
      <span class="text">Inventory</span>
    </div>

    <div class="panel-content">
      <!-- INVENTORY TAB -->


      <!-- Inventory Tab -->
    </div>
  </section>

  <script src="../resources/js/script.js"></script>

</body>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>

</html>
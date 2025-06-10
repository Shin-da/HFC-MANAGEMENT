<?php
require '../reusable/redirect404.php';
require '../session/session.php';
require '../database/dbconnect.php';
// The user is logged in; continue with the page contents

if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}
$sql = "SELECT * FROM Inventory";
$result = $conn->query($sql);
$page = isset($_GET['page']) ? $_GET['page'] : 1;
$limit = 10;
$offset = ($page - 1) * $limit;

$sql = "SELECT * FROM Inventory LIMIT $limit OFFSET $offset";
$result = $conn->query($sql);
$inventoryCount = $conn->query("SELECT COUNT(*) FROM Inventory")->fetch_assoc()['COUNT(*)'];
$totalPages = ceil($inventoryCount / $limit);
?>
<!DOCTYPE html>
<html>

<head>
  <title>INVENTORY</title>
  <?php require '../reusable/header.php'; ?>
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
  <!-- === Inventory === -->
  <section class=" panel">
    <div class="panel-content">

      <?php
      // TOP NAVBAR
      include '../reusable/navbarNoSearch.html';
      // include 'inventorynav.html';
      ?>

      <div class="container-fluid">
        <div class="table-header">
          <div class="title">
            <h2>INVENTORY</h2>
          </div>
          <span>
            <a href="add.inventory.php" class="btn btn-primary">
              <i class="bx bx-plus"></i>
              Add New Order
            </a>
          </span>
        </div>

        <div class="container-fluid">

        </div>
      </div>

      <div class="card">
        <div class="table-container">
          <div class="table-header">
            <div class="title">
              <h2>INVENTORY</h2>
            </div>

            <div class="search-box">
              <i class='bx bx-search-alt-2' style="font-size: 24px"></i>
              <input type="text" id="myInput" onkeyup="search()"
                placeholder="Search...">
            </div>
          </div>
          <div class="container-fluid">
           

          </div>
        </div>
      </div>

    </div>
  </section>

  <script src="../resources/js/script.js"></script>

  <script src="../resources/js/search.js"></script>

</body>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>

</html>
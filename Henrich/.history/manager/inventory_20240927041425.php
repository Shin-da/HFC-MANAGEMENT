<?php
require '../reusable/redirect404.php';
require '../session/session.php';
require '../database/dbconnect.php';
// The user is logged in; continue with the page contents


?>
<!DOCTYPE html>
<html>

<head>
  <title>INVENTORY</title>
  <?php require '../reusable/header.php'; ?>
  <link rel="stylesheet" type="text/css" href="../resources/css/table.css">
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

    <?php
    // TOP NAVBAR
    include '../reusable/navbarNoSearch.html';
    ?>
    <!-- Stock Management -->
    <div class="container-fluid">
      <div class="table-header">
        <div class="title">
          <h2>INVENTORY</h2>
          <h3>Stock Management</h3>
        </div>

        <div class="search-box">
          <i class='bx bx-search-alt-2' style="font-size: 24px"></i>
          <input type="text" id="myInput" onkeyup="search()"
            placeholder="Search...">
        </div>
      </div>

      <?php
      $stockManagementTableSQL = "SELECT * FROM inventory LIMIT 10"; // We only want to display 10 items for now

      if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
      }

      $sql = $stockManagementTableSQL;
      $result = $conn->query($sql);
      ?>
      <div class="container-fluid">
        <!-- Inventory Tab -->
        <table class="table" id="myTable">
          <thead>
            <tr>
              <th>Inventory ID</th>
              <th>Product Code</th>
              <th>Product Description</th>
              <th>Quantity</th>
              <th>On Hand</th>
              <th>Category</th>
              
            </tr>
          </thead>
          <tbody>
            <?php
            if ($result->num_rows > 0) {
              while ($row = $result->fetch_assoc()) {
                $iid = $row['iid'];
                $productcode = $row['productcode'];
                $productdescription = $row['productdescription'];
                $category = $row['category'];
                $onhand= $row['onhand'];
                $dateupdated = $row['dateupdated'];
            ?>
                <tr>
                  <td><?= $iid ?></td>
                  <td><?= $productcode ?></td>
                  <td><?= $productdescription ?></td>
                  <td><?= $category ?></td>
                  <td><?= $onhand ?></td>
                  <td><?= $dateupdated ?></td>

                  
                </tr>
            <?php
              }
            } else {
              echo "<tr><td colspan='8'>0 results</td></tr>";
            }
            ?>
          </tbody>
        </table>
        <div class="container">
          <ul class="pagination">
            <li><a href="?page=<?= $page - 1 <= 0 ? 1 : $page - 1 ?>" class="prev">&laquo;</a></li>
            <?php for ($i = 1; $i <= $totalPages; $i++) { ?>
              <li><a href="?page=<?= $i ?>" class="page <?= $page == $i ? 'active' : '' ?>"><?= $i ?></a></li>
            <?php } ?>
            <li><a href="?page=<?= $page + 1 > $totalPages ? $totalPages : $page + 1 ?>" class="next">&raquo;</a></li>
          </ul>
        </div>
      </div>
    </div>

    <!-- batch Encoded -->
    <div class="container-fluid">
      <div class="table-header">
        <div class="title">
          <h2>Encoding History</h2>
          <h3>Encoded by Batch</h3>
        </div>
        <span>
          <a href="add.inventory.php" class="btn btn-primary">
            <i class="bx bx-plus"></i>
            Add To Inventory
          </a>
        </span>
        <div class="search-box">
          <i class='bx bx-search-alt-2' style="font-size: 24px"></i>
          <input type="text" id="myInput" onkeyup="search()"
            placeholder="Search...">
        </div>
      </div>
      <?php
        $stockManagementTableSQL = "SELECT * FROM inventoryhistory LIMIT 10"; // We only want to display 10 items for now

        if ($conn->connect_error) {
          die("Connection failed: " . $conn->connect_error);
        }

        $sql = $stockManagementTableSQL;
        $result = $conn->query($sql);
        ?>
      <div class="container-fluid">
        <!-- Inventory Tab -->
        <table class="table" id="myTable">
          <thead>
            <tr>
              <th>Batch Id</th>
              <th>Date of Arrival</th>
              <th>Encoder</th>
              <th>Date Encoded</th>
              <th>Description</th>
              <th>Date Stock In</th>
              <th>Date Stock Out</th>
              <th>Total Boxes</th>
              <th>Total Weight</th>
              <th>Total Cost</th>
            </tr>
          </thead>
          <tbody>
            <?php
            if ($result->num_rows > 0) {
              while ($row = $result->fetch_assoc()) {
                $batchid = $row['batchid'];
                $dateofarrival = $row['dateofarrival'];
                $encoder = $row['encoder'];
                $dateencoded = $row['dateencoded'];
                $description = $row['description'];
                $datestockin = $row['datestockin'];
                $datestockout = $row['datestockout'];
                $totalboxes = $row['totalboxes'];
                $totalweight = $row['totalweight'];
                $totalcost = $row['totalcost'];
            ?>
                <tr>
                  <td><?= $batchid ?></td>
                  <td><?= $dateofarrival ?></td>
                  <td><?= $encoder ?></td>
                  <td><?= $dateencoded ?></td>
                  <td><?= $description ?></td>
                  <td><?= $datestockin ?></td>
                  <td><?= $datestockout ?></td>
                  <td><?= $totalboxes ?></td>
                  <td><?= $totalweight ?></td>
                  <td><?= $totalcost ?></td>
                </tr>

            <?php
              }
            } else {
              echo "<tr><td colspan='10'>0 results</td></tr>";
            }
            ?>
          </tbody>
        </table>
        <div class="container">
          <ul class="pagination">
            <li><a href="?page=<?= $page - 1 <= 0 ? 1 : $page - 1 ?>" class="prev">&laquo;</a></li>
            <?php for ($i = 1; $i <= $totalPages; $i++) { ?>
              <li><a href="?page=<?= $i ?>" class="page <?= $page == $i ? 'active' : '' ?>"><?= $i ?></a></li>
            <?php } ?>
            <li><a href="?page=<?= $page + 1 > $totalPages ? $totalPages : $page + 1 ?>" class="next">&raquo;</a></li>
          </ul>
        </div>
      </div>
    </div>

    <!-- inventory batch details -->
    <div class="container-fluid">
      <div class="table-header">
        <div class="title">
          <h2>Inventory Batch Details</h2>
          <h3>Batch Details</h3>
        </div>
        <span>
          <a href="add.inventory.php" class="btn btn-primary">
            <i class="bx bx-plus"></i>
            Add To Inventory
          </a>
        </span>
        <div class="search-box">
          <i class='bx bx-search-alt-2' style="font-size: 24px"></i>
          <input type="text" id="myInput" onkeyup="search()"
            placeholder="Search...">
        </div>
      </div>
      <?php
      $stockManagementTableSQL = "SELECT * FROM inventoryhistory LIMIT 10"; // We only want to display 10 items for now

      if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
  </section>

  <script src="../resources/js/script.js"></script>

  <script src="../resources/js/search.js"></script>

</body>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>

</html>
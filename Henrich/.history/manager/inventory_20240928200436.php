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
  <?php
  require '../reusable/header.php';
  ?>
  <link rel="stylesheet" type="text/css" href="../resources/css/table.css">
</head>

<body>
  <?php
  //success message
  if (isset($_GET['success'])) {
    echo '<div class="success">' . $_GET['success'] . '</div>';
  } else if (isset($_GET['error'])) {
    echo '<div class="error">' . $_GET['error'] . '</div>';
  }

  // Sidebar 
  include '../reusable/sidebar.php';
  ?>
  <!-- === Inventory === -->
  <section class=" panel">
    <?php
    // TOP NAVBAR
    include '../reusable/navbarNoSearch.html';
    ?>

    <!-- pagination -->
    <?php
    // pagination for inventory table
    $page = isset($_GET['page']) ? $_GET['page'] : 1;
    $limit = 10;
    $start = ($page - 1) * $limit;
    $items = $conn->query("SELECT * FROM inventory LIMIT $start, $limit");
    ?>

    <?php
    // pagination for inventory history table
    $page = isset($_GET['page']) ? $_GET['page'] : 1;
    $limit = 10;
    $start = ($page - 1) * $limit;
    $items = $conn->query("SELECT * FROM inventoryhistory LIMIT $start, $limit");
    ?>

    <?php
    // pagination for stock management table
    $page = isset($_GET['page']) ? $_GET['page'] : 1;
    $limit = 10;
    $start = ($page - 1) * $limit;
    $items = $conn->query("SELECT * FROM inventorybatchdetails  LIMIT $start, $limit");
    ?>

    <!-- === Batch Details  === -->
    <div class="container-fluid">
      <div class="table-header">
        <div class="title">
          <h2>BATCH DETAILS</h2>
          <h3>Batch Details (display only)</h3>
        </div>

        <div class="search-box">
          <i class='bx bx-search-alt-2' style="font-size: 24px"></i>
          <input type="text" id="myInput" onkeyup="search()"
            placeholder="Search...">
        </div>
      </div>

      <?php
      $batchDetailsTableSQL = "SELECT * FROM inventorybatchdetails LIMIT 10"; // We only want to display 10 items for now

      if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
      }

      $sql = $batchDetailsTableSQL;
      $result = $conn->query($sql);
      ?>

      <div class="container-fluid">
        <table class="table" id="">
          <thead>
            <tr>
              <th>Batch ID</th>
              <th>Batch Name</th>
              <th>Description</th>
              <th>Quantity</th>
              <th>Expiry Date</th>
              <th>Price</th>
            </tr>
          </thead>

          <tbody>
            <?php
            if ($result->num_rows > 0) {
              while ($row = $result->fetch_assoc()) {
                $batchid = $row['batchid'];
                $batchname = $row['batchname'];
                $description = $row['description'];
                $quantity = $row['quantity'];
                $expirydate = $row['expirydate'];
                $price = $row['price'];
            ?>
                <tr>
                  <td><?= $batchid ?></td>
                  <td><?= $batchname ?></td>
                  <td><?= $description ?></td>
                  <td><?= $quantity ?></td>
                  <td><?= $expirydate ?></td>
                  <td><?= $price ?></td>
                </tr>
            <?php
              }
            } else {
              echo "0 results";
            }
            ?>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Stock Management -->
    <div class="container-fluid">
      <div class="table-header">
        <div class="title">
          <h2>INVENTORY</h2>
          <h3>Stock Management (display only)</h3>
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
              <th>Category</th>
              <th>On Hand</th>
              <th>Date Updated</th>
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
                $onhand = $row['onhand'];
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
          <h3>Encoded by Batch (adding and display only)</h3>
        </div>

        <div class="search-box">
          <i class='bx bx-search-alt-2' style="font-size: 24px"></i>
          <input type="text" id="myInput" onkeyup="search()"
            placeholder="Search...">
        </div>
      </div>
      <?php
      $inventoryHistoryTableSQL = "SELECT * FROM inventoryhistory LIMIT 10"; // We only want to display 10 items for now

      if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
      }

      $sql = $inventoryHistoryTableSQL;
      $result = $conn->query($sql);
      ?>
      <div class="container-fluid">
        <!-- Inventory Tab -->
        <table class="table" id="myTable">
          <thead>
            <tr>
              <th>Batch ID</th>
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



  </section>

  <script src="../resources/js/script.js"></script>

  <script src="../resources/js/search.js"></script>

</body>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>

</html>
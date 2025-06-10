stockmovement.php

<?php require '../reusable/redirect404.php'; require '../session/session.php'; require '../database/dbconnect.php'; ?>

<!DOCTYPE html>
<html>

<head>
  <title>INVENTORY</title>
  <?php require '../reusable/header.php';?>
  <link rel="stylesheet" type="text/css" href="../resources/css/table.css">
</head>

<body>
  <?php
  if (isset($_GET['success'])) {//success message
    echo '<div class="success">' . $_GET['success'] . '</div>';
  } else if (isset($_GET['error'])) {
    echo '<div class="error">' . $_GET['error'] . '</div>';
  } ?>

<?php  include '../reusable/sidebar.php';  // Sidebar  ?>
 
  <section class=" panel"><!-- === Inventory === -->
    <?php include '../reusable/navbarNoSearch.html'; // TOP NAVBAR ?>

    <?php  // pagination for stock_movement table  
      $page = isset($_GET['page']) ? $_GET['page'] : 1; 
      $limit = 10;
      $start = ($page - 1) * $limit;
      $items = $conn->query("SELECT * FROM  stock_movement LIMIT $start, $limit");
    ?>


    <!-- === Batch Details  === -->
    <div class="container-fluid">
      <div class="table-header">
        <div class="title">
          <h2>BATCH DETAILS</h2>
          <h3>Batch Details (display only)</h3>
        </div>

        <div class="add-button">
          <a class="btn btn-primary" href="add.inventorybatchdetails.php">Add Batch</a>
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
              <th>#</th>
              <th>Batch ID</th>
              <th>Product Code</th>
              <th>Quantity</th>
              <th>Weight</th>
              <th>Price</th>
            </tr>
          </thead>

          <tbody>
            <?php
            if ($result->num_rows > 0) {
              while ($row = $result->fetch_assoc()) {
                $ibdid = $row['ibdid'];
                $batchid = $row['batchid'];
                $productcode = $row['productcode'];
                $quantity = $row['quantity'];
                $weight = $row['weight'];
                $price = $row['price'];
                
            ?>
                <tr>
                  <td><?= $ibdid ?></td>
                  <td><?= $batchid ?></td>
                  <td><?= $productcode ?></td>
                  <td><?= $quantity?></td>
                  <td><?= $weight ?></td>
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
  
</body>
<?php  include_once("../reusable/footer.php"); ?>
</html>
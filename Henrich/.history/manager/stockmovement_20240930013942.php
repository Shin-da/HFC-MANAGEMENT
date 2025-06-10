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

<?php  include '../reusable/sidebar.php';  // Sidebar  ?>
 
  <section class=" panel"><!-- === STOCK MOVEMENT === -->
    <?php include '../reusable/navbarNoSearch.html'; // TOP NAVBAR ?>

    <?php  // pagination for stock_movement table  
      $page = isset($_GET['page']) ? $_GET['page'] : 1; 
      $limit = 10;
      $start = ($page - 1) * $limit;
      $items = $conn->query("SELECT * FROM  stockmovement  LIMIT $start, $limit");
    ?>

    <div class="container-fluid">  <!-- === STOCK MOVEMENT === -->
      <div class="table-header">
        <div class="title">
          <h2>Stock Movement</h2>
          <h3>Batch Details (display only)</h3>
        </div>

        <div class="add-button">
          <a class="btn btn-primary" href="add.stockmovement.php">Add</a>
        </div>

        <div class="search-box">
          <i class='bx bx-search-alt-2' style="font-size: 24px"></i>
          <input type="text" id="myInput" onkeyup="search()"
            placeholder="Search...">
        </div>
      </div>

      <?php
      $stockMovementTableSQL = "SELECT * FROM stockmovement LIMIT 10"; // We only want to display 10 items for now

      if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
      }

      $sql = $stockMovementTableSQL;
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

  </section>
  
</body>
<?php  include_once("../reusable/footer.php"); ?>
</html>
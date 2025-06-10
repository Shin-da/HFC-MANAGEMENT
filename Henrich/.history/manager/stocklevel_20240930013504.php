<?php require '../reusable/redirect404.php'; require '../session/session.php'; require '../database/dbconnect.php'; ?>

<!DOCTYPE html>
<html>

<head>
  <title>Stock Management</title>
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


    <?php // pagination for stock management table
      $page = isset($_GET['page']) ? $_GET['page'] : 1;
      $limit = 10;
      $start = ($page - 1) * $limit;
      $items = $conn->query("SELECT * FROM inventorybatchdetails  LIMIT $start, $limit");
    ?>

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

  </section>
  
</body>
<?php  include_once("../reusable/footer.php"); ?>
</html>
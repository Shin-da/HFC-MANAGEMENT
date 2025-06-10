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
    <?php
    // TOP NAVBAR
    include '../reusable/navbar.html';
    // include 'inventorynav.html';
    ?>
    <div class="card">
      <div class="table-header">
        <div class="title">
          <h2>encode</h2>
        </div>
        <a href="addInventory.php" class="buttonn"><i class='bx bx-plus'></i></a>
        <a href="inventory.php" class="buttonn"><i class='bx bx-refresh'></i></a>
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
        <div class="container">
          <!-- Inventory Tab -->
          <table class="table" id="myTable">
            <thead>
              <tr>
                <th>Inventory ID</th>
                <th>Inventory Name</th>
                <th>Quantity</th>
                <th>Price</th>
                <th>Description</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
              <?php
              if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                  $iid = $row['iid'];
                  $inventoryName = $row['inventoryName'];
                  $quantity = $row['quantity'];
                  $price = $row['price'];
                  $description = $row['description'];
              ?>
                  <tr>
                    <td><?= $iid ?></td>
                    <td><?= $inventoryName ?></td>
                    <td><?= $quantity ?></td>
                    <td><?= $price ?></td>
                    <td><?= $description ?></td>

                    <td class="actions">
                      <a href="inventoryedit.php?iid=<?= $iid ?>" class="btn btn-primary">Edit</a>
                      <a href="inventorydelete.php?iid=<?= $iid ?>" class="btn btn-danger">Delete</a>
                    </td>
                  </tr>
              <?php
                }
              } else {
                echo "<tr><td colspan='6'>0 results</td></tr>";
              }
              ?>
            </tbody>
          </table>
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
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

    <div class="panel-content">
      <div class="container">
        <div class="table-header">
          <i class='bx bx-tachometer'></i>
          <span class="text">Inventory</span>
          <a href="addinventory.php" class="buttonn">Add Inventory</a>

          
        </div>
        <!-- Inventory Tab -->
        <table class="table">
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
      </div>
    </div>
  </section>

  <script src="../resources/js/script.js"></script>

</body>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>

</html>
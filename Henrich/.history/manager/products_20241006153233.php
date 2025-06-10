<style>
.alert {
  position: fixed;
  top: 50px;
  right: 0;
  background-color: #ff9800;
  padding: 10px;
  border-radius: 5px;
  z-index: 9999;
}
</style>
<?php
require '../database/dbconnect.php';

$items = $conn->query("SELECT * FROM inventory WHERE onhand <= 5");
if ($items->num_rows > 0) {
  echo '<div class="alert">';
  echo '<h4>Low Stock Alert!</h4>';
  while ($row = $items->fetch_assoc()) {
    echo $row['productdescription'] . ' - ' . $row['onhand'] . ' items left<br/>';
  }
  echo '</div>';
}



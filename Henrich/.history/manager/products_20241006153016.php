<?= 
"product" 
?>
<?php
require '../database/dbconnect.php';



$items = $conn->query("SELECT * FROM inventory WHERE onhand <= 5");
if ($items->num_rows > 0) {
  echo '<div style=" z-index: 9999; position: absolute; right: 0; top: 50px; background-color: #ff9800; padding: 10px; border-radius: 5px;">';
  echo '<h4>Low Stock Alert!</h4>';
  while ($row = $items->fetch_assoc()) {
    echo $row['productdescription'] . ' - ' . $row['onhand'] . ' items left<br/>';
  }
  echo '</div>';
}

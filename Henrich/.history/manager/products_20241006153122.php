<?php
require '../database/dbconnect.php';
?>


<h1>Low Stock Alert!</h1>


<?php
$items = $conn->query("SELECT * FROM inventory WHERE onhand <= 5");
if ($items->num_rows > 0) {
  while ($row = $items->fetch_assoc()) {
    echo $row['productdescription'] . ' - ' . $row['onhand'] . ' items left<br/>';
  }
} else {
  echo 'No low stock items found.';
}



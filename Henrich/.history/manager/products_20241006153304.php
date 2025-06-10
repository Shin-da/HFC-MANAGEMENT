/*************  ✨ Codeium Command 🌟  *************/
<style>
.alert {
  position: fixed;
  top: 50px;
  right: 10px;
  right: 0;
  background-color: #ff9800;
  padding: 15px;
  border-radius: 10px;
  padding: 10px;
  border-radius: 5px;
  z-index: 9999;
  box-shadow: 0 0 10px rgba(0,0,0,0.2);
  width: 300px;
}
.alert h4 {
  margin-top: 0;
  font-weight: bold;
  color: #fff;
}
.alert ul {
  list-style: none;
  padding: 0;
  margin: 0;
}
.alert li {
  padding: 5px 0;
  border-bottom: 1px solid rgba(0,0,0,0.1);
}
.alert li:last-child {
  border-bottom: none;
}
.alert .product-name {
  font-weight: bold;
}
</style>
<?php
require '../database/dbconnect.php';

$items = $conn->query("SELECT * FROM inventory WHERE onhand <= 5");
if ($items->num_rows > 0) {
  echo '<div class="alert">';
  echo '<h4>Low Stock Alert!</h4>';
  echo '<ul>';
  while ($row = $items->fetch_assoc()) {
    echo '<li><span class="product-name">' . $row['productdescription'] . '</span> - <span class="quantity">' . $row['onhand'] . '</span> items left</li>';
    echo $row['productdescription'] . ' - ' . $row['onhand'] . ' items left<br/>';
  }
  echo '</ul>';
  echo '</div>';
}



/******  afd2871a-e418-429c-bd60-47400c37760f  *******/
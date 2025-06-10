<style>
  .alert {
    background-color: var(--sidebar-color);
    padding: 25px;
    border-radius: 10px;
    border: 2px dashed var(--accent-color);
    z-index: 9999;
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
  .alert .close {
    border: 1px solid var(--accent-color);
    padding: 10px;
    color: var(--accent-color);
    cursor: pointer;
    float:right;
    font-size: 1.5em;
    display: inline-block;
    width: 30px;
    height: 30px;
    line-height: 30px;
    text-align: center;
  }
</style>
<?php
  require '../database/dbconnect.php';

  $items = $conn->query("SELECT * FROM inventory WHERE onhand <= 10 ORDER BY onhand ASC");
  if ($items->num_rows > 0) {
    echo '<div class="alert">';
    echo '<span class="close" onclick="this.parentElement.style.display = \'none\'"><i class="bx bx-x"></i></span>';
    echo '<h4>Low Stock Alert!</h4>';
    echo '<ul>';
    while ($row = $items->fetch_assoc()) {
      $quantity = $row['onhand'];
      $legend = "";
      if ($quantity == 0) {
        $legend = "<span style='color: red'>OUT OF STOCK</span>";
      } else if ($quantity <= 5) {
        $legend = "<span style='color: orange'>LOW STOCK</span>";
      }
      echo '<li><span class="product-name">' . $row['productname'] . '</span> - <span class="quantity">' . $row['onhand'] . '</span> units left ' . $legend . '</li>';
    }
    echo '</ul>';
    echo '</div>';
  }
?>


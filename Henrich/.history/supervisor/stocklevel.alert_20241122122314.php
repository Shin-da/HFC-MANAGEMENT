<style>
  .alert {
    width: 100%;
    border-bottom: 1px solid var(--orange-color);
    z-index: 9999;
  }
  .alert-wrapper {
    width: 100%;
    display: flex;
    flex-direction: column;
  }
  .alert h4 {
    margin-top: 0;
    font-weight: bold;
    color: #DD5746;
    display: flex;
    align-items: center;
    background-color: var(--orange-color);
    padding: 10px;
  }
  .alert h4 i {
    margin-right: 5px;
    font-size: 2em; /* Make the icon big */
  }
  .alert .product-name {
    font-weight: bold;
  }
  .alert .close {
    background-color: var(--sidebar-color);
    border: 1px solid #fff;
    cursor: pointer;
    border-radius: 2px;
    float: right;
    font-size: 1.5em;
    display: inline-block;
    margin-left: 10px;
  }
  .alert .close:hover {
    background-color: #fff; 
    color: var(--accent-color);
  }
  .alert p {
    background-color: var(--sidebar-color);
    padding: 10px;
  }
</style>

<?php
  require '../database/dbconnect.php';

  $items = $conn->query("SELECT * FROM inventory WHERE onhand <= 10 ORDER BY onhand ASC");
  $outOfStockItems = $conn->query("SELECT * FROM inventory WHERE onhand = 0 ORDER BY productname ASC");

  if (isset($_COOKIE['show_stock_alert'])) {
    $show_stock_alert = $_COOKIE['show_stock_alert'];
  } else {
    $show_stock_alert = true;
  }

  if ($outOfStockItems->num_rows > 0 && $show_stock_alert) {
    echo '<div class="alert-wrapper">';
    echo '<div class="alert">';
    echo '<h4><i class="bx bx-error" style="color: red"></i> Out of Stock Alert! <span class="close" onclick="this.parentElement.parentElement.style.display = \'none\'"><i class="bx bx-x"></i></span></h4>';
    echo '<p>';
    while ($row = $outOfStockItems->fetch_assoc()) {
      echo '<span class="product-name">' . $row['productname'] . '</span> - <span style="color: var(--accent-color)">OUT OF STOCK</span><br />';
    }
    echo '</p>';
    echo '</div>';
    echo '</div>';
  }

  if ($items->num_rows > 0 && $show_stock_alert) {
    echo '<div class="alert-wrapper">';
    echo '<div class="alert">';
    echo '<h4><i class="bx bx-error" style="color: orange"></i> Low Stock Alert! <span class="close" onclick="this.parentElement.parentElement.style.display = \'none\'"><i class="bx bx-x"></i></span></h4>';
    echo '<p>';
    while ($row = $items->fetch_assoc()) {
      $quantity = $row['onhand'];
      $legend = '';
      if ($quantity > 0) {
        if ($quantity <= 5) {
          $legend = "<span style='color: orange'>VERY LOW STOCK</span>";
        } else {
          $legend = "<span style='color: yellow'>LOW STOCK</span>";
        }
        echo '<span class="product-name">' . $row['productname'] . '</span> - <span class="quantity">' . $quantity . '</span> units left ' . $legend . '<br />';
      }
    }
    echo '</p>';
    echo '</div>';
    echo '</div>';
  }

  if (isset($_POST['close_stock_alert'])) {
    setcookie('show_stock_alert', false, time() + (86400 * 30), '/');
  }
?>


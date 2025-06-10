<style>
  .alert {
    /* position: fixed;
    top: 0;
    left: 0; */
    width: 100%;
    background-color: var(--sidebar-color);
    padding: 10px;
    border-bottom:  1px solid var(--orange-color);
    z-index: 9999;
  }
  .out-of-stock-alert {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    background-color: #DD5746;
    padding: 10px;
    border-bottom:  1px solid var(--accent-color);
    z-index: 9999;
  }
  .alert h4, .out-of-stock-alert h4 {
    margin-top: 0;
    font-weight: bold;
    color: #DD5746;
    display: flex;
    align-items: center;
  }
  .out-of-stock-alert h4 {
    color: var(--accent-color);
  }
  .alert h4 i, .out-of-stock-alert h4 i {
    margin-right: 5px;
  }
  .alert .product-name, .out-of-stock-alert .product-name {
    font-weight: bold;
  }
  .alert .close, .out-of-stock-alert .close {
    background-color: var(--sidebar-color);
    border: 1px solid #fff;
    cursor: pointer;
    border-radius: 2px;
    float: right;
    font-size: 1.5em;
    display: inline-block;
    width: 30px;
    height: 30px;
    text-align: center;
  }
  .alert .close:hover, .out-of-stock-alert .close:hover {
    background-color: #fff; 
    color: var(--accent-color);
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
    echo '<div class="out-of-stock-alert">';
    echo '<i class="bx bx-error"></i><span class="close" onclick="this.parentElement.style.display = \'none\'"><i class="bx bx-x"></i></span>';
    echo '<h4><i class="bx bx-exclamation" style="color: red"></i> Out of Stock Alert!</h4>';
    echo '<ul style="display: flex; flex-wrap: wrap; justify-content: center;">';
    while ($row = $outOfStockItems->fetch_assoc()) {
      echo '<li style="margin: 5px; text-align: center;"><span class="product-name">' . $row['productname'] . '</span> - <span style="color: var(--accent-color)">OUT OF STOCK</span></li>';
    }
    echo '</ul>';
    echo '</div>';
  }

  if ($items->num_rows > 0 && $show_stock_alert) {
    echo '<div class="alert">';
    echo '<i class="bx bx-error"></i><span class="close" onclick="this.parentElement.style.display = \'none\'"><i class="bx bx-x"></i></span>';
    echo '<h4><i class="bx bx-exclamation" style="color: orange"></i> Low Stock Alert!</h4>';
    echo '<ul style="display: flex; flex-wrap: wrap; justify-content: center;">';
    while ($row = $items->fetch_assoc()) {
      $quantity = $row['onhand'];
      $legend = '';
      if ($quantity > 0) {
        if ($quantity <= 5) {
          $legend = "<span style='color: orange'>VERY LOW STOCK</span>";
        } else {
          $legend = "<span style='color: yellow'>LOW STOCK</span>";
        }
        echo '<li style="margin: 5px; text-align: center;"><span class="product-name">' . $row['productname'] . '</span> - <span class="quantity">' . $quantity . '</span> units left ' . $legend . '</li>';
      }
    }
    echo '</ul>';
    echo '</div>';
  }

  if (isset($_POST['close_stock_alert'])) {
    setcookie('show_stock_alert', false, time() + (86400 * 30), '/');
  }



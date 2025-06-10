<style>
  .alert, .out-of-stock-alert {
    width: 100%;
    padding: 10px;
    border-bottom: 1px solid var(--orange-color);
    z-index: 9999;
    display: flex;
    flex-direction: column;
    align-items: center;
    position: relative;
  }

  .alert {
    background-color: var(--sidebar-color);
  }

  .out-of-stock-alert {
    background-color: #DD5746;
  }

  .alert h4, .out-of-stock-alert h4, .alert i, .out-of-stock-alert i, .alert .close, .out-of-stock-alert .close {
    font-size: 1.2em;
    display: flex;
    align-items: center;
    margin: 0;
  }

  .alert h4 i, .out-of-stock-alert h4 i {
    margin-right: 10px;
    font-size: 1.5em;
  }

  .alert .product-name, .out-of-stock-alert .product-name {
    font-weight: bold;
  }

  .alert .close, .out-of-stock-alert .close {
    position: absolute;
    top: 10px;
    right: 10px;
    background-color: var(--sidebar-color);
    border: 1px solid #fff;
    cursor: pointer;
    border-radius: 2px;
    width: 30px;
    height: 30px;
    text-align: center;
    line-height: 30px;
  }

  .alert .close:hover, .out-of-stock-alert .close:hover {
    background-color: #fff;
    color: var(--accent-color);
  }

  .alert ul, .out-of-stock-alert ul {
    list-style: none;
    padding: 0;
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    margin: 10px 0 0;
  }

  .alert li, .out-of-stock-alert li {
    margin: 5px;
    text-align: center;
  }
</style>

<?php
  require '../database/dbconnect.php';

  $items = $conn->query("SELECT * FROM inventory WHERE onhand <= 10 ORDER BY onhand ASC");
  $outOfStockItems = $conn->query("SELECT * FROM inventory WHERE onhand = 0 ORDER BY productname ASC");

  $show_stock_alert = $_COOKIE['show_stock_alert'] ?? true;

  if ($outOfStockItems->num_rows > 0 && $show_stock_alert) {
    echo '<div class="out-of-stock-alert">';
    echo '<i class="bx bx-error" style="font-size: 2em;"></i><span class="close" onclick="this.parentElement.style.display = \'none\'"><i class="bx bx-x"></i></span>';
    echo '<h4><i class="bx bx-exclamation"></i> Out of Stock Alert!</h4>';
    echo '<ul>';
    while ($row = $outOfStockItems->fetch_assoc()) {
      echo '<li><span class="product-name">' . $row['productname'] . '</span> - <span style="color: var(--accent-color)">OUT OF STOCK</span></li>';
    }
    echo '</ul>';
    echo '</div>';
  }

  if ($items->num_rows > 0 && $show_stock_alert) {
    echo '<div class="alert">';
    echo '<i class="bx bx-error" style="font-size: 2em;"></i><span class="close" onclick="this.parentElement.style.display = \'none\'"><i class="bx bx-x"></i></span>';
    echo '<h4><i class="bx bx-exclamation" style="color: orange"></i> Low Stock Alert!</h4>';
    echo '<ul>';
    while ($row = $items->fetch_assoc()) {
      $quantity = $row['onhand'];
      $legend = $quantity <= 5 ? "<span style='color: orange'>VERY LOW STOCK</span>" : "<span style='color: yellow'>LOW STOCK</span>";
      echo '<li><span class="product-name">' . $row['productname'] . '</span> - <span class="quantity">' . $quantity . '</span> units left ' . $legend . '</li>';
    }
    echo '</ul>';
    echo '</div>';
  }

  if (isset($_POST['close_stock_alert'])) {
    setcookie('show_stock_alert', false, time() + (86400 * 30), '/');
  }
?>


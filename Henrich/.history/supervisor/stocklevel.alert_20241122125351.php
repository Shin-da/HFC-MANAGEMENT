<style>
  .alert, .out-of-stock-alert {
    background-color: var(--sidebar-color);
    width: 100%;
    border-bottom: 1px solid var(--orange-color);
    z-index: 9999;
  }
  .out-of-stock-alert {
    border-bottom: 1px solid var(--accent-color);
  }
  .alert-content, .out-of-stock-content {
    display: flex;
    align-items: center;
    background-color: var(--orange-color);
    padding: 10px;
    justify-content: space-between;
  }
  .out-of-stock-content {
    background-color: #DD5746;
  }
  .alert-content h4, .out-of-stock-content h4 {
    margin-top: 0;
    font-weight: bold;
    color: #DD5746;
  }
  .out-of-stock-content h4 {
    color: var(--accent-color);
  }
  .alert-content i, .out-of-stock-content i {
    margin-right: 5px;
    font-size: 2em;
  }
  .alert-content .product-name, .out-of-stock-content .product-name {
    font-weight: bold;
  }
  .alert-content .close, .out-of-stock-content .close {
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
    position: absolute;
    right: 10px;
  }
  .alert-content .close:hover, .out-of-stock-content .close:hover {
    background-color: #fff;
    color: var(--accent-color);
  }
  .alert-content p, .out-of-stock-content p {
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
    echo '<div class="out-of-stock-alert">';
    echo '<div class="out-of-stock-content">';
    echo '<i class="bx bx-error"></i><span class="close" onclick="this.parentElement.parentElement.style.display = \'none\'"><i class="bx bx-x"></i></span>';
    echo '<h4><i class="bx bx-exclamation" style="color: red"></i> Out of Stock Alert!</h4>';
    echo '</div>';
    echo '<p>';
    while ($row = $outOfStockItems->fetch_assoc()) {
      echo '<span class="product-name">' . $row['productname'] . '</span> - <span style="color: var(--accent-color)">OUT OF STOCK</span><br />';
    }
    echo '</p>';
    echo '</div>';
  }
  if ($items->num_rows > 0 && $show_stock_alert) { ?>
    <div class="alert">
    <div class="alert-icon"><i class="bx bx-error-circle" style="color:#ffffff"> </i></div>
    echo '<div class="alert-content">
    echo '<h4> Low Stock Alert!</h4>
    <span class="close" onclick="this.parentElement.parentElement.style.display = \'none\'">
    <i class="bx bx-x"></i>
    </span>

    </div>
    <p>
  <?php  
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
  }

  if (isset($_POST['close_stock_alert'])) {
    setcookie('show_stock_alert', false, time() + (86400 * 30), '/');
  }
?>


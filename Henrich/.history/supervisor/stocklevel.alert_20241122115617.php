<style>
  .alert, .out-of-stock-alert {
    margin-top: 5px;
    margin-left: 1rem;
    margin-right: 1rem;
    padding: 25px;
    border-radius: 10px;
    border: 1px solid;
    z-index: 9999;
  }

  .alert {
    background-color: var(--sidebar-color);
    border-color: var(--orange-color);
  }

  .out-of-stock-alert {
    background-color: #DD5746;
    border-color: var(--accent-color);
  }

  .alert h4, .out-of-stock-alert h4 {
    margin-top: 0;
    font-weight: bold;
    display: flex;
    align-items: center;
  }

  .alert h4 {
    color: #DD5746;
  }

  .out-of-stock-alert h4 {
    color: var(--accent-color);
  }

  .alert h4 i, .out-of-stock-alert h4 i {
    margin-right: 5px;
  }

  .alert ul, .out-of-stock-alert ul {
    list-style: none;
    padding: 0;
    margin: 0;
  }

  .alert li, .out-of-stock-alert li {
    padding: 5px 0;
    border-bottom: 1px solid rgba(0,0,0,0.1);
  }

  .alert li:last-child, .out-of-stock-alert li:last-child {
    border-bottom: none;
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

$show_stock_alert = isset($_COOKIE['show_stock_alert']) ? $_COOKIE['show_stock_alert'] : true;

function renderAlert($items, $alertClass, $iconColor, $headerText, $itemCallback) {
  if ($items->num_rows > 0 && $GLOBALS['show_stock_alert']) {
    echo "<div class=\"${alertClass}\">";
    echo '<i class="bx bx-error"></i><span class="close" onclick="this.parentElement.style.display = \'none\'"><i class="bx bx-x"></i></span>';
    echo "<h4><i class=\"bx bx-exclamation\" style=\"color: ${iconColor}\"></i> ${headerText}</h4>";
    echo '<ul>';
    while ($row = $items->fetch_assoc()) {
      echo $itemCallback($row);
    }
    echo '</ul>';
    echo '</div>';
  }
}

renderAlert($outOfStockItems, 'out-of-stock-alert', 'red', 'Out of Stock Alert!', function($row) {
  return '<li><span class="product-name">' . $row['productname'] . '</span> - <span style="color: var(--accent-color)">OUT OF STOCK</span></li>';
});

renderAlert($items, 'alert', 'orange', 'Low Stock Alert!', function($row) {
  $quantity = $row['onhand'];
  if ($quantity > 0) {
    $legend = ($quantity <= 5) ? "<span style='color: orange'>VERY LOW STOCK</span>" : "<span style='color: yellow'>LOW STOCK</span>";
    return '<li><span class="product-name">' . $row['productname'] . '</span> - <span class="quantity">' . $quantity . '</span> units left ' . $legend . '</li>';
  }
  return '';
});

if (isset($_POST['close_stock_alert'])) {
  setcookie('show_stock_alert', false, time() + (86400 * 30), '/');
}


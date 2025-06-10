<?php
require '../database/dbconnect.php';
$items = $conn->query("SELECT * FROM inventory WHERE onhand <= 10 ORDER BY onhand ASC");
$outOfStockItems = $conn->query("SELECT * FROM inventory WHERE onhand = 0 ORDER BY productname ASC");

if (isset($_COOKIE['show_stock_alert']) && $_COOKIE['show_stock_alert'] !== 'false') {
    $show_stock_alert = true;
} else {
    $show_stock_alert = false;
}

if ($outOfStockItems->num_rows > 0 && $show_stock_alert) { ?>
  <div class="out-of-stock-alert">
    <?php $i = 1; while ($row = $outOfStockItems->fetch_assoc()) { ?>
      <div class="alert-item out-of-stock-item" style="animation-delay: <?= $i * 0.25 ?>s">
        <i class="bx bx-error"></i>

        <div class="details">
          <span style="color: var(--sidebar-color)"><strong>OUT OF STOCK </strong> </span> - <span class="product-name"><?= $row['productname'] ?></span>
        </div>
        <span class="close" onclick="this.parentElement.style.display = 'none'"><i class="bx bx-x"></i></span>
      </div>
    <?php $i++; } ?>
  </div>
<?php }

if ($items->num_rows > 0 && $show_stock_alert) { ?>
  <div class="alert">
    <?php $i = 1; while ($row = $items->fetch_assoc()) {
      $quantity = $row['onhand'];
      $legend = '';
      if ($quantity > 0) {
        if ($quantity <= 5) {
          $legend = "<strong><span >VERY LOW STOCK  </span></strong>";
        } else {
          $legend = "<strong><span >LOW STOCK  </span></strong>";
        } ?>
        <div class="alert-item" style="animation-delay: <?= $i * 1 ?>s">
          <i class="bx bx-error-circle"></i>

          <div class="details"><?= $legend ?> -
            <span class="product-name"><?= $row['productname'] ?></span> - <span class="quantity"><?= $quantity ?></span> units left
          </div>
          <span class="close" onclick="this.parentElement.style.display = 'none'"><i class="bx bx-x"></i></span>
        </div>
    <?php $i++; }
    } ?>
  </div>
<?php }



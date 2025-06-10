<style>
  .alert,
  .out-of-stock-alert {
    /* margin-left: 10px; */
    background-color: var(--sidebar-color);
    width: 100%;
    border-bottom: 1px solid var(--orange-color);
    z-index: 9999;
    display: flex;
    flex-direction: column; /* Change to column for individual item display */
  }

  .out-of-stock-alert {
    border-bottom: 1px solid var(--accent-color);
  }

  .alert-item {
    display: flex;
    align-items: center;
    background-color: var(--orange-color);
    padding: 10px;
    gap: 30px;
    border-radius: 5px;
    /* justify-content: space-between; */
    margin-bottom: 5px; /* Add margin between items */
  }

  .out-of-stock-item {
    background-color: #DD5746;
  }

  .alert-item h4,
  .out-of-stock-item h4 {
    margin-top: 0;
    font-weight: bold;
    color: #DD5746;
  }

  .out-of-stock-item h4 {
    color: var(--accent-color);
  }

  .alert-item i,
  .out-of-stock-item i {
    margin-right: 5px;
    font-size: 2em;
  }

  .alert-item .product-name,
  .out-of-stock-item .product-name {
    font-weight: bold;
  }

  .alert-item .close,
  .out-of-stock-item .close {
    background-color: var(--sidebar-color);
    border: 1px solid #fff;
    cursor: pointer;
    border-radius: 2px;
    /* float: right; */
    font-size: 1.5em;
    display: inline-block;
    width: 30px;
    height: 30px;
    text-align: center;
    position: absolute;
    right: 10px;
    line-height: 30px;
  }

  .alert-item .close i,
  .out-of-stock-item .close i {
    font-size: 1em;
    margin: 0;
  }

  .alert-item .close:hover,
  .out-of-stock-item .close:hover {
    background-color: #fff;
    color: var(--accent-color);
  }

  .alert-item p,
  .out-of-stock-item p {
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

if ($outOfStockItems->num_rows > 0 && $show_stock_alert) { ?>
  <div class="out-of-stock-alert">
    <?php while ($row = $outOfStockItems->fetch_assoc()) { ?>
      <div class="alert-item out-of-stock-item">
        <i class="bx bx-error"></i>
        
        <span style="color: var(--accent-color)">OUT OF STOCK</span> - <span class="product-name"><?= $row['productname'] ?></span>
        <span class="close" onclick="this.parentElement.style.display = 'none'"><i class="bx bx-x"></i></span>
      </div>
    <?php } ?>
  </div>
<?php }

if ($items->num_rows > 0 && $show_stock_alert) { ?>
  <div class="alert">
    <?php while ($row = $items->fetch_assoc()) {
      $quantity = $row['onhand'];
      $legend = '';
      if ($quantity > 0) {
        if ($quantity <= 5) {
          $legend = "<span style='color: orange'>VERY LOW STOCK</span>";
        } else {
          $legend = "<span style='color: yellow'>LOW STOCK</span>";
        } ?>
        <div class="alert-item">
          <i class="bx bx-error-circle"></i><?= $legend ?>
          <span class="product-name"><?= $row['productname'] ?></span> - <span class="quantity"><?= $quantity ?></span> units left 
          <span class="close" onclick="this.parentElement.style.display = 'none'"><i class="bx bx-x"></i></span>
        </div>
      <?php }
    } ?>
  </div>
<?php }

if (isset($_POST['close_stock_alert'])) {
  setcookie('show_stock_alert', false, time() + (86400 * 30), '/');
}
?>


<?php
require '../database/dbconnect.php';

$items = $conn->query("SELECT * FROM inventory WHERE onhand <= 10 ORDER BY onhand ASC");
$outOfStockItems = $conn->query("SELECT * FROM inventory WHERE onhand = 0 ORDER BY productname ASC");

if (isset($_COOKIE['show_stock_alert']) && $_COOKIE['show_stock_alert'] !== 'false') {
    $show_stock_alert = true;
} else {
    $show_stock_alert = false;
}

?>
<script>
function closeAlert(alertId) {
    document.getElementById(alertId).style.display = 'none';
    document.cookie = 'show_stock_alert=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/';
    setTimeout(function() {
        document.cookie = 'show_stock_alert=true; path=/';
    }, 5000); // 5000 milliseconds = 5 seconds
}
</script>
<style>
  .alert,
  .out-of-stock-alert {
    
    width:  400
    padding-left: 20px;
    padding-right: 20px;
    background-color: var(--sidebar-color);
    width: 100%;
    z-index: 9999;
    display: flex;
    flex-direction: column;
    transition: all 0.5s ease-in-out;
  }

  .alert-item {;
    display: flex;
    align-items: center;
    background-color: var(--orange-color);
    border-radius: 5px;
    margin-bottom: 2px;
    transition: all 0.5s ease-in-out;
    animation: show-alert 0.25s ease-in-out;
    animation-delay: 1s;
    animation-fill-mode: both;
    will-change: transform, opacity;
    transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
  }

  .out-of-stock-item {
    background-color:var(--accent-color);
    will-change: transform, opacity;
    transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
  }

  .details {
    color : var(--sidebar-color);
    gap: 10px;
    padding: 10px;
    padding-left: 50px;
    display: flex;
    flex-direction: row;
    justify-content: space-between;
  }

  .alert-item h4,
  .out-of-stock-item h4 {
    margin-top: 0;
    font-weight: bold;
  }

  .out-of-stock-item h4 {
    color: var(--sidebar-color);
  }

  .alert-item i,
  .out-of-stock-item i {
    font-size: 2em;
  }

  .alert-item .bx-error-circle,
  .out-of-stock-item .bx-error {
    color: var(--sidebar-color);
    padding: 5px;
    cursor: pointer;
    border-radius: 5px;
    font-size: 2em;
    display: inline-block;
    width: auto;
    height: auto;
    text-align: center;
    position: absolute;
    left: 6px;
  }

  .alert-item .product-name,
  .out-of-stock-item .product-name {
    color: var(--sidebar-color);
  }

  .alert-item .close,
  .out-of-stock-item .close {
    cursor: pointer;
    border-radius: 2px;
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

  @keyframes show-alert {
    from {
      opacity: 0;
      transform: translateY(-50px);
    }
    to {
      opacity: 1;
      transform: translateY(0);
    }
  }
</style>

<?php
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




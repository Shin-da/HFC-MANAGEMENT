<?php
require '../includes/config.php';
$items = $conn->query("SELECT * FROM inventory WHERE onhandquantity <= 10 ORDER BY onhandquantity ASC");
$outOfStockItems = $conn->query("SELECT * FROM inventory WHERE onhandquantity = 0 ORDER BY productname ASC");

// Include the master inventory CSS file
echo '<link rel="stylesheet" href="../assets/css/inventory-master.css">';

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

<div class="inventory-theme">
    <?php if ($outOfStockItems->num_rows > 0 && $show_stock_alert) { ?>
      <div class="stock-alerts-container">
        <div class="alerts-header">
            <h3>Stock Alerts</h3>
        </div>
        <div class="alerts-list">
            <?php $i = 1; while ($row = $outOfStockItems->fetch_assoc()) { ?>
              <div class="alert-item out-of-stock" style="animation-delay: <?= $i * 0.25 ?>s">
                <i class="bx bx-error"></i>
                <div class="alert-content">
                    <div class="alert-header">
                        <h4><?= $row['productname'] ?></h4>
                    </div>
                    <div class="alert-details">
                        <span class="status">Out of Stock</span>
                    </div>
                </div>
                <button class="btn-action" onclick="this.parentElement.style.display = 'none'">
                    <i class="bx bx-x"></i>
                </button>
              </div>
            <?php $i++; } ?>
        </div>
      </div>
    <?php } ?>

    <?php if ($items->num_rows > 0 && $show_stock_alert) { ?>
      <div class="stock-alerts-container">
        <?php if ($outOfStockItems->num_rows == 0) { ?>
            <div class="alerts-header">
                <h3>Stock Alerts</h3>
            </div>
        <?php } ?>
        <div class="alerts-list">
            <?php $i = 1; while ($row = $items->fetch_assoc()) {
              $quantity = $row['onhandquantity'];
              $alertClass = $quantity <= 5 ? 'alert-item low-stock' : 'alert-item';
              $alertText = $quantity <= 5 ? 'Very Low Stock' : 'Low Stock';
              
              if ($quantity > 0) { ?>
                <div class="<?= $alertClass ?>" style="animation-delay: <?= $i * 0.25 ?>s">
                  <i class="bx bx-error-circle"></i>
                  <div class="alert-content">
                      <div class="alert-header">
                          <h4><?= $row['productname'] ?></h4>
                      </div>
                      <div class="alert-details">
                          <span class="quantity"><?= $quantity ?> units left</span>
                          <span class="status"><?= $alertText ?></span>
                      </div>
                  </div>
                  <button class="btn-action" onclick="this.parentElement.style.display = 'none'">
                      <i class="bx bx-x"></i>
                  </button>
                </div>
            <?php $i++; }
            } ?>
        </div>
      </div>
    <?php } ?>
    
    <?php if ($outOfStockItems->num_rows == 0 && $items->num_rows == 0 && $show_stock_alert) { ?>
        <div class="stock-alerts-container">
            <div class="alerts-header">
                <h3>Stock Alerts</h3>
            </div>
            <div class="no-alerts">
                <i class="bx bx-check-circle"></i>
                <p>No stock alerts at this time</p>
            </div>
        </div>
    <?php } ?>
</div>





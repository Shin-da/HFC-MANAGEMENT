<div class="sub-menu-item <?php echo isActivePage('inventory'); ?>">
    <a class="sub-menu-link" href="#" onclick="toggleSubMenu('inventorySubMenu')">
        <i class='bx bx-package'></i>
        <span>Inventory</span>
        <i class='bx bx-chevron-down arrow-down'></i>
    </a>
    <div class="sub-menu-container" id="inventorySubMenu">
        <a class="sub-item <?php echo isCurrentPage('stocklevel'); ?>" href="stocklevel.php">
            <i class='bx bx-layer'></i>
            <span>Stock Level</span>
        </a>
        <a class="sub-item <?php echo isCurrentPage('inventory-forecast'); ?>" href="inventory-forecast.php">
            <i class='bx bx-line-chart'></i>
            <span>Forecasting</span>
        </a>
        <a class="sub-item <?php echo isCurrentPage('stocklogs'); ?>" href="stocklogs.php">
            <i class='bx bx-history'></i>
            <span>Stock Logs</span>
        </a>
        <a class="sub-item <?php echo isCurrentPage('stockmovement'); ?>" href="stockmovement.php">
            <i class='bx bx-transfer-alt'></i>
            <span>Stock Movement</span>
        </a>
    </div>
</div> 
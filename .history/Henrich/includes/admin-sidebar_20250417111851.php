<?php
require_once dirname(__FILE__) . '/functions.php'; // Add this line

function isMenuActive($pages): string {
    $currentPage = Page::getCurrentPage();
    return in_array($currentPage, (array)$pages) ? 'active' : '';
}

function isSubmenuExpanded($pages): string {
    $currentPage = Page::getCurrentPage();
    return in_array($currentPage, (array)$pages) ? 'show' : '';
}
?>

<nav class="sidebar">
    <header>
        <div class="image-text">
            <span class="image">
                <img src="<?= BASE_URL ?>assets/images/hfclogo.png" alt="HFC Logo">
            </span>
            <div class="header-text">
                <span class="name">HFC Management</span>
                <span class="role"><?= htmlspecialchars($_SESSION['role']) ?></span>
            </div>
        </div>
        <i class='bx bx-chevron-right toggle'></i>
    </header>

    <div class="menu-bar">
        <div class="menu">
            <ul class="menu-links">
                <!-- Dashboard - Available to both admin and supervisor -->
                <li class="nav-link <?= isMenuActive('index') ?>">
                    <a href="<?= BASE_URL ?>admin/index.php">
                        <i class='bx bxs-dashboard icon'></i>
                        <span class="text">Dashboard</span>
                    </a>
                </li>

                <?php if (hasPermission('admin')): ?>
                <!-- Admin Only Section -->
                <li class="menu-title">
                    <span class="text">Administrative</span>
                </li>

                <li class="nav-link <?= isMenuActive(['manage-users', 'edit-user']) ?>">
                    <a href="<?= BASE_URL ?>admin/manage-users.php">
                        <i class='bx bxs-user-account icon'></i>
                        <span class="text">Manage Users</span>
                    </a>
                </li>   

                <li class="nav-link <?= isMenuActive('manage-account-requests') ?>">
                    <a href="<?= BASE_URL ?>admin/manage-account-requests.php">
                        <i class='bx bxs-user-plus icon'></i>
                        <span class="text">Account Requests</span>
                    </a>
                </li>
                <li><a href="<?= BASE_URL ?>admin/manage-requests.php">Manage Requests</a></li>
                <li><a href="<?= BASE_URL ?>admin/manage-new-accounts.php">Manage New Accounts</a></li>
                <li><a href="<?= BASE_URL ?>admin/manage-customers.php">Manage Customer Accounts</a></li>
                <?php endif; ?>

                <!-- Operations Section - Available to both -->
                <!-- <li class="menu-title">
                    <span class="text">Operations</span>
                </li> -->

                <!-- Sales Management - Available to both -->
                <!-- <li class="nav-link submenu <?= isMenuActive(['sales', 'customerorder', 'orderedproducts']) ?>">
                    <a href="#" class="submenu-title">
                        <i class='bx bxs-cart icon'></i>
                        <span class="text">Sales Management</span>
                        <i class='bx bx-chevron-down arrow'></i>
                    </a>
                    <ul class="submenu-items <?= isSubmenuExpanded(['sales', 'customerorder', 'orderedproducts']) ?>">
                        <li><a href="<?= BASE_URL ?>admin/sales.php">Sales Analytics</a></li>
                        <li><a href="<?= BASE_URL ?>admin/customerorder.php">Customer Orders</a></li>
                        <?php if (hasPermission('admin')): ?>
                        <li><a href="<?= BASE_URL ?>admin/orderedproducts.php">Order Logs</a></li>
                        <?php endif; ?>
                    </ul>
                </li> -->

                <!-- Inventory Management - Available to both -->
                <!-- <li class="nav-link submenu <?= isMenuActive(['stocklevel', 'stockactivitylog', 'stockmovement']) ?>"></li>
                    <a href="#" class="submenu-title">
                        <i class='bx bxs-package icon'></i>
                        <span class="text">Inventory</span>
                        <i class='bx bx-chevron-down arrow'></i>
                    </a>
                    <ul class="submenu-items <?= isSubmenuExpanded(['stocklevel', 'stockactivitylog', 'stockmovement']) ?>">
                        <li><a href="<?= BASE_URL ?>admin/stocklevel.php">Stock Level</a></li>
                        <li><a href="<?= BASE_URL ?>admin/stockactivitylog.php">Stock Logs</a></li>
                        <?php if (hasPermission('admin')): ?>
                        <li><a href="<?= BASE_URL ?>admin/stockmovement.php">Stock Movement</a></li>
                        <?php endif; ?>
                    </ul>
                </li> -->

                <?php if (hasPermission('admin')): ?>
                <!-- Settings Section - Admin Only -->
                <li class="menu-title">
                    <span class="text">Settings</span>
                </li>

                <li class="nav-link <?= isMenuActive('system-settings') ?>">
                    <a href="<?= BASE_URL ?>admin/system-settings.php"></a>
                        <i class='bx bxs-cog icon'></i>
                        <span class="text">System Settings</span>
                    </a>
                </li>
                <?php endif; ?>
            </ul>
        </div>

    </div>
</nav>
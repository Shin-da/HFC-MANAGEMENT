<nav class="sidebar close hidden" >
    <!-- Header -->
    <header>
        <div class="logo-details">
            <img src="../resources/images/henrichlogo.png" alt="henrich logo">
        </div>
        <div class="user-info">
            <i class="bx bx-user"></i>
            <span class="role"><?php echo $_SESSION['role']; ?></span>
        </div>
    </header>

    <!-- Navigation -->
    <div class="menu-wrapper">
        <ul class="nav-links">
            <!-- Main Navigation -->
            <li class="<?php echo ($current_page == 'index') ? 'active' : ''; ?>">
                <a href="index.php">
                    <i class="bx bx-home-alt"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <li class="<?php if ($current_page == 'messages') echo 'active'; ?>"><a href="messages.php"><i class="bx bx-message-square-detail icon"></i><span>Messages</span></a></li>
            
            <li class="break">
                <p class="disabled-link">Supplier</p>
            </li>
            <li class="<?php if ($current_page == 'mekeni') echo 'active'; ?>"><a href="mekeni.php"><i class="bx bx-store icon"></i><span>Mekeni</span></a></li>

            <li class="break">
                <p class="disabled-link">Henrich's Operations</p>
            </li>
            <!-- Sales Dropdown -->
            <li class="nav-item dropdown <?php if (in_array($current_page, ['sales', 'orderedproducts', 'customerorder', 'returns'])) echo 'active'; ?>">
                <a href="#" class="dropdown-toggle">
                    <i class="bx bx-line-chart icon"></i>
                    <span>Sales</span>
                    <i class="bx bx-chevron-down dropdown-arrow"></i>
                </a>
                <ul class="dropdown-menu">
                    <li><a href="sales.php" class="<?php if ($current_page == 'sales') echo 'active'; ?>">Sales Report</a></li>
                    <li><a href="customerorder.php" class="<?php if ($current_page == 'customerorder') echo 'active'; ?>">Customer Orders</a></li>
                    <?php if ($_SESSION['role'] == 'admin') : ?>
                    <li><a href="orderedproducts.php" class="<?php if ($current_page == 'orderedproducts') echo 'active'; ?>">Ordered Products</a></li>
                    <?php endif; ?>
                    <li><a href="returns.php" class="<?php if ($current_page == 'returns') echo 'active'; ?>">Returns</a></li>
                </ul>
            </li>

            <!-- Inventory Dropdown -->
            <li class="nav-item dropdown <?php if (in_array($current_page, ['stocklevel', 'stockmovement', 'stockactivitylog'])) echo 'active'; ?>">
                <a href="#" class="dropdown-toggle">
                    <i class="bx bx-archive icon"></i>
                    <span>Inventory</span>
                    <i class="bx bx-chevron-down dropdown-arrow"></i>
                </a>
                <ul class="dropdown-menu">
                    <li class="<?php if ($current_page == 'stocklevel') echo 'active'; ?>"><a href="stocklevel.php"> <i class="bx bxs-right-arrow sub-arrow <?php if ($current_page == 'stocklevel') echo 'active'; ?>"></i> Stock Level</a></li>
                    <li class="<?php if ($current_page == 'stockmovement') echo 'active'; ?>"><a href="stockmovement.php"> <i class="bx bxs-right-arrow sub-arrow <?php if ($current_page == 'stockmovement') echo 'active'; ?>"></i> Stock Movement</a></li>
                    <li class="<?php if ($current_page == 'stockactivitylog') echo 'active'; ?>"><a href="stockactivitylog.php"> <i class="bx bxs-right-arrow sub-arrow <?php if ($current_page == 'stockactivitylog') echo 'active'; ?>"></i> Stock Activity Log</a></li>
                </ul>
            </li>
            <li class="break">
                <p class="disabled-link"></p>
            </li>
            <li class="<?php if ($current_page == 'products') echo 'active'; ?>"><a href="products.php"><i class="bx bx-package icon"></i><span>Products</span></a></li>
            <li class="<?php if ($current_page == 'customer') echo 'active'; ?>"><a href="customer.php"><i class="bx bx-user icon"></i><span>Customer </span></a></li>

            <li class="break">
                <p class="disabled-link"></p>
            </li>

            <li class="break">
                <p class="disabled-link">Account Management</p>
            </li>
            <?php if ($_SESSION['role'] == 'admin') : ?>
            <li class="<?php if ($current_page == 'index') echo 'active'; ?>"><a href="index.php"><i class="bx bxs-user icon"></i> User Accounts</a></li>
            <?php endif; ?>
            <li class="<?php if ($current_page == 'customeraccount') echo 'active'; ?>"><a href="customeraccount.php"><i class="bx bx-user-circle"></i> Customer Accounts</a></li>
        </ul>
    </div>
</nav>
<div class="panel">
    <!-- Your page content goes here -->
    <?php /* ...existing content... */ ?>
</div>

<script src="../resources/js/sidebar.js"></script>
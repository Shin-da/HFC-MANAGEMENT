<nav class="sidebar">
    <!-- Header -->
    <header>
        <div class="logo-details">
            <img src="../resources/images/hfclogo.png" alt="henrich logo">
            <span class="logo-name">Henrich Management</span>
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
            <li class="sub-menu <?php if ($current_page == 'sales' || $current_page == 'orderedproducts' || $current_page == 'customerorder' || $current_page == 'returns') echo 'active'; ?>">
                <a href="javascript:void(0);"><i class="bx bx-line-chart icon"></i><span>Sales</span><i class="arrow bx bxs-down-arrow icon"></i></a>
                <ul>
                    <li class="<?php if ($current_page == 'sales') echo 'active'; ?>"><a href="sales.php"> <i class="bx bxs-right-arrow sub-arrow<?php if ($current_page == 'sales') echo 'active'; ?> "></i> Sales Report</a></li>
                    <li class="<?php if ($current_page == 'customerorder') echo 'active'; ?>"><a href="customerorder.php"> <i class="bx bxs-right-arrow sub-arrow<?php if ($current_page == 'customerorder') echo 'active'; ?> "></i> Customer Orders</a></li>
                    <?php if ($_SESSION['role'] == 'admin') { ?>
                    <li class="<?php if ($current_page == 'orderedproducts') echo 'active'; ?>"><a href="orderedproducts.php"> <i class="bx bxs-right-arrow sub-arrow<?php if ($current_page == 'orderedproducts') echo 'active'; ?>"></i> Ordered Products</a></li>
                    <?php } ?>
                    <li class="<?php if ($current_page == 'returns') echo 'active'; ?>"><a href="returns.php"> <i class="bx bxs-right-arrow sub-arrow<?php if ($current_page == 'returns') echo 'active'; ?> "></i> Returns</a></li>
                </ul>
            </li>

            <li class="sub-menu <?php if ($current_page == 'stocklevel' || $current_page == 'stockmovement' || $current_page == 'stockactivitylog' || $current_page == 'add.stockmovement') echo 'active'; ?>">
                <a href="javascript:void(0);"><i class="bx bx-archive icon"></i><span>Inventory</span><i class="arrow bx bxs-down-arrow pull-right"></i></a>
                <ul>
                    <li class="<?php if ($current_page == 'stocklevel') echo 'active'; ?>"><a href="stocklevel.php"> <i class="bx bxs-right-arrow sub-arrow <?php if ($current_page == 'stocklevel') echo 'active'; ?>"></i> Stock Level</a></li>
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

<script src="../resources/js/sidebar.js"></script>
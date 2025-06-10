<nav class="sidebar">
    <div class="sidebar-content">
        <header class="sidebar-header">
            <a href="javascript:void(0)" class="brand">
                <img src="../resources/images/hfclogo.png" alt="henrich logo">
                <span>Henrich Management</span>
            </a>
            <div class="user-session">
                <i class="bx bx-user icon"></i>
                <span><?php echo $_SESSION['role']; ?></span>
            </div>
        </header>

        <ul class="nav-links">
            <li class="<?php echo ($current_page == 'index') ? 'active' : ''; ?>">
                <a href="index.php">
                    <i class="bx bx-home-alt icon"></i>
                    <span>Dashboard</span>
                </a>
            </li>

            <!-- Navigation section: Messages -->
            <li class="<?php echo ($current_page == 'messages') ? 'active' : ''; ?>">
                <a href="messages.php">
                    <i class="bx bx-message-square-detail icon"></i>
                    <span>Messages</span>
                </a>
            </li>

            <!-- Section: Supplier -->
            <li class="nav-section">
                <span class="section-title">Supplier</span>
            </li>
            <li class="<?php echo ($current_page == 'mekeni') ? 'active' : ''; ?>">
                <a href="mekeni.php">
                    <i class="bx bx-store icon"></i>
                    <span>Mekeni</span>
                </a>
            </li>

            <!-- Operations section -->
            <li class="nav-section">
                <span class="section-title">Henrich's Operations</span>
            </li>
            <li class="sub-menu <?php if ($current_page == 'sales' || $current_page == 'orderedproducts' || $current_page == 'customerorder' || $current_page == 'returns') echo 'active'; ?>">
                <a href="javascript:void(0);">
                    <i class="bx bx-line-chart icon"></i>
                    <span>Sales</span>
                    <i class="arrow bx bxs-down-arrow icon"></i>
                </a>
                <ul>
                    <li class="<?php if ($current_page == 'sales') echo 'active'; ?>">
                        <a href="sales.php">
                            <i class="bx bxs-right-arrow sub-arrow<?php if ($current_page == 'sales') echo 'active'; ?>"></i>
                            Sales Report
                        </a>
                    </li>
                    <li class="<?php if ($current_page == 'customerorder') echo 'active'; ?>">
                        <a href="customerorder.php">
                            <i class="bx bxs-right-arrow sub-arrow<?php if ($current_page == 'customerorder') echo 'active'; ?>"></i>
                            Customer Orders
                        </a>
                    </li>
                    <?php if ($_SESSION['role'] == 'admin') { ?>
                        <li class="<?php if ($current_page == 'orderedproducts') echo 'active'; ?>">
                            <a href="orderedproducts.php">
                                <i class="bx bxs-right-arrow sub-arrow<?php if ($current_page == 'orderedproducts') echo 'active'; ?>"></i>
                                Ordered Products
                            </a>
                        </li>
                    <?php } ?>
                    <li class="<?php if ($current_page == 'returns') echo 'active'; ?>">
                        <a href="returns.php">
                            <i class="bx bxs-right-arrow sub-arrow<?php if ($current_page == 'returns') echo 'active'; ?>"></i>
                            Returns
                        </a>
                    </li>
                </ul>
            </li>

            <li class="sub-menu <?php if ($current_page == 'stocklevel' || $current_page == 'stockmovement' || $current_page == 'stockactivitylog' || $current_page == 'add.stockmovement') echo 'active'; ?>">
                <a href="javascript:void(0);">
                    <i class="bx bx-archive icon"></i>
                    <span>Inventory</span>
                    <i class="arrow bx bxs-down-arrow pull-right"></i>
                </a>
                <ul>
                    <li class="<?php if ($current_page == 'stocklevel') echo 'active'; ?>">
                        <a href="stocklevel.php">
                            <i class="bx bxs-right-arrow sub-arrow <?php if ($current_page == 'stocklevel') echo 'active'; ?>"></i>
                            Stock Level
                        </a>
                    </li>
                    <li class="<?php if ($current_page == 'stockmovement') echo 'active'; ?>">
                        <a href="stockmovement.php">
                            <i class="bx bxs-right-arrow sub-arrow <?php if ($current_page == 'stockmovement') echo 'active'; ?>"></i>
                            Stock Movement
                        </a>
                    </li>
                    <li class="<?php if ($current_page == 'stockactivitylog') echo 'active'; ?>">
                        <a href="stockactivitylog.php">
                            <i class="bx bxs-right-arrow sub-arrow <?php if ($current_page == 'stockactivitylog') echo 'active'; ?>"></i>
                            Stock Activity Log
                        </a>
                    </li>
                </ul>
            </li>

            <!-- Account Management section -->
            <li class="nav-section">
                <span class="section-title">Account Management</span>
            </li>
            <?php if ($_SESSION['role'] == 'admin') : ?>
                <li class="<?php echo ($current_page == 'useraccounts') ? 'active' : ''; ?>">
                    <a href="useraccounts.php">
                        <i class="bx bxs-user icon"></i>
                        <span>User Accounts</span>
                    </a>
                </li>
            <?php endif; ?>
            <li class="<?php echo ($current_page == 'customeraccount') ? 'active' : ''; ?>">
                <a href="customeraccount.php">
                    <i class="bx bx-user-circle"></i>
                    <span>Customer Accounts</span>
                </a>
            </li>
        </ul>
    </div>
</nav>

<style>
    /* ======= Sidebar ========  */

    /* ================================================ Sidebar settings ======== */
    .sidebar.close~.panel,
    .sidebar.close~.panel .top {
        left: 68px;
        width: calc(100% - 68px);
    }

    .sidebar.hidden~.panel,
    .sidebar.hidden~.panel .top {
        left: 0;
        width: 100%;
    }

    .sidebar {
        background: var(--accent-color);
        border-right: 1px solid var(--border-color);
        position: fixed;
        width: 220px;
        height: 100%;
        top: 0;
        left: 0;
        z-index: 100;
    }

    .sidebar.open {
        width: 220px;
    }

    .sidebar.close {
        width: 68px;
    }

    .sidebar.hidden {
        width: 0;
    }

    .sidebar .sidebar-content {
        display: flex;
        flex-direction: column;
        height: 100%;
    }

    .sidebar-header {
        background-color: var(--sidebar-color);
        padding: 15px;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .sidebar-header .brand {
        display: flex;
        align-items: center;
        color: var(--accent-color);
        text-decoration: none;
    }

    .sidebar-header .brand img {
        width: 30px;
        margin-right: 10px;
    }

    .user-session {
        background-color: var(--accent-color-dark);
        color: var(--text-color-white);
        font-size: 12px;
        padding: 10px;
        display: flex;
        align-items: center;
    }

    .user-session .icon {
        margin-right: 5px;
    }

    .nav-links {
        list-style: none;
        padding: 0;
        margin: 0;
        flex-grow: 1;
    }

    .nav-links li {
        border-bottom: 1px solid rgba(255, 255, 255, .05);
    }

    .nav-links a {
        display: flex;
        align-items: center;
        color: var(--text-color-white);
        text-decoration: none;
        padding: 18px 25px;
        font-size: 12px;
        transition: all 200ms ease-in;
    }

    .nav-links a:hover {
        color: var(--orange-color);
    }

    .nav-links a .icon {
        font-size: 16px;
        margin-right: 10px;
    }

    .nav-section {
        padding: 10px 25px;
        color: var(--orange-color);
        font-size: 10px;
    }

    .sub-menu ul {
        display: none;
        padding: 0;
        margin: 0;
    }

    .sub-menu ul li {
        background: var(--accent-color-dark);
    }

    .sub-menu ul li a {
        color: var(--text-color-white);
        font-size: 12px;
        padding: 13px 50px;
    }

    .sub-menu ul li a:hover {
        color: var(--orange-color);
    }

    .sub-menu ul li a .sub-arrow {
        display: none;
    }

    .nav-links li.active > a {
        color: var(--accent-color);
    }

    .nav-links li.active > a:hover {
        color: var(--orange-color);
    }

    .nav-links li.active ul {
        display: block;
    }

    .nav-links li.active ul li a:hover {
        color: var(--orange-color);
    }

    .sub-menu ul li.active a {
        background-color: var(--accent-color-dark);
        color: var(--orange-color);
        border: solid 1px var(--accent-color-dark);
    }
</style>

<script>
    // accordion menu script
    $("#leftside-navigation .sub-menu > a").click(function(e) {
        $("#leftside-navigation ul ul").slideUp(), $(this).next().is(":visible") || $(this).next().slideDown(),
            e.stopPropagation()
    })
</script>

<script>
    //active menu item
    // Get all menu items
    var menuItems = document.querySelectorAll('.sidebar li');

    console.log('menuItems:', menuItems);

    // Function to check if a menu item is active
    function isMenuItemActive(menuItem) {
        console.log('menuItem:', menuItem);
        var href = menuItem.querySelector('a').href;
        var pathname = window.location.pathname;
        console.log('href:', href);
        console.log('pathname:', pathname);
        if (href === pathname) {
            return true;
        }
        // Check if the current URL is a descendant of the menu item
        var submenuItems = menuItem.querySelectorAll('ul li a');
        for (var i = 0; i < submenuItems.length; i++) {
            if (submenuItems[i].href === pathname) {
                return true;
            }
        }
        // Check if the current URL is a descendant of a sub-menu item
        var subMenuItems = menuItem.querySelectorAll('ul li ul li a');
        for (var i = 0; i < subMenuItems.length; i++) {
            if (subMenuItems[i].href === pathname) {
                return true;
            }
        }
        return false;
    }

    // Add active class to menu items
    menuItems.forEach(function(menuItem) {
        console.log('menuItem:', menuItem);
        if (isMenuItemActive(menuItem)) {
            menuItem.classList.add('active');
            // Also add active class to parent menu item
            var parentMenuItem = menuItem.parentNode.parentNode;
            if (parentMenuItem.classList.contains('sub-menu')) {
                parentMenuItem.classList.add('active');
            }
        }
    });
</script>
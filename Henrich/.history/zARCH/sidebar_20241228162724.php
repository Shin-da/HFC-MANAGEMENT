<style>
    /* ====== active links ========= */
    .sidebar li a.active {
        background-color: var(--sidebar-color) !important;
    }

    .sidebar li a.active i,
    .sidebar li a.active .text {
        color: var(--accent-color) !important;
    }

    .sidebar .sub-nav-link a.active {
        background-color: var(--accent-color-dark) !important;
        color: var(--accent-color) !important;
    }

    .sidebar .sub-nav-link a.active i,
    .sidebar .sub-nav-link a.active .text {
        color: var(--text-color-white) !important;
    }

    /* ====== dropdown links (sub-menu  )========= */

    .sub-nav-link .sub-menu-links li {
        padding: 5px;
    }

    .sub-nav-link .sub-menu-links li a {
        color: var(--text-color);
    }

    .sub-nav-link:hover .sub-menu-links {
        display: block;
    }

    .sub-nav-link .sub-menu-links li:hover {
        background-color: var(--accent-color-dark);
    }

    .sub-nav-link .sub-menu-links.show {
        display: block;
    }

    .sub-menu-links {
        display: none;
    }

    .sub-menu-links.show {
        display: block;
    }
</style>

<nav class="sidebar open close hidden">

    <header>
        <div class="image-text">
            <span class="image">
                <img draggable="false" src="../resources/images/hfclogo.png" alt="logo">
            </span>

            <div class="text header-text">
                <span class="name">Henrich</span>
                <span class="profession"> Management</span>
            </div>
        </div>
    </header>

    <div class="menu-bar">
        <div class="session">
            <li class="nav-link">
                <i class="bx bx-user icon"></i>
                <div class="text header-text">
                    <?php echo strtoupper($_SESSION['role']); ?>
                </div>
            </li>

            <li class="profile">
                <span>
                    <i class="bx bx-mail icon"></i>
                    <div class="text header-text">
                        <?php echo ($_SESSION['email']); ?>
                    </div>
                </span>
            </li>
        </div>

        <div class="menu">
            <ul class="menu-links">
                <li class="nav-link" id="Dashboard" data-tooltip="Dashboard">
                    <a href="dashboard.php">
                        <i class="bx bx-home-alt icon"></i>
                        <span class="text nav-text">Dashboard</span>
                    </a>
                </li>
                <li class="nav-link" id="Products" data-tooltip="Products">
                    <a href="products.php">
                        <i class="bx bx-package icon"></i>
                        <span class="text nav-text">Products</span>
                    </a>
                </li>

                <!-- Link with sub-menus -->
                <li class="sub-menu" id="Inventory" data-tooltip="Inventory">
                    <a a href="javascript:void(0);" class="sub-nav-link">
                        <i class="bx bx-food-menu icon"></i>
                        <span class="text nav-text">Inventory</span>
                        <i class="bx bx-chevron-down arrow"></i>
                    </a>

                    <!-- Sub-menus -->
                    <ul class="sub-menu-links">
                        <a  href="stocklevel.php" <?php if ($current_page === 'stocklevel') echo 'class="active"'; ?>>
                            <i class="bx bx-chevron-right arrow"></i>
                            <span class="text nav-text">Stock Level</span>
                        </a>
                        <a href="stockmovement.php" <?php if ($current_page === 'stockmovement') echo 'class="active"'; ?>>
                            <i class="bx bx-chevron-right arrow"></i>
                            <span class="text nav-text">Stock Movement</span>
                        </a>
                    </ul>
                </li>

                <li class="nav-link" id="Sales" data-tooltip="Sales">
                    <a href="sales.php">
                        <i class="bx bx-dollar icon"></i>
                        <span class="text nav-text">Sales</span>
                    </a>

                    <!-- Link with sub-menus -->
                <li class="sub-menu">
                    <a id="Transactions" data-tooltip="Transactions">
                        <i class="bx bx-cart-alt icon"></i>
                        <span class="text nav-text">Transactions</span>
                        <i class="bx bx-chevron-down arrow"></i>
                    </a>
                    <!-- Sub-menus -->
                    <ul class="sub-menu-links">
                        <li class="sub-nav-link">
                            <a href="supplier.php" data-tooltip="Suppliers Orders">
                                <i class="bx bx-chevron-right arrow"></i>
                                <span class="text nav-text">Supplier Orders</span>
                            </a>
                        </li>
                        <li class="sub-nav-link">
                            <a href="customerorder.php" data-tooltip="Customer Orders">
                                <i class="bx bx-chevron-right arrow"></i>
                                <span class="text nav-text">Customer Orders</span>
                            </a>
                        </li>
                        <li class="sub-nav-link">
                            <a href="returns.php" data-tooltip="Returns">
                                <i class="bx bx-chevron-right arrow"></i>
                                <span class="text nav-text">Returns</span>
                            </a>
                        </li>
                        <li class="sub-nav-link">
                            <a href="orderhistory.php" data-tooltip="Order History">
                                <i class="bx bx-chevron-right arrow"></i>
                                <span class="text nav-text">Order History</span>
                            </a>
                        </li>
                    </ul>
                </li>
                <li class="nav-link" id="Customers" data-tooltip="Customers">
                    <a href="customer.php">
                        <i class="bx bx-user icon"></i>
                        <span class="text nav-text">Customers</span>
                    </a>
                </li>

            </ul>
        </div>


    </div>
    <div class="bottom-content">
        <li class>
            <a href="logout.php" data-tooltip="Logout">
                <i class="bx bx-log-out icon"></i>
                <span class="text nav-text">Logout</span>
            </a>
        </li>
        <li class="mode">
            <div class="moon-sun">
                <i class="bx bx-moon icon moon"></i>
                <i class="bx bx-sun icon sun "></i>
            </div>
            <span class="mode-text text">Dark mode</span>

            <div class="toggle-switch">
                <span class="switch"></span>
            </div>

        </li>
    </div>
</nav>

<script>
    // Add accordion function to sub-menus
    const subMenuLinks = document.querySelectorAll('.sub-nav-link');

    subMenuLinks.forEach((link) => {
        link.addEventListener('click', (e) => {
            e.preventDefault();
            const subMenu = link.nextElementSibling;
            subMenu.classList.toggle('show');
        });
    });
</script>

<script>
    // Add active class to links and sub-menus
    const links = document.querySelectorAll('.sidebar li a, .sidebar .sub-nav-link a');

    links.forEach((link) => {
        if (link.classList.contains('active')) {
            // Add active class to parent sub-menu
            const parentSubMenu = link.closest('.sub-menu');
            if (parentSubMenu) {
                parentSubMenu.classList.add('active');
                parentSubMenu.querySelector('.sub-menu-link').classList.add('active');
            }
        }
    });
</script>
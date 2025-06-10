<nav class="sidebar" id="sidebar">
    <header>
        <div class="logo-details">
            <img src="../resources/images/hfclogo.png" alt="HFC Logo">
            <span>Management</span>
        </div>
    </header>

    <div class="menu-wrapper">
        <ul class="nav-links">
            <li class="<?php echo ($current_page == 'dashboard') ? 'active' : ''; ?>">
                <a href="../supervisor/index.php">
                    <i class="bx bx-grid-alt"></i>
                    <span>Dashboard</span>
                </a>
            </li>

            <li class="break">
                <p class="disabled-link">Operations</p>
            </li>

            <li class="nav-item dropdown <?php echo (in_array($current_page, ['sales', 'orders'])) ? 'active' : ''; ?>">
                <a href="#" class="dropdown-toggle">
                    <i class="bx bx-store"></i>
                    <span>Sales</span>
                    <i class="bx bx-chevron-down dropdown-arrow"></i>
                </a>
                <ul class="dropdown-menu">
                    <li><a href="sales.php" class="<?php echo ($current_page == 'sales') ? 'active' : ''; ?>">Sales Report</a></li>
                    <li><a href="orders.php" class="<?php echo ($current_page == 'orders') ? 'active' : ''; ?>">Orders</a></li>
                </ul>
            </li>

            <li class="nav-item dropdown <?php echo (in_array($current_page, ['inventory', 'stock'])) ? 'active' : ''; ?>">
                <a href="#" class="dropdown-toggle">
                    <i class="bx bx-package"></i>
                    <span>Inventory</span>
                    <i class="bx bx-chevron-down dropdown-arrow"></i>
                </a>
                <ul class="dropdown-menu">
                    <li><a href="inventory.php" class="<?php echo ($current_page == 'inventory') ? 'active' : ''; ?>">Stock Level</a></li>
                    <li><a href="stock-movement.php" class="<?php echo ($current_page == 'stock') ? 'active' : ''; ?>">Stock Movement</a></li>
                </ul>
            </li>

            <li class="break">
                <p class="disabled-link">Management</p>
            </li>

            <li class="<?php echo ($current_page == 'employees') ? 'active' : ''; ?>">
                <a href="employees.php">
                    <i class="bx bx-user"></i>
                    <span>Employees</span>
                </a>
            </li>

            <li class="<?php echo ($current_page == 'reports') ? 'active' : ''; ?>">
                <a href="reports.php">
                    <i class="bx bx-file"></i>
                    <span>Reports</span>
                </a>
            </li>
        </ul>
    </div>

    <div class="user-info">
        <img src="../resources/images/default-avatar.png" alt="User Avatar">
        <div>
            <span class="user-name"><?php echo $_SESSION['username']; ?></span>
            <span class="user-role">Supervisor</span>
        </div>
        <div class="theme-toggle">
            <button id="themeToggle" class="theme-btn">
                <i class='bx bx-moon'></i>
            </button>
        </div>
        <a href="../login/logout.php" class="logout-btn">
            <i class="bx bx-log-out"></i>
        </a>
    </div>
</nav>

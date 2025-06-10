<nav class="sidebar close hidden">
    <div id="leftside-navigation" class="nano">
        <div class="nano-content">
            <ul class="nano-content">

                <li class="header ">
                    <a href="javascript:void(0)" class="disabled-link"><img src="../resources/images/hfclogoWhite.png" alt="henrich logo"></i><span>Henrich Management</span></a>
                </li>

                <li class="session ">
                    <a href="javascript:void(0);"><i class="bx bx-user icon"></i><span>
                            <?php echo $_SESSION['role']; ?>
                        </span>
                    </a>

                </li>

                <li class="<?php if ($current_page == 'dashboard') echo 'active'; ?>"><a href="dashboard.php"><i class="bx bx-home-alt icon"></i><span>Dashboard</span></a></li>
                <li class="break" style="margin-top: 40px; ;">
                    <p class="disabled-link">Supplier</p>
                </li>
                <li class="<?php if ($current_page == 'mekeni') echo 'active'; ?>"><a href="mekeni.php"><i class="bx bx-store icon"></i><span>Mekeni</span></a></li>

                <li class="break" style="margin-top: 40px; ;">
                    <p class="disabled-link">Henrich's Operations</p>
                    <!-- <hr style="height: 1px;  color: var(--border-color);" width="90%;  "> -->
                </li>
                <li class="sub-menu <?php if ($current_page == 'sales' || $current_page == 'transactions' || $current_page == 'orderhistory'    || $current_page == 'returns') echo 'active'; ?>">
                    <a href="javascript:void(0);"><i class="bx bx-line-chart icon"></i><span>Sales</span><i class="arrow bx bxs-down-arrow icon"></i></a>
                    <ul>
                        <li class="<?php if ($current_page == 'sales') echo 'active'; ?>"><a href="sales.php"> <i class="bx bxs-right-arrow sub-arrow<?php if ($current_page == 'sales') echo 'active'; ?> "></i> Sales Report</a></li>
                        <li class="<?php if ($current_page == 'transactions') echo 'active'; ?>"><a href="transactions.php"> <i class="bx bxs-right-arrow sub-arrow<?php if ($current_page == 'transactions') echo 'active'; ?>"></i> Transactions</a></li>
                        <li class="<?php if ($current_page == 'orderhistory') echo 'active'; ?>"><a href=""> <i class="bx bxs-right-arrow sub-arrow<?php if ($current_page == 'orderhistory') echo 'active'; ?> "></i> Order History</a></li>
                        <li class="<?php if ($current_page == 'returns') echo 'active'; ?>"><a href="returns.php"> <i class="bx bxs-right-arrow sub-arrow<?php if ($current_page == 'returns') echo 'active'; ?> "></i> Returns</a></li>

                    </ul>
                </li>

                <li class="sub-menu <?php if ($current_page == 'stocklevel' || $current_page == 'stockmovement' || $current_page == 'stockactivitylog' || $current_page == 'add.stockmovement') echo 'active'; ?>">
                    <a href="javascript:void(0);"><i class="bx bx-archive icon"></i><span>Inventory</span><i class="arrow bx bxs-down-arrow pull-right"></i></a>
                    <ul>
                        <li class="<?php if ($current_page == 'stocklevel') echo 'active'; ?>"><a href="stocklevel.php"> <i class="bx bx-right-arrow sub-arrow"></i> Stock Level</a></li>
                        <li class="<?php if ($current_page == 'stockmovement') echo 'active'; ?>"><a href="stockmovement.php"> <i class="bx bx-right-arrow sub-arrow"></i> Stock Movement</a></li>
                        <li class="<?php if ($current_page == 'stockactivitylog') echo 'active'; ?>"><a href="stockactivitylog.php"> <i class="bx bx-right-arrow sub-arrow"></i> Stock Activity Log</a></li>
                    </ul>
                </li>
                <li class="break" style="margin-top: 10px; ;">
                    <p class="disabled-link">


                    </p>
                    <!-- <hr style="height: 1px;  color: var(--border-color);" width="90%;  "> -->
                </li>
                <li class="<?php if ($current_page == 'products') echo 'active'; ?>"><a href="products.php"><i class="bx bx-package icon"></i><span>Products</span></a></li>
                <li class="<?php if ($current_page == 'customerorder') echo 'active'; ?>"><a href="customerorder.php"><i class="bx bx-user icon"></i><span>Customer </span></a></li>

                <li class="break" style="margin-top: 40px; ;">
                    <p class="disabled-link">
                        Account Management
                    </p>
                    <!-- <hr style="height: 1px;  color: var(--border-color);" width="90%;  "> -->
                </li>
                <li class="<?php if ($current_page == 'index') echo 'active'; ?>"><a href="index.php"><i class="bx bxs-user icon"></i> User Accounts</a></li>
                <li class="<?php if ($current_page == 'customeraccount') echo 'active'; ?>"><a href="customeraccount.php"><i class="bx bx-user-circle"></i> Customer Accounts</a></li>

            </ul>
        </div>
</nav>

<style>
    /* ======= Sidebar ========  */
    :root {
        --primary: #0f1214;
        --background: #121212;
        --text-primary: #ffffff;
        --action-hover: rgba(255, 255, 255, 0.08);
        --action-selected: rgba(255, 255, 255, 0.16);

    }

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
        /* 
        -webkit-transition: all .3s ease-in-out;
        -moz-transition: all .3s ease-in-out;
        -o-transition: all .3s ease-in-out;
        -ms-transition: all .3s ease-in-out;
        transition: all .3s ease-in-out; */
        z-index: 100;
    }

    .sidebar.open {
        width: 220px;
    }

    .sidebar.close {
        width: 68px;

        .sub-menu ul li a {
            opacity: 0;
        }

        .sub-menu a .arrow {
            display: none;
        }

        .text {
            display: none;
        }

        .sub-menu-links {
            display: none;
        }

        li a span {
            display: none;
        }

        .break {
            opacity: 0;
        }
    }


    .sidebar.hidden {
        width: 0;

        .image-text {
            opacity: 0;
        }

        .sub-menu-links {
            display: none;
        }

        .icon,
        i {
            opacity: 0;
        }

        .a {
            opacity: 0;
        }

    }


    /* ============================================= Sidebar settings ======== */

    /* ========================================== Sidebar initialization ======== */

    /*  -----------------------------Sidebar Header */
    .sidebar .header {
        background-color: var(--accent-color-dark);
        color: var(--text-color-white);
        position: relative;
        height: 50px;
        padding: 15px 0 0 0;
        /* height: 50px; */
        /* border-bottom: 1px solid var(--border-color); */
    }

    .sidebar .header img {
        width: 30px;
        /* display: flex;
        align-items: center;
        justify-content: center; */
    }

    .sidebar .header a {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0 16px;

        a:hover {
            color: var(--accent-color-dark);
        }
    }

    .session {
        background-color: var(--accent-color);
        color: var(--text-color-white);
        font-size: 12px;
        /* background-color: #aebf; */
        /* height: 50px; */
        margin-bottom: 40px;

        a {
            border-bottom: 1px solid #ff9e9e6e;

        }
    }

    /* --------------------------------------- Sidebar Header */


    /*  ---------------------------------------Unordered List */
    .nano-content ul {
        margin: -2px 0 0;
        padding: 0;
    }

    /*  ---------------------------------------List Items */
    .nano-content ul li {
        list-style-type: none;
        border-bottom: 1px solid rgba(255, 255, 255, .05);
    }

    /*  ---------------------------------------Links */
    .nano-content ul a {
        display: flex;
        justify-content: space-between;
        color: var(--text-color-white);
        text-decoration: none;
        display: block;
        padding: 12px 0 12px 35px;
        font-size: 12px;
        outline: 0;
        -webkit-transition: all 200ms ease-in;
        -moz-transition: all 200ms ease-in;
        -o-transition: all 200ms ease-in;
        -ms-transition: all 200ms ease-in;
        transition: all 200ms ease-in;

        /* --------------------------------------- Hover */
        &:hover {
            color: var(--orange-color);

        }

        /*  ---------------------------------------Span */
        span {
            display: inline-block;
        }

        /*  ---------------------------------------Icon */
        i {
            font-size: 16px;
            width: 30px;

            /* --------------------------------------- Icon pull-right */
            &.arrow {
                padding-top: 3px;
                margin-right: 25px;
                float: right;
                font-size: 12px;
                /* line-height: 18px; */
            }

            /*  ---------------------------------------sub-arrow */
            &.sub-arrow {
                display: none;

            }
        }

        i.sub-arrow {

            transition: all .3s ease-in-out;
            font-size: 8px;
            padding-top: 3px;

            margin-right: 25px;
            /* line-height: 18px; */
        }

    }

    /*  ---------------------------------------disabled link */
    .disabled-link {
        color: var(--orange-color);
        text-decoration: none;
        display: block;
        padding: 0px 0px 8px 25px;
        font-size: 10px;
    }

    /* ----------------------------------------------------------------------------Submenu */
    .sub-menu ul {
        display: none;
        margin: 0;
        padding: 0;

        li {
            background: var(--accent-color-dark);
            /* border-bottom: none; */

            /* Links */
            a {
                color: var(--text-color-white);
                font-size: 12px;
                /* padding-top: 13px; */
                padding-bottom: 13px;
                padding-left: 50px;
                /* color: #aeb2b7; */

                &:hover {
                    color: var(--orange-color);
                    transition: transform .3s ease-in-out;

                    .sub-arrow {
                        transition: var(--tran-03);
                        display: block;
                        float: left;
                    }
                }

                i {
                    padding-top: 3px;
                    padding-left: 20px;
                    /* margin-right: 20px; */
                    font-size: 9px;
                }
            }
        }
    }

    /* ----------------------------------------------------------------------------Submenu */

    /* ========================================= Sidebar initialization ======== */


    /* ----------------------------------------------------------------------------Active List Items */

    .nano-content ul li.active {
        &>a {
            /* background-color: var(--sidebar-color); */
            color: var(--accent-color);
        }

        &>a:hover {
            color: var(--orange-color);
        }

        ul {
            display: block;
        }

        ul li a:hover {
            color: var(--orange-color);
        }

        i .arrow {
            transform: rotate(90deg);
        }

        i.sub-arrow .active {
            display: block;
            margin-right: 25px;
        }
    }

    /* Submenu */
    .sub-menu ul li.active a {

        background-color: var(--accent-color-dark);
        color: var(--orange-color);
        border: solid 1px var(--accent-color-dark);
    }

    /* ---------------------------------------------------------------------------- Active List Items */
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
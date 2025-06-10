<?php


// Helper function to check if a menu item should be active
function isActive($pages)
{
    global $current_page;
    return in_array($current_page, (array)$pages) ? 'active' : '';
}

// Helper function to check if a submenu should be expanded
function isSubmenuExpanded($pages)
{
    global $current_page;
    return in_array($current_page, (array)$pages) ? 'show active' : '';
}
?>

<nav class="sidebar">
    <header>
        <div class="image-header">
            <span class="image">
                <img class="small-logo" src="../resources/images/Image.png" alt="Small Logo">
                <img class="long-logo" src="../resources/images/Imagee.png" alt="Full Logo">
            </span>
        </div>
    </header>

    <div class="menu-bar">
        <!-- Session Info -->
        <div class="session">
            <i class="bx bx-user icon"></i>
            <span class="text"><?php echo strtoupper($_SESSION['role']); ?></span>
        </div>

        <!-- Menu Links -->
        <ul class="menu-links">
            <!-- Dashboard -->
            <li class="nav-link <?php echo isActive('index'); ?>">
                <a href="index.php" data-label="Dashboard">
                    <i class="bx bx-grid-alt"></i>
                    <span class="text">Dashboard</span>
                </a>
            </li>

            <!-- <li class="break" style="margin-top: 20px;">
                <p>Mekeni</p>
            </li> -->

            <!-- Supplier -->
            <!-- <li class="nav-link <?php echo isActive('mekeni'); ?>">
                <a href="mekeni.php" data-label="Mekeni">
                    <i class="bx bx-store"></i>
                    <span class="text">Mekeni</span>
                </a>
            </li> -->


            <li class="break" style="margin-top: 20px;">
                <p>Operations</p>
            </li>
            <!-- Sales Menu -->
            <li class="nav-link sub-menu" data-menu-id="sales">
                <a href="#" class="menu-link" data-label="Sales">
                    <i class="bx bx-cart"></i>
                    <span class="text">Sales</span>
                    <i class="bx bx-chevron-down arrow"></i>
                </a>
                <ul class="sub-menu-links">
                    <li class="sub-nav-link <?php echo isActive('sales'); ?>">
                        <a href="sales.php">
                            <span class="text">Sales Analytics</span>
                        </a>
                    </li>
                    <li class="sub-nav-link <?php echo isActive('customerorder', 'add.customerorder'); ?>">
                        <a href="customerorder.php">
                            <span class="text">Customer Orders</span>
                        </a>
                    </li>
                    <li class="sub-nav-link <?php echo isActive('orderedproducts'); ?>">
                        <a href="orderedproducts.php">
                            <span class="text">Order Logs</span>
                        </a>
                    </li>
                </ul>
            </li>
            <!-- Inventory Menu -->
            <li class="nav-link sub-menu" data-menu-id="inventory">
                <a href="#" class="menu-link" data-label="Inventory">
                    <i class="bx bx-package"></i>
                    <span class="text">Inventory</span>
                    <i class="bx bx-chevron-down arrow"></i>
                </a>
                <ul class="sub-menu-links">
                    <li class="sub-nav-link <?php echo isActive('stocklevel'); ?>">
                        <a href="stocklevel.php">
                            <span class="text">Stock Level</span>
                        </a>
                    </li>
                    <li class="sub-nav-link <?php echo isActive('inventory-forecast'); ?>">
                        <a href="inventory-forecast.php">
                            <span class="text">Forecasting</span>
                        </a>
                    </li>
                    <li class="sub-nav-link <?php echo isActive('stockactivitylog'); ?>">
                        <a href="stockactivitylog.php">
                            <span class="text">Stock Logs</span>
                        </a>
                    </li>
                    <li class="sub-nav-link <?php echo isActive('stockmovement'); ?>">
                        <a href="stockmovement.php">
                            <span class="text">stockmovement</span>
                        </a>
                    </li>
                </ul>
            </li>


            <li class="break" style="margin-top: 20px;">
                <p>Manage</p>
            </li>

            <li class="nav-link <?php echo isActive('products'); ?>">
                <a href="products.php" data-label="Products">
                    <i class="bx bx-box"></i>
                    <span class="text">Products</span>
                </a>
            </li>
            <li class="nav-link <?php echo isActive('customer'); ?>">
                <a href="customer.php" data-label="Customers">
                    <i class="bx bx-group"></i>
                    <span class="text">Customers</span>
                </a>
            </li>
            <li class="nav-link <?php echo isActive('customeraccount'); ?>">
                <a href="customeraccount.php" data-label="Customer Accounts">
                    <i class="bx bx-user-circle"></i>
                    <span class="text">Customer Accounts</span>
                </a>
            </li>
            <!-- <li class="nav-link <?php echo isActive('chat'); ?>">
                <a href="<?php echo BASE_URL; ?>chat/" data-label="Messages">
                    <i class="bx bx-message-square-dots"></i>
                    <span class="text">Messages</span>
                </a>
            </li> -->

            <!-- Example menu item -->
            <!-- <li class="nav-link">
                <a href="example.php" data-label="Example Menu">
                    <i class="bx bx-example"></i>
                    <span class="text">Example Menu</span>
                </a>
            </li> -->
        </ul>
    </div>
</nav>

<script>
    console.log('Sidebar dropdown script loaded!'); // Add this line at the very top

document.addEventListener('DOMContentLoaded', function() {
    console.log('====== Sidebar Dropdown Initialization ======');
    
    // Test DOM elements
    const sidebar = document.querySelector('.sidebar');
    console.log('Sidebar element:', sidebar);
    
    const subMenus = document.querySelectorAll('.sub-menu');
    console.log('Found sub-menus:', subMenus.length);
    
    const menuLinks = document.querySelectorAll('.sub-menu > .menu-link');
    console.log('Found menu links:', menuLinks.length);
    
    if (menuLinks.length === 0) {
        console.warn('No menu links found! Check your HTML structure and selectors.');
        console.log('Expected structure:', `
            <li class="nav-link sub-menu">
                <a href="#" class="menu-link">
                    ...
                </a>
                <ul class="sub-menu-links">
                    ...
                </ul>
            </li>
        `);
    }

    console.log('DOM Content Loaded');
    
    // Get all menu links that have submenus
    console.log('Found menu links:', menuLinks.length);

    menuLinks.forEach((link, index) => {
        console.log(`Setting up listener for menu link ${index}`);
        
        link.addEventListener('click', function(e) {
            console.log('Menu link clicked');
            e.preventDefault();
            e.stopPropagation();

            const subMenu = this.closest('.sub-menu');
            console.log('SubMenu found:', subMenu);
            
            const arrow = this.querySelector('.arrow');
            console.log('Arrow found:', arrow);

            // Log current states
            console.log('Current submenu active state:', subMenu.classList.contains('active'));

            // Close all other open menus
            document.querySelectorAll('.sub-menu.active').forEach(menu => {
                if (menu !== subMenu) {
                    console.log('Closing other menu:', menu);
                    menu.classList.remove('active');
                    menu.querySelector('.arrow').style.transform = 'rotate(0deg)';
                }
            });

            // Toggle current menu
            subMenu.classList.toggle('active');
            console.log('New active state:', subMenu.classList.contains('active'));
            
            arrow.style.transform = subMenu.classList.contains('active') ? 'rotate(180deg)' : 'rotate(0deg)';
            console.log('Arrow rotation:', arrow.style.transform);
        });
    });

    // Initialize active state based on current page
    const currentPath = window.location.pathname;
    console.log('Current path:', currentPath);
    
    document.querySelectorAll('.sub-menu-links a').forEach(link => {
        console.log('Checking link:', link.getAttribute('href'));
        if (currentPath.includes(link.getAttribute('href'))) {
            console.log('Found matching link:', link.getAttribute('href'));
            const subMenu = link.closest('.sub-menu');
            if (subMenu) {
                console.log('Setting initial active state');
                subMenu.classList.add('active');
                const arrow = subMenu.querySelector('.arrow');
                if (arrow) {
                    arrow.style.transform = 'rotate(180deg)';
                }
            }
        }
    });
});

</script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Set initial sidebar state
        const sidebar = document.querySelector('.sidebar');
        if (window.innerWidth > 991) {
            sidebar.classList.add('open');
            sidebar.classList.remove('close', 'hidden');
        } else if (window.innerWidth < 991 && window.innerWidth > 600) {
            sidebar.classList.add('close');
            sidebar.classList.remove('open', 'hidden');
        } else {
            sidebar.classList.add('hidden');
            sidebar.classList.remove('open', 'close');
        }

        // Initialize submenu states
        const currentPath = window.location.pathname;
        document.querySelectorAll('.sub-menu').forEach(submenu => {
            const links = submenu.querySelectorAll('.sub-nav-link a');
            links.forEach(link => {
                if (currentPath.includes(link.getAttribute('href'))) {
                    submenu.classList.add('active');
                    link.classList.add('active');
                }
            });
        });
    });

    document.addEventListener('DOMContentLoaded', function() {
        const sidebar = document.querySelector('.sidebar');
        const sidebarToggle = document.getElementById('sidebar-toggle');
        const content = document.querySelector('.content-wrapper');
        const BREAKPOINTS = {
            MOBILE: 768,
            TABLET: 992,
            DESKTOP: 1200
        };

        // Get saved state from localStorage
        const savedState = localStorage.getItem('sidebarState') || 'open';

        // Initialize sidebar state
        function initSidebar() {
            if (window.innerWidth < 768) {
                sidebar.classList.add('hidden');
                content.style.marginLeft = '0';
            } else {
                sidebar.classList.remove('hidden');
                if (savedState === 'closed') {
                    sidebar.classList.add('close');
                    content.style.marginLeft = '64px';
                } else {
                    content.style.marginLeft = '250px';
                }
            }
        }

        // Toggle sidebar function
        function toggleSidebar() {
            const isMobile = window.innerWidth < 768;

            if (isMobile) {
                sidebar.classList.toggle('hidden');
                const isHidden = sidebar.classList.contains('hidden');
                content.style.marginLeft = isHidden ? '0' : '250px';
                localStorage.setItem('sidebarState', isHidden ? 'hidden' : 'open');
            } else {
                sidebar.classList.toggle('close');
                const isClosed = sidebar.classList.contains('close');
                content.style.marginLeft = isClosed ? '64px' : '250px';
                localStorage.setItem('sidebarState', isClosed ? 'closed' : 'open');
            }

            // Update toggle button icon
            const toggleIcon = sidebarToggle.querySelector('i');
            if (sidebar.classList.contains('hidden') || sidebar.classList.contains('close')) {
                toggleIcon.classList.replace('bx-menu', 'bx-menu-alt-right');
            } else {
                toggleIcon.classList.replace('bx-menu-alt-right', 'bx-menu');
            }
        }

        // Event listeners
        if (sidebarToggle) {
            sidebarToggle.addEventListener('click', toggleSidebar);
        }

        // Handle window resize
        let resizeTimer;
        window.addEventListener('resize', () => {
            clearTimeout(resizeTimer);
            resizeTimer = setTimeout(() => {
                initSidebar();
            }, 250);
        });

        // Handle clicks outside sidebar on mobile
        document.addEventListener('click', (e) => {
            if (window.innerWidth < 768 &&
                !sidebar.contains(e.target) &&
                !sidebarToggle.contains(e.target) &&
                !sidebar.classList.contains('hidden')) {
                toggleSidebar();
            }
        });

        // Initial setup
        initSidebar();
    });
</script>
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

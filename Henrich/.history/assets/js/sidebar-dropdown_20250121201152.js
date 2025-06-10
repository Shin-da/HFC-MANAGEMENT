document.addEventListener('DOMContentLoaded', function() {
    // Handle dropdown toggles
    const dropdownToggles = document.querySelectorAll('.dropdown-toggle');
    
    dropdownToggles.forEach(toggle => {
        toggle.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            const dropdownItem = this.closest('.nav-item.dropdown');
            const dropdownMenu = dropdownItem.querySelector('.dropdown-menu');
            const arrow = dropdownItem.querySelector('.dropdown-arrow');
            
            // Close other dropdowns
            document.querySelectorAll('.nav-item.dropdown.active').forEach(item => {
                if (item !== dropdownItem) {
                    item.classList.remove('active');
                    const otherArrow = item.querySelector('.dropdown-arrow');
                    if (otherArrow) otherArrow.style.transform = 'rotate(0deg)';
                }
            });
            
            // Toggle current dropdown
            const isActive = dropdownItem.classList.toggle('active');
            
            // Rotate arrow and set max-height for animation
            if (isActive) {
                arrow.style.transform = 'rotate(180deg)';
                dropdownMenu.style.maxHeight = dropdownMenu.scrollHeight + "px";
            } else {
                arrow.style.transform = 'rotate(0deg)';
                dropdownMenu.style.maxHeight = "0px";
            }
        });
    });
    
    // Close dropdowns when clicking outside
    document.addEventListener('click', (e) => {
        if (!e.target.closest('.nav-item.dropdown')) {
            document.querySelectorAll('.nav-item.dropdown.active').forEach(item => {
                item.classList.remove('active');
                const arrow = item.querySelector('.dropdown-arrow');
                const menu = item.querySelector('.dropdown-menu');
                if (arrow) arrow.style.transform = 'rotate(0deg)';
                if (menu) menu.style.maxHeight = "0px";
            });
    });
    
    // Prevent dropdown close when clicking inside
    document.querySelectorAll('.dropdown-menu').forEach(menu => {
        menu.addEventListener('click', e => e.stopPropagation());
    });
});

document.addEventListener('DOMContentLoaded', function() {
    // Handle dropdown toggles
    const dropdownToggles = document.querySelectorAll('.dropdown-toggle');
    
    dropdownToggles.forEach(toggle => {
        toggle.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            // Get the parent dropdown item
            const dropdownItem = this.closest('.nav-item.dropdown');
            const dropdownMenu = dropdownItem.querySelector('.dropdown-menu');
            const dropdownArrow = dropdownItem.querySelector('.dropdown-arrow');
            
            // Close other dropdowns
            document.querySelectorAll('.nav-item.dropdown.active').forEach(item => {
                if (item !== dropdownItem) {
                    item.classList.remove('active');
                    const menu = item.querySelector('.dropdown-menu');
                    menu.style.maxHeight = '0px';
                }
            });
            
            // Toggle current dropdown
            dropdownItem.classList.toggle('active');
            
            // Animate dropdown menu height
            if (dropdownItem.classList.contains('active')) {
                dropdownMenu.style.maxHeight = dropdownMenu.scrollHeight + 'px';
                dropdownArrow.style.transform = 'rotate(180deg)';
            } else {
                dropdownMenu.style.maxHeight = '0px';
                dropdownArrow.style.transform = 'rotate(0)';
            }
        });
    });
    
    // Close dropdowns when clicking outside
    document.addEventListener('click', () => {
        document.querySelectorAll('.nav-item.dropdown.active').forEach(item => {
            item.classList.remove('active');
            const menu = item.querySelector('.dropdown-menu');
            const arrow = item.querySelector('.dropdown-arrow');
            menu.style.maxHeight = '0px';
            arrow.style.transform = 'rotate(0)';
        });
    });
    
    // Prevent dropdown close when clicking inside
    document.querySelectorAll('.dropdown-menu').forEach(menu => {
        menu.addEventListener('click', e => e.stopPropagation());
    });
});

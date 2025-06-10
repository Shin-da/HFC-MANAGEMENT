document.addEventListener('DOMContentLoaded', function() {
    const notificationBtn = document.querySelector('.notification-btn');
    const notificationDropdown = document.querySelector('.notification-dropdown');

    if (notificationBtn && notificationDropdown) {
        // Clear any existing listeners
        const newBtn = notificationBtn.cloneNode(true);
        notificationBtn.parentNode.replaceChild(newBtn, notificationBtn);

        // Add new click handler
        newBtn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            console.log('Notification button clicked');
            notificationDropdown.classList.toggle('active');
        });

        // Close when clicking outside
        document.addEventListener('click', function(e) {
            if (!notificationDropdown.contains(e.target)) {
                notificationDropdown.classList.remove('active');
            }
        });

        // Prevent closing when clicking inside dropdown
        const dropdownContent = notificationDropdown.querySelector('.notification-dropdown-content');
        if (dropdownContent) {
            dropdownContent.addEventListener('click', function(e) {
                e.stopPropagation();
            });
        }
    }
});

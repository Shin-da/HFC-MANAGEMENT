document.addEventListener('DOMContentLoaded', function() {
    const navbarToggle = document.getElementById('navbar-toggle');
    const navbar = document.querySelector('.navbar');

    if (navbarToggle) {
        navbarToggle.addEventListener('click', function() {
            navbar.classList.toggle('expanded');
            document.querySelector('.content-wrapper').classList.toggle('nav-expanded');
        });
    }

    // Close expanded navbar when clicking outside
    document.addEventListener('click', function(e) {
        if (!navbar.contains(e.target) && navbar.classList.contains('expanded')) {
            navbar.classList.remove('expanded');
            document.querySelector('.content-wrapper').classList.remove('nav-expanded');
        }
    });
});

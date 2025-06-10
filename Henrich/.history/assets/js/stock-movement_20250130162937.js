document.addEventListener('DOMContentLoaded', function() {
    initializeStockMovement();
    setupThemeHandling();
});

function initializeStockMovement() {
    initializeSelect2();
    setupFormValidation();
    setupEventListeners();
}

function setupThemeHandling() {
    // Update UI colors based on theme
    const currentTheme = document.documentElement.getAttribute('data-theme');
    updateUITheme(currentTheme);
    
    // Listen for theme changes
    document.addEventListener('themeChanged', (e) => {
        updateUITheme(e.detail.theme);
    });
}

function updateUITheme(theme) {
    const isDark = theme === 'dark';
    
    // Update Select2 theme
    $('.select2-container').addClass(isDark ? 'select2-dark' : 'select2-light');
    
    // Update form controls
    document.querySelectorAll('.form-control').forEach(control => {
        control.style.backgroundColor = isDark ? 'var(--dark-input-bg)' : 'var(--input-bg)';
        control.style.color = isDark ? 'var(--dark-text)' : 'var(--text-color)';
    });
}

function setupFormValidation() {
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', (e) => {
            if (!form.checkValidity()) {
                e.preventDefault();
                e.stopPropagation();
            }
            form.classList.add('was-validated');
        });
    });
}

function setupEventListeners() {
    // Search functionality
    const searchInput = document.getElementById('general-search');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const searchText = this.value.toLowerCase();
            const tbody = document.querySelector('#table-body');
            const rows = tbody.getElementsByTagName('tr');

            Array.from(rows).forEach(row => {
                let text = row.textContent.toLowerCase();
                row.style.display = text.includes(searchText) ? '' : 'none';
            });
        });
    }

    // Reset button functionality
    const resetButton = document.querySelector('form button[type="reset"]');
    if (resetButton) {
        resetButton.addEventListener('click', () => {
            const tbody = document.querySelector('#table-body');
            const rows = tbody.getElementsByTagName('tr');
            Array.from(rows).forEach(row => row.style.display = '');
        });
    }
}

function filterTable(tbody, value, columnIndex) {
    const rows = tbody.getElementsByTagName('tr');
    const searchText = value.toLowerCase();

    Array.from(rows).forEach(row => {
        const cell = row.cells[columnIndex];
        const text = cell.textContent.toLowerCase();
        row.style.display = text.includes(searchText) ? '' : 'none';
    });
}

// ... existing JavaScript functions ...

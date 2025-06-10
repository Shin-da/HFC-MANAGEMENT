// Admin Core JavaScript

// Toast notifications
function showToast(message, type = 'success') {
    Swal.fire({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
        icon: type,
        title: message
    });
}

// Confirmation dialog
function confirmAction(title, text, icon = 'warning') {
    return Swal.fire({
        title: title,
        text: text,
        icon: icon,
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, proceed!',
        cancelButtonText: 'Cancel'
    });
}

// Handle AJAX errors
function handleAjaxError(error) {
    console.error('AJAX Error:', error);
    showToast(error.responseText || 'An error occurred. Please try again.', 'error');
}

// Table search functionality
function initTableSearch(inputId, tableId) {
    const searchInput = document.getElementById(inputId);
    const table = document.getElementById(tableId);
    
    if (!searchInput || !table) return;
    
    searchInput.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        const rows = table.getElementsByTagName('tr');
        
        Array.from(rows).forEach(row => {
            if (row.classList.contains('header')) return;
            
            const text = row.textContent.toLowerCase();
            row.style.display = text.includes(searchTerm) ? '' : 'none';
        });
    });
}

// Status filter functionality
function initStatusFilter(selectId, tableId, columnIndex) {
    const filterSelect = document.getElementById(selectId);
    const table = document.getElementById(tableId);
    
    if (!filterSelect || !table) return;
    
    filterSelect.addEventListener('change', function() {
        const filterValue = this.value.toLowerCase();
        const rows = table.getElementsByTagName('tr');
        
        Array.from(rows).forEach(row => {
            if (row.classList.contains('header')) return;
            
            const statusCell = row.cells[columnIndex];
            if (!statusCell) return;
            
            const status = statusCell.textContent.toLowerCase();
            row.style.display = !filterValue || status.includes(filterValue) ? '' : 'none';
        });
    });
}

// File upload preview
function initFilePreview(inputId, previewId) {
    const input = document.getElementById(inputId);
    const preview = document.getElementById(previewId);
    
    if (!input || !preview) return;
    
    input.addEventListener('change', function() {
        if (this.files && this.files[0]) {
            const reader = new FileReader();
            
            reader.onload = function(e) {
                preview.src = e.target.result;
            };
            
            reader.readAsDataURL(this.files[0]);
        }
    });
}

// Form validation
function validateForm(formId, rules = {}) {
    const form = document.getElementById(formId);
    if (!form) return true;
    
    let isValid = true;
    
    // Clear previous errors
    form.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
    form.querySelectorAll('.invalid-feedback').forEach(el => el.remove());
    
    // Check each rule
    Object.entries(rules).forEach(([fieldId, rule]) => {
        const field = document.getElementById(fieldId);
        if (!field) return;
        
        const value = field.value.trim();
        
        if (rule.required && !value) {
            showFieldError(field, 'This field is required');
            isValid = false;
        } else if (rule.email && !isValidEmail(value)) {
            showFieldError(field, 'Please enter a valid email address');
            isValid = false;
        } else if (rule.minLength && value.length < rule.minLength) {
            showFieldError(field, `Must be at least ${rule.minLength} characters`);
            isValid = false;
        }
    });
    
    return isValid;
}

function showFieldError(field, message) {
    field.classList.add('is-invalid');
    const feedback = document.createElement('div');
    feedback.className = 'invalid-feedback';
    feedback.textContent = message;
    field.parentNode.appendChild(feedback);
}

function isValidEmail(email) {
    return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
}

// Export functions
window.AdminCore = {
    showToast,
    confirmAction,
    handleAjaxError,
    initTableSearch,
    initStatusFilter,
    initFilePreview,
    validateForm
};

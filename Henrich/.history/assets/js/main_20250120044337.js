// Main JavaScript file
'use strict';

// Utility functions
const HFC = {
    // DOM ready handler
    ready: function(callback) {
        if (document.readyState !== 'loading') {
            callback();
        } else {
            document.addEventListener('DOMContentLoaded', callback);
        }
    },

    // AJAX helper
    ajax: function(url, options = {}) {
        return fetch(url, options)
            .then(response => response.json())
            .catch(error => console.error('Error:', error));
    },

    // Form validation helper
    validateForm: function(formElement) {
        let isValid = true;
        const requiredFields = formElement.querySelectorAll('[required]');
        
        requiredFields.forEach(field => {
            if (!field.value.trim()) {
                isValid = false;
                field.classList.add('error');
            } else {
                field.classList.remove('error');
            }
        });

        return isValid;
    }
};

// Initialize application
HFC.ready(() => {
    console.log('Application initialized');
    // Add your initialization code here
});

// Core Alert Configuration
const AlertConfig = {
    baseConfig: {
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33'
    },
    toastConfig: {
        toast: true,
        position: 'top-end',
        timer: 3000,
        timerProgressBar: true,
        showConfirmButton: false
    }
};

// Main Alert System
const Alerts = {
    // Basic Toast Notification
    toast(message, type = 'success') {
        const Toast = Swal.mixin(AlertConfig.toastConfig);
        Toast.fire({
            icon: type,
            title: message
        });
    },

    // Standard Alert
    show(title, message, type = 'info') {
        return Swal.fire({
            ...AlertConfig.baseConfig,
            title: title,
            text: message,
            icon: type
        });
    },

    // Confirmation Dialog
    confirm(title, message, callback) {
        return Swal.fire({
            ...AlertConfig.baseConfig,
            title: title,
            text: message,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes',
            cancelButtonText: 'No'
        }).then((result) => {
            if (result.isConfirmed && callback) {
                callback();
            }
        });
    },

    // Inventory Specific Alerts
    inventory: {
        lowStock(product, quantity, threshold) {
            return Alerts.show(
                'Low Stock Alert',
                `${product} is running low (${quantity}/${threshold} units remaining)`,
                'warning'
            );
        },

        updateSuccess(type = 'updated') {
            Alerts.toast(`Stock successfully ${type}`);
        },

        deleteConfirm(productName, callback) {
            return Alerts.confirm(
                'Delete Product',
                `Are you sure you want to delete ${productName}?`,
                callback
            );
        },

        outOfStock(product) {
            return Alerts.show(
                'Out of Stock',
                `${product} is out of stock!`,
                'error'
            );
        }
    },

    // Form Validation Alerts
    form: {
        invalidInput(message) {
            Alerts.show('Invalid Input', message, 'error');
        },
        
        success(message = 'Form submitted successfully') {
            Alerts.toast(message);
        },

        confirm(callback) {
            return Alerts.confirm(
                'Submit Form',
                'Are you sure you want to submit this form?',
                callback
            );
        }
    }
};

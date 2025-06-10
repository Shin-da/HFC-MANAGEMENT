const AdminCore = {
    // ...existing code...

    initLoadingStates() {
        document.querySelectorAll('form').forEach(form => {
            form.addEventListener('submit', () => {
                form.classList.add('loading');
            });
        });

        document.querySelectorAll('button[data-loading]').forEach(button => {
            button.addEventListener('click', () => {
                button.classList.add('loading');
                button.disabled = true;
            });
        });
    },

    showToast(message, type = 'success') {
        const toast = document.createElement('div');
        toast.className = `toast toast-${type} fade-in`;
        toast.innerHTML = `
            <div class="toast-content">
                <i class="fas ${this.getToastIcon(type)}"></i>
                <span>${message}</span>
            </div>
        `;
        document.body.appendChild(toast);
        
        setTimeout(() => {
            toast.remove();
        }, 3000);
    },

    getToastIcon(type) {
        return {
            success: 'fa-check-circle',
            error: 'fa-exclamation-circle',
            warning: 'fa-exclamation-triangle',
            info: 'fa-info-circle'
        }[type] || 'fa-info-circle';
    },

    initializeTheme() {
        const theme = localStorage.getItem('admin-theme') || 'light';
        document.body.classList.toggle('dark-theme', theme === 'dark');
    },

    setupAccessibility() {
        // Add ARIA labels
        document.querySelectorAll('button:not([aria-label])').forEach(button => {
            if (button.textContent) {
                button.setAttribute('aria-label', button.textContent.trim());
            }
        });

        // Add keyboard navigation
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                document.querySelectorAll('.modal.active').forEach(modal => {
                    modal.classList.remove('active');
                });
            }
        });
    },

    async handleUserAction(action, data) {
        try {
            const formData = new FormData();
            formData.append('action', action);
            
            Object.entries(data).forEach(([key, value]) => {
                formData.append(key, value);
            });
            
            const response = await fetch('/admin/api/admin-actions.php', {
                method: 'POST',
                body: formData
            });
            
            const result = await response.json();
            
            if (!result.success) {
                throw new Error(result.message);
            }
            
            this.showNotification('Action completed successfully', 'success');
            return result;
            
        } catch (error) {
            this.showNotification(error.message, 'error');
            throw error;
        }
    },

    async refreshDashboardData() {
        try {
            const response = await fetch('/admin/api/get-dashboard-data.php');
            const data = await response.json();
            
            if (!data.success) {
                throw new Error(data.message);
            }
            
            this.updateDashboardStats(data.stats);
            this.updateCharts(data);
            
        } catch (error) {
            this.showNotification('Error refreshing dashboard data', 'error');
        }
    },

    updateDashboardStats(stats) {
        Object.entries(stats).forEach(([key, value]) => {
            const element = document.getElementById(`${key}Count`);
            if (element) {
                this.animateNumber(element, parseInt(element.textContent), value);
            }
        });
    },

    animateNumber(element, start, end) {
        const duration = 1000;
        const steps = 60;
        const step = (end - start) / steps;
        let current = start;
        
        const timer = setInterval(() => {
            current += step;
            if ((step > 0 && current >= end) || (step < 0 && current <= end)) {
                element.textContent = end;
                clearInterval(timer);
            } else {
                element.textContent = Math.round(current);
            }
        }, duration / steps);
    },

    initializeDataTables() {
        const tables = document.querySelectorAll('.datatable');
        tables.forEach(table => {
            $(table).DataTable({
                responsive: true,
                pageLength: 10,
                order: [[0, 'desc']],
                language: {
                    search: "_INPUT_",
                    searchPlaceholder: "Search..."
                }
            });
        });
    }
};

// Initialize enhanced features
document.addEventListener('DOMContentLoaded', () => {
    AdminCore.init();
    AdminCore.initLoadingStates();
    AdminCore.initializeTheme();
    AdminCore.setupAccessibility();
    AdminCore.initializeDataTables();
});

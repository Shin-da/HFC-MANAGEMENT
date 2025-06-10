document.addEventListener('DOMContentLoaded', function() {
    initializeCounters();
    setupRefreshButton();
    initializeAlerts();
});

function initializeCounters() {
    const counters = document.querySelectorAll('.stat-number');
    counters.forEach(counter => {
        const target = parseInt(counter.innerText);
        animateCounter(counter, 0, target);
    });
}

function animateCounter(element, start, end) {
    let current = start;
    const increment = end / 30;
    const duration = 1000;
    const stepTime = duration / 30;

    const timer = setInterval(() => {
        current += increment;
        if (current >= end) {
            element.textContent = end;
            clearInterval(timer);
        } else {
            element.textContent = Math.floor(current);
        }
    }, stepTime);
}

function setupRefreshButton() {
    const refreshBtn = document.querySelector('.btn-refresh');
    if (refreshBtn) {
        refreshBtn.addEventListener('click', async () => {
            refreshBtn.classList.add('rotating');
            await refreshStats();
            setTimeout(() => refreshBtn.classList.remove('rotating'), 1000);
        });
    }
}

async function refreshStats() {
    try {
        const response = await fetch('get-dashboard-stats.php');
        const data = await response.json();
        updateDashboardStats(data);
    } catch (error) {
        showNotification('Error refreshing stats', 'error');
    }
}

function updateDashboardStats(data) {
    const supervisorCount = document.getElementById('supervisorCount');
    const requestCount = document.getElementById('requestCount');
    
    if (supervisorCount) {
        animateCounter(supervisorCount, 
            parseInt(supervisorCount.textContent), 
            data.supervisor_count);
    }
    
    if (requestCount) {
        animateCounter(requestCount, 
            parseInt(requestCount.textContent), 
            data.pending_requests);
    }
}

function initializeAlerts() {
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        alert.addEventListener('click', () => alert.remove());
    });
}

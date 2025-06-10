document.addEventListener('DOMContentLoaded', function() {
    if (document.getElementById('kpiChart')) {
        initializeKPIChart();
    }
    if (document.getElementById('salesChart')) {
        initializeSalesChart();
    }
    if (document.getElementById('branchPerformanceTable')) {
        loadBranchPerformance();
    }
});

function initializeKPIChart() {
    const ctx = document.getElementById('kpiChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
            datasets: [{
                label: 'Revenue Growth',
                data: [12, 19, 15, 25, 22, 30],
                borderColor: 'rgb(75, 192, 192)',
                tension: 0.1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false
        }
    });
}

function initializeSalesChart() {
    const ctx = document.getElementById('salesChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['Q1', 'Q2', 'Q3', 'Q4'],
            datasets: [{
                label: 'Sales Performance',
                data: [65, 59, 80, 81],
                backgroundColor: 'rgba(54, 162, 235, 0.2)',
                borderColor: 'rgb(54, 162, 235)',
                borderWidth: 1
            }]
        }
    });
}

function loadBranchPerformance() {
    // Implement AJAX call to fetch branch performance data
    fetch('/api/branches/performance')
        .then(response => response.json())
        .then(data => updateBranchTable(data))
        .catch(error => console.error('Error:', error));
}

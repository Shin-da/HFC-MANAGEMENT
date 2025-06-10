document.addEventListener('DOMContentLoaded', function() {
    // Initialize Charts
    initializeSalesChart();
    initializeCategoryChart();
    initializeInventoryChart();
    
    // Handle Tab Navigation
    const tabButtons = document.querySelectorAll('.tab-btn');
    tabButtons.forEach(button => {
        button.addEventListener('click', () => {
            tabButtons.forEach(btn => btn.classList.remove('active'));
            button.classList.add('active');
        });
    });
});

function initializeSalesChart() {
    const ctx = document.getElementById('salesTrendsChart').getContext('2d');
    // Chart configuration
}

function initializeCategoryChart() {
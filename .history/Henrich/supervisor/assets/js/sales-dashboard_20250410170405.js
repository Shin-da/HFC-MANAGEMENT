/**
 * Sales Dashboard Main JS
 * Consolidated file for all sales dashboard functionality
 */

document.addEventListener('DOMContentLoaded', function() {
    console.log("===== DASHBOARD INITIALIZATION =====");
    
    // Add global error handler to catch unhandled exceptions
    window.addEventListener('error', function(event) {
        console.error('Global error caught:', event.error);
        showErrorMessage('JavaScript error: ' + event.error.message);
    });
    
    // Check if Chart.js is loaded
    if (typeof Chart === 'undefined') {
        console.error("Chart.js is not loaded!");
        showErrorMessage("Chart.js library is missing. Please check your network connection or contact support.");
        return;
    }
    
    console.log("Starting dashboard initialization...");
    initializeDashboard();
    setupEventListeners();
    
    // Debug API URLs
    console.log("Debug: API URL base path examples:");
    console.log("- Absolute path: /HFC%20MANAGEMENT/Henrich/supervisor/api/sales-data.php");
    console.log("- Document location:", document.location.href);
});

/**
 * Get base URL for API calls
 */
function getApiBaseUrl() {
    // Try several methods to determine the base URL
    let baseUrl = '';
    
    // Method 1: From document location
    const pathParts = document.location.pathname.split('/');
    const hfcIndex = pathParts.findIndex(part => part === 'HFC MANAGEMENT' || part === 'HFC%20MANAGEMENT');
    
    if (hfcIndex !== -1) {
        baseUrl = '/' + pathParts.slice(1, hfcIndex + 3).join('/');
    } else {
        // Fallback to hardcoded path
        baseUrl = '/HFC%20MANAGEMENT/Henrich/supervisor';
    }
    
    console.log("Using API base URL:", baseUrl);
    return baseUrl;
}

/**
 * Initialize the dashboard components
 */
async function initializeDashboard() {
    showLoading();
    try {
        console.log("Fetching all required data...");
        
        // Load all required data
        const salesData = await fetchSalesData();
        console.log("✓ Sales data loaded:", salesData);
        
        const analyticsData = await fetchAnalyticsData();
        console.log("✓ Analytics data loaded:", analyticsData);
        
        const recommendationsData = await fetchRecommendationsData();
        console.log("✓ Recommendations data loaded:", recommendationsData);
        
        const inventoryData = await fetchInventoryData();
        console.log("✓ Inventory data loaded:", inventoryData);
        
        // Check for valid response format
        if (!salesData || salesData.status !== 'success') {
            throw new Error('Invalid sales data format');
        }
        
        if (!analyticsData || analyticsData.status !== 'success') {
            throw new Error('Invalid analytics data format');
        }
        
        console.log("Updating UI components...");
        
        // Update UI components
        updateSalesMetrics(salesData);
        console.log("✓ Sales metrics updated");
        
        initializeCharts(salesData, analyticsData);
        console.log("✓ Charts initialized");
        
        displayRecommendations(recommendationsData);
        console.log("✓ Recommendations displayed");
        
        updateInventoryStatus(inventoryData);
        console.log("✓ Inventory status updated");
        
        console.log("Dashboard initialization complete!");
        hideLoading();
    } catch (error) {
        console.error('Error initializing dashboard:', error);
        showErrorMessage('Failed to load dashboard data: ' + error.message);
        hideLoading();
    }
}

/**
 * Setup event listeners for dashboard interactions
 */
function setupEventListeners() {
    // Date range picker
    const dateRangeSelector = document.getElementById('dateRangeSelector');
    if (dateRangeSelector) {
        dateRangeSelector.addEventListener('change', function() {
            const period = this.value;
            refreshDashboard(period);
        });
    }

    // Custom date range picker
    const customDateForm = document.getElementById('customDateForm');
    if (customDateForm) {
        customDateForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const startDate = document.getElementById('startDate').value;
            const endDate = document.getElementById('endDate').value;
            refreshDashboardWithCustomDates(startDate, endDate);
        });
    }

    // Export buttons
    const exportButtons = document.querySelectorAll('.export-btn');
    exportButtons.forEach(button => {
        button.addEventListener('click', function() {
            const type = this.dataset.type;
            exportData(type);
        });
    });
}

/**
 * Fetch sales data from API
 */
async function fetchSalesData(period = 'month', startDate = null, endDate = null) {
    const baseUrl = getApiBaseUrl();
    let url = `${baseUrl}/api/sales-data.php?action=sales&period=${period}`;
    
    if (startDate && endDate) {
        url += `&start_date=${startDate}&end_date=${endDate}`;
    }
    
    console.log("Fetching sales data from:", url);
    
    try {
        const response = await fetch(url);
        console.log("Sales data response status:", response.status);
        
        if (!response.ok) {
            const errorText = await response.text();
            console.error("Error response text:", errorText);
            throw new Error(`Failed to fetch sales data: ${response.status} ${response.statusText}`);
        }
        
        const data = await response.json();
        console.log("Sales data received:", data);
        return data;
    } catch (error) {
        console.error("Error in fetchSalesData:", error);
        // Try alternative URL as fallback
        console.log("Trying fallback URL...");
        const fallbackUrl = `/HFC%20MANAGEMENT/Henrich/supervisor/api/sales-data.php?action=sales&period=${period}`;
        
        if (url !== fallbackUrl) {
            const fallbackResponse = await fetch(fallbackUrl);
            if (!fallbackResponse.ok) {
                throw error;
            }
            return await fallbackResponse.json();
        }
        throw error;
    }
}

/**
 * Fetch analytics data from API
 */
async function fetchAnalyticsData(period = 'month', startDate = null, endDate = null) {
    const baseUrl = getApiBaseUrl();
    let url = `${baseUrl}/api/sales-data.php?action=analytics&period=${period}`;
    
    if (startDate && endDate) {
        url += `&start_date=${startDate}&end_date=${endDate}`;
    }
    
    console.log("Fetching analytics data from:", url);
    
    try {
        const response = await fetch(url);
        
        if (!response.ok) {
            const errorText = await response.text();
            console.error("Error response text:", errorText);
            throw new Error(`Failed to fetch analytics data: ${response.status} ${response.statusText}`);
        }
        
        return await response.json();
    } catch (error) {
        console.error("Error in fetchAnalyticsData:", error);
        // Try alternative URL as fallback
        const fallbackUrl = `/HFC%20MANAGEMENT/Henrich/supervisor/api/sales-data.php?action=analytics&period=${period}`;
        
        if (url !== fallbackUrl) {
            const fallbackResponse = await fetch(fallbackUrl);
            if (!fallbackResponse.ok) {
                throw error;
            }
            return await fallbackResponse.json();
        }
        throw error;
    }
}

/**
 * Fetch recommendations data from API
 */
async function fetchRecommendationsData() {
    const baseUrl = getApiBaseUrl();
    const url = `${baseUrl}/api/sales-data.php?action=recommendations`;
    console.log("Fetching recommendations data from:", url);
    
    try {
        const response = await fetch(url);
        
        if (!response.ok) {
            const errorText = await response.text();
            console.error("Error response text:", errorText);
            throw new Error(`Failed to fetch recommendations: ${response.status} ${response.statusText}`);
        }
        
        return await response.json();
    } catch (error) {
        console.error("Error in fetchRecommendationsData:", error);
        // Try alternative URL as fallback
        const fallbackUrl = `/HFC%20MANAGEMENT/Henrich/supervisor/api/sales-data.php?action=recommendations`;
        
        if (url !== fallbackUrl) {
            const fallbackResponse = await fetch(fallbackUrl);
            if (!fallbackResponse.ok) {
                throw error;
            }
            return await fallbackResponse.json();
        }
        throw error;
    }
}

/**
 * Fetch inventory data from API
 */
async function fetchInventoryData() {
    const baseUrl = getApiBaseUrl();
    const url = `${baseUrl}/api/sales-data.php?action=inventory`;
    console.log("Fetching inventory data from:", url);
    
    try {
        const response = await fetch(url);
        
        if (!response.ok) {
            const errorText = await response.text();
            console.error("Error response text:", errorText);
            throw new Error(`Failed to fetch inventory data: ${response.status} ${response.statusText}`);
        }
        
        return await response.json();
    } catch (error) {
        console.error("Error in fetchInventoryData:", error);
        // Try alternative URL as fallback
        const fallbackUrl = `/HFC%20MANAGEMENT/Henrich/supervisor/api/sales-data.php?action=inventory`;
        
        if (url !== fallbackUrl) {
            const fallbackResponse = await fetch(fallbackUrl);
            if (!fallbackResponse.ok) {
                throw error;
            }
            return await fallbackResponse.json();
        }
        throw error;
    }
}

/**
 * Update sales metrics in the dashboard
 */
function updateSalesMetrics(data) {
    if (!data || data.status !== 'success') return;
    
    const metrics = data.metrics;
    
    // Update stats cards
    const totalSalesEl = document.getElementById('totalSales');
    const totalOrdersEl = document.getElementById('totalOrders');
    const avgOrderValueEl = document.getElementById('avgOrderValue');
    
    if (totalSalesEl) totalSalesEl.textContent = formatCurrency(metrics.total_sales);
    if (totalOrdersEl) totalOrdersEl.textContent = metrics.total_orders;
    if (avgOrderValueEl) avgOrderValueEl.textContent = formatCurrency(metrics.avg_order_value);
}

/**
 * Initialize all charts in the dashboard
 */
function initializeCharts(salesData, analyticsData) {
    if (!salesData || salesData.status !== 'success' || !analyticsData || analyticsData.status !== 'success') return;
    
    initializeSalesTrendChart(salesData.data);
    initializeCategoryDistributionChart(analyticsData.category_trends);
    initializeProductPerformanceChart(analyticsData.top_products);
    initializeMonthlyTrendsChart(analyticsData.monthly_trends);
}

/**
 * Initialize sales trend chart
 */
function initializeSalesTrendChart(data) {
    const salesTrendCtx = document.getElementById('salesTrendChart');
    if (!salesTrendCtx) return;
    
    const chartLabels = data.map(item => item.date);
    const chartData = data.map(item => parseFloat(item.total_sales));
    
    new Chart(salesTrendCtx, {
        type: 'line',
        data: {
            labels: chartLabels,
            datasets: [{
                label: 'Daily Sales',
                data: chartData,
                fill: false,
                borderColor: '#df5c36',
                tension: 0.1,
                backgroundColor: '#df5c36'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Sales Amount'
                    }
                },
                x: {
                    title: {
                        display: true,
                        text: 'Date'
                    }
                }
            }
        }
    });
}

/**
 * Initialize category distribution chart
 */
function initializeCategoryDistributionChart(data) {
    const categoryCtx = document.getElementById('categoryDistributionChart');
    if (!categoryCtx) return;
    
    const chartLabels = data.map(item => item.productcategory);
    const chartData = data.map(item => parseFloat(item.total_sales));
    
    new Chart(categoryCtx, {
        type: 'doughnut',
        data: {
            labels: chartLabels,
            datasets: [{
                data: chartData,
                backgroundColor: [
                    '#df5c36',
                    '#de9a45',
                    '#e5ba90',
                    '#933d24',
                    '#a6ab8a',
                    '#6a362b'
                ]
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'right'
                },
                title: {
                    display: true,
                    text: 'Sales by Category'
                }
            }
        }
    });
}

/**
 * Initialize product performance chart
 */
function initializeProductPerformanceChart(data) {
    const productCtx = document.getElementById('productPerformanceChart');
    if (!productCtx) return;
    
    const chartLabels = data.map(item => item.productname);
    const chartData = data.map(item => parseFloat(item.total_quantity));
    
    new Chart(productCtx, {
        type: 'bar',
        data: {
            labels: chartLabels,
            datasets: [{
                label: 'Units Sold',
                data: chartData,
                backgroundColor: '#de9a45'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            indexAxis: 'y',
            scales: {
                x: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Units Sold'
                    }
                }
            },
            plugins: {
                title: {
                    display: true,
                    text: 'Top Selling Products'
                }
            }
        }
    });
}

/**
 * Initialize monthly trends chart
 */
function initializeMonthlyTrendsChart(data) {
    const monthlyCtx = document.getElementById('monthlyTrendsChart');
    if (!monthlyCtx) return;
    
    const chartLabels = data.map(item => {
        const monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        return monthNames[item.month - 1] + ' ' + item.year;
    });
    const chartData = data.map(item => parseFloat(item.total_sales));
    
    new Chart(monthlyCtx, {
        type: 'line',
        data: {
            labels: chartLabels,
            datasets: [{
                label: 'Monthly Sales',
                data: chartData,
                fill: true,
                borderColor: '#933d24',
                backgroundColor: 'rgba(147, 61, 36, 0.1)',
                tension: 0.3
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Sales Amount'
                    }
                }
            },
            plugins: {
                title: {
                    display: true,
                    text: 'Monthly Sales Trends'
                }
            }
        }
    });
}

/**
 * Display recommendations in the dashboard
 */
function displayRecommendations(data) {
    if (!data || data.status !== 'success') return;
    
    const recommendationsContainer = document.getElementById('recommendationsContainer');
    if (!recommendationsContainer) return;
    
    // Clear existing recommendations
    recommendationsContainer.innerHTML = '';
    
    // Add each recommendation
    data.recommendations.forEach(recommendation => {
        const card = document.createElement('div');
        card.className = 'recommendation-card';
        
        let iconClass = 'bx bx-bulb';
        switch(recommendation.type) {
            case 'inventory':
                iconClass = 'bx bx-package';
                break;
            case 'growth':
                iconClass = 'bx bx-trending-up';
                break;
            case 'product_mix':
                iconClass = 'bx bx-category';
                break;
        }
        
        card.innerHTML = `
            <div class="recommendation-icon">
                <i class="${iconClass}"></i>
            </div>
            <div class="recommendation-content">
                <h4>${recommendation.title}</h4>
                <p>${recommendation.action}</p>
                ${recommendation.metrics ? `
                <div class="metrics">
                    <span>Growth Rate: ${recommendation.metrics.avg_growth_rate}</span>
                </div>
                ` : ''}
                ${recommendation.categories ? `
                <div class="categories">
                    <span>Categories: ${recommendation.categories.join(', ')}</span>
                </div>
                ` : ''}
            </div>
        `;
        
        recommendationsContainer.appendChild(card);
    });
    
    // Display seasonality information if detected
    if (data.seasonality && data.seasonality.detected) {
        const seasonalityCard = document.createElement('div');
        seasonalityCard.className = 'recommendation-card seasonality';
        
        const peakMonths = data.seasonality.peak_months.map(month => {
            const monthNames = ['January', 'February', 'March', 'April', 'May', 'June', 
                               'July', 'August', 'September', 'October', 'November', 'December'];
            return monthNames[month - 1];
        }).join(', ');
        
        seasonalityCard.innerHTML = `
            <div class="recommendation-icon">
                <i class="bx bx-calendar"></i>
            </div>
            <div class="recommendation-content">
                <h4>Seasonal Pattern Detected</h4>
                <p>Peak sales occur in: ${peakMonths}</p>
                <p>Consider planning promotions and inventory around these peak periods.</p>
            </div>
        `;
        
        recommendationsContainer.appendChild(seasonalityCard);
    }
}

/**
 * Update inventory status in the dashboard
 */
function updateInventoryStatus(data) {
    if (!data || data.status !== 'success') return;
    
    const inventoryContainer = document.getElementById('inventoryStatusContainer');
    if (!inventoryContainer) return;
    
    // Update inventory summary metrics
    const totalItemsEl = document.getElementById('totalItems');
    const lowStockItemsEl = document.getElementById('lowStockItems');
    const outOfStockItemsEl = document.getElementById('outOfStockItems');
    
    if (totalItemsEl) totalItemsEl.textContent = data.summary.total_items;
    if (lowStockItemsEl) lowStockItemsEl.textContent = data.summary.low_stock_items;
    if (outOfStockItemsEl) outOfStockItemsEl.textContent = data.summary.out_of_stock_items;
    
    // Update inventory table
    const inventoryTable = document.getElementById('inventoryTable');
    if (inventoryTable && inventoryTable.querySelector('tbody')) {
        const tbody = inventoryTable.querySelector('tbody');
        tbody.innerHTML = '';
        
        // Show only low stock items in the dashboard
        const lowStockItems = data.data.filter(item => item.availablequantity <= item.reorderlevel);
        
        lowStockItems.slice(0, 10).forEach(item => {
            const row = document.createElement('tr');
            row.className = item.availablequantity <= 0 ? 'out-of-stock' : 'low-stock';
            
            row.innerHTML = `
                <td>${item.productname}</td>
                <td>${item.productcategory}</td>
                <td>${item.availablequantity}</td>
                <td>${item.reorderlevel}</td>
                <td>
                    <span class="status-badge ${item.availablequantity <= 0 ? 'danger' : 'warning'}">
                        ${item.availablequantity <= 0 ? 'Out of Stock' : 'Low Stock'}
                    </span>
                </td>
            `;
            
            tbody.appendChild(row);
        });
    }
}

/**
 * Refresh dashboard with selected period
 */
function refreshDashboard(period) {
    showLoading();
    Promise.all([
        fetchSalesData(period),
        fetchAnalyticsData(period)
    ])
    .then(([salesData, analyticsData]) => {
        updateSalesMetrics(salesData);
        initializeCharts(salesData, analyticsData);
        hideLoading();
    })
    .catch(error => {
        console.error('Error refreshing dashboard:', error);
        showErrorMessage('Failed to refresh dashboard data');
        hideLoading();
    });
}

/**
 * Refresh dashboard with custom date range
 */
function refreshDashboardWithCustomDates(startDate, endDate) {
    showLoading();
    Promise.all([
        fetchSalesData('custom', startDate, endDate),
        fetchAnalyticsData('custom', startDate, endDate)
    ])
    .then(([salesData, analyticsData]) => {
        updateSalesMetrics(salesData);
        initializeCharts(salesData, analyticsData);
        hideLoading();
    })
    .catch(error => {
        console.error('Error refreshing dashboard with custom dates:', error);
        showErrorMessage('Failed to refresh dashboard data');
        hideLoading();
    });
}

/**
 * Export data as CSV
 */
function exportAsCSV() {
    const baseUrl = getApiBaseUrl();
    // Quick CSV export implementation
    fetch(`${baseUrl}/api/sales-data.php?action=sales&period=month`)
        .then(response => response.json())
        .then(data => {
            if (!data || data.status !== 'success') return;
            
            // Convert to CSV
            const headers = Object.keys(data.data[0]).join(',');
            const rows = data.data.map(item => Object.values(item).join(',')).join('\n');
            const csv = headers + '\n' + rows;
            
            // Create download link
            const blob = new Blob([csv], { type: 'text/csv' });
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.setAttribute('hidden', '');
            a.setAttribute('href', url);
            a.setAttribute('download', 'sales_data.csv');
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
        })
        .catch(error => {
            console.error('Error exporting CSV:', error);
            showErrorMessage('Failed to export data');
        });
}

/**
 * Export data as PDF
 */
function exportAsPDF() {
    alert('PDF export functionality will be implemented here');
    // Implementation details would depend on a PDF generation library
}

/**
 * Export data as Excel
 */
function exportAsExcel() {
    alert('Excel export functionality will be implemented here');
    // Implementation would require additional libraries
}

/**
 * Format currency value
 */
function formatCurrency(value) {
    return new Intl.NumberFormat('en-PH', { 
        style: 'currency', 
        currency: 'PHP' 
    }).format(value);
}

/**
 * Show loading indicator
 */
function showLoading() {
    const loader = document.querySelector('.loading-spinner');
    if (loader) loader.style.display = 'block';
}

/**
 * Hide loading indicator
 */
function hideLoading() {
    const loader = document.querySelector('.loading-spinner');
    if (loader) loader.style.display = 'none';
}

/**
 * Show error message
 */
function showErrorMessage(message) {
    const errorContainer = document.getElementById('errorContainer');
    if (errorContainer) {
        errorContainer.textContent = message;
        errorContainer.style.display = 'block';
        
        // Auto-hide after 5 seconds
        setTimeout(() => {
            errorContainer.style.display = 'none';
        }, 5000);
    } else {
        alert(message);
    }
}

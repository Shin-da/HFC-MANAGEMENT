<script>
var bestSellingProductsData = <?= json_encode($best_selling_products) ?>;
var salesByCategoryData = <?= json_encode($sales_by_category) ?>;
var customerOrdersData = <?= json_encode($customer_orders) ?>;

var bestSellingProductsChartCtx = document.getElementById('bestSellingProductsChart').getContext('2d');
var bestSellingProductsChart = new Chart(bestSellingProductsChartCtx, {
    type: 'pie',
    data: {
        labels: bestSellingProductsData.map(function(item) {
            return item.productname;
        }),
        datasets: [{
            // label: 'Best Selling Products',
            data: bestSellingProductsData.map(function(item) {
                return item.total_quantity;
            }),
            backgroundColor: [
                'rgba(255, 99, 132, 0.5)',
                'rgba(54, 162, 235, 0.5)',
                'rgba(255, 206, 86, 0.5)',
                'rgba(75, 192, 192, 0.5)',
                'rgba(153, 102, 255, 0.5)'
            ],
            borderColor: [
                'rgba(255, 99, 132, 1)',
                'rgba(54, 162, 235, 1)',
                'rgba(255, 206, 86, 1)',
                'rgba(75, 192, 192, 1)',
                'rgba(153, 102, 255, 1)'
            ],
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                position: 'top',
            },
            title: {
                display: true,
                // text: 'Best Selling Products'
            },
            zoom: {
                pan: {
                    enabled: true,
                    mode: 'x'
                },
                zoom: {
                    enabled: true,
                    mode: 'x'
                }
            }
        }
    }
});

var salesByCategoryChartCtx = document.getElementById('salesByCategoryChart').getContext('2d');
var salesByCategoryChart = new Chart(salesByCategoryChartCtx, {
    type: 'bar',
    data: {
        labels: salesByCategoryData.map(function(item) {
            return item.productweight;
        }),
        datasets: [{
            label: 'Sales by Category',
            data: salesByCategoryData.map(function(item) {
                return item.total_quantity;
            }),
            backgroundColor: [
                'rgba(255, 159, 64, 0.5)',
                'rgba(255, 205, 86, 0.5)',
                'rgba(75, 192, 192, 0.5)',
                'rgba(54, 162, 235, 0.5)',
                'rgba(153, 102, 255, 0.5)'
            ],
            borderColor: [
                'rgba(255, 159, 64, 1)',
                'rgba(255, 205, 86, 1)',
                'rgba(75, 192, 192, 1)',
                'rgba(54, 162, 235, 1)',
                'rgba(153, 102, 255, 1)'
            ],
            borderWidth: 1
        }]
    },
    options: {
        scales: {
            y: {
                beginAtZero: true
            }
        },
        plugins: {
            zoom: {
                pan: {
                    enabled: true,
                    mode: 'x'
                },
                zoom: {
                    enabled: true,
                    mode: 'x'
                }
            }
        }
    }
});

var customerOrdersChartCtx = document.getElementById('customerOrdersChart').getContext('2d');
var customerOrdersChart = new Chart(customerOrdersChartCtx, {
    type: 'line',
    data: {
        labels: customerOrdersData.map(function(item) {
            return item.orderdate;
        }),
        datasets: [{
            label: 'Customer Orders Over Time',
            data: customerOrdersData.map(function(item) {
                return item.ordertotal;
            }),
            backgroundColor: 'rgba(201, 203, 207, 0.5)',
            borderColor: 'rgba(201, 203, 207, 1)',
            borderWidth: 1
        }]
    },
    options: {
        scales: {
            y: {
                beginAtZero: true
            }
        },
        plugins: {
            zoom: {
                pan: {
                    enabled: true,
                    mode: 'x'
                },
                zoom: {
                    enabled: true,
                    mode: 'x'
                }
            }
        }
    }
});
</script>ret
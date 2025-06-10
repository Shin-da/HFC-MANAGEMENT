// Table search functionality
function searchTable() {
    const input = document.getElementById('general-search');
    const filter = input.value.toUpperCase();
    const table = document.getElementById('myTable');
    const rows = table.getElementsByTagName('tr');

    for (let i = 2; i < rows.length; i++) {
        let found = false;
        const cells = rows[i].getElementsByTagName('td');
        
        for (let j = 0; j < cells.length; j++) {
            const cell = cells[j];
            if (cell) {
                const text = cell.textContent || cell.innerText;
                if (text.toUpperCase().indexOf(filter) > -1) {
                    found = true;
                    break;
                }
            }
        }
        rows[i].style.display = found ? '' : 'none';
    }
}

// Category filter
function filterByCategory() {
    const select = document.getElementById('category-filter');
    const category = select.value.toUpperCase();
    const table = document.getElementById('myTable');
    const rows = table.getElementsByTagName('tr');

    for (let i = 2; i < rows.length; i++) {
        const categoryCell = rows[i].getElementsByTagName('td')[3];
        if (categoryCell) {
            const text = categoryCell.textContent || categoryCell.innerText;
            rows[i].style.display = !category || text.toUpperCase() === category ? '' : 'none';
        }
    }
}

// Stock status filter
function filterByStock() {
    const select = document.getElementById('stock-status');
    const status = select.value;
    const table = document.getElementById('myTable');
    const rows = table.getElementsByTagName('tr');

    for (let i = 2; i < rows.length; i++) {
        const quantityCell = rows[i].getElementsByTagName('td')[4];
        if (quantityCell) {
            const quantity = parseInt(quantityCell.textContent || quantityCell.innerText);
            let show = true;
            
            switch(status) {
                case 'low':
                    show = quantity <= 10 && quantity > 0;
                    break;
                case 'out':
                    show = quantity === 0;
                    break;
                case 'normal':
                    show = quantity > 10;
                    break;
            }
            
            rows[i].style.display = show ? '' : 'none';
        }
    }
}

// Export functions
function exportToExcel() {
    const table = document.getElementById('myTable');
    const wb = XLSX.utils.table_to_book(table, {sheet: "Inventory"});
    XLSX.writeFile(wb, 'inventory_report.xlsx');
}

function exportToPDF() {
    const element = document.getElementById('myTable');
    html2pdf().from(element).save('inventory_report.pdf');
}

// Enhanced filtering with date range
function filterByDateRange() {
    const startDate = document.getElementById('start-date').value;
    const endDate = document.getElementById('end-date').value;
    const rows = document.querySelectorAll('#myTable tr');
    
    rows.forEach(row => {
        const dateCell = row.querySelector('td:nth-child(6)');
        if (dateCell) {
            const rowDate = new Date(dateCell.textContent);
            const show = (!startDate || rowDate >= new Date(startDate)) && 
                        (!endDate || rowDate <= new Date(endDate));
            row.style.display = show ? '' : 'none';
        }
    });
}

// Initialize value trends chart
function initValueTrendsChart() {
    const canvas = document.getElementById('valueTrendsChart');
    if (!canvas) {
        console.error('Canvas element not found');
        return;
    }

    console.log('Trends data:', trendsData); // Debug log

    if (!trendsData || !trendsData.dates || trendsData.dates.length === 0) {
        canvas.style.display = 'none';
        const noData = document.createElement('p');
        noData.textContent = 'No trend data available';
        noData.style.textAlign = 'center';
        data: {
            labels: trendsData.dates,
            datasets: [{
                label: 'Daily Inventory Value',
                data: trendsData.values,
                borderColor: '#3498db',
                tension: 0.1,
                fill: false
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                title: {
                    display: true,
                    text: 'Inventory Value Trend (Last 7 Days)'
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return '₱' + value.toLocaleString();
                        }
                    }
                }
            }
        }
    });
}

// Initialize when document is ready
document.addEventListener('DOMContentLoaded', function() {
    initValueTrendsChart();
});

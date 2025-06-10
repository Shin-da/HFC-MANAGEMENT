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
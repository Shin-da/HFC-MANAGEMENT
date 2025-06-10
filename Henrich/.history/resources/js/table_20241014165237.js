// Function to filter the table rows
function filterTable(tableBody, filterValue, columnIndex) {
  // Get all table rows
  var rows = tableBody.querySelectorAll('tr');

  // Loop through each row and hide/show it based on the filter value
  rows.forEach(function(row) {
    // Get the cell values for this row
    var cellValues = row.querySelectorAll('td');

    // Check if the row matches the filter value
    var match = true;
    if (cellValues[columnIndex] && cellValues[columnIndex].textContent.toLowerCase().indexOf(filterValue.toLowerCase()) === -1) {
      match = false;
    }

    // Hide/show the row based on the match
    if (match) {
      row.style.display = '';
    } else {
      row.style.display = 'none';
    }
  });
}

// Get all tables on the page
var tables = document.querySelectorAll('table');

// Loop through each table
tables.forEach(function(table) {
  // Get the filter inputs and table body for this table
  var filterInputs = table.querySelectorAll('.filter-input');
  var tableBody = table.querySelector('tbody');

  // Add event listeners to the filter inputs
  filterInputs.forEach(function(input) {
    input.addEventListener('input', function() {
      // Get the column index of the filter input
      var columnIndex = Array.prototype.indexOf.call(filterInputs, input);

      // Filter the table rows based on the input value
      filterTable(tableBody, input.value, columnIndex);
    });
  });
});
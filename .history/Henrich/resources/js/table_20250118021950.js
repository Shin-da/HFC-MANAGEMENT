


function search() { // Search table Function 
  var input, filter, table, tr, td, i, txtValue, noResultsRow; // Declare variables 
  input = document.getElementById("general-search");
  filter = input.value.toUpperCase();
  table = document.getElementById("myTable");
  tr = table.getElementsByTagName("tr");

  noResultsRow = document.getElementById("no-results-row");
  if (noResultsRow === null) {
    noResultsRow = document.createElement("tr");
    noResultsRow.id = "no-results-row";
    noResultsRow.innerHTML = "<td colspan='8'>No results found</td>";
    table.appendChild(noResultsRow);
  }

  // Hide the no results row by default
  noResultsRow.style.display = "none";

  // Loop through all table rows, and hide those who don't match the search query
  var resultsFound = false;
  for (i = 0; i < tr.length; i++) {
    for (var td of tr[i].getElementsByTagName("td")) {
      txtValue = td.textContent || td.innerText;
      if (txtValue.toUpperCase().indexOf(filter) > -1) {
        tr[i].style.display = "";
        resultsFound = true;
        break;
      } else {
        tr[i].style.display = "none";
      }
    }
  }

  // If no results are found, display the no results row
  if (!resultsFound) {
    noResultsRow.style.display = "";
  }

   // If the search query is empty, remove the no results row
   if (input.value.trim() === "") {
    noResultsRow.style.display = "none";
  }
}


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

    // Check if any results were found
    var resultsFound = false;
    rows.forEach(function(row) {
      if (row.style.display !== "none") {
        resultsFound = true;
      }
    });
  
    // Remove any existing no results rows
    var existingNoResultsRows = tableBody.querySelectorAll(".no-results-row");
    existingNoResultsRows.forEach(function(row) {
      row.remove();
    });
  
    // If no results were found, display a no results row
    if (!resultsFound && filterValue.trim() !== "") {
      var noResultsRow = document.createElement("tr");
      noResultsRow.className = "no-results-row";
      noResultsRow.innerHTML = "<td colspan='" + tableBody.querySelectorAll("tr")[0].querySelectorAll("td").length + "'>No results found</td>";
      tableBody.appendChild(noResultsRow);
    }
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
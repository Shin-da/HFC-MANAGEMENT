
/**
 * Filters table results based on user input
 */
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
      console.log('Filter input changed:', input.value);
      // Get the column index of the filter input
      var columnIndex = Array.prototype.indexOf.call(filterInputs, input);

      // Filter the table rows based on the input value
      filterTableRows(tableBody, input.value, columnIndex);
    });
  });

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


/**
 * Updates pagination links based on the current page
 */
function updatePaginationLinks() {
  var paginationLinks = document.querySelectorAll('.pagination a');
  var currentPage = parseInt(document.querySelector('.pagination .active').textContent);
  var totalPages = parseInt(document.querySelector('.pagination .page:last-child').textContent);

  paginationLinks.forEach(function(link) {
    var pageNumber = parseInt(link.textContent);
    if (pageNumber === currentPage) {
      link.classList.add('active');
    } else {
      link.classList.remove('active');
    }
    link.href = '?page=' + pageNumber;
  });
}

/**
 * Shows the specified page
 * @param {number} page The page number to show
 */
function showPage(page) {
  var tableBody = document.getElementById("table-body");
  var rows = tableBody.getElementsByTagName("tr");
  var filteredRows = [];
  for (var i = 0; i < rows.length; i++) {
    if (rows[i].style.display !== "none") {
      filteredRows.push(rows[i]);
    }
  }
  var startIndex = (page - 1) * 10;
  var endIndex = startIndex + 10;

  // Hide all rows
  for (var i = 0; i < rows.length; i++) {
    rows[i].style.display = "none";
  }

  // Show rows for the current page
  for (var i = startIndex; i < endIndex; i++) {
    if (i < filteredRows.length) {
      filteredRows[i].style.display = "table-row";
    }
  }
}

// Add event listeners to filter inputs
document.getElementById("iid-filter").addEventListener("input", function() {
  filterTable();
  updatePaginationLinks();
});
document.getElementById("productcode-filter").addEventListener("input", function() {
  filterTable();
  updatePaginationLinks();
});
document.getElementById("productdescription-filter").addEventListener("input", function() {
  filterTable();
  updatePaginationLinks();
});
document.getElementById("category-filter").addEventListener("input", function() {
  filterTable();
  updatePaginationLinks();
});
document.getElementById("onhand-filter").addEventListener("input", function() {
  filterTable();
  updatePaginationLinks();
});
document.getElementById("dateupdated-filter").addEventListener("input", function() {
  filterTable();
  updatePaginationLinks();
});
}

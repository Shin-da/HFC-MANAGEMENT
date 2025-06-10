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
      // Filter the table rows based on the input value
      filterTableRows(tableBody, input.value);
      updatePaginationLinks();
    });
  });

  // Function to filter the table rows
  function filterTableRows(tableBody, filterValue) {
    // Get all table rows
    var rows = tableBody.querySelectorAll('tr');

    // Loop through each row and hide/show it based on the filter value
    rows.forEach(function(row) {
      // Get the cell values for this row
      var cellValues = row.querySelectorAll('td');

      // Check if the row matches the filter value
      var match = true;
      cellValues.forEach(function(cell) {
        if (cell.textContent.toLowerCase().indexOf(filterValue.toLowerCase()) === -1) {
          match = false;
        }
      });

      // Hide/show the row based on the match
      if (match) {
        row.style.display = '';
      } else {
        row.style.display = 'none';
      }
    });
  }

  // Function to update pagination links
  function updatePaginationLinks() {
    var currentPage = getPageNumber();
    var totalPages = Math.ceil(getRowCount(tableBody) / 10);
    var paginationLinks = document.getElementById('pagination-links');
    paginationLinks.innerHTML = '';

    if (totalPages > 1) {
      // Add first page link
      var firstLink = document.createElement('a');
      firstLink.textContent = 'First';
      firstLink.href = '?page=1';
      firstLink.addEventListener('click', function() {
        showPage(1);
      });
      paginationLinks.appendChild(firstLink);

      // Add previous page link
      var previousLink = document.createElement('a');
      previousLink.textContent = 'Previous';
      previousLink.href = '?page=' + (currentPage - 1);
      previousLink.addEventListener('click', function() {
        showPage(currentPage - 1);
      });
      paginationLinks.appendChild(previousLink);

      // Add numbered page links
      for (var i = 1; i <= totalPages; i++) {
        var link = document.createElement('a');
        link.textContent = i;
        link.href = '?page=' + i;
        link.addEventListener('click', function() {
          showPage(parseInt(this.textContent));
        });
        paginationLinks.appendChild(link);

        if (i === currentPage) {
          link.classList.add('active');
        } else {
          link.classList.remove('active');
        }
      }

      // Add next page link
      var nextLink = document.createElement('a');
      nextLink.textContent = 'Next';
      nextLink.href = '?page=' + (currentPage + 1);
      nextLink.addEventListener('click', function() {
        showPage(currentPage + 1);
      });
      paginationLinks.appendChild(nextLink);

      // Add last page link
      var lastLink = document.createElement('a');
      lastLink.textContent = 'Last';
      lastLink.href = '?page=' + totalPages;
      lastLink.addEventListener('click', function() {
        showPage(totalPages);
      });
      paginationLinks.appendChild(lastLink);
    }
  }

  // Function to get the current page number from the URL
  function getPageNumber() {
    var url = window.location.href;
    var

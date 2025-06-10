
/**
 * Filters table results based on user input
 */
f
  // Get filter values
  var iidFilter = document.getElementById("iid-filter").value.toLowerCase();
  var productcodeFilter = document.getElementById("productcode-filter").value.toLowerCase();
  var productdescriptionFilter = document.getElementById("productdescription-filter").value.toLowerCase();
  var categoryFilter = document.getElementById("category-filter").value.toLowerCase();
  var onhandFilter = document.getElementById("onhand-filter").value.toLowerCase();
  var dateupdatedFilter = document.getElementById("dateupdated-filter").value.toLowerCase();

  // Get table body and rows
  var tableBody = document.getElementById("table-body");
  var rows = tableBody.getElementsByTagName("tr");

  // Filter rows
  for (var i = 0; i < rows.length; i++) {
    var row = rows[i];
    var cells = row.getElementsByTagName("td");

    var iidCell = cells[0].textContent.toLowerCase();
    var productcodeCell = cells[1].textContent.toLowerCase();
    var productdescriptionCell = cells[2].textContent.toLowerCase();
    var categoryCell = cells[3].textContent.toLowerCase();
    var onhandCell = cells[4].textContent.toLowerCase();
    var dateupdatedCell = cells[5].textContent.toLowerCase();

    // Check if the row matches the filter
    var match = true;

    if (iidFilter !== "" && !iidCell.includes(iidFilter)) {
      match = false;
    }
    if (productcodeFilter !== "" && !productcodeCell.includes(productcodeFilter)) {
      match = false;
    }
    if (productdescriptionFilter !== "" && !productdescriptionCell.includes(productdescriptionFilter)) {
      match = false;
    }
    if (categoryFilter !== "" && !categoryCell.includes(categoryFilter)) {
      match = false;
    }
    if (onhandFilter !== "" && !onhandCell.includes(onhandFilter)) {
      match = false;
    }
    if (dateupdatedFilter !== "" && !dateupdatedCell.includes(dateupdatedFilter)) {
      match = false;
    }

    // Update visibility of the "no results" row
    var noResultsRow = document.getElementById("no-results-row");
    if (noResultsRow !== null) {
      if (match) {
        noResultsRow.style.display = "none";
      } else {
        noResultsRow.style.display = "";
      }
    }

    // Update visibility of the row
    if (match) {
      row.style.display = "";
    } else {
      row.style.display = "none";
    }
  }

  // Update pagination links
  updatePaginationLinks();
}

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

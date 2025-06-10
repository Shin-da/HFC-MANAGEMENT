
/**
 * Filters table results based on user input
 */
</div>
<div class="container" style="display: flex; justify-content: center; flex-direction: column; align-items: center; ">
  <ul class="pagination"><!-- Pagination for stock management -->
    <li><a href="?page=<?= $page - 1 <= 1 ? 1 : $page - 1 ?>" class="prev">&laquo;</a></li>
    <?php for ($i = 1; $i <= $totalPages; $i++) { ?>
      <li><a href="?page=<?= $i ?>" class="page <?= $page == $i ? 'active' : '' ?>"><?= $i ?></a></li>
    <?php } ?>
    <li><a href="?page=<?= $page + 1 > $totalPages ? $totalPages : $page + 1 ?>" class="next">&raquo;</a></li>
  </ul>

</div>
</div>

</div>
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

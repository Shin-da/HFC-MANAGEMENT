


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

 <script>
                var rowIndex = 0;

                function addTableRow() {
                    var tableRef = document.getElementById('customerOrderTable');
                    var newRow = tableRef.insertRow();
                    rowIndex++;

                    var columns = ['productcode', 'productname', 'productprice', 'productweight', 'quantity', 'totalweight', 'totalprice'];
                    columns.forEach((key, i) => {
                        var newCell = newRow.insertCell(i);
                        var newText;
                        switch (key) {

                            case 'totalprice':
                                newText = document.createElement('input');
                                newText.type = 'text';
                                newText.name = `${key}[]`;
                                newText.placeholder = key.charAt(0).toUpperCase() + key.slice(1);
                                newText.readOnly = true;
                                break;
                            case 'totalweight':
                                newText = document.createElement('input');
                                newText.type = 'text';
                                newText.name = `${key}[]`;
                                newText.placeholder = key.charAt(0).toUpperCase() + key.slice(1);
                                newText.readOnly = true;
                                break;
                            case 'productcode':
                                newText = document.createElement('select');
                                newText.name = `${key}[]`;
                                newText.placeholder = key.charAt(0).toUpperCase() + key.slice(1);
                                newText.id = `productcode${rowIndex}`;
                                newText.rowId = rowIndex;
                                newText.onchange = function() {
                                    getProductInfo(this);
                                }
                                newText.innerHTML = `<option value="" disabled selected>ProductCode</option>`;
                                <?php foreach ($productcodes as $productcode): ?>
                                    newText.innerHTML += `<option value="<?= $productcode ?>"><?= $productcode ?></option>`;
                                <?php endforeach; ?>
                                break;
                            case 'productname':
                                newText = document.createElement('input');
                                newText.type = 'text';
                                newText.name = 'productname[]';
                                newText.id = `productname${rowIndex}`;
                                newText.placeholder = key.charAt(0).toUpperCase() + key.slice(1);
                                newText.readOnly = true;
                                break;
                            case 'productweight':
                                newText = document.createElement('input');
                                newText.type = 'text';
                                newText.name = 'productweight[]';
                                newText.id = `productweight${rowIndex}`;
                                newText.placeholder = key.charAt(0).toUpperCase() + key.slice(1);
                                newText.readOnly = true;
                                break;
                            case 'productprice':
                                newText = document.createElement('input');
                                newText.type = 'text';
                                newText.name = 'productprice[]';
                                newText.id = `productprice${rowIndex}`;
                                newText.placeholder = key.charAt(0).toUpperCase() + key.slice(1);
                                newText.readOnly = true;
                                break;
                            case 'quantity':
                                newText = document.createElement('input');
                                newText.type = 'number';
                                newText.name = 'quantity[]';
                                newText.id = `quantity${rowIndex}`;
                                newText.placeholder = key.charAt(0).toUpperCase() + key.slice(1);
                                newText.oninput = function() {
                                    if (this.value <= 0) {
                                        this.value = 1;
                                    }
                                    calculateTotal(this);
                                }
                                break;
                            default:
                                break;
                        }
                        newCell.appendChild(newText);
                    });
                    var actionCell = newRow.insertCell(-1);
                    var deleteButton = document.createElement('button');
                    deleteButton.type = 'button';
                    deleteButton.className = 'btn btn-danger';
                    deleteButton.onclick = function() {
                        deleteTableRow(this);
                    }
                    deleteButton.innerText = 'X';
                    actionCell.appendChild(deleteButton);
                }

                function deleteTableRow(element) {
                    var row = element.parentNode.parentNode;
                    row.parentNode.removeChild(row);
                }

                function getProductInfo(select) {
                    var rowId = select.rowId;
                    var productCode = select.value;

                    var xmlhttp = new XMLHttpRequest();
                    xmlhttp.onreadystatechange = function() {
                        if (this.readyState == 4 && this.status == 200) {
                            var response = JSON.parse(this.responseText);
                            var productNameElement = document.getElementById(`productname${rowId}`);
                            var productPriceElement = document.getElementById(`productprice${rowId}`);
                            var productWeightElement = document.getElementById(`productweight${rowId}`);
                            if (productNameElement && productWeightElement && productPriceElement) {
                                productNameElement.value = response.productname;
                                productPriceElement.value = response.productprice;
                                productWeightElement.value = response.productweight;
                            } else {
                                console.error(`add.customerorder.php:${rowId} HTML elements with IDs 'productname${rowId}', 'productweight${rowId}' and 'productprice${rowId}' not found in the DOM`);
                            }
                        }
                    };

                    xmlhttp.open("GET", "./process/get_product_info.php?productcode=" + productCode, true);
                    xmlhttp.send();
                }

                function calculateTotal(inputElement) {
                    var row = inputElement.closest('tr');
                    var quantity = parseFloat(row.querySelector('input[name="quantity[]"]').value) || 0;
                    var productWeight = parseFloat(row.querySelector('input[name="productweight[]"]').value) || 0;
                    var productPrice = parseFloat(row.querySelector('input[name="productprice[]"]').value) || 0;

                    var totalWeight = quantity * productWeight;
                    var totalPrice = quantity * productPrice;

                    row.querySelector('input[name="totalweight[]"]').value = totalWeight.toFixed(2);
                    row.querySelector('input[name="totalprice[]"]').value = totalPrice.toFixed(2);

                    updateOrderTotalAndWeight();
                }

                function updateOrderTotalAndWeight() {
                    var table = document.getElementById('customerOrderTable');
                    var totalOrderWeight = 0;
                    var ordertotal = 0;

                    Array.from(table.rows).forEach(row => {
                        var totalWeightCell = row.querySelector('input[name="totalweight[]"]');
                        var totalPriceCell = row.querySelector('input[name="totalprice[]"]');
                        if (totalWeightCell && totalPriceCell) {
                            totalOrderWeight += parseFloat(totalWeightCell.value || 0);
                            ordertotal += parseFloat(totalPriceCell.value || 0);
                        }
                    });

                    document.getElementById('totalOrderWeight').textContent = totalOrderWeight.toFixed(2);
                    document.getElementById('ordertotal').textContent = ordertotal.toFixed(2);
                }

                document.getElementById("productcode<?= $i ?>").addEventListener("change", function() {
                    var productCode = this.value;
                    var xhttp = new XMLHttpRequest();
                    xhttp.onreadystatechange = function() {
                        if (this.readyState === 4 && this.status === 200) {
                            var response = JSON.parse(this.responseText);
                            if (response.availablequantity <= 0) {
                                alert("Product is out of stock!");
                            }
                        }
                    };
                    xhttp.open("GET", "./process/check_available_quantity.php?productcode=" + productCode, true);
                    xhttp.send();
                });
            </script>
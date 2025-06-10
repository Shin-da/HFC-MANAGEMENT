
function search() {
     // Declare variables 
     var input, filter, table, tr, td, i, txtValue;
     input = document.getElementById("myInput");
     filter = input.value.toUpperCase();
     table = document.getElementById("orders-table");
     tr = table.getElementsByTagName("tr");

     // Loop through all table rows, and hide those who don't match the search query
     for (i = 0; i < tr.length; i++) {
          td = tr[i].getElementsByTagName("td")[1];
          if (td) {
               txtValue = td.textContent || td.innerText;
               if (txtValue.toUpperCase().indexOf(filter) > -1) {
                    tr[i].style.display = "";
               } else {
                    tr[i].style.display = "none";
               }
          }
     }

     // TODO: Sort the table rows according to the search result
     // Define the sort order (ascending or descending)
     var sortOrder = "asc";
     // Loop through all table rows
     for (i = 0; i < tr.length; i++) {
          // Get the text content of the second column
          td = tr[i].getElementsByTagName("td")[1];
          if (td) {
               txtValue = td.textContent || td.innerText;
               // Sort the table rows based on the text content
               if (sortOrder === "asc") {
                    tr[i].parentNode.appendChild(tr[i]);
               } else if (sortOrder === "desc") {
                    tr[i].parentNode.insertBefore(tr[i], tr[0]);
               }
          }
     }
}

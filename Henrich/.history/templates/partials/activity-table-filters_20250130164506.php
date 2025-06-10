<tr class="filter-row">
    <th><input type="text" placeholder="Search Batch ID ... " id="batchid-filter" onkeyup="filterTable(document.querySelector('#myTable tbody'), this.value, 0)"></th>
    <th><input type="text" placeholder="Search Date of Arrival ... " id="dateofarrival-filter" onkeyup="filterTable(document.querySelector('#myTable tbody'), this.value, 1)"></th>
    <th><input type="text" placeholder="Search Date Encoded ... " id="dateencoded-filter" onkeyup="filterTable(document.querySelector('#myTable tbody'), this.value, 2)"></th>
    <th><input type="text" placeholder="Search Encoder ... " id="encoder-filter" onkeyup="filterTable(document.querySelector('#myTable tbody'), this.value, 3)"></th>
    <th><input type="text" placeholder="Search Description ... " id="description-filter" onkeyup="filterTable(document.querySelector('#myTable tbody'), this.value, 4)"></th>
    <th><input type="text" placeholder="Search Total Number Of Boxes ... " id="totalNumberOfBoxes-filter" onkeyup="filterTable(document.querySelector('#myTable tbody'), this.value, 5)"></th>
    <th><input type="text" placeholder="Search Overall Total Weight (kg) ... " id="overalltotalweight-filter" onkeyup="filterTable(document.querySelector('#myTable tbody'), this.value, 6)"></th>
</tr>

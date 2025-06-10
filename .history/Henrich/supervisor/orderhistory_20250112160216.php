                <tr 
                <?php
                    switch ($status) {
                        case 'Pending':
                            echo "style='background-color:rgb(254, 254, 193);'"; // subtle yellow
                            break;
                        case 'Completed':
                            echo "style='background-color: #cfffdc;'";
                            break;
                        case 'Cancelled':
                            echo "style='background-color: #ffdcdc;'";
                            break;
                        case 'Ongoing':
                            echo "style='background-color: #e6ffe6;'";
                            break;
                    }
                ?>
                onclick="location.href='orderhistorydetail.php?hid=<?= $hid ?>'">
                    <td><?= $symbol ?></td>
                    <td><?= $hid ?></td>
                    <td><?= $timeoforder ?></td>
                    <td>&#x20B1; <?= number_format($ordertotal, 2) ?></td>
                    <td><?= $status ?></td>
                    <td><?= $orderdate ?></td>
                    <td><?= $datecompleted ?></td>
                </tr>
        <?php
        }
        $conn->close();
        ?>
        <script>
            function filterTable() {
                var input, filter, table, tr, td, i, txtValue;
                input = document.getElementById("general-search");
                filter = input.value.toUpperCase();
                table = document.getElementById("myTable");
                tr = table.getElementsByTagName("tr");

                let hasRecords = false;
                for (i = 0; i < tr.length; i++) {
                    td = tr[i].getElementsByTagName("td")[0];
                    if (td) {
                        txtValue = td.textContent || td.innerText;
                        if (txtValue.toUpperCase().indexOf(filter) > -1) {
                            tr[i].style.display = "";
                            hasRecords = true;
                        } else {
                            tr[i].style.display = "none";
                        }
                    }
                }
                const noRecords = document.getElementById('no-records');
                if (!hasRecords) {
                    noRecords.style.display = 'block';
                } else {
                    noRecords.style.display = 'none';
                }
            }
        </script>
    </tbody>
</table>
<p id="no-records" style="display: none; text-align: center; font-size: 16px; color: red;">No records found. Please try changing your search criteria.</p>
<div class="pagination-box">
    <ul class="pagination">
        <li class="page-item <?php echo $page == 1 ? 'disabled' : '' ?>">
            <a class="page-link" href="<?= $page == 1 ? '#' : "?page=" . ($page - 1) . "&limit=$limit" ?>">Previous</a>
        </li>

        <?php for ($i = 1; $i <= $totalPages; $i++) { ?>
            <li class="page-item <?php echo $page == $i ? 'active' : '' ?>">
                <a class="page-link" href="?page=<?= $i ?>&limit=<?= $limit ?>"><?= $i ?></a>
            </li>
        <?php } ?>
        <li class="page-item <?php echo $page == $totalPages ? 'disabled' : '' ?>">
            <a class="page-link" href="<?= $page == $totalPages ? '#' : "?page=" . ($page + 1) . "&limit=$limit" ?>">Next</a>
        </li>
    </ul>
</div>


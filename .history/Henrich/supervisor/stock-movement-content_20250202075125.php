<section class="panel">
    <div class="container-fluid">
        <div class="table-header" style="border-left: 8px solid var(--primary-color);">
            <div class="title">
                <span><h2>Stock Movement</h2></span>
                <span style="font-size: 12px;">Display only</span>
            </div>
            <div class="title">
                <span><?php echo date('l, F jS') ?></span>
            </div>
        </div>

        <div class="table-header">
            <!-- Search form -->
            <div>
                <form class="form">
                    <button>
                        <svg width="17" height="16" fill="none" xmlns="http://www.w3.org/2000/svg" role="img" aria-labelledby="search">
                            <path d="M7.667 12.667A5.333 5.333 0 107.667 2a5.333 5.333 0 000 10.667zM14.334 14l-2.9-2.9" stroke="currentColor" stroke-width="1.333" stroke-linecap="round" stroke-linejoin="round"></path>
                        </svg>
                    </button>
                    <input class="input" id="general-search" onkeyup="search()" placeholder="Search the table..." required="" type="text">
                    <button class="reset" type="reset">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </form>
            </div>

            <div style="display: flex; justify-content: space-around; align-items: center; width: 100%;">
                <div class="dataTables_info">
                    Showing <?= $start + 1 ?> to <?= $start + $limit ?> of <?= Page::get('totalRecords') ?> entries
                </div>
                <div class="filter-box">
                    <label for="limit">Show</label>
                    <select id="limit" onchange="location.href='?page=<?= $page ?>&limit=' + this.value">
                        <option value="10" <?php echo $limit == 10 ? 'selected' : '' ?>>10</option>
                        <option value="25" <?php echo $limit == 25 ? 'selected' : '' ?>>25</option>
                        <option value="50" <?php echo $limit == 50 ? 'selected' : '' ?>>50</option>
                        <option value="100" <?php echo $limit == 100 ? 'selected' : '' ?>>100</option>
                        <option value="500" <?php echo $limit == 500 ? 'selected' : '' ?>>500</option>
                    </select>
                    <label for="limit">entries</label>
                </div>
            </div>
        </div>

        <div class="container-fluid" style="overflow-x:scroll;">
            <table class="table" id="myTable">
                <!-- ...existing table header... -->
                <tbody id="table-body">
                    <?php
                    $items = Page::get('items');
                    if ($items->num_rows > 0) {
                        while ($row = $items->fetch_assoc()) {
                    ?>
                            <tr>
                                <td><?= $row['ibdid'] ?></td>
                                <td><?= $row['batchid'] ?></td>
                                <td><?= $row['productcode'] ?></td>
                                <td><?= $row['productname'] ?></td>
                                <td><?= $row['numberofbox'] ?> boxes</td>
                                <td><?= $row['totalpacks'] ?> packs</td>
                                <td><?= $row['totalweight'] ?> kg</td>
                                <td><?= $row['dateencoded'] ?></td>
                            </tr>
                    <?php
                        }
                    } else {
                    ?>
                        <tr><td colspan="8">No records found</td></tr>
                    <?php
                    }
                    ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="container pagination-container">
            <ul class="pagination">
                <li><a href="?page=<?= max(1, $page - 1) ?>&limit=<?= $limit ?>" class="prev">&laquo;</a></li>
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <li><a href="?page=<?= $i ?>&limit=<?= $limit ?>" 
                          class="page <?= $page == $i ? 'active' : '' ?>"><?= $i ?></a></li>
                <?php endfor; ?>
                <li><a href="?page=<?= min($totalPages, $page + 1) ?>&limit=<?= $limit ?>" 
                      class="next">&raquo;</a></li>
            </ul>
        </div>
    </div>
</section>

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
                    <!-- ...existing search form code... -->
                </form>
            </div>

            <div style="display: flex; justify-content: space-around; align-items: center; width: 100%;">
                <div class="dataTables_info">
                    Showing <?= $start + 1 ?> to <?= $start + $limit ?> of <?= Page::get('totalRecords') ?> entries
                </div>
                <div class="filter-box">
                    <!-- ...existing filter box code... -->
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
                            // ...existing row rendering code...
                        }
                    }
                    ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="container pagination-container">
            <!-- ...existing pagination code... -->
        </div>
    </div>
</section>

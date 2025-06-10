<div class="table-section animate-fade-in delay-3">
    <div class="table-container theme-aware">
        <div class="table-header">
            <div class="title">
                <h2>Stock Activity Log</h2>
                <span style="font-size: 12px;">Encoded by Batch</span>
            </div>
            <div class="title">
                <span><?php echo date('l, F jS') ?></span>
            </div>
        </div>

        <div class="container-fluid" style="overflow-x: auto;">
            <table class="table" id="myTable">
                <thead>
                    <tr>
                        <th>Batch ID</th>
                        <th>Date of Arrival</th>
                        <th>Date Encoded</th>
                        <th>Encoder</th>
                        <th>Description [productcode (packs)]</th>
                        <th>Total Number Of Boxes</th>
                        <th>Overall Total Weight (kg)</th>
                    </tr>
                </thead>
                <thead>
                    <?php include 'activity-table-filters.php'; ?>
                </thead>
                <tbody>
                    <?php include 'activity-table-rows.php'; ?>
                </tbody>
            </table>
        </div>

        <?php include 'activity-table-pagination.php'; ?>
    </div>
</div>

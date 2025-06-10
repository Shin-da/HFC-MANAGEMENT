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
                        <th>Description [productcode (pieces)]</th>
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

<style>
/* Add these styles to match the theme */
.theme-table {
    background: var(--bg-white);
    border: 1px solid var(--sage-200);
    border-radius: 8px;
    overflow: hidden;
}

.theme-table thead th {
    background: var(--forest-primary);
    color: var(--bg-white);
    font-weight: 500;
    padding: 1rem;
    text-align: left;
}

.theme-table tbody td {
    padding: 1rem;
    border-bottom: 1px solid var(--sage-100);
    color: var(--text-primary);
}

.theme-table tbody tr:hover {
    background: var(--sage-50);
}

.status-badge {
    padding: 0.5rem 1rem;
    border-radius: 2rem;
    font-size: 0.875rem;
    font-weight: 500;
    text-align: center;
    display: inline-block;
}

.status-danger {
    background: var(--rust-light);
    color: var(--text-light);
}

.status-warning {
    background: var(--accent-warning);
    color: var(--text-dark);
}

.status-success {
    background: var(--forest-light);
    color: var(--text-light);
}

.btn-icon {
    padding: 0.5rem;
    border-radius: 0.5rem;
    border: none;
    cursor: pointer;
    transition: all 0.2s ease;
    color: var(--bg-white);
}

.btn-info {
    background: var(--forest-primary);
}

.btn-warning {
    background: var(--accent-warning);
}

.themed-select {
    padding: 0.5rem;
    border: 1px solid var(--sage-200);
    border-radius: 0.5rem;
    background: var(--bg-white);
    color: var(--text-primary);
}

.pagination-link {
    padding: 0.5rem 1rem;
    border: 1px solid var(--sage-200);
    color: var(--text-primary);
    background: var(--bg-white);
    border-radius: 0.5rem;
    transition: all 0.2s ease;
}

.pagination-link:hover,
.pagination-link.active {
    background: var(--forest-primary);
    color: var(--bg-white);
    border-color: var(--forest-primary);
}

.pagination-link.disabled {
    opacity: 0.5;
    pointer-events: none;
}

.text-secondary {
    color: var(--text-secondary);
}

.text-muted {
    color: var(--sage-400);
}
</style>
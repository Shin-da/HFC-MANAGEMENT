<div class="table-container">
    <table id="activityTable" class="display activity-table" style="width:100%">
        <thead>
            <tr>
                <th>Date & Time</th>
                <th>Product Code</th>
                <th>Product Name</th>
                <th>Activity Type</th>
                <th>Quantity</th>
                <th>User</th>
                <th>Details</th>
            </tr>
        </thead>
        <tbody>
            <!-- Data will be populated by DataTables -->
        </tbody>
    </table>
</div>

<script>
$(document).ready(function() {
    $('#activityTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '../api/get_activity_logs.php',
            type: 'POST',
            data: function(d) {
                d.startDate = $('#startDate').val();
                d.endDate = $('#endDate').val();
                d.activityType = $('#activityType').val();
            }
        },
        columns: [
            { 
                data: 'dateencoded',
                render: function(data) {
                    return moment(data).format('MMM D, YYYY HH:mm');
                }
            },
            { data: 'productcode' },
            { data: 'productname' },
            { 
                data: 'movement_type',
                render: function(data) {
                    return `<span class="activity-status ${data.toLowerCase()}">${data}</span>`;
                }
            },
            { 
                data: 'quantity',
                render: function(data, type, row) {
                    return `${number_format(data)} ${row.unit}`;
                }
            },
            { data: 'encoder' },
            {
                data: null,
                render: function(data) {
                    return `<button class="btn-icon" onclick="viewActivityDetails('${data.id}')">
                        <i class='bx bx-info-circle'></i>
                    </button>`;
                }
            }
        ],
        order: [[0, 'desc']],
        pageLength: 25,
        responsive: true
    });
});
</script>

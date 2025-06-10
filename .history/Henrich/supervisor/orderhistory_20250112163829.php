<?php
require '../reusable/redirect404.php';
require '../session/session.php';
require '../database/dbconnect.php';

$current_page = basename($_SERVER['PHP_SELF'], '.php');
?>
<!DOCTYPE html>
<html>

<head>
    <title>Transactions</title>
    <?php require '../reusable/header.php'; ?>
    <?php require 'sweetalert.php'; ?>
    <link rel="stylesheet" type="text/css" href="../resources/css/table.css">
    <style>
        .filters {
            display: flex;
            justify-content: space-between;
            margin-bottom: 1rem;
            width: 100%;
        }

        .filters>div button {
            border: none;
            border-radius: 5px;
            padding: 5px 10px;
            cursor: pointer;
        }

        .filters>div button:hover {}

        .filters>div button:focus {
            outline: none;
        }

        .filters>div button:active {}

        thead {
            /* Add styles for thead here */
        }

        tbody {
            /* Add styles for tbody here */
        }

        .Pending {
            background-color: var(--orange-color);
            color: white;
            border: 1px solid #ebccd1;
        }

        .Completed {
            background-color: var(--blue-color);
            color: white;
            border: 1px solid #d6e9c6;
        }

        .Cancelled {
            background-color: var(--accent-color);
            color: white;
            border: 1px solid #a94442;
        }

        .Ongoing {
            background-color: #87ceeb;
            color: white;
            border: 1px solid #87ceeb;
        }

        .icons {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 10px;
        }

        .icons a {
            display: flex;
            align-items: center;
            font-size: 1.2em;
            color: var(--text-color);
            transition: color 0.3s;
        }

        .icons a:hover {
            color: var(--accent-color);
        }

        .card-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-direction: column;
            background-color: #f8f9fa;
            padding: 10px;
            border-top: 1px solid #dee2e6;
            border-radius: 0 0 5px 5px;
        }
    </style>

</head>
                        <div class="filters">
                            <div style="display: flex; justify-content: center; align-items: center; gap: 10px;">
                                <div>
                                    <label for="start-date">Start Date:</label>
                                    <input type="date" id="start-date" onchange="filterByDate()">
                                    <label for="end-date">End Date:</label>
                                    <input type="date" id="end-date" onchange="filterByDate()">
                                </div>
                                <div>
                                    <form class="form">
                                        <button>
                                            <svg width="17" height="16" fill="none" xmlns="http://www.w3.org/2000/svg" role="img" aria-labelledby="search">
                                                <path d="M7.667 12.667A5.333 5.333 0 107.667 2a5.333 5.333 0 000 10.667zM14.334 14l-2.9-2.9" stroke="currentColor" stroke-width="1.333" stroke-linecap="round" stroke-linejoin="round"></path>
                                            </svg>
                                        </button>
                                        <input class="input" id="general-search" onkeyup="filterTable()" placeholder="Search the table..." required="" type="text">
                                        <button class="reset" type="reset">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                                <div>
                                    <button class="status-button All" onclick="filterByStatus('All')">All</button>
                                    <button class="status-button Pending" onclick="filterByStatus('Pending')">Pending</button>
                                    <button class="status-button Ongoing" onclick="filterByStatus('Ongoing')">Ongoing</button>
                                    <button class="status-button Completed" onclick="filterByStatus('Completed')">Completed</button>
                                    <button class="status-button Cancelled" onclick="filterByStatus('Cancelled')">Cancelled</button>
                                </div>
                            </div>
                        </div>

                        <script>
                            function filterByDate() {
                                const startDate = document.getElementById('start-date').value;
                                const endDate = document.getElementById('end-date').value;
                                const rows = document.querySelectorAll('#myTable tbody tr');

                                let hasRecords = false;
                                rows.forEach(row => {
                                    const orderDate = row.querySelector('td:nth-child(5)').textContent; // assuming order date is in the 5th column
                                    if ((startDate && orderDate < startDate) || (endDate && orderDate > endDate)) {
                                        row.style.display = 'none';
                                    } else {
                                        row.style.display = 'table-row';
                                        hasRecords = true;
                                    }
                                });

                                const noRecords = document.getElementById('no-records');
                                if (!hasRecords) {
                                    noRecords.style.display = 'block';
                                } else {
                                    noRecords.style.display = 'none';
                                }
                            }

                            function filterByStatus(status) {
                                const rows = document.querySelectorAll('#myTable tbody tr');

                                let hasRecords = false;
                                rows.forEach(row => {
                                    const orderStatus = row.querySelector('td:nth-child(4)').textContent; // assuming order status is in the 4th column
                                    if (status === 'All' || orderStatus === status) {
                                        row.style.display = 'table-row';
                                        hasRecords = true;
                                    } else {
                                        row.style.display = 'none';
                                    }
                                });

                                const noRecords = document.getElementById('no-records');
                                if (!hasRecords) {
                                    noRecords.style.display = 'block';
                                } else {
                                    noRecords.style.display = 'none';
                                }
                            }

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
<?php require '../reusable/footer.php'; ?>


</html>

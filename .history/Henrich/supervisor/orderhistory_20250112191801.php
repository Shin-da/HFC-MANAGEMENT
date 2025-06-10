<?php
require '../reusable/redirect404.php';
require '../session/session.php';
require '../database/dbconnect.php';

$current_page = basename($_SERVER['PHP_SELF'], '.php');

if ($_SESSION['usertype'] === 'admin') {
    $actions = [
        'edit' => '<a href="editorder.php?hid=:hid"><i class="bx bx-edit bx-fw"></i></a>',
        'delete' => '<a href="deleteorder.php?hid=:hid" onclick="return confirm(\'Are you sure you want to delete this order?\')"><i class="bx bx-trash bx-fw"></i></a>'
    ];
} else {
    $actions = [];
}
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
            color: black;
            font-weight: bold;
            border: 1px solid #ebccd1;
        }

        .Completed {
            background-color: var(--blue-color);
            color: black;
            font-weight: bold;
            border: 1px solid #d6e9c6;
        }

        .Cancelled {
            background-color: var(--accent-color);
            color: black;
            font-weight: bold;
            border: 1px solid #a94442;
        }

        .Ongoing {
            background-color: #87ceeb;
            color: black;
            font-weight: bold;
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

<body>
    <?php include '../reusable/sidebar.php'; // Sidebar   
    ?>
    <!-- === Orders History === -->
    <section class="panel">
        <?php include '../reusable/navbarNoSearch.html'; // TOP NAVBAR         
        ?>
        <div class="container-fluid">
            <!-- Stock Management -->
            <div class="table-header" style="border-left: 8px solid #fa1;">
                <div class="title">
                    <span>
                        <h2>Customer Orders </h2>
                    </span>
                    <span style="font-size: 12px;"> Customer Orders (Online and Walk-in)</span>
                </div>

                <div class="title">
                    <span><?php echo date('l, F jS'); ?></span>
                </div>
            </div>

            <div class="table-header">
                <div style=" display: flex; justify-content: space-around; align-items: center; width: 100%;">

                    <div class="icons">
                        <a href="javascript:window.history.replaceState(null, '', location.pathname); location.reload()" class="icon-link"><i class="bx bx-refresh"></i></a>
                        <a href="#" class="icon-link" id="export"><i class="bx bx-download"></i></a>
                        <a href="add.customerorder.php" class="icon-link"><i class="bx bx-plus"></i> </a>
                    </div>
                </div>
                <?php
                $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
                $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
                $start = ($page - 1) * $limit;
                $items = $conn->query("SELECT * FROM orderhistory ORDER BY orderdate DESC, timeoforder DESC LIMIT $start, $limit");
                $totalRecords = $conn->query("SELECT COUNT(*) FROM orderhistory")->fetch_row()[0];
                $totalPages = ceil($totalRecords / $limit);
                ?>
                <div style=" display: flex; justify-content: space-between; align-items: center; width: 100%;">
                    <div class="filter-box">
                        <label for="limit">Show</label>
                        <select id="limit" onchange="location.href='?page=<?= $page ?>&limit=' + this.value">
                            <option value="10" <?php echo $limit == 10 ? 'selected' : '' ?>>10</option>
                            <option value="25" <?php echo $limit == 25 ? 'selected' : '' ?>>25</option>
                            <option value="50" <?php echo $limit == 50 ? 'selected' : '' ?>>50</option>
                            <option value="100" <?php echo $limit == 100 ? 'selected' : '' ?>>100</option>
                        </select>
                        <label for="limit">entries</label>
                    </div>
                </div>
            </div>
            <?php
            $result = $conn->query("SELECT * FROM orderhistory ORDER BY orderdate DESC, timeoforder DESC LIMIT $limit OFFSET $start");
            ?>
            <div class="">
                <div class="container filters">
                    <div style="display: flex; justify-content: center; align-items: center; gap: 10px;">
                        <div>
                            <label for="start-date">Start Date:</label>
                            <input type="date" id="start-date" onchange="filterTable()">
                            <label for="end-date">End Date:</label>
                            <input type="date" id="end-date" onchange="filterTable()">
                        </div>
                        <div>
                            <form class="form" onsubmit="filterTable(); return false;">
                                <button>
                                    <svg width="17" height="16" fill="none" xmlns="http://www.w3.org/2000/svg" role="img" aria-labelledby="search">
                                        <path d="M7.667 12.667A5.333 5.333 0 107.667 2a5.333 5.333 0 000 10.667zM14.334 14l-2.9-2.9" stroke="currentColor" stroke-width="1.333" stroke-linecap="round" stroke-linejoin="round"></path>
                                    </svg>
                                </button>
                                <input class="input" id="general-search" placeholder="Search the table..." type="text">
                                <button class="reset" type="reset" onclick="filterTable()">
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
                            <button class="status-button Completed" onclick="filterByStatus('Completed')">


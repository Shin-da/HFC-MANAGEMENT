<?php
require '../reusable/redirect404.php';
require '../session/session.php';
require '../database/dbconnect.php';
$current_page = basename($_SERVER['PHP_SELF'], '.php');
?>

<!DOCTYPE html>
<html>

<head>
    <title>Product List</title>
    <?php require '../reusable/header.php'; ?>
    <link rel="stylesheet" type="text/css" href="../resources/css/table.css">
</head>

<body>

    <?php include_once("../reusable/sidebar.php"); ?>

    <section class=" panel"><!-- === PRODUCT LIST === -->
        <?php include '../reusable/navbarNoSearch.html'; // TOP NAVBAR  
        ?>

        <div class="container-fluid"> <!-- Product List -->
            <div class="table-header">
                <div style=" display: flex; justify-content: space-around; align-items: center; width: 100%;">
/*************  ✨ Codeium Command 🌟  *************/
                    <div class="title">
                        <h2 style="display: inline;">Product List</h2>
                        <span style="font-size: smaller;">(Product Details)</span>
                        <h2>Product List (Product Details)</h2>
                    </div>
/******  6c71ac39-fe25-49bd-b65d-ac688a04f4d2  *******/

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

                <?php // pagination for product table
                    $page = isset($_GET['page']) ? $_GET['page'] : 1;
                    $limit = isset($_GET['limit']) ? $_GET['limit'] : 10;
                    $start = ($page - 1) * $limit;
                    $items = $conn->query("SELECT * FROM productlist  LIMIT $start, $limit");
                    $totalRecords = $conn->query("SELECT COUNT(*) FROM productlist")->fetch_row()[0];
                    $totalPages = ceil($totalRecords / $limit);
                ?>
                <div style=" display: flex; justify-content: space-around; align-items: center; width: 100%;">
                    <div class="dataTables_info" id="example_info" role="status" aria-live="polite">Showing <?= $start + 1 ?> to <?= $start + $limit ?> of <?= $totalRecords ?> entries</div>
                    <div class="filter-box"> <!-- Filter results by number of entries -->
                        <label for="limit">Show</label>
                        <select id="limit" onchange="location.href='?page=<?= $page ?>&limit=' + this.value">
                            <option value="10" <?php echo $limit == 10 ? 'selected' : '' ?>>10</option>
                            <option value="25" <?php echo $limit == 25 ? 'selected' : '' ?>>25</option>
                            <option value="50" <?php echo $limit == 50 ? 'selected' : '' ?>>50</option>
                            <option value="100" <?php echo $limit == 100 ? 'selected' : '' ?>>100</option>
                            <option value="500" <?php echo $limit == 500 ? 'selected' : '' ?>>500</option>
                            <option value="<?= $totalRecords ?>" <?php echo $limit == $totalRecords ? 'selected' : '' ?>>All</option>
                        </select>
                        <label for="limit">entries</label>
                    </div>
                </div>
            </div>
            <?php
                $limit = isset($_GET['limit']) ? $_GET['limit'] : 10;
                $offset = ($page - 1) * $limit;

                $stockManagementTableSQL = "SELECT * FROM productlist ORDER BY productcode ASC LIMIT $limit OFFSET $offset";
                if ($conn->connect_error) {
                    die("Connection failed: " . $conn->connect_error);
                }
                $sql = $stockManagementTableSQL;
                $result = $conn->query($sql);
            ?>
            <div class="">
                <div class="container-fluid" style="overflow-x:Scroll;">
                    <table class="table" id="myTable">
                        <thead>
                            <tr>
                                <th>Product Code</th>
                                <th>Product Name</th>
                                <th>Product Weight</th>
                                <th>Product Price</th>
                                <th>Product Category</th>
                            </tr>
                            <tr class="filter-row">
                                <th><input type="text" placeholder="Search Product Code..." id="productcode-filter" onkeyup="filterTable(document.querySelector('#myTable tbody'), this.value, 0)"></th>
                                <th><input type="text" placeholder="Search Product Name..." id="productname-filter" onkeyup="filterTable(document.querySelector('#myTable tbody'), this.value, 1)"></th>
                                <th><input type="text" placeholder="Search Product Weight..." id="productweight-filter" onkeyup="filterTable(document.querySelector('#myTable tbody'), this.value, 2)"></th>
                                <th><input type="text" placeholder="Search Product Price..." id="productprice-filter" onkeyup="filterTable(document.querySelector('#myTable tbody'), this.value, 3)"></th>
                                <th><input type="text" placeholder="Search Product Category..." id="productcategory-filter" onkeyup="filterTable(document.querySelector('#myTable tbody'), this.value, 4)"></th>
                            </tr>
                        </thead>
                        <tbody id="table-body">
                            <?php
                            if ($result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) {
                                    $productcode = $row['productcode'];
                                    $productname = $row['productname'];
                                    $productweight = $row['productweight'];
                                    $productprice = $row['productprice'];
                                    $productcategory = $row['productcategory'];
                            ?>
                                    <tr>
                                        <td><?= $productcode ?></td>
                                        <td><?= $productname ?></td>
                                        <td><?= $productweight ?></td>
                                        <td><?= $productprice ?></td>
                                        <td><?= $productcategory ?></td>
                                    </tr>
                            <?php
                                }
                            } else {
                                echo "<tr><td colspan='5'>0 results</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
                <div class="container" style="display: flex; justify-content: center; flex-direction: column; align-items: center; ">
                    <ul class="pagination"><!-- Pagination for stock management -->
                        <li><a href="?page=<?= $page - 1 <= 1 ? 1 : $page - 1 ?>" class="prev">&laquo;</a></li>
                        <?php for ($i = 1; $i <= $totalPages; $i++) { ?>
                            <li><a href="?page=<?= $i ?>" class="page <?= $page == $i ? 'active' : '' ?>"><?= $i ?></a></li>
                        <?php } ?>
                        <li><a href="?page=<?= $page + 1 > $totalPages ? $totalPages : $page + 1 ?>" class="next">&raquo;</a></li>
                    </ul>

                </div>
            </div>

        </div>
    </section>
</body>

<script src="../resources/js/table.js"></script>
<?php include_once("../reusable/footer.php"); ?>

</html>
<?php // Context from Code Snippet admin/admin.php:(83-88)
// admin/admin.php
// <?php
// } else {
//      header("Location: admin.php");
//      exit();
// }
// ?>

<?php

require '../reusable/redirect404.php';
require '../session/session.php';
require '../database/dbconnect.php';

if (isset($_SESSION['uid']) && isset($_SESSION['role'])) {
    if ($_SESSION['role'] == 'Manager') {   

        $sql = "SELECT * FROM inventory";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $onhand[] = $row['onhand'];
                $productcode[] = $row['productcode'];
            }
        }
        


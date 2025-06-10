<?php 
    require '../database/dbconnect.php';
    require '../session/session.php';
    require '../session/manager_session.php';
    require '../reusable/redirect404.php';

    if (isset($_POST['submit'])) {
        $ProductCode = $_POST['ProductCode'];
        $Product_Name = $_POST['Product_Name'];
        $Quantity = $_POST['Quantity'];
        $Weight = $_POST['Weight'];
        $Price = $_POST['Price'];
?>
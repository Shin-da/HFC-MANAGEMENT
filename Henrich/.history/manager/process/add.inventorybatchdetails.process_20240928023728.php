<!-- add.inventorybatchdetails.process -->
<?php
require '/xampp/htdocs/HenrichProto/database/dbconnect.php';


Fatal error: Uncaught mysqli_sql_exception: Cannot add or update a child row: a foreign key constraint fails (`dbhenrichfoodcorps`.`inventorybatchdetails`, CONSTRAINT `inventorybatchdetails_ibfk_1` FOREIGN KEY (`batchid`) REFERENCES `inventoryhistory` (`batchid`)) in C:\xampp\htdocs\HenrichProto\manager\process\add.inventorybatchdetails.process.php:22 Stack trace: #0 C:\xampp\htdocs\HenrichProto\manager\process\add.inventorybatchdetails.process.php(22): mysqli_stmt->execute() #1 {main} thrown in C:\xampp\htdocs\HenrichProto\manager\process\add.inventorybatchdetails.process.php on line 22
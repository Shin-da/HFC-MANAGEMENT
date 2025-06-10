<!-- add.inventorybatchdetails.process -->
<?php
require '/xampp/htdocs/HenrichProto/database/dbconnect.php';


Fatal error: Uncaught mysqli_sql_exception: Cannot add or update a child row: a foreign key constraint fails (`dbhenrichfoodcorps`.`inventorybatchdetails`, CONSTRAINT `inventorybatchdetails_ibfk_1` FOREIGN KEY (`batchid`) REFERENCES `inventoryhistory` (`batchid`)) 
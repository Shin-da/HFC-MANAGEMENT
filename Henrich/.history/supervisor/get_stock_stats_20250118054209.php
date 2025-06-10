<?php
require '../database/dbconnect.php';

$statsQuery = $conn->query("SELECT 
    COUNT(*) as total_records,
    SUM(numberofbox) as total_boxes,
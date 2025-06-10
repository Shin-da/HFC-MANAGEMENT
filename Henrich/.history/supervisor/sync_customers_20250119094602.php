<?php
require '../database/dbconnect.php';

try {
    // Start transaction
    $conn->begin_transaction();

    // Get the maximum customerid from customerdetails
    $max_id_query = "SELECT COALESCE(MAX(customerid), 0) as max_id FROM customerdetails";
    $max_id_result = $conn->query($max_id_query);
    $next_id = $max_id_result->fetch_assoc()['max_id'] + 1;

    // Get unique customers from customerorder that don't exist in customerdetails
    $sql = "INSERT INTO customerdetails (customerid, customername, customeraddress, customerphonenumber)
            SELECT 
                @row_number:=@row_number + 1 + $next_id,
                co.customername,
                co.customeraddress,
                co.customerphonenumber
            FROM (SELECT DISTINCT customername, customeraddress, customerphonenumber 
                  FROM customerorder) co
            LEFT JOIN customerdetails cd 
                ON cd.customername = co.customername 
                AND cd.customerphonenumber = co.customerphonenumber
            CROSS JOIN (SELECT @row_number:=0) r
            WHERE cd.customerid IS NULL";
    
    // Execute the query
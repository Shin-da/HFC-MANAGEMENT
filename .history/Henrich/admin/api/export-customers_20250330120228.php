<?php
require_once '../../includes/config.php';
require_once '../access_control.php';

try {
    // Set headers for CSV download
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="customers_' . date('Y-m-d') . '.csv"');

    // Create output stream
    $output = fopen('php://output', 'w');

    // Add CSV headers
    fputcsv($output, [
        'Account ID',
        'Customer Name',
        'Customer Address',
        'Phone Number',
        'Customer ID',
        'Username',
        'Email',
        'Status',
        'Created At'
    ]);

    // Get all customers
    $stmt = $GLOBALS['pdo']->query("SELECT * FROM customeraccount ORDER BY customername ASC");
    $customers = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Write data rows
    foreach ($customers as $customer) {
        fputcsv($output, [
            $customer['accountid'],
            $customer['customername'],
            $customer['customeraddress'],
            $customer['customerphonenumber'],
            $customer['customerid'],
            $customer['username'],
            $customer['useremail'],
            $customer['status'],
            $customer['created_at'] ?? date('Y-m-d H:i:s')
        ]);
    }

    // Close the output stream
    fclose($output);
} catch (Exception $e) {
    // If there's an error, redirect back with error message
    header("Location: ../customeraccount.php?error=" . urlencode($e->getMessage()));
    exit();
} 
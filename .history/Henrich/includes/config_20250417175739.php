<?php
// Prevent multiple inclusions
if (!defined('CONFIG_INCLUDED')) {
    define('CONFIG_INCLUDED', true);

    // Constants
    define('DB_HOST', 'localhost');
    define('DB_USER', 'root');
    define('DB_PASSWORD', '');
    define('DB_NAME', 'dbhenrichfoodcorps');
    define('BASE_URL', '/HFC MANAGEMENT/Henrich/');

    // --- Simplified PDO Initialization ---
    try {
        $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
        $pdo = new PDO($dsn, DB_USER, DB_PASSWORD, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]);
        // Assign to global scope immediately
        $GLOBALS['pdo'] = $pdo; 
        
    } catch (PDOException $e) {
        error_log("PDO Connection Error in config.php: " . $e->getMessage());
        die("Database connection failed. Please check logs."); // More informative error
    }
    // --- End Simplified PDO Initialization ---

    // --- Optional: MySQLi Initialization (if still needed elsewhere) ---
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
    if ($conn->connect_error) {
        error_log("MySQLi Connection Error: " . $conn->connect_error);
        // Don't die here if PDO is the primary connection
        // die("Database connection failed"); 
    } else {
        $conn->set_charset("utf8mb4");
    }
    // --- End MySQLi Initialization ---

    // Start session if not already started
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    // Ensure BASE_URL ends with trailing slash
    // if (!defined('BASE_URL')) { // This check might be redundant now
    //     $baseUrl = '/HFC MANAGEMENT/Henrich/';
    //     define('BASE_URL', $baseUrl);
    // }

    // Add API URL constant
    if (!defined('API_URL')) {
        define('API_URL', BASE_URL . 'api/');
    }

    // Only define constants if not already defined
    if (!defined('ROOT_PATH')) {
        define('ROOT_PATH', dirname(__DIR__));
    }

    if (!defined('SITE_NAME')) {
        define('SITE_NAME', 'HFC Management');
    }

    if (!defined('COMPANY_NAME')) {
        define('COMPANY_NAME', 'Henrich Food Corps');
    }

    if (!defined('ADMIN_EMAIL')) {
        define('ADMIN_EMAIL', 'admin@example.com');
    }

    if (!defined('INCLUDE_PATH')) {
        define('INCLUDE_PATH', ROOT_PATH . '/includes/');
    }

    if (!defined('ASSETS_PATH')) {
        define('ASSETS_PATH', ROOT_PATH . '/assets/');
    }

    if (!defined('UPLOAD_PATH')) {
        define('UPLOAD_PATH', ROOT_PATH . '/uploads/');
    }

    // Removed the Database class and extra initializations

    // Set timezone if not already set
    if (date_default_timezone_get() !== 'Asia/Manila') {
        date_default_timezone_set('Asia/Manila');
    }
}
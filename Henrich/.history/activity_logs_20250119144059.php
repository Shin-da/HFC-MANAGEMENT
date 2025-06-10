<?php
require_once 'includes/db_connection.php';
require_once 'includes/session.php';

// Filtering options
$filter_user = isset($_GET['user']) ? $_GET['user'] : '';
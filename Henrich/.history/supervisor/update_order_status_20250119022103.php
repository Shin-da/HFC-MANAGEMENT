<?php
require '../session/session.php';
require '../database/dbconnect.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$orderId = $data['orderId'] ?? null;
$newStatus = $data['status'] ?? null;

if (!$orderId || !$newStatus) {
    echo json_encode(['success' => false, 'message' => 'Missing required parameters']);
    exit;
}

try {
    $conn->begin_transaction();

    // Prepare the base query
    $query = "UPDATE customerorder SET 
              status = ?, 
              datemodified = NOW()";

    // Add datecompleted field if status is being set to Completed
    if ($newStatus === 'Completed') {
        $query .= ", datecompleted = NOW()";
$stmt->close();
$conn->close();

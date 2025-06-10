<?php
require '../includes/session.php';
require '../includes/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $conn->begin_transaction();

        // Generate reference number
        $ref_number = 'MEK-' . date('Ymd') . '-' . rand(1000, 9999);
        
        // Create main order
        $stmt = $conn->prepare("INSERT INTO mekeni_orders (order_date, reference_number, created_by) VALUES (NOW(), ?, ?)");
        $stmt->bind_param("si", $ref_number, $_SESSION['user_id']);
        $stmt->execute();
        $order_id = $conn->insert_id;

        // Process order items
        $products = $_POST['product_id'];
        $quantities = $_POST['quantity'];
        $total_amount = 0;

        $stmt = $conn->prepare("INSERT INTO mekeni_order_items (order_id, product_id, quantity, unit_price) VALUES (?, ?, ?, ?)");
        
        foreach($products as $key => $product_id) {
            // Get product price
            $price_query = $conn->query("SELECT price FROM products WHERE id = $product_id");
            $unit_price = $price_query->fetch_assoc()['price'];
            
            $quantity = $quantities[$key];
            $stmt->bind_param("iiid", $order_id, $product_id, $quantity, $unit_price);
            $stmt->execute();
            
            $total_amount += ($unit_price * $quantity);
        }

        // Update total amount
        $conn->query("UPDATE mekeni_orders SET total_amount = $total_amount WHERE order_id = $order_id");

        $conn->commit();
        echo json_encode(['status' => 'success', 'message' => 'Order created successfully', 'order_id' => $order_id]);
    } catch (Exception $e) {
        $conn->rollback();
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
    exit;
}

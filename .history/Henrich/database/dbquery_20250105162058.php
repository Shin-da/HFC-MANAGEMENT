<?php
// database/dbquery.php

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $body = file_get_contents('php://input');
  $data = json_decode($body, true);
  
  if ($data && isset($data['query'])) {
    $query = $data['query'];
    $params = isset($data['params']) ? $data['params'] : array();
    
    try {
      $conn = new mysqli("localhost", "root", "", "dbHenrichFoodCorps");
      if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
      }
      
      $stmt = $conn->prepare($query);
      if ($stmt === false) {
        throw new Exception("Prepare failed: " . $conn->error);
      }
      
      if (count($params) > 0) {
        $types = str_repeat('s', count($params));
        $stmt->bind_param($types, ...$params);
      }
      
      $stmt->execute();
      
      if (strpos($query, 'INSERT') !== false) {
        $response = array("affected_rows" => $conn->affected_rows);
      } elseif (strpos($query, 'DELETE') !== false) {
        $response = array("affected_rows" => $conn->affected_rows);
      } else {
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
          $data = array();
          while ($row = $result->fetch_assoc()) {
            $data[] = $row;
          }
          $response = $data;
        } else {
          $response = array();
        }
      }
      
      $stmt->close();
      $conn->close();
      
      header('Content-Type: application/json');
      echo json_encode($response);
    } catch (Exception $e) {
      header('Content-Type: application/json');
      echo json_encode(array("error" => $e->getMessage()));
    }
  } else {
    header('Content-Type: application/json');
    echo json_encode(array("error" => "Invalid request"));
  }
}

<?php
require 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $CAS_number = $_POST['CAS_number'];
    $description = $_POST['description'];
    $quantity = $_POST['quantity'];
    
    $imagePath = ''; // Handle file upload if necessary

    $collection = $database->products;
    $insertResult = $collection->insertOne([
        'name' => $name,
        'CAS_number' => $CAS_number,
        'description' => $description,
        'quantity' => $quantity,
        'image' => $imagePath
    ]);

    if ($insertResult->getInsertedCount() > 0) {
        echo json_encode(['success' => 'Product added successfully']);
    } else {
        echo json_encode(['error' => 'Failed to add product']);
    }
}
?>

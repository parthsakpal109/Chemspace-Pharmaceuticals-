<?php
header('Content-Type: application/json');
$conn = new mysqli("localhost", "root", "", "pharma_db");

if ($conn->connect_error) {
    die(json_encode(["error" => "Database connection failed: " . $conn->connect_error]));
}

// Ensure product ID is received
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die(json_encode(["error" => "Product ID is required."]));
}

$product_id = intval($_GET['id']); // Sanitize input

// Prepare delete statement
$stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
$stmt->bind_param("i", $product_id);
if ($stmt->execute()) {
    echo json_encode(["success" => "Product deleted successfully."]);
} else {
    echo json_encode(["error" => "Failed to delete product."]);
}

$stmt->close();
$conn->close();
?>

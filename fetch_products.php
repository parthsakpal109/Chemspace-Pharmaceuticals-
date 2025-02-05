<?php
header('Content-Type: application/json');

// Connect to MySQL
$conn = new mysqli("localhost", "root", "", "pharma_db");

if ($conn->connect_error) {
    die(json_encode(["error" => "Database connection failed: " . $conn->connect_error]));
}

// Fetch products (without requiring 'image' column)
$sql = "SELECT id, name, cas_number, formula, description FROM products";
$result = $conn->query($sql);

$products = [];
while ($row = $result->fetch_assoc()) {
    // Assign a default image if the image column does not exist
    $row['image'] = "default.png"; // Placeholder image (optional)
    $products[] = $row;
}

// Check if products exist
if (empty($products)) {
    die(json_encode(["error" => "No products found in the database."]));
}

echo json_encode($products);
$conn->close();
?>

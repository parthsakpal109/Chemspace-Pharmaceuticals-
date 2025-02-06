<?php
header('Content-Type: application/json');

// Connect to MySQL
$conn = new mysqli("localhost", "root", "", "pharma_db");

if ($conn->connect_error) {
    die(json_encode(["error" => "Database connection failed: " . $conn->connect_error]));
}

// Get search query from frontend
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

error_log("Search Query Received: " . $search); // Debugging log

$sql = "SELECT * FROM products";
if (!empty($search)) {
    // Search by product name, CAS number, or category
    $sql .= " WHERE name LIKE ? OR cas_number LIKE ? OR category_id LIKE ?";
}

$stmt = $conn->prepare($sql);
if (!empty($search)) {
    $search_param = "%{$search}%";
    $stmt->bind_param("sss", $search_param, $search_param, $search_param);
}
$stmt->execute();
$result = $stmt->get_result();
$products = [];

while ($row = $result->fetch_assoc()) {
    $products[] = $row;
}


/*
// Fetch products (without requiring 'image' column)
$sql = "SELECT id, name, cas_number, formula, description FROM products";
$result = $conn->query($sql);

$products = [];
while ($row = $result->fetch_assoc()) {
    // Assign a default image if the image column does not exist
    $row['image'] = "default.png"; // Placeholder image (optional)
    $products[] = $row;
}
*/
// Check if products exist
if (empty($products)) {
    die(json_encode(["error" => "No products found in the database."]));
}

echo json_encode($products, JSON_PRETTY_PRINT);
exit;
//$conn->close();
?>

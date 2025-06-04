<?php
header('Content-Type: application/json');

// Connect to MySQL
$conn = new mysqli("localhost", "root", "", "pharma_db");

if ($conn->connect_error) {
    die(json_encode(["error" => "Database connection failed: " . $conn->connect_error]));
}

// Get filters from URL
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$category = isset($_GET['category']) ? trim($_GET['category']) : '';

// Debug logging
error_log("Search term: " . $search);
error_log("Category filter: " . $category);

// Base query
$sql = "SELECT * FROM products";
$conditions = [];
$params = [];
$types = "";

// Add search condition
if (!empty($search)) {
    $conditions[] = "(name LIKE ? OR cas_number LIKE ?)";
    $search_param = "%$search%";
    $params[] = $search_param;
    $params[] = $search_param;
    $types .= "ss";
}

// Add category condition - handle both ID and name
if (!empty($category)) {
    if (is_numeric($category)) {
        // Category is an ID
        $conditions[] = "category_id = ?";
        $params[] = intval($category);
        $types .= "i";
        error_log("Filtering by category_id: " . intval($category));
    } else {
        // Category is a name
        $conditions[] = "category_name = ?";
        $params[] = $category;
        $types .= "s";
        error_log("Filtering by category_name: " . $category);
    }
}

// Combine conditions
if (!empty($conditions)) {
    $sql .= " WHERE " . implode(" AND ", $conditions);
}

$sql .= " ORDER BY name";

error_log("Final SQL: " . $sql);

// Prepare statement
$stmt = $conn->prepare($sql);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();
$products = [];

while ($row = $result->fetch_assoc()) {
    if (!isset($row['image']) || empty($row['image'])) {
        $row['image'] = "default.png";
    }
    $products[] = $row;
}

error_log("Found " . count($products) . " products");

echo json_encode($products, JSON_PRETTY_PRINT);
$stmt->close();
$conn->close();
?>

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

// Add category condition
if (!empty($category)) {
    $conditions[] = "category = ?";
    $params[] = $category;
    $types .= "s";
}

// Combine conditions
if (!empty($conditions)) {
    $sql .= " WHERE " . implode(" AND ", $conditions);
}

// Prepare statement
$stmt = $conn->prepare($sql);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();
$products = [];

while ($row = $result->fetch_assoc()) {
    // Ensure image column exists and handle NULL values
    if (!isset($row['image']) || empty($row['image'])) {
        $row['image'] = "default.png";
    }
    $products[] = $row;
}

// Output
if (empty($products)) {
    echo json_encode([]);
    exit;
}

echo json_encode($products, JSON_PRETTY_PRINT);
exit;

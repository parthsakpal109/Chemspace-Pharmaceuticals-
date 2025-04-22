<?php
header('Content-Type: application/json');

// Connect to MySQL
$conn = new mysqli("localhost", "root", "", "pharma_db");

if ($conn->connect_error) {
    die(json_encode(["error" => "Database connection failed: " . $conn->connect_error]));
}

// Check if a specific product ID is requested
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $product_id = intval($_GET['id']);
    
    // Fetch single product
    $stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $product = $result->fetch_assoc();
        
        // Handle NULL image
        if (!isset($product['image']) || empty($product['image'])) {
            $product['image'] = "default.png";
        }
        
        echo json_encode($product);
    } else {
        echo json_encode(["error" => "Product not found"]);
    }
    
    $stmt->close();
    $conn->close();
    exit;
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
    $conditions[] = "category_id = ?";
    $params[] = $category;
    $types .= "i";
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
$stmt->close();
$conn->close();
exit;

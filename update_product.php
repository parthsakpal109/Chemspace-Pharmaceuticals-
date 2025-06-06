<?php
session_start();

// Check if admin is logged in
if (!isset($_SESSION["admin_id"])) {
    header('Content-Type: application/json');
    echo json_encode(["status" => "error", "message" => "Not authorized"]);
    exit;
}

// Connect to MySQL
$conn = new mysqli("localhost", "root", "", "pharma_db");

if ($conn->connect_error) {
    header('Content-Type: application/json');
    echo json_encode(["status" => "error", "message" => "Database connection failed: " . $conn->connect_error]);
    exit;
}

if ($_SERVER["REQUEST_METHOD"] != "POST") {
    header('Content-Type: application/json');
    echo json_encode(["status" => "error", "message" => "Invalid request method"]);
    exit;
}

// Extract POST data
$product_id = intval($_POST['product_id']);
$name = $_POST['name'] ?? '';
$cas_number = $_POST['cas_number'] ?? '';
$description = $_POST['description'] ?? '';
$category_name = $_POST['category_name'] ?? ''; // NEW: Get category

// Validate required fields
if (empty($product_id) || empty($name) || empty($cas_number) || empty($description)) {
    header('Content-Type: application/json');
    echo json_encode(["status" => "error", "message" => "Missing required fields"]);
    exit;
}

// Get category_id from category_name
$category_id = null;
if (!empty($category_name)) {
    $cat_stmt = $conn->prepare("SELECT id FROM categories WHERE category_name = ?");
    $cat_stmt->bind_param("s", $category_name);
    $cat_stmt->execute();
    $cat_result = $cat_stmt->get_result();
    if ($cat_result->num_rows > 0) {
        $cat_row = $cat_result->fetch_assoc();
        $category_id = $cat_row['id'];
    }
    $cat_stmt->close();
}

// Get current product data
$stmt = $conn->prepare("SELECT image FROM products WHERE id = ?");
$stmt->bind_param("i", $product_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header('Content-Type: application/json');
    echo json_encode(["status" => "error", "message" => "Product not found"]);
    exit;
}

$current_product = $result->fetch_assoc();
$image_path = $current_product['image']; // Default to current image

// Handle Image Upload if a new image is provided
if (isset($_FILES['image']) && $_FILES['image']['size'] > 0) {
    $target_dir = "uploads/";
    $file_extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
    $new_filename = uniqid() . '.' . $file_extension;
    $target_file = $target_dir . $new_filename;
    
    // Check file type
    $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
    if (!in_array(strtolower($file_extension), $allowed_types)) {
        header('Content-Type: application/json');
        echo json_encode(["status" => "error", "message" => "Only JPG, PNG, and GIF files are allowed"]);
        exit;
    }
    
    // Check file size (2MB max)
    if ($_FILES['image']['size'] > 2 * 1024 * 1024) {
        header('Content-Type: application/json');
        echo json_encode(["status" => "error", "message" => "File size must be less than 2MB"]);
        exit;
    }
    
    // Move uploaded file
    if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
        $image_path = $new_filename;
        
        // Delete old image if it exists and is not the default
        if (!empty($current_product['image']) && $current_product['image'] != 'default.png' && file_exists($target_dir . $current_product['image'])) {
            unlink($target_dir . $current_product['image']);
        }
    } else {
        header('Content-Type: application/json');
        echo json_encode(["status" => "error", "message" => "Failed to upload image"]);
        exit;
    }
}

// Update product in database including category
$stmt = $conn->prepare("UPDATE products SET name = ?, cas_number = ?, description = ?, image = ?, category_id = ?, category_name = ? WHERE id = ?");
$stmt->bind_param("ssssisi", $name, $cas_number, $description, $image_path, $category_id, $category_name, $product_id);

if ($stmt->execute()) {
    header('Content-Type: application/json');
    echo json_encode(["status" => "success", "message" => "Product updated successfully"]);
} else {
    header('Content-Type: application/json');
    echo json_encode(["status" => "error", "message" => "Error updating product: " . $stmt->error]);
}

$stmt->close();
$conn->close();
?>

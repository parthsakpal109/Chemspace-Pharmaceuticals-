<?php
session_start();

// Check if admin is logged in
if (!isset($_SESSION["admin_id"])) {
    die("Not authorized");
}

// Connect to MySQL
$conn = new mysqli("localhost", "root", "", "pharma_db");

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

// Extract POST data
$name = $_POST['name'] ?? '';
$cas_number = $_POST['cas_number'] ?? '';
$description = $_POST['description'] ?? '';
$category_name = $_POST['category_name'] ?? '';

if (empty($name) || empty($cas_number) || empty($description) || empty($category_name)) {
    die("Error: Missing required fields.");
}

// Get category_id from category_name
$category_id = null;
$cat_stmt = $conn->prepare("SELECT id FROM categories WHERE category_name = ?");
$cat_stmt->bind_param("s", $category_name);
$cat_stmt->execute();
$cat_result = $cat_stmt->get_result();
if ($cat_result->num_rows > 0) {
    $cat_row = $cat_result->fetch_assoc();
    $category_id = $cat_row['id'];
}
$cat_stmt->close();

// Handle Image Upload
$image_path = "default.png"; // Default image
if (isset($_FILES['image']) && $_FILES['image']['size'] > 0) {
    $target_dir = "uploads/";
    
    // Create uploads directory if it doesn't exist
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0755, true);
    }
    
    $file_extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
    $new_filename = uniqid() . '.' . $file_extension;
    $target_file = $target_dir . $new_filename;
    
    if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
        $image_path = $new_filename;
    } else {
        $image_path = "default.png"; // Fallback if upload fails
    }
}

// Insert into Database with both category_id and category_name
$stmt = $conn->prepare("INSERT INTO products (name, cas_number, description, image, category_id, category_name) VALUES (?, ?, ?, ?, ?, ?)");
$stmt->bind_param("ssssis", $name, $cas_number, $description, $image_path, $category_id, $category_name);

if ($stmt->execute()) {
    echo "✅ Product added successfully!";
} else {
    echo "❌ Error inserting product: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>

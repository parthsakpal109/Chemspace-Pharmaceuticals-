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
$category_id = $_POST['category_id'] ?? null;

if (empty($name) || empty($cas_number) || empty($description)) {
    die("Error: Missing required fields.");
}

// Handle Image Upload
$image_path = "default.png"; // Default image
if (isset($_FILES['image']) && $_FILES['image']['size'] > 0) {
    $target_dir = "uploads/";
    $file_extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
    $new_filename = uniqid() . '.' . $file_extension;
    $target_file = $target_dir . $new_filename;

    if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
        $image_path = $new_filename;
    } else {
        $image_path = "default.png"; // Fallback if upload fails
    }
}

// Insert into Database (all-in-one table)
$stmt = $conn->prepare("INSERT INTO products (name, cas_number, description, image, category_name) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("sssss", $name, $cas_number, $description, $image_path, $category_name);
// Update the INSERT to include category relationship
if ($category_id) {
    // Insert into product_category junction table
    $cat_stmt = $conn->prepare("INSERT INTO product_category (product_id, category_id) VALUES (?, ?)");
    $cat_stmt->bind_param("ii", $product_id, $category_id);
    $cat_stmt->execute();
}
if ($stmt->execute()) {
    echo "✅ Product added successfully!";
} else {
    echo "❌ Error inserting product: " . $stmt->error;
}

$stmt->close();
$conn->close();

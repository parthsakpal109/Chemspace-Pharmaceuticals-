<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Enable Logging to a File
ini_set('log_errors', 1);
ini_set('error_log', 'error_log.txt');

$conn = new mysqli("localhost", "root", "", "pharma_db");

if ($conn->connect_error) {
    error_log("Database Connection Failed: " . $conn->connect_error);
    die("Database connection failed: " . $conn->connect_error);
}

$name = $_POST['name'] ?? '';
$cas_number = $_POST['cas_number'] ?? '';
$description = $_POST['description'] ?? '';
$image = $_FILES['image']['name'] ?? '';

if (empty($name) || empty($cas_number) || empty($description)) {
    error_log("Missing required fields.");
    die("Error: Missing required fields.");
}

$target_dir = "uploads/";
$target_file = $target_dir . basename($image);

if (!empty($image) && !move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
    error_log("File upload failed for: $target_file");
    die("Error: Failed to upload image.");
}

$stmt = $conn->prepare("INSERT INTO products (name, cas_number, description, image) VALUES (?, ?, ?, ?)");
if (!$stmt) {
    error_log("SQL Prepare Failed: " . $conn->error);
    die("Database Error: SQL Prepare Failed.");
}

$stmt->bind_param("ssss", $name, $cas_number, $description, $image);

if ($stmt->execute()) {
    echo "Product added successfully!";
} else {
    error_log("SQL Execution Failed: " . $stmt->error);
    die("Database Error: " . $stmt->error);
}

$stmt->close();
$conn->close();
?>

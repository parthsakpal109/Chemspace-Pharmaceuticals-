<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Enable Logging to a File
ini_set('log_errors', 1);
ini_set('error_log', 'error_log.txt');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $conn = new mysqli("localhost", "root", "", "pharma_db");

    if ($conn->connect_error) {
        die("Database connection failed: " . $conn->connect_error);
    }

    // Extract POST data
    $name = $_POST['name'] ?? '';
    $cas_number = $_POST['cas_number'] ?? '';
    $description = $_POST['description'] ?? '';
    $image = $_FILES['image']['name'] ?? '';

    if (empty($name) || empty($cas_number) || empty($description)) {
        die("Error: Missing required fields.");
    }

     // Handle Image Upload
    $image = $_FILES['image']['name'] ?? '';
    if (!empty($image)) {
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($image);

         // Move uploaded file
        if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
             $image_path = basename($image); // Store only filename in the database
        } else {
             $image_path = "default.png"; // Fallback image if upload fails
        }
    } else {
         $image_path = "default.png"; // Default image
    }
    // Insert into Database
    $stmt = $conn->prepare("INSERT INTO products (name, cas_number, description, image, range_value) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $name, $cas_number, $description, $image, $range);

    if ($stmt->execute()) {
        echo "✅ Product added successfully!";
    } else {
        echo "❌ Error inserting product: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
    ?>

<?php
// Database connection
$host = 'localhost';
$dbname = 'product_db';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    http_response_code(500);
    echo "Database connection failed: " . $e->getMessage();
    exit();
}

// Check if the form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate inputs
    $name = isset($_POST['name']) ? htmlspecialchars(trim($_POST['name'])) : null;
    $CAS_number = isset($_POST['CAS_number']) ? htmlspecialchars(trim($_POST['CAS_number'])) : null;
    $description = isset($_POST['description']) ? htmlspecialchars(trim($_POST['description'])) : null;
    $quantity = isset($_POST['quantity']) ? htmlspecialchars(trim($_POST['quantity'])) : null;

    // Validate image upload
    if (!empty($_FILES['image']['name'])) {
        $targetDir = "uploads/"; // Ensure the uploads directory exists
        $imageName = basename($_FILES['image']['name']);
        $targetFile = $targetDir . $imageName;

        // Move uploaded file to the uploads directory
        if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
            $image = $targetFile; // Save the relative path for the database
        } else {
            echo "Error uploading the image.";
            exit();
        }
    } else {
        echo "Image is required.";
        exit();
    }

    // Ensure all fields are filled out
    if (!$name || !$CAS_number || !$description || !$quantity) {
        http_response_code(400);
        echo "All fields are required.";
        exit();
    }

    try {
        // Insert product into the database
        $query = $pdo->prepare("
            INSERT INTO products (name, CAS_number, description, quantity, image, created_at)
            VALUES (:name, :CAS_number, :description, :quantity, :image, NOW())
        ");
        $query->execute([
            ':name' => $name,
            ':CAS_number' => $CAS_number,
            ':description' => $description,
            ':quantity' => $quantity,
            ':image' => $image
        ]);

        echo "Product added successfully.";
    } catch (PDOException $e) {
        http_response_code(500);
        echo "Failed to add product: " . $e->getMessage();
    }
} else {
    http_response_code(405);
    echo "Invalid request method.";
}
?>

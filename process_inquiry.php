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
    $email = isset($_POST['email']) ? filter_var(trim($_POST['email']), FILTER_VALIDATE_EMAIL) : null;
    $message = isset($_POST['message']) ? htmlspecialchars(trim($_POST['message'])) : null;

    if (!$name || !$email || !$message) {
        http_response_code(400);
        echo "Invalid input. All fields are required, and email must be valid.";
        exit();
    }

    try {
        // Insert inquiry into the database
        $query = $pdo->prepare("
            INSERT INTO inquiries (name, email, message, created_at)
            VALUES (:name, :email, :message, NOW())
        ");
        $query->execute([
            ':name' => $name,
            ':email' => $email,
            ':message' => $message
        ]);

        echo "Thank you for your inquiry, $name. We will get back to you shortly.";
    } catch (PDOException $e) {
        http_response_code(500);
        echo "Failed to save your inquiry: " . $e->getMessage();
    }
} else {
    http_response_code(405);
    echo "Invalid request method.";
}
?>

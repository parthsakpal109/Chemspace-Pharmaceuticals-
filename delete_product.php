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
    echo json_encode(['error' => 'Database connection failed: ' . $e->getMessage()]);
    exit();
}

// Check if a product ID is provided
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $productId = intval($_GET['id']); // Sanitize the input

    try {
        // Fetch the product's image path before deletion
        $query = $pdo->prepare("SELECT image FROM products WHERE id = :id");
        $query->execute([':id' => $productId]);
        $product = $query->fetch(PDO::FETCH_ASSOC);

        if ($product) {
            // Delete the product from the database
            $deleteQuery = $pdo->prepare("DELETE FROM products WHERE id = :id");
            $deleteQuery->execute([':id' => $productId]);

            // Delete the image file from the server
            if (file_exists($product['image'])) {
                unlink($product['image']);
            }

            echo json_encode(['success' => 'Product deleted successfully']);
        } else {
            http_response_code(404);
            echo json_encode(['error' => 'Product not found']);
        }
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to delete product: ' . $e->getMessage()]);
    }
} else {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid product ID']);
}
?>

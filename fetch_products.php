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

// Handle search query if provided
$search = isset($_GET['search']) ? htmlspecialchars(trim($_GET['search'])) : '';

try {
    if ($search) {
        $query = $pdo->prepare("
            SELECT * FROM products 
            WHERE name LIKE :search OR CAS_number LIKE :search
        ");
        $query->execute(['search' => "%$search%"]);
    } else {
        $query = $pdo->query("SELECT * FROM products");
    }

    $products = $query->fetchAll(PDO::FETCH_ASSOC);

    if (empty($products)) {
        echo json_encode([]);
    } else {
        echo json_encode($products);
    }
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to fetch products: ' . $e->getMessage()]);
    exit();
}
?>

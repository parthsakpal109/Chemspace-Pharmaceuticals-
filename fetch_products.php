<?php
require 'db_connection.php';

$collection = $database->products; // Collection Name

$searchQuery = isset($_GET['search']) ? trim($_GET['search']) : '';

$filter = [];
if ($searchQuery) {
    $filter = ['$or' => [
        ['name' => new MongoDB\BSON\Regex($searchQuery, 'i')],
        ['CAS_number' => new MongoDB\BSON\Regex($searchQuery, 'i')]
    ]];
}

$products = $collection->find($filter)->toArray();

// Convert MongoDB ObjectId to string
foreach ($products as &$product) {
    $product['_id'] = (string) $product['_id'];
}

header('Content-Type: application/json');
echo json_encode($products);
?>

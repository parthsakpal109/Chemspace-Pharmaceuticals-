<?php
require 'db_connection.php';

if (isset($_GET['id'])) {
    $id = new MongoDB\BSON\ObjectId($_GET['id']);
    
    $collection = $database->products;
    $deleteResult = $collection->deleteOne(['_id' => $id]);

    if ($deleteResult->getDeletedCount() > 0) {
        echo json_encode(['success' => 'Product deleted successfully']);
    } else {
        echo json_encode(['error' => 'Failed to delete product']);
    }
}
?>

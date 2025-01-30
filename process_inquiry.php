<?php
require 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $message = $_POST['message'];

    $collection = $database->inquiries;
    $insertResult = $collection->insertOne([
        'name' => $name,
        'email' => $email,
        'message' => $message,
        'date' => new MongoDB\BSON\UTCDateTime()
    ]);

    if ($insertResult->getInsertedCount() > 0) {
        echo json_encode(['success' => 'Inquiry submitted successfully']);
    } else {
        echo json_encode(['error' => 'Failed to submit inquiry']);
    }
}
?>

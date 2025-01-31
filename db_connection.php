<?php
require 'vendor/autoload.php'; // Include Composer's autoloader

$uri = ;"mongodb+srv://jarejaydeep:Bcj3H3XKI0pGoImc@cluster0.fxrir.mongodb.net/?retryWrites=true&w=majority&appName=Cluster0"

try {
    $client = new MongoDB\Client($uri);
    $database = $client->selectDatabase('chemspace_db'); // Replace with your database name
    echo "Connected to MongoDB successfully";
} catch (Exception $e) {
    die("Error: " . $e->getMessage());
}
?>

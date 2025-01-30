<?php
require 'vendor/autoload.php';

$uri = "mongodb+srv://jarejaydeep:Bcj3H3XKI0pGoImc@cluster0.fxrir.mongodb.net/?retryWrites=true&w=majority&appName=Cluster0";

try {
    $client = new MongoDB\Client($uri);
    $database = $client->selectDatabase('chemspace_db'); // Change to your database name
    echo "✅ Successfully connected to MongoDB!";
} catch (Exception $e) {
    die("❌ Connection failed: " . $e->getMessage());
}
?>

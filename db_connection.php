<?php
$host = "localhost";
$user = getenv('DB_USER');  // Use environment variables
$pass = getenv('DB_PASS');  // Use environment variables
$dbname = "chemspace_db";

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}
?>

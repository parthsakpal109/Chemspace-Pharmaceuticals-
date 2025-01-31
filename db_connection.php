<?php
$host = "localhost"; // Now using local MySQL on EC2
$user = "chemspace_user"; // User created in MySQL
$pass = "your_secure_password"; // Set during MySQL setup
$dbname = "chemspace_db"; // Your database name

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}
?>

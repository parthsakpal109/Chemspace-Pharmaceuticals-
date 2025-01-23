<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "product_db";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle search parameter
$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';

$sql = "SELECT * FROM products WHERE name LIKE '%$search%' OR CAS_number LIKE '%$search%'";
$result = $conn->query($sql);

$products = array();
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $products[] = $row;
    }
} else {
    // Handle no results or query error
    if (!$result) {
        file_put_contents('error_log.txt', $conn->error . "\n", FILE_APPEND);
    }
}

$conn->close();

header('Content-Type: application/json');
echo json_encode($products);
?>

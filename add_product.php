<?php
require 'db_connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $CAS_number = mysqli_real_escape_string($conn, $_POST['CAS_number']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $quantity = mysqli_real_escape_string($conn, $_POST['quantity']);

    $sql = "INSERT INTO products (name, CAS_number, description, quantity) 
            VALUES ('$name', '$CAS_number', '$description', '$quantity')";

    if ($conn->query($sql) === TRUE) {
        echo "Product added successfully!";
    } else {
        echo "Error: " . $conn->error;
    }
}
?>

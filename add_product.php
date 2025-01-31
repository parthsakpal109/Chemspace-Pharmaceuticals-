<?php
require 'db_connection.php';
require 'vendor/autoload.php'; // AWS SDK for PHP

use Aws\S3\S3Client;
use Aws\Exception\AwsException;

$s3 = new S3Client([
    'region' => 'us-east-1', // Change to your AWS region
    'version' => 'latest',
    'credentials' => [
        'key' => getenv('AWS_ACCESS_KEY'),
        'secret' => getenv('AWS_SECRET_KEY'),
    ]
]);

$bucketName = 'your-s3-bucket-name'; // Replace with your S3 bucket

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $CAS_number = $_POST['CAS_number'];
    $description = $_POST['description'];
    $quantity = $_POST['quantity'];

    // Handle file upload
    if ($_FILES['image']['error'] == 0) {
        $fileName = basename($_FILES['image']['name']);
        $fileTmpPath = $_FILES['image']['tmp_name'];

        try {
            $result = $s3->putObject([
                'Bucket' => $bucketName,
                'Key' => "products/$fileName",
                'SourceFile' => $fileTmpPath,
                'ACL' => 'public-read'
            ]);

            $imageURL = $result['ObjectURL'];

            // Insert product into database
            $sql = "INSERT INTO products (name, CAS_number, description, quantity, image) 
                    VALUES ('$name', '$CAS_number', '$description', '$quantity', '$imageURL')";
            
            if ($conn->query($sql) === TRUE) {
                echo "Product added successfully!";
            } else {
                echo "Error: " . $conn->error;
            }
        } catch (AwsException $e) {
            echo "Error uploading to S3: " . $e->getMessage();
        }
    } else {
        echo "File upload error.";
    }
}
?>

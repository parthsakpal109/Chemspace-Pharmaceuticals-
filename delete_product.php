<?php
require 'db_connection.php';
require 'vendor/autoload.php';

use Aws\S3\S3Client;

$s3 = new S3Client([
    'region' => 'us-east-1',
    'version' => 'latest',
    'credentials' => [
        'key' => getenv('AWS_ACCESS_KEY'),
        'secret' => getenv('AWS_SECRET_KEY'),
    ]
]);

$bucketName = 'your-s3-bucket-name';

if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['id'])) {
    $id = $_GET['id'];

    // Get product image URL before deleting
    $sql = "SELECT image FROM products WHERE id='$id'";
    $result = $conn->query($sql);
    
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $imageURL = $row['image'];
        $imageKey = str_replace("https://$bucketName.s3.amazonaws.com/", '', $imageURL);

        // Delete image from S3
        try {
            $s3->deleteObject([
                'Bucket' => $bucketName,
                'Key' => $imageKey
            ]);
        } catch (Exception $e) {
            echo "Error deleting from S3: " . $e->getMessage();
        }

        // Delete product from database
        $deleteSQL = "DELETE FROM products WHERE id='$id'";
        if ($conn->query($deleteSQL) === TRUE) {
            echo json_encode(["success" => "Product deleted successfully."]);
        } else {
            echo json_encode(["error" => "Failed to delete product."]);
        }
    } else {
        echo json_encode(["error" => "Product not found."]);
    }
}
?>

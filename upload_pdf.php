<?php
// Database connection configuration
$servername = "localhost";
$username = "zancanap";
$password = "@Tutideze15";
$dbname = "argiles";

// Connect to MySQL database
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Absolute path to the upload directory
$uploadDirectory = "/data/www/html/argiles/pdfs/";

// Check if the directory exists
if (!is_dir($uploadDirectory)) {
    die("Upload directory does not exist. Please create it manually: " . $uploadDirectory);
}

// Handle PDF Upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['fileUpload'])) {
    $uploadedFile = $_FILES['fileUpload']['tmp_name'];
    $fileName = $_FILES['fileUpload']['name'];
    $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

    // Validate PDF format
    if ($fileExtension === 'pdf') {
        $destination = $uploadDirectory . $fileName;

        // Move the uploaded file
        if (move_uploaded_file($uploadedFile, $destination)) {
            // Extract class name from the file name (before the first underscore)
            $className = pathinfo($fileName, PATHINFO_FILENAME);

            // Insert file metadata into the database
            $stmt = $conn->prepare("INSERT INTO UploadedPDFs (fileName, className) VALUES (?, ?)");
            $stmt->bind_param("ss", $fileName, $className);

            if ($stmt->execute()) {
                // Redirect to the visualization page
                header("Location: pdf_visualization.php");
                exit();
            } else {
                echo "Failed to insert file metadata into the database.";
            }
        } else {
            echo "Failed to move the uploaded file.";
        }
    } else {
        echo "Invalid file format. Please upload a valid PDF file.";
    }
} else {
    echo "No file uploaded.";
}

// Close the database connection
$conn->close();
?>


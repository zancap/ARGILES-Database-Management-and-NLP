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

// Fetch all uploaded PDFs
$query = "SELECT fileName, className FROM UploadedPDFs ORDER BY uploadDate DESC";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Visualisation des PDFs</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f9f9f9;
            color: #333;
        }
        .container {
            margin-top: 20px;
        }
        .pdf-list a {
            color: #5a7d6e;
            text-decoration: none;
            font-weight: bold;
        }
        .pdf-list a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="text-center">Visualisation des PDFs</h1>
        <?php if ($result && $result->num_rows > 0): ?>
            <ul class="pdf-list">
                <?php while ($row = $result->fetch_assoc()): ?>
                    <li>
                        <h5><?= htmlspecialchars($row['className']) ?></h5>
                        <a href="/argiles/pdfs/<?= htmlspecialchars($row['fileName']) ?>" target="_blank">Voir le PDF</a>
                    </li>
                <?php endwhile; ?>
            </ul>
        <?php else: ?>
            <p class="text-center">Aucun PDF disponible.</p>
        <?php endif; ?>
    </div>
</body>
</html>

<?php
$conn->close();
?>



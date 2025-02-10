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
        /* Base Styling */
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f9f9f9;
            color: #333;
            margin: 0;
            padding: 0;
        }

        /* Sidebar */
        .sidebar {
            width: 250px;
            position: fixed;
            top: 0;
            left: 0;
            height: 100%;
            background-color: #2c3e50;
            color: #fff;
            padding: 20px;
            box-shadow: 2px 0 5px rgba(0,0,0,0.1);
            z-index: 1000;
        }
        .sidebar a {
            color: #fff;
            text-decoration: none;
            display: block;
            margin: 15px 0;
            font-size: 16px;
        }
        .sidebar a:hover {
            text-decoration: underline;
        }
		
		/* Retour à l'accueil button */
		.btn-back {
		  display: inline-block;
		  margin-top: 20px;
		  background-color: #5a7d6e;
		  color: #fff;
		  border: none;
		  padding: 10px 20px;
		  border-radius: 5px;
		  text-decoration: none;
		  transition: background-color 0.3s ease;
		}
		.btn-back:hover {
		  background-color: #4a665a;
		}

        /* Main Content */
        .main-content {
            margin-left: 270px; /* Offset for the sidebar */
            padding: 20px;
            max-width: 900px;
            margin-right: auto;
            margin-left: auto;
        }

        /* Header & Footer Styling */
        header {
            background-color: #5a7d6e;
            color: #fff;
            padding: 30px 40px;
            text-align: center;
            border-radius: 8px;
            margin: 40px auto;
            max-width: 900px;
        }
        footer {
            background-color: #2c3e50;
            color: #fff;
            text-align: center;
            padding: 30px 40px;
            margin: 40px auto;
            max-width: 900px;
            border-radius: 8px;
        }
        footer a {
            color: #88b097;
            text-decoration: none;
        }
        footer a:hover {
            text-decoration: underline;
        }

        /* PDF List */
        .pdf-list {
            list-style: none;
            padding: 0;
        }
        .pdf-item {
            margin-bottom: 20px;
            text-align: center;
        }
        .pdf-item h5 {
            color: #5a7d6e;
        }

        /* Scrollable & Zoomable PDF Viewer */
        .pdf-container {
            width: 100%;
            height: 600px; /* Initial height */
            overflow: auto; /* Enables both vertical & horizontal scrolling */
            position: relative;
            background: #f9f9f9;
        }
        .pdf-viewer {
            width: 100%;
            height: 100%;
            border: none;
            transform-origin: top left;
        }

        /* Zoom Controls */
        .zoom-controls {
            text-align: center;
            margin-bottom: 15px;
        }
        .zoom-btn {
            background-color: #88b097;
            color: #fff;
            border: none;
            padding: 8px 15px;
            border-radius: 5px;
            margin: 0 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        .zoom-btn:hover {
            background-color: #769c86;
        }
    </style>
</head>
<body>

	<!-- Floating Sidebar -->
	<div class="sidebar">
		<h4>Menu</h4>
		<a href="index.html">Accueil</a>
		<a href="functions.html">Explorez les fonctionnalites</a>
		<a href="database_visualization.php">Contenu de la base de données</a>
		<a href="pdf_visualization.php">Visualisation des copies d'élèves originales</a>
		<a href="upload.html">Téléversez des fichiers</a>
		<a href="https://creativecommons.org/licenses/by-nc-sa/4.0/" target="_blank">Licence</a>
	</div>

    <!-- Header -->
    <header>
        <h1>Visualisation des PDFs</h1>
        <p>Consultez les documents scannés directement depuis la plateforme.</p>
		<a href="index.html" class="btn-back">← Retour à l'accueil</a>
    </header>

    <!-- Main Content -->
    <div class="main-content">
        <?php if ($result && $result->num_rows > 0): ?>
            <ul class="pdf-list">
                <?php while ($row = $result->fetch_assoc()): ?>
                    <li class="pdf-item">
                        <h5><?= htmlspecialchars($row['className']) ?></h5>

                        <!-- Zoom Controls -->
                        <div class="zoom-controls">
                            <button class="zoom-btn" onclick="zoomIn('pdf-viewer-<?= htmlspecialchars($row['fileName']) ?>')">🔍 +</button>
                            <button class="zoom-btn" onclick="zoomOut('pdf-viewer-<?= htmlspecialchars($row['fileName']) ?>')">🔍 -</button>
                        </div>

                        <!-- Scrollable & Zoomable PDF Viewer -->
                        <div class="pdf-container">
                            <iframe id="pdf-viewer-<?= htmlspecialchars($row['fileName']) ?>" 
                                    class="pdf-viewer" 
                                    src="/argiles/pdfs/<?= htmlspecialchars($row['fileName']) ?>#toolbar=0" 
                                    allow="fullscreen"></iframe>
                        </div>
                    </li>
                <?php endwhile; ?>
            </ul>
        <?php else: ?>
            <p class="text-center">Aucun PDF disponible.</p>
        <?php endif; ?>
    </div>

    <!-- Footer -->
    <footer>
        <p>© 2025 Projet ARGILES | Université Grenoble Alpes</p>
        <p>Ce site est sous licence <a href="https://creativecommons.org/licenses/by-nc-sa/4.0/" target="_blank">Creative Commons BY-NC-SA 4.0</a>.</p>
        <p>Contactez le directeur du projet : 
            <a href="https://www.univ-grenoble-alpes.fr/thomas-lebarbe-538931.kjsp" target="_blank">Thomas Lebarbé</a>
        </p>
    </footer>

    <!-- JavaScript for Zoom Functionality -->
    <script>
        function zoomIn(id) {
            let pdfViewer = document.getElementById(id);
            let currentScale = parseFloat(pdfViewer.style.transform.replace('scale(', '').replace(')', '')) || 1;
            pdfViewer.style.transform = 'scale(' + (currentScale + 0.1) + ')';
        }

        function zoomOut(id) {
            let pdfViewer = document.getElementById(id);
            let currentScale = parseFloat(pdfViewer.style.transform.replace('scale(', '').replace(')', '')) || 1;
            if (currentScale > 0.5) {
                pdfViewer.style.transform = 'scale(' + (currentScale - 0.1) + ')';
            }
        }
    </script>

</body>
</html>

<?php
$conn->close();
?>





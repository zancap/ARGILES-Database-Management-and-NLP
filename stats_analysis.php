<?php
session_start();

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Ensure correct encoding
header('Content-Type: text/html; charset=utf-8');

// Define XML file paths
$xml_files = [
    "/data/www/html/argiles/xmls/4-G1_4-G2_Sirius.xml",
    "/data/www/html/argiles/xmls/CE1_CE2_CM1_CM2_RomeoJuliette.xml"
];

// Function to extract available titles from XML files
function get_titles($xml_files) {
    $available_titles = [];
    foreach ($xml_files as $file) {
        if (file_exists($file)) {
            $xml = simplexml_load_file($file);
            $title = (string)$xml['sujet'];
            if (!in_array($title, $available_titles)) {
                $available_titles[] = $title;
            }
        }
    }
    return $available_titles;
}

// Retrieve available titles
$available_titles = get_titles($xml_files);

// Initialize result variables
$error_message = "";
$data = "";

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $novel = isset($_POST["novel_title"]) ? escapeshellarg($_POST["novel_title"]) : null;
    $min_id = isset($_POST["min_id"]) ? intval($_POST["min_id"]) : null;
    $max_id = isset($_POST["max_id"]) ? intval($_POST["max_id"]) : null;

    if ($novel === null || $min_id === null || $max_id === null) {
        $error_message = "Erreur : Tous les champs requis (Titre du roman, ID min, ID max) doivent être renseignés.";
    } else {
        // Execute the Python script for statistics
        $scriptPath = escapeshellcmd("/data/www/html/argiles/utils/stats_analysis.py");
        $command = "python3 $scriptPath $novel $min_id $max_id 2>&1";
        $data = shell_exec($command);

        if ($data === null) {
            $error_message = "Erreur : Impossible d'exécuter le script Python.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Statistiques - Analyse des Réponses</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* General Styles */
        body {
            font-family: 'Roboto', sans-serif;
            background: linear-gradient(160deg, #d8a7b1 10%, #2f5d62 90%);
            background-attachment: fixed;
            color: #333;
            margin: 0;
            padding: 0;
            position: relative;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* Sidebar */
        .sidebar {
            width: 250px;
            position: fixed;
            top: 0;
            left: -250px;
            height: 100%;
            background-color: #2c3e50;
            color: #fff;
            padding: 20px;
            transition: left 0.3s ease-in-out;
            text-align: left;
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

        /* Toggle Menu */
        #menuToggle {
            position: fixed;
            top: 15px;
            left: 15px;
            background-color: #5a7d6e;
            color: #fff;
            padding: 10px 15px;
            border-radius: 5px;
            cursor: pointer;
            z-index: 1100;
        }

        /* Header */
        header {
            background-color: #5a7d6e;
            color: #fff;
            padding: 30px 60px;
            border-radius: 12px;
            text-align: center;
            max-width: 95%;
            margin: 20px auto;
        }

        /* Return to Home Button */
        .btn-back {
            display: inline-block;
            margin-top: 20px;
            background-color: #2c3e50;
            color: #fff;
            border: none;
            padding: 12px 20px;
            border-radius: 5px;
            text-decoration: none;
            font-weight: bold;
            font-size: 16px;
            transition: background-color 0.3s ease;
        }
        .btn-back:hover {
            background-color: #1e2b38;
        }

        /* Form Section */
        .form-container {
            background: #fff;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 3px 3px 10px rgba(0, 0, 0, 0.15);
            text-align: center;
            max-width: 600px;
            width: 100%;
            margin: auto;
        }

        /* Results Section */
        .results {
            margin-top: 20px;
            background: #f9f9f9;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 2px 2px 5px rgba(0,0,0,0.1);
            text-align: left;
            max-width: 600px;
            width: 100%;
            overflow-x: auto;
        }

        /* Footer */
		footer {
			background-color: #2c3e50;
			color: #fff;
			text-align: center;
			padding: 20px 180px;  
			border-radius: 12px;
			max-width: 100%;  
			margin: 20px auto; 
			position: relative;
			z-index: 1;
		}

        /* Wave Decoration */
		.wave {
			position: absolute;
			bottom: 0;
			left: 0;
			width: 100%;
			height: 180px;  
			background: url('img/wave.svg') repeat-x;
			opacity: 0.3;
			z-index: 5;  
		}
    </style>
</head>
<body>

    <!-- Menu Toggle -->
    <div id="menuToggle" onclick="toggleSidebar()">☰ Menu</div>

    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <br><br>
        <a href="index.html">Accueil</a>
        <a href="database_visualization.php">Contenu de la base de données</a>
        <a href="pdf_visualization.html">Visualisation des copies d'élèves</a>
        <a href="login.php">Téléversez des fichiers</a>
        <a href="functions.html">Fonctionnalités</a>
        <a href="https://creativecommons.org/licenses/by-nc-sa/4.0/" target="_blank">Licence</a>
    </div>

    <!-- Header -->
    <header>
        <h1>Analyse des Réponses - Statistiques</h1>
        <p>Obtenez une analyse des mots les plus fréquents utilisés par les élèves.</p>
        <a href="index.html" class="btn-back">← Retour à l'accueil</a>
    </header>

    <!-- Form -->
    <div class="form-container">
        <form method="post">
            <label for="novel_title">Titre du roman :</label>
            <select name="novel_title" id="novel_title" class="form-control" required>
                <option value="">Sélectionnez un titre</option>
                <?php foreach ($available_titles as $title) { ?>
                    <option value="<?php echo htmlspecialchars($title); ?>"><?php echo htmlspecialchars($title); ?></option>
                <?php } ?>
            </select>
            <button type="submit" class="btn-submit mt-3">Analyser</button>
        </form>
    </div>

    <div class="wave"></div>

	<footer>
		<p>© 2025 Projet ARGILES | Université Grenoble Alpes</p>
		<p>Licence <a href="https://creativecommons.org/licenses/by-nc-sa/4.0/" target="_blank">CC BY-NC-SA 4.0</a></p>
		<p>Contact : <a href="https://www.univ-grenoble-alpes.fr/thomas-lebarbe-538931.kjsp" target="_blank">Thomas Lebarbé</a></p>
	</footer>

    <script>
        function toggleSidebar() {
            var sidebar = document.getElementById("sidebar");
            sidebar.style.left = sidebar.style.left === "-250px" ? "0" : "-250px";
        }
    </script>

</body>
</html>

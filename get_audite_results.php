<?php
session_start();

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Ensure correct encoding
header('Content-Type: text/html; charset=utf-8');

// Function to parse XML files and extract available IDs and titles
function get_titles_and_ids($xml_files) {
    $available_ids = [];
    $available_titles = [];

    foreach ($xml_files as $file) {
        if (file_exists($file)) {
            $xml = simplexml_load_file($file);
            
            // Extract novel title
            $title = (string)$xml['sujet'];
            if (!in_array($title, $available_titles)) {
                $available_titles[] = $title;
            }

            // Extract IDs
            foreach ($xml->question as $question) {
                foreach ($question->groupe as $groupe) {
                    foreach ($groupe->reponse as $reponse) {
                        $id = (string)$reponse['idAudite'];
                        if (!in_array($id, $available_ids)) {
                            $available_ids[] = $id;
                        }
                    }
                }
            }
        }
    }

    return [$available_titles, $available_ids];
}

// Define paths to XML files
$xml_files = [
    "/data/www/html/argiles/xmls/4-G1_4-G2_Sirius.xml",
    "/data/www/html/argiles/xmls/CE1_CE2_CM1_CM2_RomeoJuliette.xml"
];

// Retrieve available titles and IDs
list($available_titles, $available_ids) = get_titles_and_ids($xml_files);

// Initialize result variables
$error_message = "";
$data = "";
$evocation = $like = $dislike = $expectation = "";

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $audite_id = isset($_POST['audite_id']) ? intval($_POST['audite_id']) : null;
    $novel_title = isset($_POST['novel_title']) ? escapeshellarg($_POST['novel_title']) : null;

    if ($audite_id === null || empty($novel_title)) {
        $error_message = "Erreur: Tous les champs requis (ID du personnage, Titre du roman) doivent être renseignés.";
    } else {
        // Execute the Python script
        $scriptPath = escapeshellcmd("/data/www/html/argiles/utils/get_audite.py");
        $command = "python3 $scriptPath $audite_id $novel_title 2>&1";
        $data = shell_exec($command);

        if ($data === null) {
            $error_message = "Erreur : Impossible d'exécuter le script Python.";
        } else {
            // Process results
            $parts = explode("|", trim($data));
            if (count($parts) === 4) {
                list($evocation, $like, $dislike, $expectation) = array_map('htmlspecialchars', $parts);
            } else {
                $error_message = "Erreur : Réponse du script Python incorrecte.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Analyse des personnages</title>
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
            align-items: center;
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
		
		/* Button */
		.btn-back {
			display: inline-block;
			background-color: #4a665a; /* Darker green */
			color: #fff;
			border: none;
			padding: 12px 20px;
			border-radius: 6px;
			text-decoration: none;
			font-weight: bold;
			font-size: 16px;
			transition: background-color 0.3s ease;
		}

		.btn-back:hover {
			background-color: #3a5347; /* Even darker green on hover */
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

        /* Footer */
        footer {
            background-color: #2c3e50;
            color: #fff;
            text-align: center;
            padding: 30px 60px;
            border-radius: 12px;
            max-width: 95%;
            margin-top: auto;
            position: relative;
            z-index: 10;
        }
        footer a {
            color: #88b097;
            text-decoration: none;
        }
        footer a:hover {
            text-decoration: underline;
        }

        /* Wave */
        .wave {
            position: absolute;
            bottom: 0;
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
    </div>

    <!-- Header -->
    <header>
        <h1>Analyse d'un personnage</h1>
        <p>Obtenez une analyse des perceptions des élèves sur un personnage d'un roman.</p>
        <a href="index.html" class="btn-back">← Retour à l'accueil</a>
    </header>

    <!-- Form -->
    <div class="form-container">
        <form method="post">
            <label for="audite_id">ID du personnage :</label>
            <select name="audite_id" class="form-control" required>
                <option value="">Sélectionnez un ID</option>
                <?php foreach ($available_ids as $id) { echo "<option value='$id'>$id</option>"; } ?>
            </select>

            <label for="novel_title">Titre du roman :</label>
            <select name="novel_title" class="form-control" required>
                <option value="">Sélectionnez un titre</option>
                <?php foreach ($available_titles as $title) { echo "<option value='$title'>$title</option>"; } ?>
            </select>

            <button type="submit" class="btn-submit mt-3">Analyser</button>
        </form>
    </div>

    <div class="wave"></div>

    <!-- Footer -->
    <footer>
        <p>© 2025 Projet ARGILES | Université Grenoble Alpes</p>
		<p>Contact : <a href="https://www.univ-grenoble-alpes.fr/thomas-lebarbe-538931.kjsp" target="_blank">Thomas Lebarbé</a></p>
        <p>Licence <a href="https://creativecommons.org/licenses/by-nc-sa/4.0/" target="_blank">CC BY-NC-SA 4.0</a></p>
    </footer>

    <script>
        function toggleSidebar() {
            var sidebar = document.getElementById("sidebar");
            sidebar.style.left = (sidebar.style.left === "-250px") ? "0" : "-250px";
        }
    </script>

</body>
</html>

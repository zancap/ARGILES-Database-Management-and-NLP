<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-Type: text/html; charset=utf-8');

// Retrieve GET parameters
$novel_title = isset($_GET['novel_title']) ? escapeshellarg($_GET['novel_title']) : null;
$min_id = isset($_GET['min_id']) ? intval($_GET['min_id']) : 100;
$max_id = isset($_GET['max_id']) ? intval($_GET['max_id']) : 200;

$error_message = "";
$data = "";

// If parameters exist, run the Python analysis script
if ($novel_title) {
    $scriptPath = escapeshellcmd("/data/www/html/argiles/utils/stats.py");
    $command = "python3 $scriptPath $novel_title $min_id $max_id 2>&1";
    $data = shell_exec($command);
}

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Analyse Statistique</title>
    <link rel="stylesheet" href="styles/python_style.css">
</head>
<body>

<!-- Sidebar -->
<div id="menuToggle" onclick="toggleSidebar()">☰ Menu</div>
<div class="sidebar" id="sidebar">
	<br><br>
	<a href="index.html">Accueil</a>
    <a href="functions.html">Explorez les fonctionnalités</a>
    <a href="database_visualization.php">Contenu de la base de données</a>
    <a href="pdf_visualization.html">Visualisation des copies d'élèves originales</a>
    <a href="login.php">Téléversez des fichiers</a>
    <a href="get_audite_results.php">Analyses détaillées</a>
</div>

<!-- Header -->
<header>
    <h1>Analyses Statistiques</h1>
    <a href="index.html" class="btn-back">← Retour à l'accueil</a>
</header>

<!-- Results -->
<div class="results-container">
    <h3>Résultats de l'analyse</h3>
    <?php if (!empty($data)): ?>
        <pre><?php echo htmlspecialchars($data); ?></pre>
    <?php else: ?>
        <p>Aucune donnée trouvée ou erreur lors de l'exécution.</p>
    <?php endif; ?>
</div>

<!-- Footer -->
<footer>
    <p>© 2025 Projet ARGILES | Université Grenoble Alpes</p>
	<p>Contactez le directeur du projet : <a href="https://www.univ-grenoble-alpes.fr/thomas-lebarbe-538931.kjsp" target="_blank">Thomas Lebarbé</a> </p>
    <p>Ce site est sous licence <a href="https://creativecommons.org/licenses/by-nc-sa/4.0/" target="_blank">Creative Commons BY-NC-SA 4.0</a>.</p>
	<br>
</footer>

<script>
function toggleSidebar() {
    var sidebar = document.getElementById("sidebar");
    sidebar.style.left = (sidebar.style.left === "-250px") ? "0" : "-250px";
}
</script>

</body>
</html>

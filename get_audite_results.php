<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-Type: text/html; charset=utf-8');

// Correction de l'encodage pour limiter les erreurs
setlocale(LC_CTYPE, "fr.UTF-8");
putenv("PYTHONIOENCODING=utf-8");
putenv("LANG=fr_FR.UTF-8");
putenv("PYTHONUTF8=1");

// Retrieve GET parameters
$audite_id = isset($_GET['audite_id']) ? intval($_GET['audite_id']) : null;
$novel_title = isset($_GET['novel_title']) ? $_GET['novel_title'] : null;

$error_message = "";
$data = "";

// If parameters exist, run the Python analysis script
if ($audite_id && $novel_title) {
    $scriptPath = escapeshellcmd("./utils/get_audite.py");
    $command = "python3 $scriptPath '$audite_id' '$novel_title' 2>&1";
    $data = shell_exec($command);
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Analyse Personnage</title>
    <link rel="stylesheet" href="style/python_style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style/index.css" />
    <link rel="stylesheet" href="style/table.css" />
</head>
<body>

<!-- Header -->
<header>
    <h1>Analyses Détaillées</h1>
    <a href="database_visualization.php" class="btn-back">← Retour à la BDD</a>
</header>

<!-- Results -->
<div class="results-container">
    <h3>Résultats de l'analyse</h3>
    <?php   if (!empty($data)) {
                $val = strtok($data,'|');
                print '<div class="table_header">';
                for ($i = 0; $i<4; $i++) {
                    $type_and_reply = explode(' : ',$val);
                    print '<div class="table_header_col cell_idShow question_'.trim($type_and_reply[0]).'">';
                    print '<div id="type_'.trim($type_and_reply[0]).'" class="table_header_cell_title"><b>'.strtoupper(trim($type_and_reply[0])).'</b></div>';
                    print '<div class="table_cell">'.$type_and_reply[1].'</div>';
                    print '</div>';
                    $val = strtok('|');
                }
                print '</div>';
            }
            else {
                print "<p>Aucune donnée trouvée ou erreur lors de l'exécution.</p>";
            } ?>
</div>

<!-- Footer -->
<footer>
    <p>© 2025 Projet ARGILES | Université Grenoble Alpes</p>
	<p>Contactez le directeur du projet : <a href="https://www.univ-grenoble-alpes.fr/thomas-lebarbe-538931.kjsp" target="_blank">Thomas Lebarbé</a> </p>
    <p>Ce site est sous licence <a href="https://creativecommons.org/licenses/by-nc-sa/4.0/" target="_blank">Creative Commons BY-NC-SA 4.0</a>.</p>
	<br>
</footer>

</body>
</html>

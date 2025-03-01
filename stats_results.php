<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-Type: text/html; charset=utf-8');
$url = $_SERVER['REQUEST_URI'];
$url = explode('/',$url);
$url = implode('/',array_splice($url,0,-1));

// Correction encodage
setlocale(LC_CTYPE, "fr.UTF-8");
putenv("PYTHONIOENCODING=utf-8");
putenv("LANG=fr.UTF-8");

// Retrieve GET parameters
$arguments = isset($_GET['arguments']) ? explode('AND',trim(escapeshellarg($_GET['arguments']),"'")) : '';

$error_message = "";
$data = "";

// If parameters exist, run the Python analysis script
if ($arguments != '') {
    $scriptPath = "./utils/stats.py";
    $scriptPath = escapeshellcmd($scriptPath);
    $command = ["python3",$scriptPath];
    foreach($arguments as $argument) {
        array_push($command,$argument);
    }
    array_push($command,'./xmls/'); // Lien vers les fichiers xml
    array_push($command,"2>&1");
    $command = implode(' ',$command);
    $data = htmlspecialchars(shell_exec($command));
}

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Analyse Statistique</title>
    <link rel="stylesheet" href="style/python_style.css">
    <link rel="stylesheet" href="style/table.css">
</head>
<body>

<!-- Header -->
<header>
    <h1>Analyses Statistiques</h1>
    <a href="database_visualization.php" class="btn-back">← Retour à la BDD</a>
</header>

<!-- Results -->
<div class="results-container">
    <h3>Résultats de l'analyse</h3>
    <div class='container' id='results'>
        <?php if (empty($data)): ?>
            <p>Aucune donnée trouvée ou erreur lors de l'exécution.</p>
        <?php else: ?>
			<div class='table_main'>
				<div class='table_header'>
					<?php
					$cats = explode(';',$data);
					for($i = 0; $i < count($cats) - 1; $i++) {
						$values = explode('|',$cats[$i]);
						print "<div class='table_header_col width-25 cell_".trim($values[0])."' style='padding:0px'>";
						print "<div id='type_".trim($values[0])."' class='table_header_cell_title' style='padding:10px'>".strtoupper($values[0])."</div>";
						for($j = 1; $j < count($values)/2; $j++) {
							print "<div class='flex_row'>";
							print "<div class='table_cell width-50 question_".$values[0]."'>".$values[2*$j-1]."</div><div class='table_cell width-50 question_".$values[0]."'>".$values[2*$j]."</div>";
							print "</div>";
						}
						print "</div>";
					}
		endif; ?>
				</div>
			</div>
	</div>
</div>

<button class='up_button' onclick='javascript:function() {$(".table_main").scrollTop = 0;}'>
    <img src='img/arrow_up' class='arrow_up up_img' />
</button>

<!-- Footer -->
<footer>
    <p>© 2025 Projet ARGILES | Université Grenoble Alpes</p>
	<p>Contactez le directeur du projet : <a href="https://www.univ-grenoble-alpes.fr/thomas-lebarbe-538931.kjsp" target="_blank">Thomas Lebarbé</a> </p>
    <p>Ce site est sous licence : <a href="https://creativecommons.org/licenses/by-nc-sa/4.0/" target="_blank">Creative Commons BY-NC-SA 4.0</a>.</p>
</footer>

<script>
    <?php
        if (!empty($data)):
            echo "$('#results').innerHTML = '";
            echo htmlspecialchars($data);
            echo "';";
        endif;
    ?>
</script>

</body>
</html>

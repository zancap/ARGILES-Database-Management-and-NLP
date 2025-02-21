<?php
include_once("../connexion.php");
$requete = $_POST['req'];
$url = $_SERVER['REQUEST_URI'];
$url = explode('/',$url);
$url = implode('/',array_splice($url,0,-2));	

if ($requete == 'NONE') {
	print "<div class='table_main'>
			<div id='no_data'>
		Pas de données sélectionnées.
		Veuillez sélectionner au moins un ouvrage, un type de question et un groupe.
		</div>
	</div>";
} else {
	
	// Database column names query
	$result = $db->query($requete);
	$vars = $result->fetchAll(PDO::FETCH_ORI_FIRST);

	$vars = array_keys($vars[0]);	// Récupération des noms des catégories
	$index_id = 0 ; $index_type = 0 ; $index_oeuvre = 0 ; $index_idAudite = 0 ;
	foreach($vars as $i => $var) {
		if ( $vars[0] != 'NULL') {
			if ($var == "id") { 
				$index_id = $i;
			} elseif($var == "type") {	// Récupération de l'index du type
				$index_type = $i;
			} elseif ($var == 'sujet') {
				$index_oeuvre = $i;
			} elseif ($var == 'idAudite') {
				$index_idAudite = $i;
			}
		} else { $index_id = 0 ; $index_type = 0; $index_oeuvre = 0; $index_idAudite = 0;}
	} ?>


	<div>
	<label id='nb_lines' for='nb_lines'>Nombre de lignes : <?php
		$nb_lines = $result->rowCount();
		print_r($nb_lines); ?>
	</label>
	</div>

	<div class="table_main">
		<div class="table_header">
			<?php	
			foreach(array_keys($vars) as $i) {
				print '<div class="table_header_col cell_'.$vars[$i].'" style="padding:0px">';
					
					print '<div class="table_header_cell_title">'.$vars[$i].'</div>';
						
					// Exécution de la requête SQL
					$reponse = $db->query($requete);
				
					$ligne = $reponse->fetchAll();
												
					// Parcours des résultats avec la méthode fetch()
					foreach($ligne as $cell) {
						if ($i == $index_id) {
							print "<a href='".$url."/get_audite_results.php?audite_id=".$cell[$index_idAudite]."&novel_title=".$cell[$index_oeuvre]."' target='_blank'>";
						}
						print "<div class='table_cell question_".$cell[$index_type]."'>";
						print $cell[$i];
						print "</div>";
						if ($i == $index_id) {
						print "</a>";
						}
					}
						
				print '</div>';
			}
			print '</div>';
		
		print '</div>';

	$reponse->closeCursor();
}
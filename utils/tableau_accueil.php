<?php
include_once("../connexion.php");
$requete = $_POST['req'];

// Database column names query
$result = $db->query($requete);
$vars = $result->fetchAll(PDO::FETCH_ORI_FIRST);

$vars = array_keys($vars[0]);	// Récupération des noms des catégories
foreach($vars as $i => $var) {
	if ( $vars[0] != 'NULL') {
		if ($var == "type") {	// Récupération de l'index du type
			$index_type = $i;
		}
	} else { $index_type = 0;}
}



print '<div class="table_main">';
	print '<div class="table_header">';
		
		foreach(array_keys($vars) as $i) {
			print '<div class="table_header_col cell_'.$vars[$i].'" style="padding:0px">';
				
				print '<div class="table_header_cell_title">'.$vars[$i].'</div>';
					
					// Exécution de la requête SQL
					$reponse = $db->query($requete);
				
					$ligne = $reponse->fetchAll();
												
					// Parcours des résultats avec la méthode fetch()
					foreach($ligne as $cell) {
						switch ($cell[$index_type]) {	// Coloration de la case selon le type de question
							case "evocation":
								print "<div class='table_cell question_evocation'>".$cell[$i]."</div>";
								break;
							
							case "like":
								print "<div class='table_cell question_like'>".$cell[$i]."</div>";
								break;

							case "dislike":
								print "<div class='table_cell question_dislike'>".$cell[$i]."</div>";
								break;

							case "expectation":
								print "<div class='table_cell question_expectation'>".$cell[$i]."</div>";
								break;

							default:
								print "<div class='table_cell'>".$cell[$i]."</div>";
								break;
						}
					}
					
			print '</div>';
		}
	print '</div>';
	
print '</div>';

$reponse->closeCursor();
<?php
include_once("../connexion.php");
$requete = $_POST['req'];

$reponse = $db->query($requete);
$vars = $reponse->fetchAll(PDO::FETCH_ORI_FIRST);
$vars = array_keys($vars[0]);	// Récupération des noms des catégories
foreach($vars as $i => $var) {
	if ($var == "type") {	// Récupération de l'index du type
		$index_type = $i;
	}
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
		/*
		print '<div class="table_header_col cell_groupe">';
			
			print '<div class="table_header_cell_title">Groupe</div>';
				
			// Exécution de la requête SQL
			$reponse = $db->query($requete);
		
			$ligne = $reponse->fetchAll(PDO::FETCH_COLUMN,1);
										
			// Parcours des résultats avec la méthode fetch()
			foreach($ligne as $cell) {
				print "<div class='table_cell'>".$cell."</div>";
			}
							
		print '</div>';
				
		print '<div class="table_header_col cell_level">';
			print '<div class="table_header_cell_title">Niveau</div>';
				
			// Exécution de la requête SQL
			$reponse = $db->query($requete);
		
			$ligne = $reponse->fetchAll(PDO::FETCH_COLUMN,2);
										
			// Parcours des résultats avec la méthode fetch()
			foreach($ligne as $cell) {
				print "<div class='table_cell'>".$cell."</div>";
			}
				
		print '</div>';
				
		print '<div class="table_header_col cell_student">';
			print '<div class="table_header_cell_title">Élève</div>';
		
				// Exécution de la requête SQL
				$reponse = $db->query($requete);
				
				$ligne = $reponse->fetchAll(PDO::FETCH_COLUMN,3);
											
				// Parcours des résultats avec la méthode fetch()
				foreach($ligne as $cell) {
					print "<div class='table_cell'>".$cell."</div>";
				}
				
		print '</div>';
				
		print '<div class="table_header_col cell_classe">';
			print '<div class="table_header_cell_title">Classe</div>';

				// Exécution de la requête SQL
				$reponse = $db->query($requete);
				
				$ligne = $reponse->fetchAll(PDO::FETCH_COLUMN,4);
											
				// Parcours des résultats avec la méthode fetch()
				foreach($ligne as $cell) {
					print "<div class='table_cell'>".$cell."</div>";
				}
				
		print '</div>';
		
		print '<div class="table_header_col cell_year">';
			print '<div class="table_header_cell_title">Année</div>';

				// Exécution de la requête SQL
				$reponse = $db->query($requete);
				
				$ligne = $reponse->fetchAll(PDO::FETCH_COLUMN,5);
											
				// Parcours des résultats avec la méthode fetch()
				foreach($ligne as $cell) {
					print "<div class='table_cell'>".$cell."</div>";
				}
				
		print '</div>';
		
		print '<div class="table_header_col cell_corpus">';
			print '<div class="table_header_cell_title">Corpus</div>';
			
				// Exécution de la requête SQL
				$reponse = $db->query($requete);
				
				$ligne = $reponse->fetchAll(PDO::FETCH_COLUMN,6);
											
				// Parcours des résultats avec la méthode fetch()
				foreach($ligne as $cell) {
					print "<div class='table_cell'>".$cell."</div>";
				}
				
		print '</div>';
		
		print '<div class="table_header_col cell_turn">';
			print '<div class="table_header_cell_title">Temps</div>';

				// Exécution de la requête SQL
				$reponse = $db->query($requete);
				
				$ligne = $reponse->fetchAll(PDO::FETCH_COLUMN,7);
											
				// Parcours des résultats avec la méthode fetch()
				foreach($ligne as $cell) {
					print "<div class='table_cell'>".$cell."</div>";
				}
				
		print '</div>';
		
		print '<div class="table_header_col cell_teacher">';
			print '<div class="table_header_cell_title">Interv.Ens</div>';

				// Exécution de la requête SQL
				$reponse = $db->query($requete);
				
				$ligne = $reponse->fetchAll(PDO::FETCH_COLUMN,8);
											
				// Parcours des résultats avec la méthode fetch()
				foreach($ligne as $cell) {
					if ($cell == '0') {	//	Traitement du booléen 0-1
						print "<div class='table_cell'>Non</div>";
					} else {
						print "<div class='table_cell'>Oui</div>";
					}
				}
				
		print '</div>';
		
		print '<div class="table_header_col cell_norm">';
			print '<div class="table_header_cell_title">Normalisation</div>';

				// Exécution de la requête SQL
				$reponse = $db->query($requete);
				
				$ligne = $reponse->fetchAll(PDO::FETCH_COLUMN,9);
											
				// Parcours des résultats avec la méthode fetch()
				foreach($ligne as $cell) {
					if ($cell == '0') {	//	Traitement du booléen 0-1
						print "<div class='table_cell'>Non</div>";
					} else {
						print "<div class='table_cell'>Oui</div>";
					}
				}
		print '</div>';
	*/
	print '</div>';
	
print '</div>';

$reponse->closeCursor();
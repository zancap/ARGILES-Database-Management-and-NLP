<?php

include_once("connexion.php");

$requete = $_POST['requete'];

print '<div class="table_main">';
	print '<div class="table_header">';
	
		print '<div class="table_header_col cell_checkbox">';
			
			print '<div class="table_header_cell_title">Visualiser</div>';
				
				// Exécution de la requête SQL
				$reponse = $db->query($requete);
				
				$col = $reponse->fetchAll(PDO::FETCH_COLUMN,0);
											
				// Parcours des résultats avec la méthode fetch()
				foreach($col as $cell) {
					print '<div class="table_cell file_display" value="'.$cell.'"><input type="checkbox" class="visu_checkbox" value="'.$cell.'"></input></div>';
				}
				
			print '</div>';
			
		print '<div class="table_header_col cell_id">';
			
			print '<div class="table_header_cell_title">ID</div>';
			
				// Exécution de la requête SQL
				$reponse = $db->query($requete);
				
				$col = $reponse->fetchAll(PDO::FETCH_COLUMN,0);
											
				// Parcours des résultats avec la méthode fetch()
				foreach($col as $cell) {
					print "<div class='table_cell' value='".$cell."'>".$cell."</div>";
				}
							
			print '</div>';
					
			print '<div class="table_header_col cell_level">';
				print '<div class="table_header_cell_title">Niveau</div>';
			
				// Exécution de la requête SQL
				$reponse = $db->query($requete);
				
				$ligne = $reponse->fetchAll(PDO::FETCH_COLUMN,1);
											
				// Parcours des résultats avec la méthode fetch()
				foreach($ligne as $cell) {
					print "<div class='table_cell'>".$cell."</div>";
				}
				
			print '</div>';
					
			print '<div class="table_header_col cell_student">';
				print '<div class="table_header_cell_title">Élève</div>';
			
					// Exécution de la requête SQL
					$reponse = $db->query($requete);
					
					$ligne = $reponse->fetchAll(PDO::FETCH_COLUMN,2);
												
					// Parcours des résultats avec la méthode fetch()
					foreach($ligne as $cell) {
						print "<div class='table_cell'>".$cell."</div>";
					}
					
			print '</div>';
					
			print '<div class="table_header_col cell_classe">';
				print '<div class="table_header_cell_title">Classe</div>';
	
					// Exécution de la requête SQL
					$reponse = $db->query($requete);
					
					$ligne = $reponse->fetchAll(PDO::FETCH_COLUMN,3);
												
					// Parcours des résultats avec la méthode fetch()
					foreach($ligne as $cell) {
						print "<div class='table_cell'>".$cell."</div>";
					}
					
			print '</div>';
			
			print '<div class="table_header_col cell_version">';
				print '<div class="table_header_cell_title">Version</div>';
	
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
		
		print '</div>';
		
	print '</div>';
	
$reponse->closeCursor();
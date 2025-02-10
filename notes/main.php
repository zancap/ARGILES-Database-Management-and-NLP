<!DOCTYPE html>
<html>
	<head>
		<title>Projet PHP - Main</title>
		<meta charset="utf-8" />
		<link rel="stylesheet" href="style/index.css" />
		<script type="text/javascript" src="scripts/jquery-3.7.1.min.js"></script>
	</head>
	<body>
		<?php	include_once("scripts/connexion.php");
				include_once("scripts/pre_header.php");		?>
			
			<h3>Projet Robin</h3>
			
		<?php	include_once("scripts/post_header.php");	?>
		
		<?php
			// Création de la requête SQL
			if(isset($_GET['req'])) {
				$requete = $_GET['req'];	// Récupération de la requête en POST depuis le filtrage
				?><script>console.log(" <?php print($_GET['req']);?>");</script><?php
			} else {
				$requete = "SELECT * FROM Robin";
			}
		?>
		
		<div id='requete'>
			<label id='request_label'>SELECT * FROM Robin</label>
			<input type='text' id='requete_show'></input>
			<button id='update_request'>Filtrer</button>
		</div>
		<button id='show_request'><img src='media/wrench_icon.png' id='icon_request'></button>
		
		<script>
			$('#show_request').on({"click":function(){
				if($('#requete').css('visibility') == 'visible') {
					$('#requete').css('visibility','hidden');
					$('#show_request').css('background-color','#929AAB');
				} else {
					$('#requete').css('visibility','visible');
					$('#show_request').css('background-color','#EEEEEE');
				}
			}})
		</script>

		<div id="tableau_accueil">
			
			<script>
			
				var table_load = function() {
						
						var requete = "requete=SELECT * FROM Robin "+$('#requete_show')[0].value;	// Récupération de l'intitulé de la requête dans le titre
				
						$.ajax({
							url : 'scripts/tableau_accueil.php',
							data : requete,
							type : 'POST',
							dataType : 'html',
							success : function(reponse, statut) {
								document.getElementById('tableau_accueil').innerHTML = reponse;
							},
							error : function(reponse, statut) {
								console.log(reponse);
							}
						});
						
					};
					
				function domReady(f) {
				  if (document.readyState === 'complete') {
					f();
				  } else {
					document.addEventListener('DOMContentLoaded', f);
				  }
				}
			
				$('#requete_show')[0].value = "<?php print($requete);?>".split('Robin')[1];	// Remplis la barre du filtre avec la requête après 'SELECT * FROM Robin'
					
				domReady(table_load);	//	Crée le tableau au chargement de la page
				
				$('#update_request').on({
					"click":function(){
						var requete = "SELECT * FROM Robin "+$('#requete_show')[0].value;
						console.log(requete);
						table_load();
					}
				})
			</script>
			
		</div>

		

		<button type="submit" id="voir">visualiser</button>
		
		<script>
		
			function display_file() {	// Fonction à introduire ou à retirer
				console.log('Clicked !');
			}
			
			let case_fichiers = document.getElementsByClassName('file_display');
			for (var i = 0; i< case_fichiers.length; i++) {
				case_fichiers.item(i).addEventListener('click',display_file);
			}

			console.log(case_fichiers);
		
		</script>

		<script>
		    $(document).ready(function() {
		        $("#voir").on("click", function() {
		            var selectedID = [];
		            $(".table_cell.file_display input[type=checkbox]:checked").each(function() {
		                selectedID.push($(this).val());
		            });
		            if (selectedID.length > 0) {
		                $.ajax({
		                    url: "scripts/visualisation_fichier.php",
		                    type: "POST",
		                    data: { selectedID: selectedID },
		                    success: function(response) {
		                        // 在页面中显示服务器响应
		                        window.location.href = "result_affiche.php?response=" + encodeURIComponent(response);
		                    },
		                    error: function(xhr, status, error) {
		                        console.error("发生错误: " + error);
		                    }
		                });
		            } else {
		                alert("Veuillez sélectionner au moins un fichier.");
		            }
		        });
		    });
		</script>

		<div id="response-container"></div>

		
	</body>
</html>
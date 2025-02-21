<?php
include_once("connexion.php");
$title = "Base de données";
$db_list = file('./utils/db_list.txt',FILE_IGNORE_NEW_LINES);

function debug_to_console($data) {
    $output = $data;
    if (is_array($output))
        $output = implode(',', $output);

    echo "<script>console.log('Debug: " . $output . "' );</script>";
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Visualisation des données - Table</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style/table.css" />
    <link rel="stylesheet" href="style/index.css" />
    <script type="text/javascript" src="utils/jquery-3.7.1.min.js"></script>
    <script src="utils/scripts.js"></script> 
</head>
<body>

	<!-- Menu Toggle Button -->
	<div id="menuToggle" onclick="toggleSidebar()">☰ Menu</div>
  
    <!-- La barre laterale -->
    <div class="sidebar">
        <h4>Menu</h4>
        <a href="index.html">Accueil</a>
		<a href="functions.html">Explorez les fonctionnalités</a>
        <a href="database_visualization.php">Contenu de la base de données</a>
        <a href="pdf_visualization.php">Visualisation des copies d'élèves originales</a>
        <a href="upload.html">Téléversez des fichiers</a>
    </div>

    <!-- L'en-tete -->
    <header>
        <h1><?= htmlspecialchars($title) ?></h1>
    </header>

    <!-- Le contenu principal -->
    <div class="container">
        <h2 class="text-center">Sélectionnez un ouvrage à visualiser</h2>

        <!-- Les Filtres -->
        <div id="filter_container">
            <!-- Génération du filtre par PHP - fonction filter_load() -->
        </div>
        <p style="display:none" id="query"></p>
        <!-- ;height:0px;width:0px,padding:0px;margin:0px" ###-->

        <!-- Le tableau des données -->
            <table class="table table-bordered" id="main_table">
                <!-- Génération du tableau par PHP - fonction table_load() -->
            </table>
    </div>

    <!-- Pied de page -->
    <footer>
        <p>© 2025 Projet ARGILES | Université Grenoble Alpes</p>
        <p>Contactez le directeur du projet : <a href="https://www.univ-grenoble-alpes.fr/thomas-lebarbe-538931.kjsp" target="_blank">Thomas Lebarbé</a></p>
		<p>Ce site est sous licence <a href="https://creativecommons.org/licenses/by-nc-sa/4.0/" target="_blank">Creative Commons BY-NC-SA 4.0</a></p>
		<br>
    </footer>

    <!-- Les scripts -->
    <script id='scripts'>
     <?php 
        if (isset($_GET['query'])) {
            $query = $_GET['query'];
            $check = explode(' ',$query);
            if(in_array('SELECT',$check) && in_array('FROM',$check)) {
                echo "$('#query')[0].innerHTML = '".$query."';";
            } else {
                echo "$('#query')[0].innerHTML = 'SELECT * FROM Sirius';";
            }
        } else {
            echo "$('#query')[0].innerHTML = '";
            foreach($db_list as $i => $oeuvre) {
                print 'SELECT * FROM '.explode('|',$oeuvre)[0];
                if ($i < count($db_list)-1) {
                    print ' UNION ';
                }
            }
            print "';";
        }
        echo 'domReady(onStart);';  //  Construit le filtre
        ?>
		
	<!-- Toggle Menu Script -->
    function toggleSidebar() {
      var sidebar = document.getElementById("sidebar");

      // Ensure a default left position
      if (!sidebar.style.left) {
        sidebar.style.left = "-250px";
      }

      // Toggle sidebar visibility
      sidebar.style.left = (sidebar.style.left === "-250px") ? "0" : "-250px";
    }
    </script>

</body>
</html>
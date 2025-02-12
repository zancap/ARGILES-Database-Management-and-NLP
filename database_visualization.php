<?php
include_once("connexion.php");
$title = "Base de données";
$db_list = file('./utils/db_list.txt',FILE_IGNORE_NEW_LINES);

// Build query based on selected table
$query = "SELECT * FROM RomeoJuliette UNION SELECT * FROM Sirius";
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
</head>
<body>
    <!-- La barre laterale -->
    <div class="sidebar">
        <h4>Menu</h4>
        <a href="index.html">Accueil</a>
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
        <form method="get" class="mb-4">
            <div class="row">
                <div class="col-md-4 mb-2">
                    <label for="ouvrage" class="form-label">Filtrer par Ouvrage :</label>
                    <select id="ouvrage" name="ouvrage" class="form-select">
                        <option value="all">Tous</option>
                        <?php foreach($db_list as $i => $oeuvre) {  // Création des options pour chaque oeuvre dans db_list.txt
                            $oeuvre = strtok($oeuvre,'|');
                            $oeuvre = [$oeuvre,strtok('|')];        // Ligne format 'identifiant dans la base de données|nom courant'
                            print '<option value="'.$oeuvre[0].'">'.$oeuvre[1].'</option>';
                        } ?>
                    </select>
                </div>
                <div class="col-md-4 mb-2">
                    <label for="type" class="form-label">Filtrer par Type :</label>
                    <select id="type" name="type" class="form-select">
                        <option value="all">-- Tous --</option>
                        <?php $result = $db->query($query);
                        $vars = $result->fetchAll(PDO::FETCH_ORI_FIRST);
                        $vars = array_keys($vars[0]);	// Récupération des noms des catégories
                        foreach($vars as $i => $var) {
                            if ($var == "type") {	// Récupération de l'index du type
                                $index_type = $i;
                            }
                        }
                        $col_types = $result->fetchAll(PDO::FETCH_COLUMN,$index_type);
                        $types = [];
                        foreach($col_types as $type) {
                            if (in_array($type,$types)) {} else {
                                $types += $type;
                            }
                        print_r($types);
                        }
                        ?>
                    </select>
                </div>
                <div class="col-md-4 mb-2">
                    <label for="nomGroupe" class="form-label">Filtrer par Nom du Groupe :</label>
                    <select id="nomGroupe" name="nomGroupe" class="form-select">
                        <option value="all">-- Tous --</option>
                    </select>
                </div>
            </div>
            <button type="button" class="btn btn-primary mt-2" id="filter">Appliquer</button>
        </form>
        <p style="visibility:collapse" id="query"><?php echo $query; ?></p>

        <!-- Le tableau des données -->
            <table class="table table-bordered" id="main_table">
                <!-- Génération du tableau par PHP - fonction table_load() -->
            </table>
    </div>

    <!-- Footer -->
    <footer>
        <p>© 2025 Projet ARGILES | Université Grenoble Alpes</p>
        <p>Ce site est sous licence <a href="https://creativecommons.org/licenses/by-nc-sa/4.0/" target="_blank">Creative Commons BY-NC-SA 4.0</a>.</p>
        <p>Contactez le directeur du projet : 
            <a href="https://www.univ-grenoble-alpes.fr/thomas-lebarbe-538931.kjsp" target="_blank">Thomas Lebarbé</a>
        </p>
    </footer>
    <!-- Les scripts -->

    <script>

        var table_load = function() {
                
                var requete = "req="+$('#query').text();
        
                $.ajax({
                    type: 'POST',
                    url: 'utils/tableau_accueil.php',
                    data: requete,
                    dataType: 'html',
                    success: function(reponse, statut) {
                        document.getElementById('main_table').innerHTML = reponse;
                    },
                    error: function(reponse, statut) {
                        console.log(reponse);
                    }
                });
                
            };

        function auto_query() {
            $query = ""
            $ouvrage = $('#ouvrage')[0].value;
            $type = $('#type')[0].value;
            $nomGroupe = $('#nomGroupe')[0].value;
            console.log('ouvrage : '+$ouvrage+' | type : '+$type+' | nomGroupe = '+$nomGroupe);
            if ($ouvrage == 'all') {
                $ouvrage = ['Sirius','RomeoJuliette'];
            } else {
                $ouvrage = [$ouvrage];
            }
            
            for($i = 0; $i<$ouvrage.length ; $i++) {
                $query = $query+'SELECT * from '+$ouvrage[$i];
                if ($i < $ouvrage.length - 1) {
                    $query = $query+" UNION ";
                }
            }
            $("#query")[0].innerHTML = $query;
        }
            
        function domReady(f) {
        if (document.readyState === 'complete') {
            f(array_slice(arguments,1));
        } else {
            document.addEventListener('DOMContentLoaded', f);
        }
        }

        $("button").on({
                "click":function(){
                    auto_query();
                    var requete = $('#query')[0].innerHTML;
                    console.log(requete);
                    table_load();
                }
            })
            
        domReady(table_load);	//	Crée le tableau au chargement de la page
        domReady(auto_query);
    </script>

</body>
</html>
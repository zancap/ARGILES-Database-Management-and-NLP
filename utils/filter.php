<?php
include_once("../connexion.php");
$query = $_POST['req'];
$db_list = file('./db_list.txt',FILE_IGNORE_NEW_LINES);

// Database column names query
$result = $db->query($query);
$vars = $result->fetchAll(PDO::FETCH_ORI_FIRST);
$vars = array_keys($vars[0]);	// Récupération des noms des catégories
?>

<form method="get" class="mb-4">
    <div class="row">
        <div class="col-md-4 mb-2">
            <label for="ouvrage" class="form-label">Filtrer par Ouvrage :</label>
            <select id="ouvrage" name="ouvrage" class="form-select" onChange="javascript:update()">
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
            <select id="type" name="type" class="form-select" onChange="javascript:update()">
                <option value="all">-- Tous --</option>
                <?php
                foreach($vars as $i => $var) {
                    if ($var == "type") {	// Récupération de l'index du type
                        $index_type = $i;
                    }
                }
                $result = $db->query($query);
                $col_types = $result->fetchAll(PDO::FETCH_COLUMN,$index_type);
                $types = array_unique($col_types);

                foreach($types as $type) {
                    print '<option value="'.$type.'">'.$type.'</option>';
                }
                ?>
            </select>
        </div>
        <div class="col-md-4 mb-2">
            <label for="nomGroupe" class="form-label">Filtrer par Nom du Groupe :</label>
            <select id="nomGroupe" name="nomGroupe" class="form-select" onChange="javascript:update()">
                <option value="all">-- Tous --</option>
                <?php
                foreach($vars as $i => $var) {
                    if ($var == "nomGroupe") {	// Récupération de l'index du type
                        $index_type = $i;
                    }
                }
                $result = $db->query($query);
                $col_types = $result->fetchAll(PDO::FETCH_COLUMN,$index_type);
                $types = array_unique($col_types);

                foreach($types as $type) {
                    print '<option value="'.$type.'">'.$type.'</option>';
                }
                ?>
            </select>
        </div>
        <div class="col-md-4 mb-2">
            <label for="columns_visible" class="form-label">Colonnes visibles :</label>
            <div id="columns_visible" name="columns_visible" onChange="javascript:update()">
                <div>
                    <input type="checkbox" id="all_checkboxes" name="all_checkboxes" unchecked onChange="javascript:checkbox_update()"/>
                    <label for="all_checkboxes">Tout cocher</label>
                </div>
                <?php
                $var_sorted = $vars;
                asort($var_sorted);
                foreach($var_sorted as $i => $var) {
                    if (in_array($var,['sujet','type','idAudite','nomGroupe','idIdee','ideeView'])) {
                        print '<div>
                                    <input type="checkbox" id="'.$var.'" name="'.$var.'" checked onChange="javascript:update()"/>
                                    <label for="'.$var.'">'.$var.'</label>
                                </div>';
                    } else {
                        print '<div>
                                    <input type="checkbox" id="'.$var.'" name="'.$var.'" unchecked onChange="javascript:update()"/>
                                    <label for="'.$var.'">'.$var.'</label>
                                </div>';
                    }
                }
                ?>
            </select>
        </div>
    </div>
    <button type="button" class="btn btn-primary mt-2" id="filter">Appliquer</button>
</form>
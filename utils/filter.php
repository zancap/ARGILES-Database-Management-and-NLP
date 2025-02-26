<?php
include_once("../connexion.php");
$query = $_POST['req'];
$db_list = file('./db_list.txt',FILE_IGNORE_NEW_LINES);
$url = $_SERVER['REQUEST_URI'];
$url = explode('/',$url);
$url = implode('/',array_splice($url,0,-2));

// Database column names query
$result = $db->query($query);
$vars = $result->fetchAll(PDO::FETCH_ORI_FIRST);
$vars = array_keys($vars[0]);	// Récupération des noms des catégories
?>

<form method="get" class="mb-4">
    <div class="filter_row">
        <div class="col-md-4 filter_zone">
            <button id="ouvrage" type='button' class='filter_button' onclick="javascript:toggle_filter_visibility(this)">Filtrer par Ouvrage :</button>
            <div id="ouvrage_visible" class='filter_div' style='display:none' name="ouvrage">
                <input id='all_ouvrage' type='checkbox' value="all" class='master_checkbox' checked onchange='javascript:master_checkbox_update(this)'></input>
                <label class='master_label' for="all">-- Tous --</label>
                <?php foreach($db_list as $oeuvre) {  // Création des options pour chaque oeuvre dans db_list.txt
                    $id_oeuvre = explode('|',$oeuvre)[0];
                    $nom_oeuvre = explode('|',$oeuvre)[1];
                    print '<div>
                                <input type="checkbox" id="'.$id_oeuvre.'" name="'.$nom_oeuvre.'" class="ouvrage" checked onChange="javascript:update(this)"/>
                                <label for="'.$nom_oeuvre.'">'.$nom_oeuvre.'</label>
                            </div>';
                }
                ?>
            </div>
        </div>
        <div class="col-md-2 mb-2 filter_zone">
            <button id='type' type='button' class='filter_button' onclick="javascript:toggle_filter_visibility(this)">Filtrer par Type :</button>
            <div id='type_visible' class='filter_div' style='display:none' name='type'>
                <input type='checkbox' id="all_type" value="all" class='master_checkbox' checked onchange='javascript:master_checkbox_update(this)'></input>
                <label class='master_label' for="all">-- Tous --</label>
                <?php
                foreach($vars as $i => $var) {
                    if ($var == "type") {	// Récupération de l'index du type
                        $index_type = $i;
                    }
                }
                $result = $db->query($query);
                $col_types = $result->fetchAll(PDO::FETCH_COLUMN,$index_type);
                $types = array_unique($col_types);
                $i = 1;
                foreach($types as $type) {
                    print '<div id="type_'.$type.'"';
                    if ($i == count($types)) {
                        print 'style="border-radius:0px 0px 50px 50px"';
                    }
                    print '>
                                <input type="checkbox" id="'.$type.'" name="'.$type.'" class="type" checked onChange="javascript:update(this)"/>
                                <label for="'.$type.'">'.$type.'</label>
                            </div>';
                    $i++;
                }
                ?>
            </div>
        </div>
        <div class="col-md-4 mb-2 filter_zone">
            <button id='nomGroupe' type='button' class='filter_button' onclick="javascript:toggle_filter_visibility(this)">Filtrer par Nom du Groupe :</button>
            <div id="nomGroupe_visible" class='filter_div' style='display:none' name="nomGroupe">
                <input type='checkbox' id="all_nomGroupe" value="all" class='master_checkbox' checked onchange='javascript:master_checkbox_update(this)'></input>
                <label class='master_label' for="all">-- Tous --</label>
                <?php
                foreach($vars as $i => $var) {
                    if ($var == "nomGroupe") {	// Récupération de l'index du type
                        $index_nomGroupe = $i;
                    }
                }
                $result = $db->query($query);
                $col_groupes = $result->fetchAll(PDO::FETCH_COLUMN,$index_nomGroupe);
                $groupes = array_unique($col_groupes);

                foreach($groupes as $groupe) {
                    print '<div>
                                <input type="checkbox" id="'.$groupe.'" name="'.$groupe.'"  class="nomGroupe" checked onChange="javascript:update(this)"/>
                                <label for="'.$groupe.'">'.$groupe.'</label>
                            </div>';
                }
                ?>
            </div>
        </div>
        <div class='filter_button_div'>
            <div class='filter_button_under_div'>
                <button id='reload_button'  class='reload_button' type='button' onclick='javascript:reload()'>
                    <img src='img/reload.png' class='img_button' />
                </button>
                <button id='copy_button' class='copy_button' type='button' onclick="javascript:copy_paste()">
                    <img src='img/copy.png' class='img_button' />
                </button>
            </div>
            <div class='filter_button_under_div'>
                <?php $stats_arguments = '?arguments=';

                ?>
                <button id='stats_button' class='stats_button' type='button' onclick="javascript:open_stats('<?php echo $url; ?>')">
                    <img src='img/stats.png' class='img_button' />
                </button>
                <button id='stats_button' class='stats_button' type='button' onclick="javascript:open_stats_lemma('<?php echo $url; ?>')">
                    <img src='img/stats_lemma.png' class='img_button' />
                </button>
            </div>
        </div>
    </div>
    <div class='filter_row'>
        <div class="col-md-4 mb-2 filter_zone">
            <button id='columns' type='button' class='filter_button' onclick="javascript:toggle_filter_visibility(this)">Colonnes visibles :</button>
            <div id="columns_visible" class='filter_div' style='display:none' name="columns_visible">
                <div>
                    <input type="checkbox" id="all_column" name="all_column" class='master_checkbox' unchecked onchange='javascript:master_checkbox_update(this)'/>
                    <label class='master_label' for="all">Tout cocher</label>
                </div>
                <?php
                $var_sorted = $vars;
                asort($var_sorted);
                foreach($var_sorted as $i => $var) {
                    if (in_array($var,['sujet','type','idAudite','nomGroupe','idIdee','ideeView'])) {
                        print '<div>
                                    <input type="checkbox" id="'.$var.'" name="'.$var.'" class="column" checked onChange="javascript:update(this)"/>
                                    <label for="'.$var.'">'.$var.'</label>
                                </div>';
                    } else {
                        print '<div>
                                    <input type="checkbox" id="'.$var.'" name="'.$var.'" class="column" unchecked onChange="javascript:update(this)"/>
                                    <label for="'.$var.'">'.$var.'</label>
                                </div>';
                    }
                }
                ?>
            </div>
        </div>
    </div>
</form>
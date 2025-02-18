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

var filter_load = function(_callback = function() {autoquery()}) {

    if ($('#query').text == "") {
        var requete = "req="+$('#query').text();
        console.log('VIDE');
    } else {
        var requete = "req=SELECT * FROM Sirius";
    }

    $.ajax({
            type: 'POST',
            url: 'utils/filter.php',
            data: requete,
            dataType: 'html',
            success: function(reponse, statut) {
                document.getElementById('filter_container').innerHTML = reponse;
            },
            error: function(reponse, statut) {
                console.log(reponse);
            }
        });

    $("select").on({
        "change":function(){
            auto_query();
            filter_load;
            console.log('Select changed !');
            table_load();
        }
    })

    _callback();
}

function auto_query() {
    $query = "";
    $ouvrages = $('#ouvrage')[0].value;
    $type = $('#type')[0].value;
    $nomGroupe = $('#nomGroupe')[0].value;
    $input = $('input');
    $inputs = []
    for($var in $input) {
        if ($input[$var].nodeType == 1) {
            //console.log($input[$var]);
            $inputs.push([$input[$var].id,$input[$var].checked]);
        }
    }
    //console.log($inputs);
    console.log('ouvrage : '+$ouvrages+' | type : '+$type+' | nomGroupe = '+$nomGroupe);
    if ($ouvrages == 'all') {    // Récupère tous les documents depuis utils/db_list.txt
        $ouvrages = []
        $.get('utils/db_list.txt', function(data) {
            $inter = data.split("\n");
            for ($val in $inter) {
                $val2 = $inter[$val].split("|")[0];
                $ouvrages.push($val2);
                buildQuery($ouvrages);
            }
        }, 'text');
    } else {
        $ouvrages = [$ouvrages];
        buildQuery($ouvrages);
    }

function buildQuery($ouvrages,_callback = function() {table_load();}) {
    $query = '';

    for($index = 0; $index < $ouvrages.length ; $index++) {     // Récupération des ouvrages
        // 1. Create the query based on selection
        $query += 'SELECT ';
        if ($('#all_checkboxes')[0].checked) {
            $query += '*';
        } else {
            $inputs = $('input');
            $nb_checkboxes = 0;
            $columns = '';
            for ($i in $inputs) {     // Récupère tous les Nodes DOM des cases
                if ($inputs[$i].nodeType == 1 && $inputs[$i].id != 'all_checkboxes') {
                    if ($inputs[$i].checked == true) {
                        $columns += $inputs[$i].id;
                        $nb_checkboxes += 1;
                        if ($i < $inputs.length - 1) {
                            $columns += ', ';
                        }
                    }
                }
            }
            if ($nb_checkboxes == 0) {
                $query += 'null';
            } else {
                $query += $columns;
            }
        }
        $query += ' FROM '+$ouvrages[$index];
        console.log($query);
        if ($type != 'all') {       // Specify type of question
            $query += ' WHERE type = "'+$type+'"';
        }
        if ($nomGroupe != 'all') {  // Specify group
            $query += ' AND nomGroupe = "'+$nomGroupe+'"';
        }
        if ($index < $ouvrages.length - 1) {
            $query += " UNION ";
        }
    }

    $("#query")[0].innerHTML = $query;

    _callback();    // Callback function : autoload() after modifying the query
    }
}

function domReady(f) {  //  Starts the f function after loading
    if (document.readyState === 'complete') {
    f(array_slice(arguments,1));
    } else {
    document.addEventListener('DOMContentLoaded', f);
    }
    }

function update() {
    $inputs = $('input');
    for ($i in $inputs) {                         // Récupère tous les Nodes DOM des cases
        if ($inputs[$i].nodeType == 1 && $inputs[$i].id != 'all_checkboxes' && $inputs[$i].checked == false) {
            $('#all_checkboxes')[0].checked = false;
        }
    }
    auto_query();
    var requete = $('#query')[0].innerHTML;
}

function checkbox_update() {
    $checkbox = $('#all_checkboxes')[0].checked;    // Récupère le statut de la case
    $inputs = $('input');
    $input_checkboxes = [];
    for ($i in $inputs) {                         // Récupère tous les Nodes DOM des cases
        if ($inputs[$i].nodeType == 1 && $inputs[$i].id != 'all_checkboxes') {
            $input_checkboxes.push($inputs[$i]);
        }
    }
    for($input in $input_checkboxes) {              // Modifie chaque case de la même manière que la 1ère
        $input_checkboxes[$input].checked = $checkbox;
    }
}
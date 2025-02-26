function onStart() {
    // Loads the page at the beginning, using callbacks
    filter_load(() => {
        auto_query(() => {
            table_load(() => {
                $(".table_main")[0].scrollTop = 0;
            });
        });
    });
}

function filter_load(callback) {
    // AJAX Call to filter.php to generate the filter
    // Allows for dynamic update of the filter

    if ($('#query').text() != "") {
        var requete = "req="+$('#query').text();
    } else {
        var requete = "req=SELECT * FROM Sirius";
    }

    $.ajax({
            type: 'POST',
            url: 'utils/filter.php',
            data: requete,
            dataType: 'html',
            success: function(reponse, statut) {
                $('#filter_container').html(reponse);
        
                if (typeof callback === 'function') {
                    callback();
                }
            },
            error: function(reponse, statut) {
                console.log(reponse);
            }
        });
}

function auto_query(callback) {
    // Regenerates the query based on selected options on the filter
    // Allows for dynamic update of the table
    $query = '';
    $where_check = false;   // Tells if the "WHERE" SQL condition has been written down

    // Get all checked boxes in the filter, or all if it is checked
    $q_ouvrages = $('.ouvrage:checked').toArray();
    $q_types = $('.type:checked').toArray();
    $q_groupes = $('.nomGroupe:checked').toArray();
    $q_column = $('.column:checked').toArray();

    if ($q_ouvrages.length == 0 | $q_types.length == 0 | $q_groupes.length == 0 | $q_column.length == 0) {
        $("#query")[0].innerHTML = "NONE";
    } else {
        for(oeuvre in $q_ouvrages) {
            // 1. Create the query based on selection
            $query += 'SELECT ';
            if ($('#all_column')[0].checked) {
                $query += '* ';
            } else {
                for(col in $q_column) {
                    $query += $q_column[col].id;
                    if (col < $q_column.length - 1) {
                        $query += ', ';
                    }
                }
            }
            $query += ' FROM '+$q_ouvrages[oeuvre].id+' ';
            if (!$('#all_type')[0].checked) {
                $query += 'WHERE ( ';
                $where_check = true;
                for(type in $q_types) {
                    $query += "type='"+$q_types[type].id+"' ";
                    if (type < $q_types.length - 1) {
                        $query += 'OR ';
                    }
                }
                $query += ') ';
            }
            if (!$('#all_nomGroupe')[0].checked) {
                if ($where_check) {
                    $query += 'AND ( ';
                } else {
                    $query += 'WHERE ( ';
                }
                for(group in $q_groupes) {
                    $query += "nomGroupe='"+$q_groupes[group].id+"' ";
                    if (group < $q_groupes.length - 1) {
                        $query += 'OR ';
                    }
                }
                $query += ') ';
            }
            if (oeuvre < $q_ouvrages.length - 1) {
                $query += " UNION ";
            }
        }

        $("#query")[0].innerHTML = $query;
    
        if (typeof callback === 'function') {
            callback();
        }
    }
}

function table_load(callback) {
    // AJAX call of utils/tableau_accueil.php to generate the table
    // Allows for dynamic update of the table
                
    var requete = "req="+$("#query")[0].innerHTML;

    $.ajax({
        type: 'POST',
        url: 'utils/tableau_accueil.php',
        data: requete,
        dataType: 'html',
        success: function(reponse, statut) {
            document.getElementById('main_table').innerHTML = reponse;
            if (typeof callback === 'function') {
                callback();
            }
        },
        error: function(reponse, statut) {
            console.log(reponse);
        }
    });
    
};

function setClipboard(text) {
    // First try giving the user a copy-paste button for the query
    let data = new DataTransfer();
  
    data.items.add(text, "text/plain");
    navigator.clipboard.write(data);//.then(function () { alert("Requête copiée");      },      function () {        alert("Erreur, veuillez enregistrer manuellement la requête suivante :\n"+$query);      },    );
  }

function setClipboard_old(text) {
    // Second try giving the user a copy-paste button for the query
    // Deprecated function
    var copyText = text;
    copyText.select();
    copyText.setSelectionRange(0, 99999);
    document.execCommand("copy");
    alert("Requête copiée");
}

function copy_paste() {
    // Paperwrap for the copy functions, tries to give the user the request

    $query = "http://i3l.univ-grenoble-alpes.fr/argiles/database_visualization.php?query="+$("#query")[0].innerHTML;
    try {
        setClipboard_old($query);
        setClipboard($query);
    } catch {
        alert("Erreur, veuillez enregistrer manuellement la requête suivante :\n\n\n"+$query);
    }
}

function toggle_filter_visibility(elt) {
    // Toggles the filter visibility on click on any type of filter available
    $div_id = elt.id+"_visible";
    $div = $('#'+$div_id)[0];
    if ($div.style['display'] == "none") {
        $div.style['display'] = "block";
    } else {
        $div.style['display'] = "none";
    }

}

function update(elt) {
    // Updates when a checkbox is clicked
    $master = $('#all_'+elt.className)[0];
    $master.checked = false;    // If the checkbox turns false, turns its "all" checkbox false too
    auto_query();
    table_load();
}

function master_checkbox_update(elt) {
    // Updates when a "all" checkbox is clicked
    $bool = elt.checked;
    $checkboxes = $('.'+elt.id.split('_')[1]);
    for ($i in $checkboxes) {       // Switch all corresponding boxes to same value
        if ($checkboxes[$i].nodeType == 1) {
            $checkboxes[$i].checked = $bool;
        }
    }
    auto_query();
    table_load();
}

function open_stats(dir) {
    $arguments = [];
    $ouvrages = $('.ouvrage:checked').toArray();
    for ($i = 0; $i < $ouvrages.length; $i++) {
        $arguments.push($ouvrages[$i].id);
    }
    $groupes = $('.nomGroupe:checked').toArray();
    for ($i = 0; $i < $groupes.length; $i++) {
        $arguments.push($groupes[$i].id);
    }
    $url = "/stats_results.php?arguments="+$arguments.join('AND');
    console.log(dir);
    console.log($url);
    window.open(dir+$url);
}

function open_stats_lemma(dir) {
    if(false) { // Fonction désactivée en attendant l'ajout du package spacy fr_core_news_md -> Ligne à retirer si le package est ajouté
        $arguments = [];
        $ouvrages = $('.ouvrage:checked').toArray();
        for ($i = 0; $i < $ouvrages.length; $i++) {
            $arguments.push($ouvrages[$i].id);
        }
        $groupes = $('.nomGroupe:checked').toArray();
        for ($i = 0; $i < $groupes.length; $i++) {
            $arguments.push($groupes[$i].id);
        }
        $url = "/stats_results.php?arguments=LEMMAAND"+$arguments.join('AND');
        window.open(dir+$url);
    }
}

function reload() {
    // Reload button : Reloads the query and table based on selection. Debugging purpose.
    auto_query(() => {
        table_load(() => {
            $(".table_main")[0].scrollTop = 0;
        });
    });
}

function domReady(f) {
    //  Starts the f function after loading
    if (document.readyState === 'complete') {
        f(array_slice(arguments,1));
    } else {
        document.addEventListener('DOMContentLoaded', f);
    }
}
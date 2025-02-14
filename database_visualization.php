<?php
// Configuration de la connexion
$servername = "localhost";
$username = "zancanap";
$password = "@Tutideze15";
$dbname = "argiles";

// Connection à la base de données
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Récuperer les filtres avec les paramètres GET 
$selectedOuvrage   = $_GET['ouvrage'] ?? 'all';
$selectedType      = trim($_GET['type'] ?? '');
$selectedNomGroupe = trim($_GET['nomGroupe'] ?? '');

// Chercher les valeurs pour les menus selon l'oeuvre séléctionné
if ($selectedOuvrage === 'RomeoJuliette') {
    $distinctTypesQuery  = "SELECT DISTINCT type FROM RomeoJuliette ORDER BY type ASC";
    $distinctGroupsQuery = "SELECT DISTINCT nomGroupe FROM RomeoJuliette ORDER BY nomGroupe ASC";
} elseif ($selectedOuvrage === 'Sirius') {
    $distinctTypesQuery  = "SELECT DISTINCT type FROM Sirius ORDER BY type ASC";
    $distinctGroupsQuery = "SELECT DISTINCT nomGroupe FROM Sirius ORDER BY nomGroupe ASC";
} else {
    // Pour "all" on merge le contenu des deux tableaux
    $distinctTypesQuery  = "SELECT DISTINCT type FROM RomeoJuliette UNION SELECT DISTINCT type FROM Sirius ORDER BY type ASC";
    $distinctGroupsQuery = "SELECT DISTINCT nomGroupe FROM RomeoJuliette UNION SELECT DISTINCT nomGroupe FROM Sirius ORDER BY nomGroupe ASC";
}
$resultTypes  = $conn->query($distinctTypesQuery);
$resultGroups = $conn->query($distinctGroupsQuery);

// Controle de sécurité
$escapedType      = $conn->real_escape_string($selectedType);
$escapedNomGroupe = $conn->real_escape_string($selectedNomGroupe);

// Construction des "query" basés sur les filtres séléctionnés
if ($selectedOuvrage === 'RomeoJuliette') {
    $query = "SELECT idEtude, sujet, idQuestion, texteQuestion, type, idGroupe, nomGroupe, idAudite, idIdee, ideeView AS ideeView
              FROM RomeoJuliette
              WHERE 1";
    if (!empty($escapedType)) {
        $query .= " AND type LIKE '%$escapedType%'";
    }
    if (!empty($escapedNomGroupe)) {
        $query .= " AND nomGroupe LIKE '%$escapedNomGroupe%'";
    }
    $title = "Réponses des élèves - Manga 'Roméo et Juliette'";
} elseif ($selectedOuvrage === 'Sirius') {
    $query = "SELECT idEtude, sujet, idQuestion, texteQuestion, type, idGroupe, nomGroupe, idAudite, idIdee, ideeView AS ideeView
              FROM Sirius
              WHERE 1";
    if (!empty($escapedType)) {
        $query .= " AND type LIKE '%$escapedType%'";
    }
    if (!empty($escapedNomGroupe)) {
        $query .= " AND nomGroupe LIKE '%$escapedNomGroupe%'";
    }
    $title = "Réponses des élèves - Roman 'Sirius'";
} else {
    $query = "SELECT 'RomeoJuliette' AS ouvrage, idEtude, sujet, idQuestion, texteQuestion, type, idGroupe, nomGroupe, idAudite, idIdee, ideeView AS ideeView
              FROM RomeoJuliette
              WHERE 1";
    if (!empty($escapedType)) {
        $query .= " AND type LIKE '%$escapedType%'";
    }
    if (!empty($escapedNomGroupe)) {
        $query .= " AND nomGroupe LIKE '%$escapedNomGroupe%'";
    }
    $query .= " UNION ALL
                SELECT 'Sirius' AS ouvrage, idEtude, sujet, idQuestion, texteQuestion, type, idGroupe, nomGroupe, idAudite, idIdee, ideeView AS ideeView
                FROM Sirius
                WHERE 1";
    if (!empty($escapedType)) {
        $query .= " AND type LIKE '%$escapedType%'";
    }
    if (!empty($escapedNomGroupe)) {
        $query .= " AND nomGroupe LIKE '%$escapedNomGroupe%'";
    }
    $title = "Réponses des élèves - Tous les ouvrages";
}

$result = $conn->query($query);
if (!$result) {
    die("Query error: " . $conn->error);
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Visualisation des données - Table</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
    body {
        font-family: 'Roboto', sans-serif;
        background-color: #f9f9f9;
        color: #333;
        margin: 0;
    }

    /* Sidebar */
    .sidebar {
        width: 250px;
        position: fixed;
        top: 0;
        left: 0;
        height: 100%;
        background-color: #2c3e50;
        color: #fff;
        padding: 20px;
        box-shadow: 2px 0 5px rgba(0,0,0,0.1);
        z-index: 1000;
    }
    .sidebar a {
        color: #fff;
        text-decoration: none;
        display: block;
        margin: 15px 0;
        font-size: 16px;
    }
    .sidebar a:hover {
        text-decoration: underline;
    }

    /* Header */
    header {
        background-color: #5a7d6e; /* Green background */
        color: #fff;
        padding: 40px 20px;
        text-align: center;
        border-radius: 8px;
        margin: 20px auto; /* Centered with vertical margins */
        max-width: 1000px;  /* Limits the header width */
    }

    .container {
        margin-left: 270px;
        padding: 20px;
    }

    /* Table Styling */
    table {
        background-color: #fff;
        border-collapse: collapse;
        width: 100%;
        margin-top: 20px;
        border: 2px solid #5a7d6e; /* Green border */
    }
    table th {
        background-color: #5a7d6e; /* Green header background */
        color: #fff;
        padding: 10px;
        border: 2px solid #5a7d6e;
    }
    table td {
        border: 1px solid #5a7d6e; /* Green cell borders */
        padding: 8px;
    }

    /* Footer */
    footer {
        background-color: #2c3e50;
        color: #fff;
        text-align: center;
		padding: 40px 20px; /* Matches header padding */
        margin-top: 20px;
        max-width: 1000px;  /* Matches header width */
        margin-left: auto;
        margin-right: auto;
        border-radius: 8px; /* Matches header border-radius */
    }
    footer a {
        color: #88b097;
        text-decoration: none;
    }
    footer a:hover {
        text-decoration: underline;
    }

    /* Back button (if needed) */
    .btn-back {
        display: inline-block;
        margin-top: 20px;
        background-color: #5a7d6e;
        color: #fff;
        border: none;
        padding: 10px 20px;
        border-radius: 5px;
        text-decoration: none;
        transition: background-color 0.3s ease;
    }
    .btn-back:hover {
        background-color: #4a665a;
    }
</style>

</head>
<body>
    <!-- La barre laterale -->
    <div class="sidebar">
        <h4>Menu</h4>
        <a href="index.html">Accueil</a>
		<a href="functions.html">Explorez les fonctionnalites</a>
        <a href="database_visualization.php">Contenu de la base de données</a>
        <a href="pdf_visualization.html">Visualisation des copies d'élèves originales</a>
        <a href="login.php">Téléversez des fichiers</a>
    </div>
    <!-- L'en-tete -->
    <header>
        <h1><?= htmlspecialchars($title) ?></h1>
		<a href="index.html" class="btn-back">← Retour à l'accueil</a>
    </header>
    <!-- Le contenu principal -->
    <div class="container">
        <h2 class="text-center">Sélectionnez un ouvrage à visualiser</h2>
        <!-- Les Filtres -->
        <form method="GET" class="mb-4">
            <div class="row">
                <div class="col-md-4 mb-2">
                    <label for="ouvrage" class="form-label">Filtrer par Ouvrage :</label>
                    <select id="ouvrage" name="ouvrage" class="form-select">
                        <option value="all" <?= $selectedOuvrage === 'all' ? 'selected' : '' ?>>Tous</option>
                        <option value="Sirius" <?= $selectedOuvrage === 'Sirius' ? 'selected' : '' ?>>Roman "Sirius"</option>
                        <option value="RomeoJuliette" <?= $selectedOuvrage === 'RomeoJuliette' ? 'selected' : '' ?>>Manga "Roméo et Juliette"</option>
                    </select>
                </div>
                <div class="col-md-4 mb-2">
                    <label for="type" class="form-label">Filtrer par Type :</label>
                    <select id="type" name="type" class="form-select">
                        <option value="">-- Tous --</option>
                        <?php while($rowType = $resultTypes->fetch_assoc()): ?>
                            <option value="<?= htmlspecialchars($rowType['type']) ?>" <?= ($selectedType === $rowType['type'] ? 'selected' : '') ?>>
                                <?= htmlspecialchars($rowType['type']) ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="col-md-4 mb-2">
                    <label for="nomGroupe" class="form-label">Filtrer par Nom du Groupe :</label>
                    <select id="nomGroupe" name="nomGroupe" class="form-select">
                        <option value="">-- Tous --</option>
                        <?php while($rowGroup = $resultGroups->fetch_assoc()): ?>
                            <option value="<?= htmlspecialchars($rowGroup['nomGroupe']) ?>" <?= ($selectedNomGroupe === $rowGroup['nomGroupe'] ? 'selected' : '') ?>>
                                <?= htmlspecialchars($rowGroup['nomGroupe']) ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
            </div>
            <button type="submit" class="btn btn-primary mt-2">Appliquer</button>
        </form>
        <!-- Le tableau des données -->
        <?php if ($result->num_rows > 0): ?>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Ouvrage</th>
                        <th>ID Étude</th>
                        <th>Sujet</th>
                        <th>ID Question</th>
                        <th>Texte Question</th>
                        <th>Type Question</th>
                        <th>ID Groupe</th>
                        <th>Nom Groupe</th>
                        <th>ID Audite</th>
                        <th>ID Idée</th>
                        <th>Texte Idée</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['ouvrage'] ?? $selectedOuvrage) ?></td>
                            <td><?= htmlspecialchars($row['idEtude']) ?></td>
                            <td><?= htmlspecialchars($row['sujet']) ?></td>
                            <td><?= htmlspecialchars($row['idQuestion']) ?></td>
                            <td><?= htmlspecialchars($row['texteQuestion']) ?></td>
                            <td><?= htmlspecialchars($row['type']) ?></td>
                            <td><?= htmlspecialchars($row['idGroupe']) ?></td>
                            <td><?= htmlspecialchars($row['nomGroupe']) ?></td>
                            <td><?= htmlspecialchars($row['idAudite']) ?></td>
                            <td><?= htmlspecialchars($row['idIdee']) ?></td>
                            <td><?= htmlspecialchars($row['ideeView']) ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p class="text-center">Aucune donnée disponible dans la base de données.</p>
        <?php endif; ?>
    </div>
    <!-- Footer -->
    <footer>
        <p>© 2025 Projet ARGILES | Université Grenoble Alpes</p>
        <p>Ce site est sous licence <a href="https://creativecommons.org/licenses/by-nc-sa/4.0/" target="_blank">Creative Commons BY-NC-SA 4.0</a>.</p>
        <p>Contactez le directeur du projet : 
            <a href="https://www.univ-grenoble-alpes.fr/thomas-lebarbe-538931.kjsp" target="_blank">Thomas Lebarbé</a>
        </p>
    </footer>
</body>
</html>
<?php
$conn->close();
?>



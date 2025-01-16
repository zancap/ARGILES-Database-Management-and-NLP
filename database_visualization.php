<?php
// Database connection configuration
$servername = "localhost";
$username = "zancanap";
$password = "@Tutideze15";
$dbname = "argiles";

// Connect to MySQL database
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle filters
$selectedOuvrage = $_GET['ouvrage'] ?? 'all';

// Build query based on selected table
if ($selectedOuvrage === 'RomeoJuliette') {
    $query = "SELECT * FROM RomeoJuliette";
    $title = "Base de données - Manga 'Roméo et Juliette'";
} elseif ($selectedOuvrage === 'Sirius') {
    $query = "SELECT * FROM Sirius";
    $title = "Base de données - Roman 'Sirius'";
} else {
    $query = "
        SELECT 'RomeoJuliette' AS ouvrage, idEtude, sujet, idQuestion, texteQuestion, type, idGroupe, nomGroupe, idAudite, idIdee, ideePreview 
        FROM RomeoJuliette
        UNION ALL
        SELECT 'Sirius' AS ouvrage, idEtude, sujet, idQuestion, texteQuestion, type, idGroupe, nomGroupe, idAudite, idIdee, ideePreview 
        FROM Sirius
    ";
    $title = "Base de données - Tous les ouvrages";
}

$result = $conn->query($query);
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
        /* Side Menu */
        .sidebar {
            width: 250px;
            position: fixed;
            top: 0;
            left: 0;
            height: 100%;
            background-color: #2c3e50;
            color: #fff;
            padding: 20px;
            box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
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
            background-color: #2c3e50;
            color: #fff;
            padding: 20px;
            text-align: center;
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
            border: 2px solid #88b097;
        }
        table th {
            background-color: #5a7d6e;
            color: #fff;
            padding: 10px;
        }
        table td {
            border: 1px solid #ddd;
            padding: 8px;
        }
        table td:first-child,
        table th:first-child {
            border-left: 2px solid #88b097;
        }
        table td:last-child,
        table th:last-child {
            border-right: 2px solid #88b097;
        }
        /* Footer */
        footer {
            background-color: #2c3e50;
            color: #fff;
            text-align: center;
            padding: 20px 0;
            margin-top: 20px;
        }
        footer a {
            color: #88b097;
            text-decoration: none;
        }
        footer a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <h4>Menu</h4>
        <a href="index.html">Accueil</a>
        <a href="database_visualization.php">Contenu de la base de données</a>
        <a href="pdf_visualization.php">Visualisation des copies d'élèves originales</a>
        <a href="upload.html">Téléversez des fichiers</a>
    </div>

    <!-- Header -->
    <header>
        <h1><?= htmlspecialchars($title) ?></h1>
    </header>

    <!-- Main Content -->
    <div class="container">
        <h2 class="text-center">Sélectionnez un ouvrage à visualiser</h2>

        <!-- Filters -->
        <form method="GET" class="mb-4 text-center">
            <label for="ouvrage" class="form-label">Filtrer par Ouvrage :</label>
            <select id="ouvrage" name="ouvrage" class="form-select d-inline-block w-auto mx-2">
                <option value="all" <?= $selectedOuvrage === 'all' ? 'selected' : '' ?>>Tous</option>
                <option value="Sirius" <?= $selectedOuvrage === 'Sirius' ? 'selected' : '' ?>>Roman "Sirius"</option>
                <option value="RomeoJuliette" <?= $selectedOuvrage === 'RomeoJuliette' ? 'selected' : '' ?>>Manga "Roméo et Juliette"</option>
            </select>
            <button type="submit" class="btn btn-primary">Appliquer</button>
        </form>

        <!-- Data Table -->
        <?php if ($result && $result->num_rows > 0): ?>
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
                        <th>Aperçu Idée</th>
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
                            <td><?= htmlspecialchars($row['ideePreview']) ?></td>
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



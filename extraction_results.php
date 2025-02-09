<?php
// Execute the Python script and capture JSON output
$output = shell_exec("python3 /data/www/html/argiles/extraction_info.py 2>&1");

// Decode JSON output (assuming the script prints JSON)
$data = json_decode($output, true);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Extraction des Données</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { font-family: 'Roboto', sans-serif; background-color: #f9f9f9; color: #333; padding: 20px; }
        table { width: 100%; border-collapse: collapse; background: #fff; }
        th, td { padding: 10px; border: 1px solid #ddd; text-align: left; }
        th { background-color: #5a7d6e; color: white; }
    </style>
</head>
<body>

<h2>Résultats d'Extraction des Données</h2>

<?php if ($data && isset($data["word_stats"])): ?>
    <h3>Statistiques des mots les plus fréquents</h3>
    <table>
        <tr><th>Mot</th><th>Occurences</th></tr>
        <?php foreach ($data["word_stats"] as $word => $count): ?>
            <tr><td><?= htmlspecialchars($word) ?></td><td><?= htmlspecialchars($count) ?></td></tr>
        <?php endforeach; ?>
    </table>
<?php else: ?>
    <p>Aucune donnée extraite.</p>
<?php endif; ?>

</body>
</html>

<?php
$participant_id = $_GET['id_audite'] ?? '';

// Only execute if an ID is provided
if (!empty($participant_id)) {
    // Execute the Python script with the given participant ID
    $output = shell_exec("python3 /data/www/html/argiles/get_audite.py " . escapeshellarg($participant_id) . " 2>&1");
    $responses = json_decode($output, true);
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Réponses du Participant</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { font-family: 'Roboto', sans-serif; background-color: #f9f9f9; color: #333; padding: 20px; }
        table { width: 100%; border-collapse: collapse; background: #fff; }
        th, td { padding: 10px; border: 1px solid #ddd; text-align: left; }
        th { background-color: #5a7d6e; color: white; }
    </style>
</head>
<body>

<h2>Réponses du Participant <?= htmlspecialchars($participant_id) ?></h2>

<?php if ($responses && isset($responses["responses"])): ?>
    <table>
        <tr><th>Question</th><th>Réponse</th></tr>
        <?php foreach ($responses["responses"] as $question => $answer): ?>
            <tr><td><?= htmlspecialchars($question) ?></td><td><?= htmlspecialchars($answer) ?></td></tr>
        <?php endforeach; ?>
    </table>
<?php else: ?>
    <p>Aucune réponse trouvée pour ce participant.</p>
<?php endif; ?>

</body>
</html>

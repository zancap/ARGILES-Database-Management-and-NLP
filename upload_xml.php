<?php
session_start();

// Redirect to login if the user is not authenticated
if (!isset($_SESSION["authenticated"]) || $_SESSION["authenticated"] !== true) {
    header("Location: login.php");
    exit();
}

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

// Create tables for Sirius and RomeoJuliette if they don't exist
$createTables = [
    "CREATE TABLE IF NOT EXISTS Sirius (
        id INT AUTO_INCREMENT PRIMARY KEY,
        idEtude INT,
        sujet VARCHAR(255),
        idQuestion INT,
        texteQuestion TEXT,
        type VARCHAR(50),
        idGroupe INT,
        nomGroupe VARCHAR(255),
        idAudite INT,
        idIdee INT,
        ideeView TEXT
    )",
    "CREATE TABLE IF NOT EXISTS RomeoJuliette (
        id INT AUTO_INCREMENT PRIMARY KEY,
        idEtude INT,
        sujet VARCHAR(255),
        idQuestion INT,
        texteQuestion TEXT,
        type VARCHAR(50),
        idGroupe INT,
        nomGroupe VARCHAR(255),
        idAudite INT,
        idIdee INT,
        ideeView TEXT
    )"
];

foreach ($createTables as $sql) {
    $conn->query($sql);
}

// Handle XML Upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['fileUpload'])) {
    $uploadedFile = $_FILES['fileUpload']['tmp_name'];
    $fileName = $_FILES['fileUpload']['name'];
    $fileExtension = pathinfo($fileName, PATHINFO_EXTENSION);

    if ($fileExtension === 'xml' && file_exists($uploadedFile)) {
        $xml = simplexml_load_file($uploadedFile);
        $idEtude = (int) $xml['idEtude'];
        $sujet = (string) $xml['sujet'];

        // Determine the target table based on the subject
        $targetTable = $sujet === "roman Sirius" ? "Sirius" : "RomeoJuliette";

        foreach ($xml->question as $question) {
            $idQuestion = (int) $question['idQuestion'];
            $texteQuestion = (string) $question['texteQuestion'];
            $type = (string) $question['type'];

            foreach ($question->groupe as $groupe) {
                $idGroupe = (int) $groupe['idGroupe'];
                $nomGroupe = (string) $groupe['nomGroupe'];

                foreach ($groupe->reponse as $reponse) {
                    $idAudite = (int) $reponse['idAudite'];

                    foreach ($reponse->idee as $idee) {
                        $idIdee = (int) $idee['idIdee'];
                        $ideeText = (string) $idee;
                        $ideeView = $ideeText;

                        // Insert data into the appropriate table
                        $stmt = $conn->prepare("
                            INSERT INTO $targetTable 
                            (idEtude, sujet, idQuestion, texteQuestion, type, idGroupe, nomGroupe, idAudite, idIdee, ideeView) 
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
                        ");
                        $stmt->bind_param(
                            "isississis",
                            $idEtude, $sujet, $idQuestion, $texteQuestion, $type,
                            $idGroupe, $nomGroupe, $idAudite, $idIdee, $ideeView
                        );
                        $stmt->execute();
                    }
                }
            }
        }

        // Redirect to a visualization page
        header("Location: database_visualization.php");
        exit();
    } else {
        echo "Fichier erroné ou non trouvé. Le fichier doit etre en format XML.";
    }
} else {
    echo "Le téléchargement a échoué.";
}

// Close the database connection
$conn->close();
?>

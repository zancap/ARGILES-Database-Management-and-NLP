<?php
session_start();

// Ensure correct encoding
header('Content-Type: text/html; charset=utf-8');

// Redirect to login page if not authenticated
if (!isset($_SESSION["authenticated"]) || $_SESSION["authenticated"] !== true) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload - Projet ARGILES</title>

    <!-- Bootstrap and Font Awesome -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <style>
        /* Background */
        body {
            font-family: 'Roboto', sans-serif;
            background: linear-gradient(160deg, #d8a7b1 10%, #2f5d62 90%);
            background-attachment: fixed;
            color: #333;
            margin: 0;
            padding: 0;
            position: relative;
            min-height: 100vh;
        }

        /* Header */
        header {
            background-color: #5a7d6e;
            color: #fff;
            padding: 40px 20px;
            border-radius: 8px;
            text-align: center;
            max-width: 900px;
            margin: 20px auto;
            position: relative;
        }

        /* Back Button */
        .btn-back {
            background-color: #fff;
            color: #5a7d6e;
            border: 2px solid #5a7d6e;
            padding: 10px 15px;
            border-radius: 5px;
            font-size: 14px;
            text-decoration: none;
            font-weight: bold;
            transition: background-color 0.3s ease-in-out, color 0.3s ease-in-out;
            display: inline-block;
            margin-top: 15px;
        }
        .btn-back:hover {
            background-color: #5a7d6e;
            color: #fff;
        }

        /* Upload Form */
        .upload-container {
            background: #fff;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 3px 3px 10px rgba(0, 0, 0, 0.15);
            text-align: center;
            max-width: 550px;
            margin: auto;
            margin-top: 50px;
        }

        /* Upload Box */
        .file-upload {
            display: flex;
            align-items: center;
            justify-content: center;
            border: 2px dashed #5a7d6e;
            background: #f9f9f9;
            padding: 15px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 16px;
            transition: background 0.3s ease-in-out;
            width: 100%;
        }
        .file-upload:hover {
            background: #e6e6e6;
        }
        .file-upload i {
            margin-right: 8px;
        }

        /* File Name Display */
        .selected-file {
            font-size: 14px;
            color: #333;
            margin-top: 10px;
            display: none; /* Initially hidden */
        }

        /* Upload Button */
        .btn-upload {
            background-color: #5a7d6e;
            color: #fff;
            border: none;
            padding: 12px 20px;
            border-radius: 5px;
            transition: background-color 0.3s ease-in-out;
            font-size: 16px;
            margin-top: 15px;
            width: 100%;
        }
        .btn-upload:hover {
            background-color: #4a665a;
        }

        /* File Naming Info */
        .file-info {
            font-size: 14px;
            color: #555;
            background: #f1f1f1;
            padding: 10px;
            border-radius: 8px;
            margin-top: 15px;
            box-shadow: 2px 2px 5px rgba(0,0,0,0.1);
            text-align: left;
        }

        /* Logout Button */
        .btn-logout {
            background-color: #c0392b;
            color: #fff;
            border: none;
            padding: 10px 15px;
            border-radius: 5px;
            text-decoration: none;
            transition: background-color 0.3s ease;
            display: block;
            text-align: center;
            margin-top: 20px;
            width: 100%;
        }
        .btn-logout:hover {
            background-color: #a93226;
        }

        /* Footer */
        footer {
            background-color: #2c3e50;
            color: #fff;
            text-align: center;
            padding: 40px 20px;
            border-radius: 8px;
            max-width: 900px;
            margin: 20px auto;
        }
        footer a {
            color: #88b097;
            text-decoration: none;
        }
        footer a:hover {
            text-decoration: underline;
        }

        /* Wave Decoration */
        .wave {
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 120px;
            background: url('img/wave.svg') repeat-x;
            opacity: 0.3;
            z-index: 1;
        }
    </style>
</head>
<body>

    <!-- Header -->
    <header>
        <h1>Téléversement des fichiers XML</h1>
        <p>Utilisez cette page pour ajouter des données au projet ARGILES.</p>
        <a href="index.html" class="btn-back">← Retour à l'accueil</a>
    </header>

    <!-- Upload Form -->
    <div class="upload-container">
        <form action="upload_xml.php" method="post" enctype="multipart/form-data">
            <label for="fileUpload" class="file-upload"><i class="fas fa-file-upload"></i> Sélectionnez un fichier XML</label>
            <input type="file" id="fileUpload" name="fileUpload" accept=".xml" required style="display: none;">
            
            <!-- Display the selected file name -->
            <p id="selected-file" class="selected-file"></p>

            <button type="submit" class="btn-upload">Téléverser</button>
        </form>

        <div class="file-info">
            <p><strong>Format du nom du fichier :</strong></p>
            <p><em>NOMCLASSE_ETC_TitreOuvrage.xml</em></p>
            <p><strong>Exemples :</strong> <br> <em>4-G1_4-G2_Sirius.xml</em> <br> <em>CE1_CE2_CM1_CM2_RomeoJuliette.xml</em></p>
        </div>

        <a href="login.php" class="btn-logout">Se déconnecter</a>
    </div>

    <!-- Footer -->
    <footer>
        <p>© 2025 Projet ARGILES | Université Grenoble Alpes</p>
        <p>Ce site est sous licence <a href="https://creativecommons.org/licenses/by-nc-sa/4.0/" target="_blank">Creative Commons BY-NC-SA 4.0</a>.</p>
        <p>Contactez le directeur du projet : 
            <a href="https://www.univ-grenoble-alpes.fr/thomas-lebarbe-538931.kjsp" target="_blank">Thomas Lebarbé</a>
        </p>
    </footer>

    <!-- Wave Decoration -->
    <div class="wave"></div>

    <!-- JavaScript to Display Selected File Name -->
    <script>
        document.getElementById("fileUpload").addEventListener("change", function() {
            let fileName = this.files[0] ? this.files[0].name : "";
            let fileDisplay = document.getElementById("selected-file");

            if (fileName) {
                fileDisplay.textContent = "Fichier sélectionné : " + fileName;
                fileDisplay.style.display = "block"; 
            } else {
                fileDisplay.style.display = "none"; 
            }
        });
    </script>

</body>
</html>



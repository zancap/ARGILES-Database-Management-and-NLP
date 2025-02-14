<?php
session_start();

// Define valid username and password
$valid_username = "argiles";
$valid_password = "#ProjetProIDL2425";

// If form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"];
    $password = $_POST["password"];

    // Verify credentials
    if ($username === $valid_username && $password === $valid_password) {
        $_SESSION["authenticated"] = true; // Store authentication state
        header("Location: upload.php"); // Redirect to upload page
        exit();
    } else {
        $error_message = "Identifiants incorrects. Veuillez réessayer.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - Projet ARGILES</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script> <!-- Eye icon -->
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
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }

        /* Readability */
        body::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.4);
            z-index: -1;
        }

        /* Login Box */
        .login-container {
            background: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 2px 2px 10px rgba(0,0,0,0.2);
            max-width: 400px;
            width: 100%;
            text-align: center;
        }

        /* Input Field */
        .password-container {
            position: relative;
        }

        .password-container input {
            width: 100%;
            padding-right: 40px; /* Make space for eye icon */
        }

        /* Eye Button */
        .toggle-password {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            cursor: pointer;
        }

        /* Buttons */
        .btn-login {
            background-color: #5a7d6e;
            color: #fff;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            transition: background-color 0.3s ease;
            width: 100%;
        }
        .btn-login:hover {
            background-color: #4a665a;
        }

        .btn-back {
            background-color: #2c3e50;
            color: #fff;
            border: none;
            padding: 10px 15px;
            border-radius: 5px;
            text-decoration: none;
            transition: background-color 0.3s ease;
        }
        .btn-back:hover {
            background-color: #1e2b38;
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
    <h1>Connexion au Projet ARGILES</h1>
    <p>Accédez à l’espace sécurisé pour téléverser des fichiers XML.</p>
    <a href="index.html" class="btn-back">← Retour à l'accueil</a>
</header>

<!-- Login Form -->
<div class="main-content">
    <div class="login-container">
        <h2>Connexion</h2>
        <?php if (isset($error_message)): ?>
            <p style="color: red;"><?= $error_message ?></p>
        <?php endif; ?>
        <form method="POST">
            <div class="mb-3">
                <label for="username" class="form-label">Nom d'utilisateur</label>
                <input type="text" id="username" name="username" class="form-control" required>
            </div>
            <div class="mb-3 password-container">
                <label for="password" class="form-label">Mot de passe</label>
                <input type="password" id="password" name="password" class="form-control" required>
                <button type="button" class="toggle-password" onclick="togglePassword()">
                    <i class="fas fa-eye"></i>
                </button>
            </div>
            <button type="submit" class="btn-login">Se connecter</button>
        </form>
    </div>
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

<!-- Password Toggle Script -->
<script>
    function togglePassword() {
        let passwordField = document.getElementById("password");
        let toggleButton = document.querySelector(".toggle-password i");
        if (passwordField.type === "password") {
            passwordField.type = "text";
            toggleButton.classList.remove("fa-eye");
            toggleButton.classList.add("fa-eye-slash");
        } else {
            passwordField.type = "password";
            toggleButton.classList.remove("fa-eye-slash");
            toggleButton.classList.add("fa-eye");
        }
    }
</script>

</body>
</html>
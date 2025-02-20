<?php
include_once("connexion.php");

// Define admin credentials
$username = "argiles";  
$password = "#ProjetProIDL2425"; 

// Hash the password securely
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// Prepare and execute SQL query to insert user
$sql = "INSERT INTO users (username, password_hash) VALUES (:username, :password_hash)";
$stmt = $db->prepare($sql);

try {
    $stmt->execute(['username' => $username, 'password_hash' => $hashed_password]);
    echo "✅ Utilisateur-administrateur ajouté avec succès !";
} catch (PDOException $e) {
    echo "❌ Erreur : " . $e->getMessage();
}
?>
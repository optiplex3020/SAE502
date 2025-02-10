<?php
$servername = "db";
$username = "root";
$password = "vitrygtr";
$dbname = "auth_db";

// Connexion à la base de données
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Récupérer les données du formulaire
$username = $_POST['username'];
$password = $_POST['password'];

// Vérifier l'utilisateur dans la base de données
$stmt = $conn->prepare("SELECT password_hash FROM UserAccounts WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$stmt->store_result();
$stmt->bind_result($password_hash);

if ($stmt->fetch() && password_verify($password, $password_hash)) {
    echo "Connexion réussie !";
} else {
    echo "Nom d'utilisateur ou mot de passe incorrect.";
}

$stmt->close();
$conn->close();
?>
<?php
header('Content-Type: application/json'); // Indique que la réponse est en JSON

$servername = "db";
$username = "root";
$password = "vitrygtr";
$dbname = "auth_db";

// Connexion à la base de données
$conn = new mysqli($servername, $username, $password, $dbname);

// Vérifier la connexion
if ($conn->connect_error) {
    die(json_encode(["status" => "error", "message" => "Connection failed: " . $conn->connect_error]));
}

// Récupérer les données du formulaire
$username = $_POST['username'];
$password = $_POST['password'];

// Validation du mot de passe côté serveur
$passwordRegex = '/^(?=.*[A-Z])(?=.*\d).{8,}$/'; // 8 caractères, une majuscule, un chiffre
if (!preg_match($passwordRegex, $password)) {
    echo json_encode(["status" => "error", "message" => "Le mot de passe doit contenir au moins 8 caractères, une majuscule et un chiffre."]);
    exit; // Arrête l'exécution du script
}

// Vérifier si l'utilisateur existe déjà
$check_user = $conn->prepare("SELECT id FROM UserAccounts WHERE username = ?");
$check_user->bind_param("s", $username);
$check_user->execute();
$check_user->store_result();

if ($check_user->num_rows > 0) {
    // L'utilisateur existe déjà
    echo json_encode(["status" => "error", "message" => "L'utilisateur existe déjà."]);
} else {
    // Hacher le mot de passe
    $password_hash = password_hash($password, PASSWORD_DEFAULT);

    // Insérer l'utilisateur dans la base de données
    $stmt = $conn->prepare("INSERT INTO UserAccounts (username, password_hash) VALUES (?, ?)");
    $stmt->bind_param("ss", $username, $password_hash);

    if ($stmt->execute()) {
        // Enregistrement réussi
        echo json_encode(["status" => "success", "message" => "Enregistrement réussi !"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Erreur : " . $stmt->error]);
    }

    $stmt->close();
}

$check_user->close();
$conn->close();
?>
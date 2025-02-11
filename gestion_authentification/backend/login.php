<?php
session_start(); // Démarrer la session

header('Content-Type: application/json'); // Indique que la réponse est en JSON

$servername = "db"; // Nom du service Docker pour MySQL
$username = "root";
$password = "vitrygtr";
$dbname = "auth_db";

// Connexion à la base de données
$conn = new mysqli($servername, $username, $password, $dbname);

// Vérifier la connexion
if ($conn->connect_error) {
    echo json_encode(["status" => "error", "message" => "Erreur de connexion à la base de données"]);
    exit();
}

// Vérifier si la requête est en JSON ou en POST standard
$data = json_decode(file_get_contents("php://input"), true);
if (!$data) {
    $data = $_POST; // Si JSON non trouvé, essayer avec $_POST
}

if (!isset($data['username']) || !isset($data['password'])) {
    echo json_encode(["status" => "error", "message" => "Veuillez remplir tous les champs"]);
    exit();
}

$username = trim($data['username']);
$password = $data['password'];

// Vérifier l'utilisateur dans la base de données
$stmt = $conn->prepare("SELECT id, password_hash FROM UserAccounts WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$stmt->store_result();
$stmt->bind_result($user_id, $password_hash);

if ($stmt->fetch()) {
    if (password_verify($password, $password_hash)) {
        // Connexion réussie
        $_SESSION['user_id'] = $user_id;
        $_SESSION['username'] = $username;

        echo json_encode(["status" => "success", "message" => "Connexion réussie"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Mot de passe incorrect"]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Nom d'utilisateur incorrect"]);
}

$stmt->close();
$conn->close();
?>

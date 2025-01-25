<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupérer les données du formulaire
    $username = $_POST['username'] ?? '';
    $lastname = $_POST['lastname'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    // Vérification des champs obligatoires
    if (empty($username) || empty($lastname) || empty($email) || empty($password)) {
        die("Erreur : Tous les champs sont obligatoires.");
    }

    // Hachage du mot de passe
    $hashedPassword = '{SSHA}' . base64_encode(hash('sha1', $password . random_bytes(8), true) . random_bytes(8));

    // Simulation de connexion à LDAP (remplacez par une vraie connexion si nécessaire)
    echo "Nom d'utilisateur : $username<br>";
    echo "Nom de famille : $lastname<br>";
    echo "Email : $email<br>";
    echo "Mot de passe haché : $hashedPassword<br>";
    echo "Les données sont prêtes pour être envoyées à LDAP.<br>";
}
?>

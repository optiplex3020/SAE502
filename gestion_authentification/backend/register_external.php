<?php
header('Content-Type: application/json');

$ldap_host = "ldap://ldap_server";
$ldap_port = 389;
$ldap_dn = "dc=mycompany,dc=com";
$ldap_admin = "cn=admin,$ldap_dn";
$ldap_password = "admin";

// Récupération des données POST
$username = $_POST['username'] ?? '';
$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';

if (empty($username) || empty($email) || empty($password)) {
    echo json_encode(["status" => "error", "message" => "Tous les champs sont requis"]);
    exit();
}

// Vérification du mot de passe
if (!preg_match('/^(?=.*[A-Z])(?=.*\d).{8,}$/', $password)) {
    echo json_encode(["status" => "error", "message" => "Le mot de passe doit contenir au moins 8 caractères, une majuscule et un chiffre."]);
    exit();
}

// Hash du mot de passe pour LDAP
$hashed_password = "{SHA}" . base64_encode(sha1($password, true));

$ldap_conn = ldap_connect($ldap_host, $ldap_port);
ldap_set_option($ldap_conn, LDAP_OPT_PROTOCOL_VERSION, 3);
ldap_bind($ldap_conn, $ldap_admin, $ldap_password);

$dn = "uid=$username,ou=users,$ldap_dn";
// Génération d'un UID unique
$uidNumber = rand(10000, 99999);  // ID unique pour chaque utilisateur LDAP
$gidNumber = "1000";  // Groupe par défaut
$homeDirectory = "/home/$username";
$loginShell = "/bin/bash";

$entry = [
    "uid" => $username,
    "cn" => $username,
    "sn" => "Utilisateur",
    "mail" => $email,
    "objectClass" => ["inetOrgPerson", "posixAccount", "shadowAccount"],
    "userPassword" => $hashed_password,
    "uidNumber" => $uidNumber,
    "gidNumber" => $gidNumber,
    "homeDirectory" => $homeDirectory,
    "loginShell" => $loginShell
];

if (ldap_add($ldap_conn, $dn, $entry)) {
    echo json_encode(["status" => "success", "message" => "Inscription réussie. Vérifiez votre e-mail."]);
} else {
    echo json_encode(["status" => "error", "message" => "Erreur lors de l'ajout à LDAP"]);
}

// Envoi de l'email
$to = $email;
$subject = "Confirmation d'inscription";
$message = "Bonjour $username,\n\nVotre compte a bien été créé.\nMerci de votre inscription.";
$headers = "From: noreply@mycompany.com";

mail($to, $subject, $message, $headers);

if (!ldap_add($ldap_conn, $dn, $entry)) {
    $error = ldap_error($ldap_conn);
    error_log("Erreur LDAP lors de l'inscription : " . $error);
    echo json_encode(["status" => "error", "message" => "Erreur lors de l'ajout à LDAP : " . $error]);
} else {
    echo json_encode(["status" => "success", "message" => "Inscription réussie. Vérifiez votre e-mail."]);
}

ldap_close($ldap_conn);
?>
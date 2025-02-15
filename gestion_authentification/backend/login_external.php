<?php
session_start();
header('Content-Type: application/json');

$ldap_host = "ldap://ldap_server";
$ldap_dn = "dc=mycompany,dc=com";

$username = $_POST['username'] ?? '';
$password = $_POST['password'] ?? '';

if (empty($username) || empty($password)) {
    echo json_encode(["status" => "error", "message" => "Tous les champs sont requis"]);
    exit();
}

// Connexion au serveur LDAP
$ldap_conn = ldap_connect($ldap_host);
ldap_set_option($ldap_conn, LDAP_OPT_PROTOCOL_VERSION, 3);
$dn = "uid=$username,ou=users,$ldap_dn";

if (@ldap_bind($ldap_conn, $dn, $password)) {
    $_SESSION['user_id'] = $username;
    $_SESSION['username'] = $username;
    echo json_encode(["status" => "success", "message" => "Connexion réussie"]);
} else {
    echo json_encode(["status" => "error", "message" => "Nom d'utilisateur ou mot de passe incorrect"]);
}

ldap_close($ldap_conn);
?>
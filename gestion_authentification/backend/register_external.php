<?php
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 1);

$ldap_host = "ldap://ldap_server";
$ldap_port = 389;
$ldap_dn = "dc=mycompany,dc=com";
$ldap_admin = "cn=admin,$ldap_dn";
$ldap_password = "admin";

$keycloak_url = "http://keycloak:8080";
$keycloak_realm = "WebApp";
$keycloak_admin_user = "admin"; // Identifiant Keycloak admin
$keycloak_admin_password = "admin"; // Mot de passe Keycloak admin
$keycloak_client_id = "admin-cli";

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
$uidNumber = rand(10000, 99999);
$gidNumber = "1000";
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
    // 🔥 Étape 2 : Ajouter l'utilisateur à Keycloak
    $keycloak_token = getKeycloakAdminToken($keycloak_url, $keycloak_admin_user, $keycloak_admin_password, $keycloak_client_id, $keycloak_realm);

    if ($keycloak_token) {
        $keycloak_user_id = createKeycloakUser($keycloak_url, $keycloak_realm, $keycloak_token, $username, $email, $password);

        if ($keycloak_user_id) {
            echo json_encode(["status" => "success", "message" => "Inscription réussie. Vérifiez votre e-mail."]);
        } else {
            echo json_encode(["status" => "error", "message" => "Utilisateur ajouté à LDAP, mais échec dans Keycloak"]);
        }
    } else {
        echo json_encode(["status" => "error", "message" => "Impossible de récupérer un token Keycloak"]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Erreur lors de l'ajout à LDAP"]);
}

ldap_close($ldap_conn);

// 🔥 Fonction pour récupérer un token d'administration Keycloak
function getKeycloakAdminToken($url, $admin_user, $admin_password, $client_id, $realm) {
    $token_url = "$url/realms/master/protocol/openid-connect/token";
    
    $data = http_build_query([
        "grant_type" => "password",
        "client_id" => $client_id,
        "username" => $admin_user,
        "password" => $admin_password
    ]);

    $options = [
        "http" => [
            "header"  => "Content-Type: application/x-www-form-urlencoded",
            "method"  => "POST",
            "content" => $data
        ]
    ];

    $context  = stream_context_create($options);
    $result = file_get_contents($token_url, false, $context);

    if ($result === FALSE) return false;

    $response = json_decode($result, true);
    return $response["access_token"] ?? false;
}

// 🔥 Fonction pour créer un utilisateur dans Keycloak
function createKeycloakUser($url, $realm, $token, $username, $email, $password) {
    $user_url = "$url/admin/realms/$realm/users";

    $user_data = [
        "username" => $username,
        "email" => $email,
        "enabled" => true,
        "credentials" => [
            [
                "type" => "password",
                "value" => $password,
                "temporary" => false
            ]
        ]
    ];

    $options = [
        "http" => [
            "header"  => "Content-Type: application/json\r\n" .
                         "Authorization: Bearer $token\r\n",
            "method"  => "POST",
            "content" => json_encode($user_data)
        ]
    ];

    $context  = stream_context_create($options);
    $result = file_get_contents($user_url, false, $context);

    return $result !== FALSE;
}
?>
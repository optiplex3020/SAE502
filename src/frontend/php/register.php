<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    if (empty($username) || empty($password)) {
        die("Erreur : Tous les champs sont obligatoires.");
    }

    if (!preg_match("/^[a-zA-Z0-9]+$/", $username)) {
        die("Erreur : Le nom d'utilisateur ne doit contenir que des lettres et des chiffres.");
    }

    // Connexion à LDAP
    $ldapConn = ldap_connect("ldap://localhost", 389);
    ldap_set_option($ldapConn, LDAP_OPT_PROTOCOL_VERSION, 3);

    if (!$ldapConn) {
        die("Erreur : Impossible de se connecter à LDAP.");
    }

    // Authentification admin LDAP
    $ldapAdminDn = "cn=admin,dc=u-pec,dc=fr";
    $ldapAdminPassword = "VotreMotDePasseAdmin";

    if (!ldap_bind($ldapConn, $ldapAdminDn, $ldapAdminPassword)) {
        die("Erreur : Impossible de s'authentifier avec le compte admin. " . ldap_error($ldapConn));
    }

    // Préparation des données utilisateur
    $dn = "cn=$username,ou=iutcv,dc=u-pec,dc=fr";
    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
    $entry = [
        "cn" => $username,
        "sn" => $username,
        "userPassword" => $hashedPassword,
        "objectClass" => ["inetOrgPerson", "top"]
    ];

    // Ajout de l'utilisateur
    if (ldap_add($ldapConn, $dn, $entry)) {
        echo "Utilisateur $username ajouté avec succès.";
    } else {
        echo "Erreur : Impossible d'ajouter l'utilisateur. Détails : " . ldap_error($ldapConn);
    }

    ldap_close($ldapConn);
} else {
    echo "Erreur : Aucune donnée reçue.";
}
?>


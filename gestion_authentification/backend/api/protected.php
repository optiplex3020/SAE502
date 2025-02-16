<?php
require 'vendor/autoload.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

header("Content-Type: application/json");

// Clé publique Keycloak
$publicKey = <<<EOD
-----BEGIN PUBLIC KEY-----
MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEA...
-----END PUBLIC KEY-----
EOD;

// Récupération du Token envoyé par le frontend
$headers = apache_request_headers();
$authHeader = isset($headers['Authorization']) ? $headers['Authorization'] : '';

if (!$authHeader || !preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
    echo json_encode(["error" => "Token manquant"]);
    http_response_code(401);
    exit();
}

$token = $matches[1];

try {
    // Vérification du Token JWT
    $decoded = JWT::decode($token, new Key($publicKey, 'RS256'));
// Vérification des rôles
$roles = $decoded->realm_access->roles;

if (!in_array("admin", $roles)) {
    echo json_encode(["error" => "Accès refusé"]);
    http_response_code(403);
    exit();
} else {
    echo json_encode([
        "message" => "Accès autorisé",
        "user" => $decoded->preferred_username,
        "roles" => $decoded->realm_access->roles
    ]);

} catch (Exception $e) {
    echo json_encode(["error" => "Token invalide"]);
    http_response_code(401);
}
<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    // Rediriger vers la page de connexion si l'utilisateur n'est pas connecté
    header("Location: /frontend/login.html");
    exit();
}
?>
<?php
session_start(); // Démarrer la session
session_destroy(); // Détruire la session
header("Location: /login.html"); // Rediriger vers la page de connexion
exit();
?>
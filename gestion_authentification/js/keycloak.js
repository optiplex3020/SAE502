const keycloak = new Keycloak({
    url: "http://localhost:8083",
    realm: "WebApp",
    clientId: "webapp-client"
});

// Initialisation de Keycloak
keycloak.init({ onLoad: 'login-required' }).then(authenticated => {
    if (authenticated) {
        console.log("Utilisateur authentifié :", keycloak.tokenParsed);
        sessionStorage.setItem("kc_token", keycloak.token);  // Stockage temporaire du Token JWT
        sessionStorage.setItem("kc_refresh_token", keycloak.refreshToken);
    } else {
        console.log("Utilisateur non authentifié, redirection en cours...");
        keycloak.login();
    }
}).catch(error => {
    console.error("Erreur Keycloak :", error);
});

// Fonction pour récupérer le Token JWT
function getToken() {
    return sessionStorage.getItem("kc_token");
}

// Fonction de déconnexion
function logout() {
    keycloak.logout();
}

// Rafraîchir le Token d'accès toutes les 60 secondes
setInterval(() => {
    keycloak.updateToken(60).then(refreshed => {
        if (refreshed) {
            console.log("Token Keycloak rafraîchi !");
            sessionStorage.setItem("kc_token", keycloak.token);
        }
    }).catch(() => {
        console.error("Impossible de rafraîchir le token, redirection vers la connexion...");
        keycloak.login();
    });
}, 60000);
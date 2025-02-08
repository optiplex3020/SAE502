document.getElementById("registerForm").addEventListener("submit", function(event) {
    const password = document.getElementById("password").value;
    const errorMessage = document.getElementById("errorMessage");

    // Vérification des règles pour le mot de passe
    const passwordRegex = /^(?=.*[A-Z])(?=.*\d)[A-Za-z\d]{8,}$/;

    if (!passwordRegex.test(password)) {
        errorMessage.textContent = "Le mot de passe doit contenir au moins 8 caractères, une majuscule et un chiffre.";
        event.preventDefault(); // Empêche l'envoi du formulaire si la validation échoue
    } else {
        errorMessage.textContent = ""; // Aucun message d'erreur si la validation passe
    }
});

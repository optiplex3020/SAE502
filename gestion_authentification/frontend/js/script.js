document.addEventListener('DOMContentLoaded', function () {
    // Sélectionnez le formulaire d'enregistrement
    const registerForm = document.querySelector('form[action="/backend/register.php"]');

    if (registerForm) {
        registerForm.addEventListener('submit', function (event) {
            const password = document.getElementById('password').value;
            const regex = /^(?=.*[A-Z])(?=.*\d).{8,}$/;

            if (!regex.test(password)) {
                alert("Le mot de passe doit contenir 8 caractères, une majuscule et un chiffre.");
                event.preventDefault(); // Empêche l'envoi du formulaire
            }
        });
    }
});
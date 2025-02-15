document.addEventListener('DOMContentLoaded', function () {
    function handleFormSubmission(form, url, redirectUrl) {
        form.addEventListener('submit', function (event) {
            event.preventDefault(); // Empêche la soumission par défaut

            const formData = new FormData(form);
            const formDataEncoded = new URLSearchParams();
            for (const [key, value] of formData.entries()) {
                formDataEncoded.append(key, value);
            }
            
            const messageDiv = document.getElementById('message');

            fetch(url, {
                method: 'POST',
                headers: { "Content-Type": "application/x-www-form-urlencoded" },
                body: formDataEncoded
            })
            .then(response => response.json())
            .then(data => {
                console.log(data); // Debug : voir la réponse dans la console
                messageDiv.textContent = data.message;
                messageDiv.classList.toggle('success', data.status === 'success');
                messageDiv.classList.toggle('error', data.status !== 'success');

                if (data.status === 'success' && redirectUrl) {
                    setTimeout(() => {
                        window.location.href = redirectUrl;
                    }, 2000);
                }
            })
            .catch(error => {
                console.error('Erreur :', error);
            });
        });
    }

    // Formulaire d'inscription
    const registerForm = document.querySelector('form[action="/backend/register.php"]');
    if (registerForm) {
        handleFormSubmission(registerForm, '/backend/register.php', '/login.html');
    }

    // Formulaire de connexion
    const loginForm = document.getElementById('loginForm');
    if (loginForm) {
        handleFormSubmission(loginForm, '/backend/login.php', '/home.html');
    }
});
document.addEventListener('DOMContentLoaded', function () {
    const registerForm = document.getElementById('registerExternalForm');
    if (registerForm) {
        registerForm.addEventListener('submit', function (event) {
            event.preventDefault(); // Empêche la soumission classique du formulaire

            const formData = new FormData(registerForm);

            fetch('/backend/register_external.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                console.log("Réponse serveur :", data);
                alert(data.message);
                if (data.status === 'success') {
                    setTimeout(() => {
                        window.location.href = "/login.html";
                    }, 2000);
                }
            })
            .catch(error => {
                console.error('Erreur lors de l’inscription :', error);
            });
        });
    }
});

document.addEventListener('DOMContentLoaded', function () {
    const loginExternalForm = document.getElementById('loginExternalForm');
    if (loginExternalForm) {
        loginExternalForm.addEventListener('submit', function (event) {
            event.preventDefault(); // Empêche la soumission classique du formulaire

            const formData = new FormData(loginExternalForm);

            fetch('/backend/login_external.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                console.log("Réponse serveur :", data);
                alert(data.message);
                if (data.status === 'success') {
                    setTimeout(() => {
                        window.location.href = "/home.html";
                    }, 2000);
                }
            })
            .catch(error => {
                console.error('Erreur lors de la connexion :', error);
            });
        });
    }
});
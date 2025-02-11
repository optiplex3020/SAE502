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

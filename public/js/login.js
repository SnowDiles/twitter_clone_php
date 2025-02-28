document.addEventListener('DOMContentLoaded', function() {
    let modal = document.getElementById('modal');
    let modalContainer = document.getElementById("modal-container");
    let closeButton = document.getElementById("close-button");
    let modalTitle = document.getElementById("modal-title");
    let nameInput = document.getElementById("name-input");
    let pseudoInput = document.getElementById("username-input");
    let confirmPassword = document.getElementById("confirm-password");
    let loginBtn = document.getElementById("login-button");
    let alreadyBtn = document.getElementById("existing-account-button");

    document.getElementById("register-open-modal").addEventListener('click', function() {
        modalContainer.style.display = "";
        modalTitle.innerHTML = "Inscrivez-vous !";
        nameInput.style.display = "";
        nameInput.required = true;
        pseudoInput.style.display = "inline-block";
        pseudoInput.required = true;
        loginBtn.innerHTML = "Inscription";
        alreadyBtn.innerHTML = "Déjà un compte ?";
        confirmPassword.style.visibility = "";
        confirmPassword.required = true;
    });

    document.getElementById("login-open-modal").addEventListener('click', function() {
        modalContainer.style.display = "";
        modalTitle.innerHTML = "Connectez-vous";
        pseudoInput.style.display = "none";
        pseudoInput.required = false;
        nameInput.style.display = "none";
        nameInput.required = false;
        loginBtn.innerHTML = "Connexion";       
        alreadyBtn.innerHTML = "Mot de passe oublié ?";
        confirmPassword.style.visibility = "hidden";
        confirmPassword.required = false;
    });

    modalContainer.addEventListener("click", function(event) {
        if (event.target === modalContainer) {
            modalContainer.style.display = "none";
        }
    });
    closeButton.addEventListener("click",function(){
        modalContainer.style.display = "none";
    })
    document.addEventListener('keydown', function(event) {
        if (event.key === "Escape") {
            modalContainer.style.display = "none";
        }
    });
});
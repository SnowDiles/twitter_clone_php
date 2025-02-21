document.addEventListener('DOMContentLoaded', function() {
    let modal = document.getElementById('modal');
    let buttons = main.getElementsByTagName('button');
    let modalContainer = document.getElementById("ModalContainer");
    let closeButton = document.getElementById("closeButton");
    let modalTitle = document.getElementById("modalTitle");
    let nameInput = document.getElementById("nameInput");
    let pseudoInput = document.getElementById("pseudoInput");
    let confirmPassword = document.getElementById("confirmPassword");
    let loginBtn = document.getElementById("loginBtn");
    let alreadyBtn = document.getElementById("alreadyBtn");

    alreadyBtn.addEventListener('click', function() {
        modal.style.display = "flex";
        modalContainer.style.display = "block";
        modalTitle.innerHTML = "Connectez-vous";
        pseudoInput.style.display = "none";
        pseudoInput.required = false;
        nameInput.style.display = "none";
        nameInput.required = false;
        confirmPassword.style.visibility = "hidden";
        confirmPassword.required = false;
        loginBtn.innerHTML = "Connexion";
        alreadyBtn.innerHTML = "Mot de passe oublié ?";
    });
    document.getElementById("registerOpenModal").addEventListener('click', function() {
        modal.style.display = "flex";
        modalContainer.style.display = "block";
        modalTitle.innerHTML = "Inscrivez-vous !";
        nameInput.style.display = "inline-block";
        nameInput.required = true;
        pseudoInput.style.display = "inline-block";
        pseudoInput.required = true;
        loginBtn.innerHTML = "Inscription";
        alreadyBtn.innerHTML = "Déjà un compte ?";
        confirmPassword.style.visibility = "visible";
        confirmPassword.required = true;
    });

    document.getElementById("connexionOpenModal").addEventListener('click', function() {
        modal.style.display = "flex";
        modalContainer.style.display = "block";
        modalTitle.innerHTML = "Connectez-vous";
        alreadyBtn.innerHTML = "Mot de passe oublié ?";
        pseudoInput.style.display = "none";
        pseudoInput.required = false;
        nameInput.style.display = "none";
        nameInput.required = false;
        loginBtn.innerHTML = "Connexion";
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
document.addEventListener('DOMContentLoaded', function() {
    let modal = document.getElementById('modal');
    let buttons = main.getElementsByTagName('button');
    let modalContainer = document.getElementById("ModalContainer");
    let closeButton = document.getElementById("closeButton");
    let modalTitle = document.getElementById("modalTitle");
    
    document.getElementById("registerOpenModal").addEventListener('click', function() {
        modal.style.display = "flex";
        modalContainer.style.display = "block";
        modalTitle.innerHTML = "Inscrivez-vous !";
        
    });

    document.getElementById("connexionOpenModal").addEventListener('click', function() {
        modal.style.display = "flex";
        modalContainer.style.display = "block";
        modalTitle.innerHTML = "Connectez-vous";
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
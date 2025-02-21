document.addEventListener('DOMContentLoaded', function() {
    let modal = document.getElementById('modal');
    let buttons = main.getElementsByTagName('button');
    let modalContainer = document.getElementById("ModalContainer");
    let closeButton = document.getElementById("closeButton");
    document.getElementById("register-open-modal").addEventListener('click', function() {
        modal.style.display = "flex";
        modalContainer.style.display = "block";
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
export class backToTop {

    constructor(backToTopButton, scrollableContainer) {
        this.backToTopButton = backToTopButton;
        this.scrollableContainer = scrollableContainer;
    }

    handlebackToTop() {
        const backToTopButton = document.getElementById(this.backToTopButton);
        const scrollableContainer = document.getElementById(this.scrollableContainer);
        backToTopButton.style.display = "none";
        scrollableContainer.addEventListener("scroll", function () {
            if (scrollableContainer.scrollTop > 5000) {
                backToTopButton.style.display = "block";
            } else {
                backToTopButton.style.display = "none";
            }
        });
        backToTopButton.addEventListener("click", function (e) {
            e.preventDefault();
            scrollableContainer.scrollTo({
                top: 0,
                behavior: "smooth",
            });
        });
    }

    init() {
        this.handlebackToTop();
    }
}











 
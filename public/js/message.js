const createConversationButton = document.getElementById("create-conversation-button");
const promptBackground = document.getElementById("prompt-background");
const closeButton = document.getElementById("close-button");

const hidePrompt = () => {
    promptBackground.classList.add("hidden");
}

createConversationButton.onclick = _ => {
    promptBackground.classList.remove("hidden");
};

closeButton.onclick = hidePrompt;
promptBackground.onclick = event => {
    if (event.target === promptBackground) {
        hidePrompt();
    }
}
document.addEventListener('keydown', (event) => {
    if (event.key === "Escape") {
        hidePrompt();
    }
});
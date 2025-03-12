const createConversationButton = document.getElementById("create-conversation-button");
const promptBackground = document.getElementById("prompt-background");
const closeButton = document.getElementById("close-button");

const sendButton = document.getElementById("send-button");
const receiverField = document.getElementById("receiver-field");
const contentField = document.getElementById("message-content-field");

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

sendButton.onclick = _ => {
    if (!receiverField.value.length) {
        alert("Veuillez entrer un nom d'utilisateur");
        return;
    }
    if (!contentField.value.length) {
        alert("Ce message est vide");
        return;
    }
    sendMessage(receiverField.value, contentField.value);
}

const sendMessage = async (receiver, content) => {
    const formData = new FormData();
    formData.append("action", "sendMessage");
    formData.append("receiver", receiver);
    formData.append("content", content);

    try {
        const response = await fetch("../../src/Controllers/MessageController.php", {
          method: "POST",
          headers: {
            "X-Requested-With": "XMLHttpRequest",
          },
          body: formData,
        });
        const responseData = await response.json();
        if (!responseData.success) {
            alert(responseData.message);
        }
      } catch (error) {
        console.error("Error while sending message:", error);
      }
}
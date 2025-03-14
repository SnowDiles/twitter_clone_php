import { handleAutoCompletion } from "./autoCompletion.js";

const createConversationButton = document.getElementById("create-conversation-button");
const promptBackground = document.getElementById("prompt-background");
const closeButton = document.getElementById("close-button");

const messageInput = document.getElementById('message-input');
const sendMessageBtn = document.getElementById('send-message-btn');

const sendButton = document.getElementById("send-button");
const receiverField = document.getElementById("receiver-field");
const contentField = document.getElementById("message-content-field");

const feed = document.getElementById("message-feed");
const conversationsContainer = document.querySelector('.w-full.p-4.flex.flex-col.gap-5');

let currentReceiverId = null;

const setCurrentReceiver = (userId) => {
    currentReceiverId = userId;
};

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

    const receiver = receiverField.value.startsWith('@') ? receiverField.value.slice(1) : receiverField.value;
    sendMessage(receiver.trim(), contentField.value);
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

const createConversationElement = (conversation) => {
    const timestamp = new Date(conversation.last_message_time);
    const now = new Date();
    const diffTime = Math.abs(now - timestamp);
    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));

    const timeDisplay = diffDays === 1 ? "1j" : `${diffDays}j`;

    return `
        <button class="w-full text-left hover:bg-gray-100 dark:hover:bg-gray-800 p-2 rounded-lg" data-user-id="${conversation.user_id}">
            <div class="flex items-start space-x-3 mb-4 w-full">
                <img src="../../assets/icons/profile.png" alt="Profile" class="w-12 h-12 rounded-full flex-shrink-0 object-cover invert dark:invert-0">
                <div class="flex-1">
                    <div class="flex items-center flex-wrap">
                        <span class="font-bold text-xl">${conversation.display_name}</span>
                        <span class="text-gray-500 ml-2 text-sm">@${conversation.username}</span>
                        <span class="text-gray-500 ml-1 text-sm">·</span>
                        <span class="text-gray-500 ml-1 text-sm">${timeDisplay}</span>
                    </div>
                    <div class="mt-1 text-black dark:text-white text-base">
                        ${conversation.last_message}
                    </div>
                </div>
            </div>
        </button>
    `;
};

const createNewMessageButton = () => {
    const button = document.createElement('button');
    button.id = 'create-conversation-button';

    const img = document.createElement('img');
    img.src = '../../assets/icons/new-message.png';
    img.alt = 'New Message';
    img.className = 'w-[24px] h-[24px] invert dark:invert-0';

    button.appendChild(img);
    button.onclick = () => {
        promptBackground.classList.remove("hidden");
    };
    return button;
};

const renderConversations = (conversations) => {
    if (!conversations || !conversations.length) {
        conversationsContainer.innerHTML = '<p class="text-center text-gray-500">Aucune conversation</p>';
        return;
    }

    conversationsContainer.innerHTML = conversations
        .map(conv => createConversationElement(conv))
        .join('');

    document.querySelectorAll('[data-user-id]').forEach(button => {
        button.addEventListener('click', () => {
            feed.innerHTML = '';
            const userId = button.getAttribute('data-user-id');
            setCurrentReceiver(userId); 
            getMessages(userId);
        });
    });
};

const handleSendMessage = async () => {
    const content = messageInput.value.trim();
    
    if (!content) {
        alert("Le message ne peut pas être vide");
        return;
    }
    
    if (!currentReceiverId) {
        alert("Veuillez sélectionner un destinataire");
        return;
    }

    try {
        await sendMessage(currentReceiverId, content);
        messageInput.value = '';
        feed.innerHTML = '';
        await getMessages(currentReceiverId);
    } catch (error) {
        console.error("Erreur lors de l'envoi du message:", error);
        alert("Erreur lors de l'envoi du message");
    }
};

sendMessageBtn.addEventListener('click', handleSendMessage);
messageInput.addEventListener('keypress', (e) => {
    if (e.key === 'Enter' && !e.shiftKey) {
        e.preventDefault();
        handleSendMessage();
    }
});

const updateHeader = (hasConversations) => {
    const headerContainer = document.querySelector('#message-header');
    if (hasConversations && !headerContainer.querySelector('#create-conversation-button')) {
        headerContainer.classList.add('justify-between');
        headerContainer.appendChild(createNewMessageButton());
    }
};

const getConversations = async () => {
    const formData = new FormData();
    formData.append("action", "getConversations");

    try {
        const response = await fetch("../../src/Controllers/MessageController.php", {
            method: "POST",
            headers: {
                "X-Requested-With": "XMLHttpRequest",
            },
            body: formData,
        });
        const responseData = await response.json();

        if (responseData.success) {
            renderConversations(responseData.conversations);
            updateHeader(responseData.conversations.length > 0);
        }
    } catch (error) {
        console.error("Error while fetching conversations:", error);
    }
}

const displayConversations = () => {
    getConversations();
};

const getMessages = async (otherId) => {
    const formData = new FormData();
    formData.append("otherId", otherId);
    formData.append("action", "getMessages");

    try {
        const response = await fetch("../../src/Controllers/MessageController.php", {
            method: "POST",
            headers: {
                "X-Requested-With": "XMLHttpRequest",
            },
            body: formData,
        });
        const responseData = await response.json();

        console.log(responseData)

        if (responseData.success) {
            responseData.messages.forEach(message => {
                displayMessage(
                    message.isSelf,
                    message.content,
                    message.username,
                    message.timestamp
                );
            });
        } else {
            alert(responseData.message);
        }
    } catch (error) {
        console.error("Error while fetching messages:", error);
    }
}

const displayMessage = (isSelf, content, username = '', timestamp) => {
    const messageContainer = document.createElement("div");
    messageContainer.classList.add("mb-4");

    const header = document.createElement("div");
    header.classList.add("flex", "items-center", "mb-1", isSelf ? "justify-end" : "justify-start");

    const usernameSpan = document.createElement("span");
    usernameSpan.textContent = isSelf ? 'Moi' : username;
    usernameSpan.classList.add("font-semibold", "text-sm");

    const timeSpan = document.createElement("span");
    const date = new Date(timestamp);
    const formattedTime = date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
    timeSpan.textContent = ` · ${formattedTime}`;
    timeSpan.classList.add("text-gray-500", "text-sm", "ml-2");

    header.appendChild(usernameSpan);
    header.appendChild(timeSpan);

    const bubble = document.createElement("pre");
    bubble.textContent = content;
    bubble.classList.add("rounded-lg", "p-4", "max-w-[80%]", "shadow-sm", "whitespace-pre-wrap", "break-words");

    if (isSelf) {
        bubble.classList.add("ml-auto", "bg-blue-500", "text-white");
    } else {
        bubble.classList.add("mr-auto", "bg-gray-100", "text-gray-800");
    }

    messageContainer.appendChild(header);
    messageContainer.appendChild(bubble);
    feed.appendChild(messageContainer);
}

document.addEventListener('DOMContentLoaded', () => {
    displayConversations();
    const textareaDesktop = document.getElementById("receiver-field");
    const userListDivDesktop = document.getElementById("user-desktop");
    const autoComplete = new handleAutoCompletion(
        textareaDesktop,
        textareaDesktop,
        userListDivDesktop,
        userListDivDesktop,
        "../../src/Controllers/MessageController.php",
        "@"
    );
    autoComplete.init();
});

function handleResponsive(toggle, idConversationList, idMessagerie) {
    document.getElementById(toggle).addEventListener("click", function () {
        document.getElementById(idConversationList).classList.toggle("hidden");
        document.getElementById(idMessagerie).classList.toggle("hidden");
    });

    document.addEventListener("click", (event) => {
        if (event.target.closest("#conversation-toggle")) {
            if (window.matchMedia("(max-width: 48rem)").matches) {
                document.getElementById("conversation-section").classList.toggle("hidden");
                document.getElementById("message-feed").classList.toggle("hidden");
            } else {
                document.getElementById("message-feed").classList.remove("hidden");
            }
        }
    });
}
handleResponsive("conversation-opener", "conversation-section", "message-feed");

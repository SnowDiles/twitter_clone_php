import { handleAutoCompletion } from "./autoCompletion.js";

const createConversationButton = document.getElementById("create-conversation-button");
const promptBackground = document.getElementById("prompt-background");
const closeButton = document.getElementById("close-button");

const sendButton = document.getElementById("send-button");
const receiverField = document.getElementById("receiver-field");
const contentField = document.getElementById("message-content-field");
const fakeConversations = [
    {
        id: 1,
        user: {
            name: "Enzo la menace",
            handle: "@enzo",
            lastMessage: "Salut Enzo",
            timestamp: "1j",
            isOwn: true
        }
    },
    {
        id: 2,
        user: {
            name: "Brahim",
            handle: "@brahim",
            lastMessage: "Tu as finis le projet ?",
            timestamp: "1j",
            isOwn: false
        }
    },
    {
        id: 3,
        user: {
            name: "Enzo la menace",
            handle: "@enzo",
            lastMessage: "Salut Enzo",
            timestamp: "1j",
            isOwn: true
        }
    }
];

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
    sendMessage(receiver, contentField.value);
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

const conversationsContainer = document.querySelector('.w-full.p-4.flex.flex-col.gap-5');

const createConversationElement = (conversation) => {
    return `
        <button class="w-full text-left hover:bg-gray-100 dark:hover:bg-gray-800 p-2 rounded-lg">
            <div class="flex items-start space-x-3 mb-4 w-full">
                <img src="../../assets/icons/profile.png" alt="Profile" class="w-12 h-12 rounded-full flex-shrink-0 object-cover invert dark:inset-0">
                <div class="flex-1">
                    <div class="flex items-center flex-wrap">
                        <span class="font-bold text-xl">${conversation.user.name}</span>
                        <span class="text-gray-500 ml-2 text-sm">${conversation.user.handle}</span>
                        <span class="text-gray-500 ml-1 text-sm">·</span>
                        <span class="text-gray-500 ml-1 text-sm">${conversation.user.timestamp}</span>
                    </div>
                    <div class="mt-1 text-black dark:text-white text-base">
                        ${conversation.user.isOwn ? 'Vous: ' : ''}${conversation.user.lastMessage}
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

const updateHeader = () => {
    const headerContainer = document.querySelector('#message-header');
    if (fakeConversations.length > 0 && !headerContainer.querySelector('#create-conversation-button')) {
        headerContainer.classList.add('justify-between');
        headerContainer.appendChild(createNewMessageButton());
    }
};

const renderConversations = () => {
    conversationsContainer.innerHTML = `
        ${fakeConversations
            .map(conv => createConversationElement(conv))
            .join('')}
    `;
};

const displayConversations = () => {
    renderConversations();
    updateHeader();
};

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
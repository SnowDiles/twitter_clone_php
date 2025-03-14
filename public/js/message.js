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

const feed = document.getElementById("message-feed");

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
        <button id="conversation-toggle" class="w-full text-left hover:bg-gray-100 dark:hover:bg-gray-800 p-2 rounded-lg">
            <div class="flex items-start space-x-3 mb-4 w-full">
                <img src="../../assets/icons/profile.png" alt="Profile" class="w-12 h-12 rounded-full flex-shrink-0 object-cover invert dark:invert-0">
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

const displayMessage = (isSelf, content, username = '', timestamp = '2025-03-12 15:45:13') => {
    const messageContainer = document.createElement("div");
    messageContainer.classList.add("mb-4");

    const header = document.createElement("div");
    header.classList.add("flex", "m-4", "items-center", "mb-1", isSelf ? "justify-end" : "justify-start");

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
    bubble.classList.add("rounded-xl", "p-4", "m-4","w-fit", "max-w-[70%]", "shadow-sm", "whitespace-pre-wrap", "break-words");

    if (isSelf) {
        bubble.classList.add("ml-auto", "bg-blue-500", "text-white");
    } else {
        bubble.classList.add("mr-auto", "bg-gray-100", "text-gray-800");
    }

    messageContainer.appendChild(header);
    messageContainer.appendChild(bubble);
    feed.appendChild(messageContainer);
}

displayMessage(true, "It's my message");
displayMessage(true, "It's my dedledjedlekdjeldkejdelkdjeldekjdelkejdlekdjedlekdjeldekjdelkjdeldkjedlekdjeldekdjldk");

displayMessage(false, "It's their message", "John Doe");
handleResponsive("conversation-opener", "conversation-section", "message-feed");
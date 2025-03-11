function createFormData() {
    const urlParams = new URLSearchParams(window.location.search);
    const page = urlParams.get('page');
    const userId = urlParams.get('userId');

    let formData = new FormData();
    formData.append('action', 'getConnections');
    formData.append('userId', userId);
    formData.append('type', page);
    
    return formData;
}

async function getConnections() {
    const formData = createFormData();

    try {
        const response = await fetch("../../src/Controllers/UserController.php", {
            method: "POST",
            headers: {
                "X-Requested-With": "XMLHttpRequest",
            },
            body: formData,
        });

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const contentType = response.headers.get("content-type");
        if (!contentType || !contentType.includes("application/json")) {
            throw new TypeError("La réponse n'est pas au format JSON!");
        }

        const responseData = await response.json();

        if (responseData.success) {
            displayConnections(responseData.data.connection);
        } else {
            console.error("Erreur serveur:", responseData.message);
            alert(responseData.message || "Une erreur est survenue");
        }
    } catch (error) {
        console.error("Erreur lors du chargement des connections:", error);
        alert("Une erreur est survenue lors du chargement des connections");
    }
}

function displayConnections(connections) {
    const container = document.querySelector('.px-4.w-full');
    container.innerHTML = '';

    connections.forEach(user => {
        const userHTML = createUserHTML(user);
        container.insertAdjacentHTML('beforeend', userHTML);
        
        if (user.showButton) {
            attachButtonListeners(user.user_id);
        }
    });
}

function createUserHTML(user) {
    const buttonHTML = user.showButton ? createButtonHTML(user) : '';
    
    return `
        <div class="flex items-center justify-between py-4">
            ${createUserInfoHTML(user)}
            ${buttonHTML}
        </div>
    `;
}

function createButtonHTML(user) {
    const buttonText = user.isFollowing ? 'Abonné' : 'Suivre';
    const buttonClass = getButtonClass(user.isFollowing);
    
    return `
        <button 
            class="px-4 py-1 rounded-full text-sm font-medium follow-button ${buttonClass}"
            data-user-id="${user.user_id}"
            data-following="${user.isFollowing}"
            data-default-text="${buttonText}"
            data-hover-text="${user.isFollowing ? 'Se désabonner' : buttonText}">
            ${buttonText}
        </button>
    `;
}

function attachButtonListeners(userId) {
    const button = document.querySelector(`[data-user-id="${userId}"]`);
    if (!button) return;

    button.addEventListener('mouseenter', () => {
        button.textContent = button.dataset.hoverText;
    });
    
    button.addEventListener('mouseleave', () => {
        button.textContent = button.dataset.defaultText;
    });
}

function getButtonClass(isFollowing) {
    return isFollowing
        ? 'bg-gray-200 dark:bg-gray-700 hover:bg-red-100 dark:hover:bg-red-900 hover:text-red-600 dark:hover:text-red-400'
        : 'bg-primary-500 dark:bg-primary-600 text-white hover:bg-primary-600 dark:hover:bg-primary-700';
}

function createUserInfoHTML(user) {
    return `
        <a href="./UserController.php?userId=${user.user_id}" class="flex items-center flex-grow">
            <div class="flex items-center">
                <div class="w-12 h-12 rounded-full bg-gray-300"></div>
                <div class="ml-3">
                    <div class="font-medium hover:underline">${user.display_name}</div>
                    <div class="text-gray-500">@${user.username}</div>
                </div>
            </div>
        </a>
    `;
}

document.addEventListener("DOMContentLoaded", () => {
    getConnections();
});
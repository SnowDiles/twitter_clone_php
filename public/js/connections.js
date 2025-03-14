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
    const buttonText = user.isFollowing ? 'Ne plus suivre' : 'Suivre';

    return `
        <button 
            id="button-follow"
            class="btn variant-filled-secondary mt-3 font-bold invert dark:invert-0"
            data-user-id="${user.user_id}"
            data-following="${user.isFollowing}">
            ${buttonText}
        </button>
    `;
}



function createUserInfoHTML(user) {
    return `
        <a href="./UserController.php?userId=${user.user_id}" class="flex items-center flex-grow">
            <div class="flex items-center">
                <img src="../../assets/icons/outline/account.png" alt="Profile" class="w-12 h-12 rounded-full invert dark:invert-0">
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

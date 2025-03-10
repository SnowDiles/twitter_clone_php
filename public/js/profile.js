async function getConnections() {
    const urlParams = new URLSearchParams(window.location.search);
    const page = urlParams.get('page');
    const userId = urlParams.get('userId');

    let formData = new FormData();
    formData.append('action', 'getConnections');
    formData.append('userId', userId);
    formData.append('type', page);

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
    const userListContainer = document.querySelector('.px-4.w-full');
    userListContainer.innerHTML = '';

    connections.forEach(user => {
        const userElement = `
            <div class="flex items-center justify-between py-4">
                <div class="flex items-center">
                    <div class="w-12 h-12 rounded-full bg-gray-300"></div>
                    <div class="ml-3">
                        <div class="font-medium">${user.display_name}</div>
                        <div class="text-gray-500">@${user.username}</div>
                    </div>
                </div>
                <button 
                    class="px-4 py-1 border border-gray-400 bg-transparent rounded-full text-sm font-medium follow-button"
                    data-username="${user.username}">
                    Abonné
                </button>
            </div>
        `;
        userListContainer.insertAdjacentHTML('beforeend', userElement);
    });
}

document.addEventListener("DOMContentLoaded", () => {
    getConnections();
});
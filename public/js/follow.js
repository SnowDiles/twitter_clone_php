document.addEventListener('DOMContentLoaded', () => {
    document.body.addEventListener('click', (event) => {
        const button = event.target.closest('#button-follow');
        if (button) {
            const formData = new FormData();
            const userId = button.dataset.userId;
            const isFollowing = button.dataset.following;

            if (!userId) {
                console.error('User ID not found on button');
                return;
            }

            formData.append('action', 'toggleFollow');
            formData.append('userId', userId);
            formData.append('isFollowing', isFollowing);

            manageFollowStatus(formData, button);
        }
    });
});

async function manageFollowStatus(formData, button) {

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
            button.dataset.following = (button.dataset.following === 'false').toString();
            updateButtonAppearance(button);
        } else {
            console.error("Erreur serveur:", responseData.message);
            alert(responseData.message || "Une erreur est survenue");
        }
    } catch (error) {
        console.error("Erreur lors du chargement des connections:", error);
        alert("Une erreur est survenue lors du chargement des connections");
    }
}

function updateButtonAppearance(button) {
    const isFollowing = button.dataset.following === 'true';

    if (isFollowing) {
        button.textContent = 'Ne plus suivre';
    } else {
        button.textContent = 'Suivre';
    }
}


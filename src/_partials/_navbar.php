<style>
    .navbar img {
        width: 100%;
        height: auto;
        filter: invert(100%);
    }

    .dark .navbar img {
        width: 100%;
        height: auto;
        filter: invert(0);
    }

    .navbar-desktop .logo {
            margin-right: 75px
        }

    @media (min-width: 600px) and (max-width: 1080px){
        .navbar-desktop {
            width: 200px !important;
        }

        .navbar-desktop img {
            width: 40px;
            filter: invert(100%);
        }

        .navbar-desktop .mb-8 {
            margin-bottom: 1rem;
        }

        .navbar-desktop .py-8 {
            padding-top: 1rem;
            padding-bottom: 1rem;
        }

        .navbar-desktop .py-2 {
            padding-top: 0.5rem;
            padding-bottom: 0.5rem;
        }

        .navbar-desktop .gap-4 {
            gap: 0.5rem;
        }

        .navbar-desktop .text-base {
            font-size: 0.875rem;
        }
    }

    @media (min-width: 768px) {
        .navbar-desktop {
            width: 440px;
        }

        .navbar-desktop img {
            width: 50px;
            filter: invert(100%);
        }

        .dark .navbar-desktop img {
            filter: invert(0);
        }
    }
</style>

<!-- View Mobile -->
<div class="navbar fixed bottom-0 left-0 right-0 flex justify-between items-center bg-surface-100-800-token shadow-lg px-4 py-2 md:invisible">
    <a href="../src/Controllers/HomeController.php" class="block w-12 h-12 order-1">
        <img src="../assets/icons/outline/home.png" alt="Home">
    </a>
    <a href="../src/Controllers/SearchController.php" class="block w-12 h-12 order-2">
        <img src="../assets/icons/search.png" alt="Search">
    </a>
    <a href="../src/Controllers/MessageController.php" class="block w-12 h-12 order-3">
        <img src="../assets/icons/outline/message.png" alt="Message">
    </a>
</div>

<!-- View Desktop -->
<div class="navbar-desktop hidden fixed left-0 top-0 bottom-0 flex-col justify-start items-center bg-surface-100-800-token shadow-lg px-4 py-8 md:flex">
    <img src="../assets/icons/logo.png" alt="Logo" class="logo mb-8 py-8">

    <div class="w-full flex flex-col items-center">
        <a href="../src/Controllers/HomeController.php" class="flex items-center gap-4 py-2">
            <div class="w-12 flex justify-center">
                <img id="home-icon" src="../assets/icons/outline/home.png" alt="Home">
            </div>
            <span class="text-base w-16">Home</span>
        </a>

        <a href="../src/Controllers/SearchController.php" class="flex items-center gap-4 py-2">
            <div class="w-12 flex justify-center">
                <img src="../assets/icons/search.png" alt="Search">
            </div>
            <span class="text-base w-16">Explore</span>
        </a>

        <a href="../src/Controllers/MessageController.php" class="flex items-center gap-4 py-2">
            <div class="w-12 flex justify-center">
                <img id="message-icon" src="../assets/icons/outline/message.png" alt="Message">
            </div>
            <span class="text-base w-16">Message</span>
        </a>

        <a href="../src/Controllers/UserController.php" class="flex items-center gap-4 py-2">
            <div class="w-12 flex justify-center">
                <img id="user-icon" src="../assets/icons/outline/account.png" alt="Compte">
            </div>
            <span class="text-base w-16">Profile</span>
        </a>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const fullPath = window.location.pathname;

        const iconMappings = {
            '/src/Controllers/HomeController.php': 'home-icon',
            '/src/Controllers/MessageController.php': 'message-icon',
            '/src/Controllers/UserController.php': 'user-icon'
        };

        for (const relativePath in iconMappings) {
            if (fullPath.includes(relativePath)) {
                const iconId = iconMappings[relativePath];
                const iconElement = document.getElementById(iconId);

                if (iconElement) {
                    const currentSrc = iconElement.src;
                    const filledSrc = currentSrc.replace('/outline/', '/filled/');
                    iconElement.src = filledSrc;
                }
                break;
            }
        }
    });
</script>   
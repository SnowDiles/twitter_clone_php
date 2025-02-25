<!-- Navbar Mobile -->
<nav class="md:hidden w-full bg-surface-100-800-token shadow-lg fixed bottom-0 left-0 right-0">
    <div class="flex justify-between items-center px-4 py-3">
        <a href="../Controllers/HomeController.php" class="flex items-center justify-center w-12 h-12">
            <img src="../../assets/icons/outline/home.png" alt="Home" class="w-10 h-10 invert dark:invert-0">
        </a>
        <a href="../Controllers/SearchController.php" class="flex items-center justify-center w-12 h-12">
            <img src="../../assets/icons/search.png" alt="Search" class="w-10 h-10 invert dark:invert-0">
        </a>
        <a href="../Controllers/MessageController.php" class="flex items-center justify-center w-12 h-12">
            <img src="../../assets/icons/outline/message.png" alt="Message" class="w-10 h-10 invert dark:invert-0">
        </a>
    </div>
</nav>

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
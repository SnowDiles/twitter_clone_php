<aside class="xl:px-[8em] md:px-[4em] md:pr-[6em] p-20 hidden md:block border-r border-r-black dark:border-r-white border-r-dashed flex-1 justify-items-end">
    <div>
        <div><a href="../Controllers/HomeController.php"><img src="../../assets/icons/logo.png" alt="" class="h-[50px] w-[50px] mb-[40px] invert dark:invert-0"></a></div>
        <nav class="flex flex-col gap-[22px] items-start">
            <div><a href="../Controllers/HomeController.php" class="flex items-center text-xl"><img src="../../assets/icons/outline/home.png" alt="Navbar home icon" id="home-icon" class="h-[50px] mr-[12px] invert dark:invert-0">Accueil</a></div>
            <div><a href="../Controllers/SearchController.php" class="flex items-center text-xl"><img src="../../assets/icons/search.png" alt="Navbar search icon" class="h-[50px] mr-[12px] invert dark:invert-0">Explorer</a></div>
            <div><a href="../Controllers/MessageController.php" class="flex items-center text-xl"><img src="../../assets/icons/outline/message.png" alt="Navbar message icon" id="message-icon" class="h-[50px] mr-[12px] invert dark:invert-0">Messages</a></div>
            <div><a href="../Controllers/UserController.php" class="flex items-center text-xl"><img src="../../assets/icons/outline/account.png" alt="Navbar profile icon" id="user-icon" class="h-[50px] mr-[12px] invert dark:invert-0">Profil</a></div>
        </nav>
    </div>
</aside>

<nav class="w-full bg-surface-100-800-token shadow-lg fixed bottom-0 left-0 right-0 block md:hidden z-40">
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
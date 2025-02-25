<div class="navbar-desktop hidden fixed left-0 top-0 bottom-0 flex-col justify-start items-center bg-surface-100-800-token shadow-lg px-4 py-8 md:flex md:w-[88px] lg:w-[400px] z-50">
    <!-- Logo -->
    <div class="w-full text-center mb-8">
        <img src="../../assets/icons/logo.png" alt="Logo" class="w-32 mb-8 invert dark:invert-0 mx-auto lg:flex md:hidden">
        <img src="../../assets/icons/logo.png" alt="Logo" class="w-12 mb-8 invert dark:invert-0 mx-auto lg:hidden md:flex">
    </div>
    <!-- Menu Items -->
    <div class="flex flex-col space-y-4 w-full ">
        <a href="../Controllers/HomeController.php" class="flex items-center lg:justify-start space-x-4 p-3 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800">
            <div class="w-12 flex justify-center shrink-0">
                <img id="home-icon" src="../../assets/icons/outline/home.png" alt="Home" class="w-12 h-12 invert dark:invert-0">
            </div>
            <span class="text-sm lg:flex xl:hidden">Home</span>
            </a>
        
        <a href="../Controllers/SearchController.php" class="flex items-center space-x-4 p-3 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800">
            <div class="w-12 flex justify-center shrink-0">
                <img src="../../assets/icons/search.png" alt="Search" class="w-12 h-12 invert dark:invert-0">
            </div>
            <span class="text-sm lg:flex xl:hidden">Explore</span>
        </a>
        <a href="../Controllers/MessageController.php" class="flex items-center space-x-4 p-3 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800">
            <div class="w-12 flex justify-center shrink-0">
                <img id="message-icon" src="../../assets/icons/outline/message.png" alt="Message" class="w-12 h-12 invert dark:invert-0">
            </div>
            <span class="text-sm lg:flex xl:hidden">Message</span>
        </a>
        <a href="../Controllers/UserController.php" class="flex items-center space-x-4 p-3 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800">
            <div class="w-12 flex justify-center shrink-0">
                <img id="user-icon" src="../../assets/icons/outline/account.png" alt="Compte" class="w-12 h-12 invert dark:invert-0">
            </div>
            <span class="text-sm lg:flex xl:hidden">Profile</span>
        </a>
    </div>
</div>

<style>
    .navbar-desktop {
        transition: width 0.3s ease;
    }

    @media (max-width: 1280px) and (min-width: 768px) {
        .navbar-desktop {
            width: 88px;
            padding-left: 0.5rem;
            padding-right: 0.5rem;
        }
        .navbar-desktop .w-8 {
            min-width: 2rem; 
            min-height: 2rem;
            margin: 0 auto;
        }
        
        .navbar-desktop a {
            padding: 0.75rem;
        }
        
    }
    @media (max-width: 1280px) {
        .navbar-desktop .text-sm {
            display: none;
        }
    }
    
</style>
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
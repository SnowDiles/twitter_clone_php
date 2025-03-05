<head>
    <title>Y - Recherche</title>
    <link rel="stylesheet" href="../../public/css/search.css">

    <?php
    require_once '../_partials/_head.php';
    ?>
</head>

<body data-theme="my-custom-theme">
    <?php
    require_once '../_partials/_navbar.php';
    ?>
    <div class="flex justify-center">
        <div class="flex gap-4 p-4 items-center max-w-xl justify-self-center w-full">
            <div class="md:invisible">
                <img src="../../assets/pptest.jpg" alt="profile picture icon" class="h-12 w-12 rounded-full">
            </div>
            <div class="relative w-full">
                <input type="search" name="search" id="search" placeholder="Rechercher" class="rounded-full p-2 h-[34px] w-[80%] 
                bg-tertiary dark:variant-filled-tertiary dark:invert text-center md:pl-10 md:text-left">
                <span class="hidden md:block absolute left-3 top-1/2 transform -translate-y-1/2">
                    <img src="../../assets/icons/search.png" alt="search icon"
                        class="h-[24px] w-[24px] hidden md:block invert dark:invert-0">
                </span>

                
                <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-4 shadow-lg w-64 
                max-h-64 overflow-y-auto absolute mt-2 z-40"
                    style="display:none" id="hashtag-desktop">
                    <ul class="space-y-2">
                    </ul>
                </div>
            </div>
        </div>

    </div>

    <div class="feed md:hidden">
        <div id="loading-mobile" class="text-center p-4 hidden">
            <span>Chargement...</span>
        </div>
    </div>
    <main class="flex-1 relative z-10">
        <div class="feed-desktop hidden md:block">
            <div id="tweets-container">
            </div>
            <div id="loading" class="text-center p-4 hidden">
                <span>Chargement...</span>
            </div>
        </div>
    </main>
    <script type="module" src="../../public/js/search.js"></script>
    <?php
    require_once '../_partials/_navbarMobile.php';
    ?>
</body>
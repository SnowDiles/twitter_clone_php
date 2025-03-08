<head>
    <title>Y - Recherche</title>
    <?php
    require_once '../_partials/_head.php';
    ?>
</head>

<body data-theme="my-custom-theme">
    <div class="flex">
        <?php
        require_once '../_partials/_navbar.php';
        ?>
        <div class=" flex-[4] min-h-screen max-h-screen overflow-y-scroll">
            <div class="flex">
                <div class="flex gap-4 p-4 items-center max-w-xl justify-self-center w-full mr-[6em] border-r border-r-black dark:border-r-white border-b border-b-black dark:border-b-white">
                    <div class="md:invisible">
                        <img src="../../assets/pptest.jpg" alt="profile picture icon" class="h-12 w-12 rounded-full">
                    </div>
                    <div class="relative w-full">
                        <input type="search" name="search" id="search" placeholder="Rechercher" class="rounded-full p-2 h-[34px] w-[300px] 
                            bg-tertiary dark:variant-filled-tertiary dark:invert text-center md:pl-10 md:text-left ">
                        <span class="hidden md:block absolute left-3 top-1/2 transform -translate-y-1/2">
                            <img src="../../assets/icons/search.png" alt="search icon"
                                class="h-[24px] w-[24px] hidden md:block invert dark:invert-0">
                        </span>
                        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-4 shadow-lg w-64 
                max-h-64 overflow-y-auto absolute mt-10 z-40" style="display:none" id="hashtag-desktop">
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
            <main class="flex-1 relative z-10 ">
                <div class="feed-desktop hidden md:block">
                    <div id="tweets-container">
                    </div>
                    <div id="loading" class="text-center p-4 hidden">
                        <span>Chargement...</span>
                    </div>
                </div>
            </main>
        </div>

    </div>

    <script type="module" src="../../public/js/search.js"></script>
</body>
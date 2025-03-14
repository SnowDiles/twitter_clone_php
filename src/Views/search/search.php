<head>
    <title>Y - Recherche</title>
    <?php
    require_once '../_partials/_head.php';
    ?>
</head>

<body data-theme="my-custom-theme">
    <div class="flex w-full">
        <?php
        require_once '../_partials/_navbar.php';
        ?>
        <div class=" flex-[4] min-h-screen max-h-screen overflow-y-scroll">
            <div class="flex">
                <div
                    class="flex gap-10 p-4 items-center w-full justify-self-center md:max-w-xl md:border-r border-r-black dark:border-r-white border-b border-b-black dark:border-b-white">
                    <div class="md:invisible">
                        <img src="../../assets/pptest.jpg" alt="profile picture icon" class="h-12 w-12 rounded-full">
                    </div>
                    <div class="relative w-full">
                        <input type="search" name="search" id="search" placeholder="Rechercher" class="rounded-full p-2 h-[34px] w-[230px] md:w-[400px]
                            bg-tertiary dark:variant-filled-tertiary dark:invert text-center md:pl-10 md:text-left ">
                        <span class=" absolute left-3 top-1/2 transform -translate-y-1/2">
                            <img src="../../assets/icons/search.png" alt="search icon"
                                class="h-[24px] w-[24px]  invert dark:invert-0">
                        </span>
                        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-4 shadow-lg w-64 
                max-h-64 overflow-y-auto absolute mt-2 z-40" style="display:none" id="hashtag-desktop">
                            <ul class="space-y-2">
                            </ul>
                        </div>
                    </div>
                </div>
            </div>


            <div class="feed md:hidden md:border-r border-r-black dark:border-r-white min-h-screen">
                <div id="loading-mobile" class="text-center p-4 hidden">
                    <span>Chargement...</span>
                </div>
            </div>
            <main class="flex-1 relative z-10 ">
                <div
                class="feed-desktop hidden md:flex min-h-screen w-full flex-[4] max-h-screen">
                    <div id="tweets-container" class="flex-1 h-full min-h-screen flex flex-col md:max-w-xl md:border-r border-r-black dark:border-r-white">
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
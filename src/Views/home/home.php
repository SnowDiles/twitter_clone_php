<head>
    <title>Y - Home</title>
    <?php include_once('../_partials/_head.php'); ?>
    <script type="module" src="https://cdn.jsdelivr.net/npm/emoji-picker-element@^1/index.js"></script>

</head>

<body data-theme="my-custom-theme" class="flex">
    <div class="md:hidden min-h-screen flex flex-col w-full">
        <div class="flex-1 pb-16 md:pb-0" id="top">
            <div
                class="header relative flex items-center p-4 md:invisible border-b border-b-black dark:border-b-white md:border-r border-r-black dark:border-r-white">
                <a href="../Controllers/UserController.php" class="block w-12 h-12 absolute left-4">
                    <img src="../../assets/icons/profile.png" alt="profile"
                        class="invert dark:invert-0 w-full h-full rounded-full">
                </a>
                <img src="../../assets/icons/logo.png" alt="logo" class="invert dark:invert-0 w-12 h-12 mx-auto">
            </div>
            <hr class="border-t md:invisible">
            <div class="feed md:invisible">
                <div id="loading-mobile" class="text-center p-4 hidden">
                    <span>Chargement...</span>
                </div>
            </div>

            <button id="btn-mobile-modale" class="fixed bottom-20 right-4 w-14 h-14 bg-blue-500 rounded-full flex 
            items-center justify-center text-white text-6xl font-bold shadow-lg hover:bg-blue-600 transition-colors">
                +
            </button>

            <div id="mobile-modal" class="fixed inset-0 bg-white dark:bg-gray-800 z-50 hidden flex-col">
                <div class="flex items-center justify-between p-4 border-b dark:border-gray-700">
                    <button id="close-mobile-modal" class="text-base">Annuler</button>
                    <img src="../../assets/icons/logo.png" alt="logo" class="invert dark:invert-0 w-8 h-8">
                    <button id="post-button-mobile" class="px-4 py-2 bg-blue-500 text-white rounded-full">
                        Poster
                    </button>
                </div>

                <div class="flex-1 p-4">
                    <div class="flex gap-4">
                        <div class="flex-1">

                            <textarea id="post-text-area-mobile" maxlength="140"
                                placeholder="écrivez votre ressenti ici"
                                class="w-full bg-transparent border-none focus:outline-none resize-none mb-4 text-xl dark:text-white h-32 "></textarea>

                        </div>

                        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-4 shadow-lg w-64 
                max-h-64 overflow-y-auto absolute mt-10 z-40" style="display:none" id="user-mobile">
                            <ul class="space-y-2">
                            </ul>
                        </div>

                    </div>
                </div>
                <div class="border-t dark:border-gray-700 p-4">
                    <div class="fixed bottom-24 z-20 hidden" style="width: 300px; height: 300px;"
                        id="emoji-picker-container-mobile">
                        <emoji-picker id="emoji-picker-itself" class="dark"
                            style="width: 100%; height: 100%;"></emoji-picker>
                    </div>
                    <div class="flex justify-start items-center">
                        <button id="upload-button-mobile" class="invert dark:invert-0 p-2 rounded-full">
                            <img src="../../assets/icons/image.png" alt="Ajouter une image" class="w-6 h-6">
                        </button>
                        <button id="emoji-toggle-mobile" class="invert dark:invert-0 p-2 rounded-full">
                            <img src="../../assets/icons/emoji.png" alt="Ajouter une icone" class="w-6 h-6">
                        </button>
                        <input type="file" id="file-input-mobile" class="hidden" multiple accept="image/*" max="4">
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php include_once('../_partials/_navbar.php'); ?>

    <div class="hidden md:flex min-h-screen w-full flex-[4] max-h-screen overflow-y-scroll" id="tweet-contain">
        <div class="flex-1 flex flex-col md:max-w-xl bg-[#d9d9d9] dark:bg-[#000000]">
            <div
                class="hidden md:block sticky top-0 z-40 header-desktop border-b border-gray-500 bg-[#d9d9d9] dark:bg-[#000000]">
                <div
                    class="max-w-xl mx-auto p-4 border-b dark:border-b-white border-r border-r-black dark:border-r-white ">
                    <div class="flex gap-4">
                        <img src="../../assets/icons/profile.png" alt="profile"
                            class="invert dark:invert-0 rounded-full w-12 h-12 object-cover">
                        <div class="flex-grow flex flex-col gap-4">
                            <div class="flex items-center gap-4">

                                <textarea id="post-text-area-desktop" maxlength="140"
                                    placeholder="écrivez votre ressenti ici"
                                    class="flex-grow bg-transparent text-xl placeholder-gray-500 border-none focus:outline-none resize-none"></textarea>
                                <div class="fixed top-36 z-20 hidden" style="width: 300px; height: 300px;"
                                    id="emoji-picker-container">
                                    <emoji-picker id="emoji-picker-itself" class="dark"
                                        style="width: 100%; height: 100%;"></emoji-picker>
                                </div>
                                <button id="post-button-desktop" class="btn variant-filled" disabled=""> Post </button>
                            </div>

                            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-4 shadow-lg w-64 
                max-h-64 overflow-y-auto absolute mt-10 z-40" style="display:none" id="user-desktop">
                                <ul class="space-y-2">
                                </ul>
                            </div>

                            <div class="flex justify-start items-center">
                                <button id="upload-button-desktop" class="invert dark:invert-0 p-2 rounded-full">
                                    <img src="../../assets/icons/image.png" alt="Ajouter une image" class="w-6 h-6">
                                </button>
                                <button id="emoji-toggle" class="invert dark:invert-0 p-2 rounded-full">
                                    <img src="../../assets/icons/emoji.png" alt="Ajouter une icone" class="w-6 h-6">
                                </button>

                                <input type="file" id="file-input-desktop" class="hidden" multiple accept="image/*"
                                    max="4">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <main class="flex-1 relative z-10 border-r border-black dark:border-white">
                <div class="feed-desktop hidden md:block">
                    <div id="tweets-container">
                    </div>
                    <div id="loading" class="text-center p-4 hidden">
                        <span>Chargement...</span>
                    </div>

                </div>
            </main>
        </div>

        <div
            class="hidden lg:flex p-6 pl-0 pt-4 min-w-fit w-20 justify-start h-fit mt-20 ml-8 rounded-xl border  border-black dark:border-white">
            <div class="p-4 space-y-2">
                <h1 class="pb-6 font-bold text-xl">Hashtags Populaires</h1>
                <ul id="hashtag-container">
                </ul>
            </div>
        </div>
    </div>



    <button id="back-to-top"
        class="fixed bottom-8 right-8 p-3 bg-gray-800 dark:bg-gray-200 text-white dark:text-gray-800 rounded-full shadow-lg hover:bg-gray-700 dark:hover:bg-gray-300 transition-colors duration-200"
        aria-label="Back to Top" onclick="window.scrollTo({ top: 0, behavior: 'smooth' });">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18" />
        </svg>
    </button>
    <script type="module" src="../../public/js/home.js"></script>
</body>
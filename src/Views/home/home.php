<head>
    <title>Y - Home</title>
    <link rel="stylesheet" href="../../public/css/home.css">
    <?php include_once('../_partials/_head.php'); ?>
</head>

<body data-theme="my-custom-theme">
    <div class="md:hidden min-h-screen flex flex-col">
        <div class="flex-1 pb-16 md:pb-0">
            <div class="header relative flex items-center p-4 md:invisible">
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
                                class="w-full bg-transparent border-none focus:outline-none resize-none mb-4 text-xl dark:text-white h-32"></textarea>
                        </div>
                        <div class="bg-white border rounded-lg p-4 shadow-lg w-64 max-h-64 overflow-y-auto absolute mt-10 z-40"
                            style="display:none" id="user-mobile">
                            <ul class="space-y-2"></ul>
                        </div>
                    </div>
                </div>
                <div class="border-t dark:border-gray-700 p-4">
                    <div class="flex justify-start items-center">
                        <button id="upload-button-mobile" class="invert dark:invert-0 p-2 rounded-full">
                            <img src="../../assets/icons/image.png" alt="Ajouter une image" class="w-6 h-6">
                        </button>
                        <input type="file" id="file-input-mobile" class="hidden" multiple accept="image/*" max="4">
                    </div>
                </div>
            </div>
        </div>
        <?php include_once('../_partials/_navbarMobile.php'); ?>
    </div>
    <div class="hidden md:flex min-h-screen">
        <?php include_once('../_partials/_navbar.php'); ?>
        <div class="flex-1 flex flex-col">
            <div class="hidden md:block sticky top-0 z-40 header-desktop border-y bg-[#d9d9d9] dark:bg-[#000000]">
                <div class="max-w-xl mx-auto p-4">
                    <div class="flex gap-4">
                        <img src="../../assets/icons/profile.png" alt="profile"
                            class="invert dark:invert-0 rounded-full w-12 h-12 object-cover">
                        <div class="flex-grow flex flex-col gap-4">
                            <div class="flex items-center gap-4">
                                <textarea id="post-text-area-desktop" maxlength="140"
                                    placeholder="écrivez votre ressenti ici"
                                    class="flex-grow bg-transparent text-xl placeholder-gray-500 border-none focus:outline-none resize-none"></textarea>
                                <button id="post-button-desktop" class="btn variant-filled" disabled=""> Post </button>
                            </div>
                            <div class="bg-white border rounded-lg p-4 shadow-lg w-64 max-h-64 overflow-y-auto absolute mt-10 z-40"
                                style="display:none" id="user-desktop">
                                <ul class="space-y-2"></ul>
                            </div>

                            <div class="flex justify-start items-center">
                                <button id="upload-button-desktop" class="invert dark:invert-0 p-2 rounded-full">
                                    <img src="../../assets/icons/image.png" alt="Ajouter une image" class="w-6 h-6">
                                </button>

                                <input type="file" id="file-input-desktop" class="hidden" multiple accept="image/*" max="4">
                            </div>
                        </div>
                    </div>
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
        </div>
    </div>
    <script type="module" src="../../public/js/home.js"></script>
</body>
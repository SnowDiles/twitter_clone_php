<head>
    <title>Y - Home</title>
    <link rel="stylesheet" href="../../public/css/home.css">
    <?php
    include_once('../_partials/_head.php');
    ?>
</head>

<body data-theme="my-custom-theme">

    <!-- Version Mobile -->
    <div class="md:hidden min-h-screen flex flex-col">
        <div class="flex-1 pb-16 md:pb-0">
            <!-- Header -->
            <div class="header relative flex items-center p-4 md:invisible">
                <a href="../Controllers/UserController.php" class="block w-12 h-12 absolute left-4">
                    <img src="../../assets/icons/profile.png" alt="profile"
                        class="invert dark:invert-0 w-full h-full rounded-full">
                </a>
                <img src="../../assets/icons/logo.png" alt="logo" class="invert dark:invert-0 w-12 h-12 mx-auto">
            </div>
            <hr class="border-t md:invisible">

            <!-- Feed -->
            <div class="feed md:invisible">
                <div class="p-4 max-w-xl">
                    <div class="flex gap-3">
                        <div class="w-12 h-12">
                            <img src="../../assets/icons/profile.png"
                                alt="profile"
                                class="invert dark:invert-0 w-full h-full rounded-full">
                        </div>
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="text-xl">Augustin V</span>
                                <span class="">@Augustin V</span>
                                <span class="text-xs">•</span>
                                <span class="">1j</span>
                            </div>
                            <div class="ml-0 mt-3">
                                <div class="text-xl">
                                    bonjour les z'amis !
                                </div>
                                <div class="flex items-center gap-4">
                                    <button class="flex items-center">
                                        <img class="invert dark:invert-0 w-5 h-5"
                                            src="../../assets/icons/comment.png"
                                            alt="Commentaire">
                                        <span>9</span>
                                    </button>
                                    <button class="flex items-center">
                                        <img class="invert dark:invert-0 w-5 h-5"
                                            src="../../assets/icons/repost.png"
                                            alt="Repost">
                                        <span>23</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php
        include_once('../_partials/_navbarMobile.php');
        ?>
    </div>


    <!-- Version Desktop -->
    <div class="hidden md:flex min-h-screen">
        <?php
        include_once('../_partials/_navbar.php')
        ?>
        <div class="flex-1 flex flex-col">
            <div class="hidden md:block sticky top-0 z-40 header-desktop border-y bg-[#d9d9d9] dark:bg-[#000000]">
                <div class="max-w-xl mx-auto p-4">
                    <div class="flex gap-4">
                        <div class="w-12 h-12">
                            <img src="../../assets/icons/profile.png"
                                alt="profile"
                                class="invert dark:invert-0 rounded-full">
                        </div>

                        <div class="flex-grow flex flex-col gap-4">
                            <div class="flex items-center gap-4">
                            <textarea id="postTextarea" maxlength="140" placeholder="écrivez votre ressenti ici" class="flex-grow bg-transparent text-xl placeholder-gray-500 border-none focus:outline-none resize-none"></textarea>
                                <button id="postButton" class="btn variant-filled" disabled=""> Post </button>
                            </div>

                            <div class="flex justify-start items-center">
                                <button id="uploadButton" class="invert dark:invert-0 p-2 rounded-full">
                                    <img src="../../assets/icons/image.png" alt="Ajouter une image" class="w-6 h-6">
                                </button>

                                <input type="file" id="fileInput" class="hidden">
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            <main class="flex-1 relative z-10">
                <div class="feed-desktop hidden md:block">
                    <div id="tweets-container">
                        <!-- Les tweets seront chargés ici -->
                    </div>
                    <div id="loading" class="text-center p-4 hidden">
                        <span>Chargement...</span>
                    </div>
                </div>
            </main>
        </div>
    </div>
    <script src="../../public/js/home.js"></script>
</body>

</html>
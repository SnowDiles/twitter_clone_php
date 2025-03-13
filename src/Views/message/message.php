<head>
    <title>Y - Messages</title>
    <?php require_once('../_partials/_head.php'); ?>
    <script src="../../public/js/message.js" defer></script>
</head>

<body data-theme="my-custom-theme" class="flex min-h-screen">
    <?php require_once('../_partials/_navbar.php') ?>

    <!-- Quand il n'y pas de conversation afficher ce bloc -->

    <!-- <div class="flex flex-[1] border-r border-black dark:border-white">
        <div class="flex flex-col items-center">
            <div class="md:w-xl w-full h-[54px] p-4 items-center flex gap-5">
                <a href="HomeController.php">
                    <img src="../../assets/icons/arrow-back.png"
                        alt="arrow back icon"
                        class="h-[24px] w-[24px] invert dark:invert-0">
                </a>
                <div class="text-lg">Messages</div>
            </div>
            <div class="md:max-w-xl justify-self-center w-full p-4 pt-16 flex flex-col gap-5">
                <div class="font-bold text-xl">
                    Bienvenue sur votre messagerie
                </div>
                <button id="create-conversation-button"
                    class="btn variant-filled-primary self-start">Écrivez un message</button>
            </div>
        </div>
    </div> -->

    <!-- Quand il y a des conversation affiché ça -->

    <div class="flex w-full md:w-[400px] border-r border-black dark:border-white">
        <div class="flex flex-col items-center w-full">
            <!-- En-tête avec bouton retour -->
            <div class="w-full h-[54px] p-4 items-center flex gap-5 border-b border-gray-500">
                <a href="HomeController.php">
                    <img src="../../assets/icons/arrow-back.png"
                        alt="arrow back icon"
                        class="h-[24px] w-[24px] invert dark:invert-0">
                </a>
                <div class="text-lg">Messages</div>
            </div>

            <!-- Contenu des conversations -->
            <div class="w-full p-4 flex flex-col gap-5">
                <!-- Premier message -->
                <button class="w-full text-left hover:bg-gray-100 dark:hover:bg-gray-800 p-2 rounded-lg">
                    <div class="flex items-start space-x-3 mb-4 w-full">
                        <!-- Avatar -->
                        <div class="w-12 h-12 rounded-full bg-gray-500 flex-shrink-0"></div>

                        <!-- Contenu du message -->
                        <div class="flex-1">
                            <!-- Nom d'utilisateur et handle -->
                            <div class="flex items-center flex-wrap">
                                <span class="font-bold text-xl">Enzo la menace</span>
                                <span class="text-gray-500 ml-2 text-sm">@enzo</span>
                                <span class="text-gray-500 ml-1 text-sm">·</span>
                                <span class="text-gray-500 ml-1 text-sm">1j</span>
                            </div>

                            <!-- Message -->
                            <div class="mt-1 text-black dark:text-white text-base">
                                Vous: Salut Enzo
                            </div>
                        </div>
                    </div>
                </button>

                <!-- Deuxième message -->
                <button class="w-full text-left hover:bg-gray-100 dark:hover:bg-gray-800 p-2 rounded-lg">
                    <div class="flex items-start space-x-3 mb-4 w-full">
                        <!-- Avatar -->
                        <div class="w-12 h-12 rounded-full bg-gray-500 flex-shrink-0"></div>

                        <!-- Contenu du message -->
                        <div class="flex-1">
                            <!-- Nom d'utilisateur et handle -->
                            <div class="flex items-center flex-wrap">
                                <span class="font-bold text-xl">Brahim</span>
                                <span class="text-gray-500 ml-2 text-sm">@brahim</span>
                                <span class="text-gray-500 ml-1 text-sm">·</span>
                                <span class="text-gray-500 ml-1 text-sm">1j</span>
                            </div>

                            <!-- Message -->
                            <div class="mt-1 text-black dark:text-white text-base">
                                Tu as finis le projet ?
                            </div>
                        </div>
                    </div>
                </button>

            </div>

            <!-- Bouton pour créer une nouvelle conversation -->
            <div class="w-full p-4 flex justify-end">
                <button id="create-conversation-button"
                    class="rounded-full bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 font-medium">
                    Nouveau message
                </button>
            </div>
        </div>
    </div>

    <div class="flex flex-[3]">
        <!-- Les messages de la conv -->
    </div>

    <div id="prompt-background"
        class="absolute w-screen h-screen bg-slate-100 bg-opacity-60 flex justify-center items-center hidden bg-li">
        <div class="flex flex-col gap-4 p-4 rounded-lg h-full w-full md:max-w-2xl md:w-4/6 md:h-auto card max-md:rounded-none">
            <div class="flex items-center w-full gap-4">
                <button id="close-button">
                    <svg class="w-[24] h-[24] lucide lucide-x" xmlns="http://www.w3.org/2000/svg"
                        width="24" height="24" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M18 6 6 18" />
                        <path d="m6 6 12 12" />
                    </svg>
                </button>
                <span>Nouveau message</span>
                <button id="send-button" class="btn variant-filled-primary ml-auto">Envoyer</button>
            </div>
            <form class="flex flex-col gap-4">
                <input id="receiver-field" class="input p-2 w-full" placeholder="À:">
                <textarea id="message-content-field" class="textarea p-2 w-full min-h-32 max-h-64"
                    placeholder="Votre message"></textarea>
            </form>
        </div>
    </div>
</body>
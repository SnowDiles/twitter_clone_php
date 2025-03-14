<head>
    <title>Y - Messages</title>
    <?php require_once('../_partials/_head.php'); ?>
    <script type="module" src="../../public/js/message.js" defer></script>
    <script type="module" src="https://cdn.jsdelivr.net/npm/emoji-picker-element@^1/index.js"></script>
</head>

<body data-theme="my-custom-theme" class="flex min-h-screen flex-col md:flex-row">
    <div class="lg:block hidden min-h-full">
        <?php require_once('../_partials/_navbar.php') ?>
    </div>

    <div id="conversation-section"
        class="md:flex hidden w-full md:w-[400px] md:border-r border-black dark:border-white">
        <div class="flex flex-col items-center w-full">
            <div id="message-header"
                class="w-full h-[54px] p-4 items-center flex gap-5 border-b border-black dark:border-white">
                <a href="./HomeController.php">
                    <img src="../../assets/icons/arrow-back.png" alt="arrow back icon"
                        class="h-[24px] w-[24px] invert dark:invert-0">
                </a>
                <div class="text-lg">Messages</div>
            </div>

            <div class="md:max-w-xl w-full justify-self-center p-4 flex flex-col gap-5">
                <div class="font-bold text-xl">
                    Bienvenue sur votre messagerie
                </div>
                <button id="create-conversation-button" class="btn variant-filled-primary self-start text-white">Écrivez
                    un message</button>
            </div>
        </div>
    </div>

    <div class="flex flex-col flex-[3] p-4 gap-4 min-h-screen md:max-w-xl w-full md:border-r border-r-black dark:border-r-white"
        id="message-feed-container">
        <div class="flex-grow overflow-hidden">
            <div id="message-feed" class="overflow-y-auto h-full">


            </div>
        </div>

        <div class="absolute top-0 left-0 m-4 md:hidden">
            <a id="conversation-opener">
                <img src="../../assets/icons/arrow-back.png" alt="arrow back icon"
                    class="h-[24px] w-[24px] invert dark:invert-0 ">
            </a>
        </div>

        <div class="w-full">
            <div class="border-t border-black dark:border-white pt-4">
                <div class="relative flex items-center gap-2">
                    <input type="text" name="message" id="message-input" placeholder="Envoyer un message"
                        class="w-full h-10 pl-10 pr-4 py-2 rounded-xl bg-gray-100 dark:bg-gray-800 text-gray-900 dark:text-gray-100 placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2
                         focus:ring-blue-500 dark:focus:ring-blue-400 border border-transparent hover:border-gray-300 dark:hover:border-gray-600 transition-all" />

                    <div class="fixed bottom-20 z-20 flex justify-end hidden" style="width: 250px; height: 250px;"
                        id="emoji-picker-container">
                        <emoji-picker id="emoji-picker-itself" class="dark"
                            style="width: 100%; height: 100%;"></emoji-picker>
                    </div>
                    <button id="send-message-btn" class="p-2 rounded-full hover:bg-gray-200 dark:hover:bg-gray-700">
                        <svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                        </svg>

                    </button>
                    <button id="emoji-toggle" class="invert dark:invert-0 p-2 rounded-full">
                        <img src="../../assets/icons/emoji.png" alt="Ajouter une icone" class="w-6 h-6 min-w-6 min-h-6">
                    </button>

                    <div class="absolute inset-y-0 left-0 flex items-center pl-2 pointer-events-none">
                        <svg class="w-5 h-5 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z">
                            </path>
                        </svg>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="prompt-background"
        class="absolute w-screen h-screen bg-black bg-opacity-50 flex justify-center items-center hidden">
        <div
            class="flex flex-col gap-6 p-6 rounded-lg bg-white dark:bg-gray-900 shadow-2xl w-full h-full md:max-w-2xl md:w-4/6 md:h-auto max-md:rounded-none">
            <div class="flex items-center w-full gap-4">
                <button id="close-button"
                    class="p-2 rounded-full hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
                    <svg class="w-6 h-6 text-gray-700 dark:text-gray-300" xmlns="http://www.w3.org/2000/svg" width="24"
                        height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                        stroke-linecap="round" stroke-linejoin="round">
                        <path d="M18 6 6 18" />
                        <path d="m6 6 12 12" />
                    </svg>
                </button>

                <span class="text-lg font-semibold text-gray-900 dark:text-gray-100">Nouveau message</span>
                <button id="send-button"
                    class="btn variant-filled-primary ml-auto px-6 py-2 rounded-lg bg-blue-500 text-white hover:bg-blue-600 dark:bg-blue-600 dark:hover:bg-blue-700 transition-colors">
                    Envoyer
                </button>
            </div>

            <form class="flex flex-col gap-4">
                <input id="receiver-field"
                    class="input p-3 w-full rounded-lg border border-gray-200 dark:border-gray-700 bg-transparent text-gray-900 dark:text-gray-100 placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 transition-all"
                    placeholder="À:" />

                <textarea id="message-content-field"
                    class="textarea p-3 w-full rounded-lg border border-gray-200 dark:border-gray-700 bg-transparent text-gray-900 dark:text-gray-100 placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 transition-all resize-y min-h-32 max-h-64"
                    placeholder="Votre message"></textarea>

                <div id="user-desktop"
                    class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-lg w-64 max-h-64 overflow-y-auto absolute mt-14 z-40 hidden">
                    <ul class="space-y-2 p-2">

                    </ul>
                </div>
            </form>
        </div>
    </div>
</body>
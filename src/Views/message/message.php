<head>
    <title>Y - Messages</title>
    <?php require_once('../_partials/_head.php'); ?>
    <script src="../../public/js/message.js" defer></script>
</head>

<body data-theme="my-custom-theme" class="flex min-h-screen">
    <?php require_once('../_partials/_navbar.php') ?>
    <div class="flex flex-[1] border-r border-black dark:border-white">
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
    </div>

    <div class="flex flex-[3]">
        
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
                <button class="btn variant-filled-primary ml-auto">Envoyer</button>
            </div>
            <form class="flex flex-col gap-4">
                <input class="input p-2 w-full" placeholder="À:">
                <textarea class="textarea p-2 w-full min-h-32 max-h-64" placeholder="Votre message"></textarea>
            </form>
        </div>
    </div>
</body>
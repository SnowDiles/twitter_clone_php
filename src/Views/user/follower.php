<head>
    <title>Followers - @<?= $CurrentUser->getUsername() ?></title>
    <?php
    include_once('../_partials/_head.php');
    ?>
</head>

<body data-theme="my-custom-theme" class="flex">
    <div class="flex min-h-screen w-full">

        <?php include_once('../_partials/_navbar.php'); ?>

        <div class="flex flex-[4]">
            <div class="flex flex-col w-full md:max-w-xl">
                <div class="flex-1 pb-16 md:pb-0">
                    <div class="p-4 border-b border-gray-500 md:hidden">
                        <div>
                            <div class="text-center text-xl font-medium">
                                @<?= $CurrentUser->getUsername() ?>
                            </div>
                        </div>
                    </div>

                    <div class="hidden md:flex items-center p-4 border-b border-gray-500">
                        <a href="UserController.php?userId=<?= $CurrentUser->getId() ?>" class="mr-4">
                            <img src="../../assets/icons/arrow-back.png"
                                alt="arrow back icon"
                                class="h-[24px] w-[24px] invert dark:invert-0">
                        </a>
                        <div>
                            <div class="text-xl font-medium"><?= $CurrentUser->getDisplayName() ?></div>
                            <div class="text-gray-500">@<?= $CurrentUser->getUsername() ?></div>
                        </div>
                    </div>

                    <div class="flex border-b border-gray-500">
                        <div class="w-1/2 text-center py-3 relative">
                            <a class="text-gray-400"
                                href="./UserController.php?page=follower&userId=<?= $CurrentUser->getId() ?>">
                                <span>Abonnés</span>
                                <div class="absolute bottom-0 left-0 right-0 h-1"></div>
                            </a>
                        </div>
                        <div class="w-1/2 text-center py-3">
                            <a class="text-gray-400"
                                href="./UserController.php?page=following&userId=<?= $CurrentUser->getId() ?>">
                                <span>Abonnement</span>
                            </a>
                        </div>
                    </div>

                    <!-- Version mobile -->
                    <div class="px-4 md:hidden">
                        <div class="flex items-center justify-between py-4">
                            <div class="flex items-center">
                                <div class="w-12 h-12 rounded-full bg-gray-300"></div>
                                <div class="ml-3">
                                    <div class="font-medium">Enzo la menace</div>
                                    <div class="text-gray-500">@enzo</div>
                                </div>
                            </div>
                            <button class="px-4 py-1 border border-gray-400 rounded-full text-sm">Abonné</button>
                        </div>

                        <div class="flex items-center justify-between py-4">
                            <div class="flex items-center">
                                <div class="w-12 h-12 rounded-full bg-gray-300"></div>
                                <div class="ml-3">
                                    <div class="font-medium">Tom</div>
                                    <div class="text-gray-500">@tommm</div>
                                </div>
                            </div>
                            <button class="px-4 py-1 border border-gray-400 rounded-full text-sm">Abonné</button>
                        </div>

                        <div class="flex items-center justify-between py-4">
                            <div class="flex items-center">
                                <div class="w-12 h-12 rounded-full bg-gray-300"></div>
                                <div class="ml-3">
                                    <div class="font-medium">Brahim</div>
                                    <div class="text-gray-500">@brahimm</div>
                                </div>
                            </div>
                            <button class="px-4 py-1 bg-white text-black rounded-full text-sm font-medium">
                                Suivre en retour
                            </button>
                        </div>
                    </div>

                    <!-- Version Desktop -->
                    <div class="px-4 w-full hidden md:block">
                        <div class="flex items-center justify-between py-4">
                            <div class="flex items-center">
                                <div class="w-12 h-12 rounded-full bg-gray-300"></div>
                                <div class="ml-3">
                                    <div class="font-medium">John Doe</div>
                                    <div class="text-gray-500">@johndoe</div>
                                </div>
                            </div>
                            <button class="px-4 py-1 border border-gray-400 rounded-full text-sm bg-transparent">
                                Abonné
                            </button>
                        </div>

                        <div class="flex items-center justify-between py-4 ">
                            <div class="flex items-center">
                                <div class="w-12 h-12 rounded-full bg-gray-300"></div>
                                <div class="ml-3">
                                    <div class="font-medium">Jane Smith</div>
                                    <div class="text-gray-500">@janesmith</div>
                                </div>
                            </div>
                            <button class="px-4 py-1 border border-gray-400 rounded-full text-sm bg-transparent">
                                Abonné
                            </button>
                        </div>

                        <div class="flex items-center justify-between py-4">
                            <div class="flex items-center">
                                <div class="w-12 h-12 rounded-full bg-gray-300"></div>
                                <div class="ml-3">
                                    <div class="font-medium">Mike Johnson</div>
                                    <div class="text-gray-500">@mikej</div>
                                </div>
                            </div>
                            <button class="px-4 py-1 bg-white text-black rounded-full text-sm font-medium">
                                Suivre en retour
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="../../public/js/profile.js"></script>
</body>
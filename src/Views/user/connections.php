<head>
    <title><?= ucfirst($page) ?> - @<?= $CurrentUser->getUsername() ?></title>
    <?php
    include_once('../_partials/_head.php');
    ?>
</head>

<body data-theme="my-custom-theme" class="flex">
    <div class="flex min-h-screen w-full">
        <?php include_once('../_partials/_navbar.php'); ?>

        <div class="flex flex-[4]">
            <div class="flex flex-col w-full md:max-w-xl">
                <div class="flex-1 pb-16 md:pb-0 border-r border-gray-500">

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
                            <a class="<?= $page === 'following' ? 'text-black dark:text-white' : 'text-gray-500' ?>"
                                href="./UserController.php?page=following&userId=<?= $CurrentUser->getId() ?>">
                                <span>Abonnement</span>
                            </a>

                        </div>
                        <div class="w-1/2 text-center py-3 relative">
                            <a class="<?= $page === 'follower' ? 'text-black dark:text-white' : 'text-gray-500' ?>"
                                href="./UserController.php?page=follower&userId=<?= $CurrentUser->getId() ?>">
                                <span>Abonnés</span>

                            </a>
                        </div>
                    </div>

                    <div class="px-4 w-full">

                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="../../public/js/profile.js"></script>

</body>
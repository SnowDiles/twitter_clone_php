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
                    <!-- Mobile Header -->
                    <div class="p-4 border-b border-gray-500 md:hidden">
                        <div>
                            <div class="text-center text-xl font-medium">
                                @<?= $CurrentUser->getUsername() ?>
                            </div>
                        </div>
                    </div>

                    <!-- Desktop Header -->
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

                    <!-- Navigation Tabs -->
                    <div class="flex border-b border-gray-500">
                        <div class="w-1/2 text-center py-3 relative">
                            <a class="<?= $page === 'follower' ? 'text-black' : 'text-gray-400' ?>"
                                href="./UserController.php?page=follower&userId=<?= $CurrentUser->getId() ?>">
                                <span>Abonnés</span>
                                <?php if ($page === 'follower'): ?>
                                    <div class="absolute bottom-0 left-0 right-0 h-1 bg-primary-500"></div>
                                <?php endif; ?>
                            </a>
                        </div>
                        <div class="w-1/2 text-center py-3 relative">
                            <a class="<?= $page === 'following' ? 'text-black' : 'text-gray-400' ?>"
                                href="./UserController.php?page=following&userId=<?= $CurrentUser->getId() ?>">
                                <span>Abonnement</span>
                                <?php if ($page === 'following'): ?>
                                    <div class="absolute bottom-0 left-0 right-0 h-1 bg-primary-500"></div>
                                <?php endif; ?>
                            </a>
                        </div>
                    </div>

                    <!-- User List -->
                    <div class="px-4 md:hidden">
                        <?php foreach ($connections as $user): ?>
                            <div class="flex items-center justify-between py-4">
                                <div class="flex items-center">
                                    <div class="w-12 h-12 rounded-full bg-gray-300"></div>
                                    <div class="ml-3">
                                        <div class="font-medium"><?= $user['displayName'] ?></div>
                                        <div class="text-gray-500">@<?= $user['username'] ?></div>
                                    </div>
                                </div>
                                <button class="px-4 py-1 <?= $user['isFollowing'] ? 'border border-gray-400' : 'bg-white text-black' ?> rounded-full text-sm font-medium">
                                    <?= $user['isFollowing'] ? 'Abonné' : ($page === 'follower' ? 'Suivre en retour' : 'Suivre') ?>
                                </button>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- Desktop User List -->
                    <div class="px-4 w-full hidden md:block">
                        <?php foreach ($connections as $user): ?>
                            <div class="flex items-center justify-between py-4">
                                <div class="flex items-center">
                                    <div class="w-12 h-12 rounded-full bg-gray-300"></div>
                                    <div class="ml-3">
                                        <div class="font-medium"><?= $user['displayName'] ?></div>
                                        <div class="text-gray-500">@<?= $user['username'] ?></div>
                                    </div>
                                </div>
                                <button class="px-4 py-1 <?= $user['isFollowing'] ? 'border border-gray-400 bg-transparent' : 'bg-white text-black' ?> rounded-full text-sm font-medium">
                                    <?= $user['isFollowing'] ? 'Abonné' : ($page === 'follower' ? 'Suivre en retour' : 'Suivre') ?>
                                </button>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

</body>
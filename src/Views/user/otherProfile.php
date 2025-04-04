<head>
    <title><?= $otherUser->getDisplayName() ?> (@<?= $otherUser->getUsername() ?>)</title>
    <?php
    include_once('../_partials/_head.php');
    ?>
    <script src="../../public/js/user.js"></script>

</head>

<body data-theme="my-custom-theme">
    <div class="flex min-h-screen w-full">
        <?php
        include_once('../_partials/_navbar.php')
        ?>
        <div class="flex flex-[4]">
            <main class="flex flex-col w-full md:max-w-xl border-r border-r-black dark:border-r-white border-r-dashed">

                <div class="h-[54px] px-4 items-center flex gap-5">
                    <a href="HomeController.php">
                        <img src="../../assets/icons/arrow-back.png" alt="arrow back icon"
                            class="h-[24px] w-[24px] invert dark:invert-0">
                    </a>
                    <div class="flex flex-col">
                        <span><?= $otherUser->getDisplayName() ?></span>
                        <span class="text-xs text-tertiary-500"><?= $otherUser->getPostsCount() ?> posts</span>                    
                    </div>
                </div>

                <div class="border-b border-b-black dark:border-b-white border-t border-t-black dark:border-t-white">
                    <div>
                        <img src="../../assets/userid_1500x500.png" alt="profile banner" class="w-full">
                    </div>

                    <div>
                        <div class="flex items-center justify-between px-3">
                            <div class="relative">
                                <div class="absolute">
                                    <div class="rounded-full w-max absolute border border-black top-1/2 transform 
                                translate-x-0 -translate-y-[85%]">
                                        <img src="../../assets/pptest.jpg" alt="profile picture"
                                            class="w-20 h-20 rounded-full">
                                    </div>
                                </div>
                            </div>
                            <div class="flex">
                                <button class="btn-icon mt-3">
                                    <img src="../../assets/icons/message.png" alt="message icon"
                                        class="w-[25px] h-[25px] invert dark:invert-0">
                                </button>

                                <button
                                    id="button-follow"
                                    class="btn variant-filled-secondary mt-3 font-bold invert dark:invert-0"
                                    data-user-id="<?= $otherUser->getId() ?>"
                                    data-following="<?= $isFollowing ? 'true' : 'false' ?>">
                                    <?= $isFollowing ? 'Ne plus suivre' : 'Suivre' ?>
                                </button>
                            </div>
                        </div>

                        <div class="px-3 flex flex-col mb-3">
                            <span class="font-bold"><?= $otherUser->getDisplayName() ?></span>
                            <span class="text-xs text-tertiary-500">@<?= $otherUser->getUsername() ?></span>
                        </div>

                        <div class="mx-3 mb-3">
                            <p><?= $otherUser->getBio() ?></p>
                        </div>

                        <div class="mx-3 flex gap-[6px] mb-3">
                            <a href="./UserController.php?page=following&userId=<?= $otherUser->getId() ?>">
                                <span class="mr-1">
                                    <?= $otherUser->getConnectionsCount($otherUser->getId(), 'following') ?>
                                </span>
                                <span class="text-tertiary-500">Abonnements</span>
                            </a>
                            <a href="./UserController.php?page=follower&userId=<?= $otherUser->getId() ?>">
                                <span class="mr-1">
                                    <?= $otherUser->getConnectionsCount($otherUser->getId(), 'follower') ?>
                                </span>
                                <span class="text-tertiary-500">Abonnés</span>
                            </a>
                        </div>
                    </div>
                </div>


                <main class="flex-1 relative z-10">
                    <div class="feed-desktop">
                        <div id="tweets-container">
                        </div>
                        <div id="loading" class="text-center p-4 hidden">
                            <span>Chargement...</span>
                        </div>
                    </div>
                </main>
            </main>
        </div>
    </div>
    <script src="../../public/js/user.js"></script>
    <script src="../../public/js/follow.js"></script>

</body>
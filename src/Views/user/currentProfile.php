<head>
    <title><?= $CurrentUser->getDisplayName() ?> (@<?= $CurrentUser->getUsername() ?>)</title>
    <?php
    include_once('../_partials/_head.php');
    ?>
</head>

<body data-theme="my-custom-theme">
    <?php
    include_once('../_partials/_navbar.php')
    ?>
    <div>
        <div class="flex flex-col items-center">
            <div class="md:max-w-xl justify-self-center w-full h-[54px] px-4 items-center flex gap-5">
                <a href="HomeController.php">
                    <img src="../../assets/icons/arrow-back.png"
                        alt="arrow back icon"
                        class="h-[24px] w-[24px] invert dark:invert-0">
                </a>
                <div class="flex flex-col">
                    <span><?= $CurrentUser->getDisplayName() ?></span>
                    <span class="text-xs text-tertiary-500">12 posts</span>
                </div>
            </div>
            <div class="md:max-w-xl justify-self-center w-full">
                <div class="border-b border-white">

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
                                <?php if ($_SESSION['user_id'] == $CurrentUser->getId()) { ?>
                                    <button class="btn variant-ringed-secondary mt-3 invert dark:invert-0 text-white dark:text-dark">
                                        Editer le profile
                                    </button>
                                <?php } else { ?>
                                    <button class="btn-icon mt-3">
                                        <img src="../../assets/icons/message.png"
                                            alt="message icon"
                                            class="w-[25px] h-[25px] invert dark:invert-0">
                                    </button>
                                    <button 
                                        class="btn variant-filled-secondary 
                                            mt-3 font-bold invert 
                                            dark:invert-0">
                                            Suivre
                                        </button>
                                <?php } ?>
                            </div>
                        </div>

                        <div class="px-3 flex flex-col mb-3">
                            <span class="font-bold"><?= $CurrentUser->getDisplayName() ?></span>
                            <span class="text-xs text-tertiary-500">@<?= $CurrentUser->getUsername() ?></span>
                        </div>

                        <div class="mx-3 mb-3">
                            <p><?= $CurrentUser->getBio() ?></p>
                        </div>

                        <div class="mx-3 flex gap-[6px] mb-3">
                            <a href="./UserController.php?page=follower&userId=<?=$CurrentUser->getId()?>">
                                <span class="mr-1">21</span>
                                <span class="text-tertiary-500">Abonnements</span>
                            </a>
                            <a href="./UserController.php?page=following&userId=<?=$CurrentUser->getId()?>">
                                <span class="mr-1">45</span>
                                <span class="text-tertiary-500">Abonnés</span>
                            </a>
                        </div>
                    </div>
                </div>

                <main></main>
            </div>
        </div>
    </div>
    <?php
    include_once('../_partials/_navbarMobile.php');
    ?>
</body>
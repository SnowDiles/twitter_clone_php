<head>
    <title><?php echo $CurrentUser->getDisplayName() ?> (@<?php echo $CurrentUser->getUsername() ?>)</title>
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
        <div class="flex flex-[4] min-h-screen max-h-screen h-full overflow-y-scroll">
            <main class="flex flex-col w-full md:max-w-xl border-r
             border-r-black dark:border-r-white border-r-dashed h-full">
                <div class="h-[54px] px-4 items-center flex gap-5 my-2">
                    <a href="HomeController.php">
                        <img src="../../assets/icons/arrow-back.png" alt="arrow back icon"
                            class="h-[24px] w-[24px] invert dark:invert-0">
                    </a>
                    <div class="flex flex-col">
                        <span><?php echo $CurrentUser->getDisplayName() ?></span>
                        <span class="text-xs text-tertiary-500">12 posts</span>
                    </div>
                </div>
                <div class="border-b border-b-black dark:border-b-white  dark:bg-black 
                border-t border-t-black dark:border-t-white">
                    <div>
                        <img src="../../assets/userid_1500x500.png" alt="profile banner" class="w-full">
                    </div>

                    <div>
                        <div class="flex items-center justify-between px-3">
                            <div class="relative">
                                <div class="absolute">
                                    <div class="rounded-full w-max absolute border 
                                    border-black dark:border-white top-1/2 transform 
                                translate-x-0 -translate-y-[85%]">
                                        <img src="../../assets/pptest.jpg" alt="profile picture"
                                            class="w-20 h-20 rounded-full">
                                    </div>
                                </div>
                            </div>
                            <div class="flex">
                                <?php if ($_SESSION['user_id'] == $CurrentUser->getId()) { ?>
                                    <button class="btn variant-ringed-secondary 
                                            mt-3 invert dark:invert-0 text-white dark:text-dark" onclick="openModal()">
                                        Editer le profile
                                    </button>
                                <?php } else { ?>
                                    <button class="btn-icon mt-3">
                                        <img src="../../assets/icons/message.png" alt="message icon"
                                            class="w-[25px] h-[25px] invert dark:invert-0">
                                    </button>
                                    <button class="btn variant-filled-secondary 
                                                mt-3 font-bold invert 
                                                dark:invert-0">
                                        Suivre
                                    </button>
                                <?php } ?>
                            </div>
                        </div>

                        <div class="px-3 flex flex-col mb-3">
                            <span class="font-bold"><?php echo $CurrentUser->getDisplayName() ?></span>
                            <span class="text-xs text-tertiary-500">@<?php echo $CurrentUser->getUsername() ?></span>
                        </div>

                        <div class="mx-3 mb-3">
                            <p><?php echo $CurrentUser->getBio() ?></p>
                        </div>

                        <div class="mx-3 flex gap-[6px] mb-3">
                            <a href="./UserController.php?page=following&userId=<?= $CurrentUser->getId() ?>">
                                <span class="mr-1">
                                    <?= $CurrentUser->getConnectionsCount($CurrentUser->getId(), 'following') ?>
                                </span>
                                <span class="text-tertiary-500">Abonnements</span>
                            </a>
                            <a href="./UserController.php?page=follower&userId=<?= $CurrentUser->getId() ?>">
                                <span class="mr-1">
                                    <?= $CurrentUser->getConnectionsCount($CurrentUser->getId(), 'follower') ?>
                                </span>
                                <span class="text-tertiary-500">Abonnés</span>
                            </a>
                        </div>
                    </div>
                </div>


                <main class="flex-1 relative z-10">
                    <div class="feed-desktop ">
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

    <div>
        <div class="bg-black bg-opacity-50 w-full 
                    h-full fixed top-0 left-0 flex items-center 
                    justify-center hidden z-[10]
                    dark:!bg-[#80808094]" 
            id="edit-profile-modal">
            <div class="h-[700px] w-[560px] card p-4 text-token space-y-4 max-w-[560px] dark:border dark:border-white">
                <div class="flex justify-between items-center">
                    <button onclick="closeModal()">
                        <img src="../../assets/icons/close.png" alt="close icon" 
                        class="h-[30px] w-[30px] invert dark:invert-0">
                    </button>
                    <span class="text-xl md:text-3xl font-bold mr-[4em]">Edit profile</span>
                    <button type="button" class="btn variant-filled" onclick="saveChanges()">Sauvegarder</button>
                </div>

                <div class="h-[150px] w-full bg-[#f0f0f0]">
                    <div
                        class="relative w-full h-full opacity-75"
                        style="background-image: url('../../assets/userid_1500x500.png'); 
                                background-size: cover; background-position: center;">
                        <div class="absolute inset-0 flex items-center justify-center">
                            <img src="../../assets/icons/edit-picture.png" alt="edit icon" class="w-10 h-10">
                        </div>
                    </div>
                    <div
                        class="absolute border border-black top-1/2 
                            rounded-full transform translate-x-[3em] -translate-y-[15em] bg-cover 
                            bg-center w-20 h-20 flex items-center justify-center opacity-75"
                        style="background-image: url('../../assets/pptest.jpg');">
                        <img src="../../assets/icons/edit-picture.png" alt="edit icon" class="w-[30px]">
                    </div>
                </div>

                <div class="flex flex-col">
                    <span class="self-center mb-2 text-xl">Modifier mes informations</span>
                    <form action="" class="flex flex-col gap-4" id="user-edit-profile">
                        <input 
                            class="input dark:border dark:!border-white" 
                            type="text"
                            placeholder="<?php echo $CurrentUser->getDisplayName() ?>"
                            name="name" />
                        <?php if ($CurrentUser->getBio() && strlen($CurrentUser->getBio()) > 0) {
                            $bio = $CurrentUser->getBio();
                        } else {
                            $bio = "Bio...";
                        }
                        ?>
                        <textarea 
                            class="textarea h-[58px] resize-none dark:border dark:!border-white" 
                            maxlength="160" placeholder="<?php echo $bio ?>" name="bio"></textarea>
                        <input 
                            class="input dark:border dark:!border-white" 
                            type="password" 
                            name="oldPassword" 
                            placeholder="Ancien mot de passe" 
                        />
                        <input 
                            class="input dark:border dark:!border-white" 
                            type="password" 
                            name="newPassword" 
                            placeholder="Nouveau mot de passe" 
                        />
                        <input 
                            class="input dark:border dark:!border-white" 
                            type="email" 
                            name="email" 
                            placeholder="<?php echo $CurrentUser->getEmail() ?>" 
                        />
                    </form>
                    <form action="" id="theme-form">
                        <div class="flex justify-evenly">
                            <div 
                                onclick="selectTheme('light')" 
                                class="bg-white text-black p-2 flex 
                                items-center flex-row-reverse gap-3 h-[40px] 
                                cursor-pointer dark:border dark:!border-white"
                            >
                                <div>
                                    <input 
                                        type="radio" 
                                        name="theme" 
                                        value="light" 
                                        id="theme-light" 
                                        <?php echo ($_SESSION['theme'] == 'light') ? 'checked' : ''; ?>>
                                </div>
                                <div><span>Light</span></div>
                            </div>
                            <div 
                                onclick="selectTheme('dark')" 
                                class="bg-black text-white 
                                p-2 flex items-center flex-row-reverse 
                                gap-3 h-[40px] cursor-pointer dark:border dark:!border-white"
                            >
                                <div>
                                    <input 
                                        type="radio" 
                                        name="theme" 
                                        value="dark" 
                                        id="theme-dark" 
                                        <?php echo ($_SESSION['theme'] == 'dark') ? 'checked' : ''; ?>>
                                </div>
                                <div><span>Dark</span></div>
                            </div>
                        </div>
                        <div class="flex justify-center items-center gap-2">
                            <a href="LogoutController.php" class="underline text-tertiary-500 hover:text-tertiary-700">
                                Voulez-vous vous déconnecter
                            </a>
                            <img src="../../assets/icons/logout.png" alt="logout icon" class="w-[20px] h-[20px">
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script src="../../public/js/editProfile.js"></script>
</body>
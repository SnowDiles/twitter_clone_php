<head>
    <title>Followers - @<?= $CurrentUser->getUsername() ?></title>
    <?php
    include_once('../_partials/_head.php');
    ?>
</head>

<body data-theme="my-custom-theme">
    <?php
    include_once('../_partials/_navbar.php')
    ?>

    <div class="min-h-screen">
        <div class="border-b border-gray-800">
            <div class="py-4 px-4 text-center text-xl font-medium">@Gus</div>
        </div>

        <!-- Mettre en color celui qui est actif -->
        <div class="flex border-b border-gray-800">
            <div class="w-1/2 text-center py-3">
                <a href="./UserController.php?page=follower&userId=<?= $CurrentUser->getId() ?>">
                    <span class="mr-1"><?= $FollowerCount ?></span>
                    <span class="text-tertiary-500">Abonnés</span>
                </a>
            </div>
            <div class="w-1/2 text-center py-3">
                <a href="./UserController.php?page=following&userId=<?= $CurrentUser->getId() ?>">
                    <span class="mr-1"><?= $FollowingCount ?></span>
                    <span class="text-tertiary-500">Abonnements</span>
                </a>
            </div>
        </div>


        <!-- Abonné faire dynamiquement -->
        <div class="px-4">
            <div class="flex items-center justify-between py-4 border-b border-gray-800">
                <div class="flex items-center">
                    <div class="w-12 h-12 rounded-full bg-gray-300"></div>
                    <div class="ml-3">
                        <div class="font-medium">Enzo la menace</div>
                        <div class="text-gray-500">@enzo</div>
                    </div>
                </div>
                <button class="px-4 py-1 border border-gray-400 rounded-full text-sm">Abonné</button>
            </div>

            <div class="flex items-center justify-between py-4 border-b border-gray-800">
                <div class="flex items-center">
                    <div class="w-12 h-12 rounded-full bg-gray-300"></div>
                    <div class="ml-3">
                        <div class="font-medium">Tom</div>
                        <div class="text-gray-500">@tommm</div>
                    </div>
                </div>
                <button class="px-4 py-1 border border-gray-400 rounded-full text-sm">Abonné</button>
            </div>

            <div class="flex items-center justify-between py-4 border-b border-gray-800">
                <div class="flex items-center">
                    <div class="w-12 h-12 rounded-full bg-gray-300"></div>
                    <div class="ml-3">
                        <div class="font-medium">Brahim</div>
                        <div class="text-gray-500">@brahimm</div>
                    </div>
                </div>
                <button class="px-4 py-1 bg-white text-black rounded-full text-sm font-medium">Suivre en retour</button>
            </div>
        </div>
    </div>

    <script src="../../public/js/profile.js"></script>
    <script>
        document.addEventListener("keydown", (event) => {
            if (event.key.toLowerCase() === 'p') {
                const html = document.documentElement;
                html.classList.toggle('dark');
            }
        });
    </script>
</body>
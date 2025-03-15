<head>
    <title>Y - Réponses</title>
    <?php require_once '../_partials/_head.php'; ?>
    <script src="../../public/js/reply.js" defer></script>
</head>

<body data-theme="my-custom-theme">
    <div class="flex min-h-screen w-full">
        <?php require_once '../_partials/_navbar.php' ?>
        <div class="flex flex-[4] md:border-r border-black dark:border-white flex-col">
            <a href="HomeController.php"
                class="flex md:max-w-xl md:border-r 
        border-black dark:border-white p-4 text-xl items-center gap-[8px]">
                <img src="../../assets/icons/arrow-back.png" alt="arrow back icon"
                    class="h-[24px] w-[24px] invert dark:invert-0">
                Post
            </a>
            <?php if ($postData) : ?>
                <div class="p-4 w-full border-b md:border-r 
        border-black dark:border-white tweet-container 
        md:max-w-xl" id="source-post" data-post-id="<?php echo $postData['post_id'] ?? '' ?>">
                    <div class="flex gap-3">
                        <div class="w-13 h-13 flex-shrink-0">
                            <img
                                src="../../assets/icons/profile.png"
                                alt="profile"
                                class="invert dark:invert-0 w-12 h-12 object-cover rounded-full">
                        </div>
                        <div>
                            <div class="flex items-center gap-2">
                                <a class="text-xl"
                                    href="./UserController.php?userId=<?php echo $postData['user_id'] ?? '' ?>">
                                    <?php echo htmlspecialchars($postData['display_name'] ?? 'User') ?>
                                </a>
                                <a class="text-tertiary-500"
                                    href="./UserController.php?userId=<?php echo $postData['user_id'] ?? '' ?>">
                                    @<?php echo htmlspecialchars($postData['username'] ?? 'user') ?>
                                </a>
                                <span class="text-xs">•</span>
                                <span class=""><?php echo $postTime ?></span>
                            </div>
                            <div class="ml-0 mt-3">
                                <div class="text-small text-xl break-all max-w-full">
                                    <?php echo $postData['content'] ?>
                                </div>

                                <?php if (isset($postMedia) && !empty($postMedia)) : ?>
                                    <?php
                                    $imageElements = '';
                                    foreach ($postMedia as $media) {
                                        $url = htmlspecialchars($media['file_name']);
                                        $imageElements .= "
                                            <a href=\"{$url}\" class=\"flex justify-center items-center overflow-hidden target=\"_blank\">
                                                <img src=\"{$url}\" alt=\"Tweet media\" class=\"max-w-full object-contain rounded-lg\">
                                            </a>
                                        ";
                                    }
                                    $imagesHtml = "
                                        <div class=\"mt-3 mb-3 grid gap-2 p-2.5 rounded-[30px] mr-5 " . (count($postMedia) > 1 ? "grid-cols-2" : "grid-cols-1") . "\">
                                            {$imageElements}
                                        </div>
                                    ";
                                    echo $imagesHtml;
                                    ?>
                                <?php endif; ?>

                                <div class="flex items-center gap-4 mt-2">
                                  
                                    <button class="repost-button flex items-center" 
                                    data-post-id="<?php echo $postData['id'] ?? '' ?>">
                                        <img class="invert dark:invert-0 w-5 h-5" 
                                        src="../../assets/icons/repost.png" alt="Repost">
                                        <span><?php echo $postData['repost_count'] ?? '0' ?></span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div
                    class="hidden md:block sticky top-0 
          z-40 header-desktop border-b border-gray-500 
          bg-[#d9d9d9] dark:bg-[#000000] md:max-w-xl">
                    <div
                        class="md:max-w-xl p-4 border-b dark:border-b-white 
                        border-r border-r-black dark:border-r-white">
                        <div class="flex gap-4">
                            <img src="../../assets/icons/profile.png" alt="profile"
                                class="invert dark:invert-0 rounded-full w-12 h-12 object-cover">
                            <div class="flex-grow flex flex-col gap-4">
                                <div class="flex items-center gap-4">
                                    <textarea id="post-text-area-desktop" maxlength="140"
                                        placeholder="écrivez votre réponse ici"
                                        class="flex-grow bg-transparent text-xl placeholder-gray-500 border-none focus:outline-none resize-none"></textarea>
                                    <button id="post-button-desktop" 
                                    class="btn variant-filled" disabled=""> Post </button>
                                </div>

                                <div class="bg-white dark:bg-gray-800 border 
                border-gray-200 dark:border-gray-700 rounded-lg p-4 shadow-lg w-64 
                max-h-64 overflow-y-auto absolute mt-10 z-40" style="display:none" id="user-desktop">
                                    <ul class="space-y-2">
                                    </ul>
                                </div>

                                <div class="flex justify-start items-center">
                                    <button id="upload-button-desktop" class="invert dark:invert-0 p-2 rounded-full">
                                        <img src="../../assets/icons/image.png" alt="Ajouter une image" class="w-6 h-6">
                                    </button>

                                    <input type="file" id="file-input-desktop" class="hidden" multiple accept="image/*"
                                        max="4">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <button id="btn-mobile-modale" class="fixed bottom-20 right-4 w-14 h-14 bg-blue-500 rounded-full flex 
            items-center justify-center text-white text-6xl font-bold 
            shadow-lg hover:bg-blue-600 transition-colors z-50 md:hidden">
                    +
                </button>
                <div id="mobile-modal" class="fixed inset-0 bg-white dark:bg-gray-800 z-50 hidden flex-col">
                    <div class="flex items-center justify-between p-4 border-b dark:border-gray-700">
                        <button id="close-mobile-modal" class="text-base">Annuler</button>
                        <img src="../../assets/icons/logo.png" alt="logo" class="invert dark:invert-0 w-8 h-8">
                        <button id="post-button-mobile" class="px-4 py-2 bg-blue-500 text-white rounded-full">
                            Poster
                        </button>
                    </div>

                    <div class="flex-1 p-4">
                        <div class="flex gap-4">
                            <div class="flex-1">
                                <textarea id="post-text-area-mobile" maxlength="140"
                                    placeholder="écrivez votre ressenti ici"
                                    class="w-full bg-transparent border-none focus:outline-none resize-none mb-4 text-xl dark:text-white h-32 "></textarea>
                            </div>

                            <div class="bg-white dark:bg-gray-800 border 
              border-gray-200 dark:border-gray-700 rounded-lg p-4 shadow-lg w-64 
                max-h-64 overflow-y-auto absolute mt-10 z-40" style="display:none" id="user-mobile">
                                <ul class="space-y-2">
                                </ul>
                            </div>

                        </div>
                    </div>
                    <div class="border-t dark:border-gray-700 p-4">
                        <div class="flex justify-start items-center">
                            <button id="upload-button-mobile" class="invert dark:invert-0 p-2 rounded-full">
                                <img src="../../assets/icons/image.png" alt="Ajouter une image" class="w-6 h-6">
                            </button>
                            <input type="file" id="file-input-mobile" class="hidden" multiple accept="image/*" max="4">
                        </div>
                    </div>
                </div>

                <main class="flex-1 relative z-10 md:border-r border-black dark:border-white md:max-w-xl">
                    <div class="feed-desktop md:block">
                        <div id="tweets-container">
                            <?php
                            if (isset($replyData) && $replyData) {
                                foreach ($replyData as $index => $reply) {
                                    ?>
                                    <div class="p-4 w-full border-b border-b-black 
                                    dark:border-b-white tweet-container" data-post-id="<?php echo $reply["post_id"] ?>">
                                        <div class="flex gap-3">
                                            <div class="w-13 h-13 flex-shrink-0">
                                                <img src="../../assets/icons/profile.png" 
                                                alt="profile" class="invert dark:invert-0 w-12 
                                                h-12 object-cover rounded-full">
                                            </div>
                                            <div>
                                                <div class="flex items-center gap-2">
                                                    <a class="text-xl" 
                                                    href="./UserController.php?userId=<?php echo $reply["user_id"] ?>">
                                                        <?= $reply["display_name"] ?>
                                                    </a>
                                                    <a 
                                                    class="text-tertiary-500" 
                                                    href="./UserController.php?userId=<?php echo $reply["user_id"] ?>"
                                                    >
                                                        @<?= $reply["username"] ?>
                                                    </a>
                                                    <span class="text-xs">•</span>
                                                    <span class=""><?php echo $replyTime[$index] ?? $replyTime ?></span>
                                                </div>
                                                <div class="ml-0 mt-3">
                                                    <div class="text-small text-xl break-all max-w-full">
                                                        <?php
                                                        if ($reply && isset($reply['content'])) {
                                                            preg_match_all('/#[a-zA-Z0-9_]+/', $reply['content'], $hashtags);
                                                            if ($hashtags) {
                                                                foreach ($hashtags[0] as $hashtag) {
                                                                    $hashtagLink = '<a href="./SearchController.php?hashtag=' . ltrim($hashtag, '#') . '" class="text-primary-500">' . $hashtag . '</a>';
                                                                    $reply['content'] = str_replace($hashtag, $hashtagLink, $reply['content']);
                                                                }
                                                            }
                                                        }
                                                        echo $reply["content"];
                                                        ?>
                                                    </div>
                                                    <!-- ${imagesHtml} -->
                                                    <div class="flex items-center gap-4 mt-2">
                                                        <button class="flex items-center">
                                                            <img 
                                                                class="invert dark:invert-0 w-5 h-5" 
                                                                src="../../assets/icons/comment.png" 
                                                                alt="Commentaire"
                                                            >
                                                        </button>
                                                        <button 
                                                            class="repost-button flex items-center" 
                                                            data-post-id="<?php echo $reply["post_id"] ?>"
                                                        >
                                                            <img 
                                                                class="invert dark:invert-0 w-5 h-5" 
                                                                src="../../assets/icons/repost.png" 
                                                                alt="Repost"
                                                            >
                                                            <!-- <span>${tweet.nbr_retweet}</span> -->
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <?php
                                }
                            } ?>
                        </div>
                    </div>
        </div>
        </main>

            <?php else : ?>
        <div class="text-center p-6">
            <p>Aucun post trouvé.</p>
        </div>
            <?php endif; ?>
    </div>
    </div>
</body>
<?php

require_once('../Models/PostModel.php');
require_once('../Models/MediaModel.php');
require_once('../Models/UserModel.php');

use Model\Post;
use Model\User;
use Model\Media;

session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['user_id'] === null || empty($_SESSION['user_id'])) {
    header("Location: AuthController.php");
    exit;
}

if ($_GET) {
    switch ($_GET["request"]) {
        case "reply":
            $postId = $_GET['postId'];
            $postData = getPost($postId);
            $postTime = getPostTime($postData);
            $replyData = getPostReply($postId);
            $postMedia = Post::getPostMediaByPostId($postId);

            if ($postData && isset($postData['content'])) {
                preg_match_all('/#[a-zA-Z0-9_]+/', $postData['content'], $hashtags);
                if ($hashtags) {
                    foreach ($hashtags[0] as $hashtag) {
                        $hashtagLink = '<a href="./SearchController.php?hashtag=' . ltrim($hashtag, '#') . '" class="text-primary-500">' . $hashtag . '</a>';
                        $postData['content'] = str_replace($hashtag, $hashtagLink, $postData['content']);
                    }
                }

                preg_match_all('/@[a-zA-Z0-9_]+/', $postData['content'], $mentions);
                if ($mentions) {
                    foreach ($mentions[0] as $mention) {
                        $userId = User::retrieveIdWithUsername(ltrim($mention, '@'));
                        if ($userId) {
                            $mentionLink = '<a href="./UserController.php?userId=' . $userId . '" class="text-primary-500">' . $mention . '</a>';
                            $postData['content'] = str_replace($mention, $mentionLink, $postData['content']);
                        }
                    }
                }
            }

            foreach ($replyData as $data) {
                $replyTime[] = getPostTime($data);
            }
            include_once('../Views/reply/reply.php');
            exit;
    }
}


if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
    header('Content-Type: application/json');

    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'addPosts':
                $content = str_replace(search: "'", replace: "'", subject: $_POST['content']);
                $post = createPost(content: $content);
                handleHashtag($post, $_POST);
                break;
            case 'addPostsMedia':
                $post = handleMediaUpload($_FILES['images'], $_POST, htmlspecialchars($_SESSION['user_id']));
                handleHashtag($post->getId(), $_POST);
                break;
            case 'addReplyToPost':
                $postId = isset($_POST['postId']) ? intval($_POST['postId']) : null;
                if (!$postId) {
                    echo json_encode(['success' => false, 'message' => 'Post ID manquant ou invalide']);
                    exit;
                }
                $content = str_replace(search: "'", replace: "'", subject: $_POST['content']);
                $replyPost = createReply($postId, $content, htmlspecialchars($_SESSION['user_id']));
                handleHashtag($replyPost->getId(), $_POST);
                break;
            case 'addReplyToPostMedia':
                $postId = isset($_POST['postId']) ? intval($_POST['postId']) : null;
                if (!$postId) {
                    echo json_encode(['success' => false, 'message' => 'Post ID manquant ou invalide']);
                    exit;
                }
                $content = str_replace(search: "'", replace: "'", subject: $_POST['content']);
                $user = User::fetch(htmlspecialchars($_SESSION['user_id']));
                $replyPost = Post::createReply($postId, $content, $user);

                if ($replyPost) {
                    for ($i = 0; $i < count($_FILES['images']['name']); $i++) {
                        $singleMedia = [
                            'name' => $_FILES['images']['name'][$i],
                            'type' => $_FILES['images']['type'][$i],
                            'tmp_name' => $_FILES['images']['tmp_name'][$i],
                            'error' => $_FILES['images']['error'][$i],
                            'size' => $_FILES['images']['size'][$i]
                        ];

                        $uploadedMedia = processMediaUpload($singleMedia);
                        if ($uploadedMedia) {
                            $mediaPath = $uploadedMedia[0];
                            $mediaShortCode = $uploadedMedia[1];
                            $media = Media::create($mediaPath, $mediaShortCode);

                            if ($media) {
                                Post::attachMedia($replyPost, $media);
                            }
                        }
                    }
                    $response = [
                        'success' => true,
                        'data' => [
                            'postId' => $replyPost->getId(),
                            'userId' => $replyPost->getUserId(),
                            'content' => $replyPost->getContent(),
                            'createdAt' => getPostTime($replyPost->getCreatedAt()),
                            'userDisplayName' => $user->getDisplayName(),
                            'userName' => $user->getUsername(),
                            'images' => array_map(function ($img) {
                                return $img['file_name'];
                            }, Post::getPostMediaByPostId($replyPost->getId()))
                        ]
                    ];
                    echo json_encode($response);
                    handleHashtag($replyPost->getId(), $_POST);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Erreur lors de la création de la réponse']);
                }
                break;
            case 'getRetweetCount':
                if (isset($_POST['postId'])) {
                    $postId = intval($_POST['postId']);
                    $retweetCount = count(Post::getRetweetPosts($postId));
                    echo json_encode(['success' => true, 'retweetCount' => $retweetCount]);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Post ID manquant']);
                }
                break;
            case 'autoCompletation':
                if (isset($_POST['username'])) {
                    $username = htmlspecialchars($_POST['username']);
                    $users = User::searchUsernames(username: $username);
                    if ($users !== false) {
                        echo json_encode(['success' => true, 'data' => ['user' => $users]]);
                        die();
                    } else {
                        echo json_encode(['success' => false]);
                        die();
                    }
                }
                break;
            case 'getAllPosts':
                $user = User::fetch(htmlspecialchars($_SESSION['user_id']));
                $offset = isset($_POST['offset']) ? intval($_POST['offset']) : 0;
                $limit = isset($_POST['limit']) ? intval($_POST['limit']) : 10;
                getAllPost($user, $offset, $limit);
                break;
            case 'trendinHashtags':
                $user = User::fetch(htmlspecialchars($_SESSION['user_id']));
                getAllPost($user);
                break;

            case 'searchUser':
                $userId = User::retrieveIdWithUsername(htmlspecialchars($_POST["userName"]));

                if ($userId !== false) {
                    echo json_encode(['success' => true, 'data' => ['userId' => $userId]]);
                    die();
                } else {
                    echo json_encode(['success' => false]);
                    die();
                }

            case 'trendingHashtags':
                $hashtags = Post::getTrendingHashtags();
                if ($hashtags !== false) {
                    echo json_encode(['success' => true, 'data' => ['hashtags' => $hashtags]]);
                    die();
                } else {
                    echo json_encode(['success' => false]);
                    die();
                }

                break;
            case 'checkMention':
                if (isset($_POST['mention']) && !empty($_POST['mention'])) {
                    $mention = ltrim($_POST['mention'], '@');
                    $userId = User::retrieveIdWithUsername($mention);
                    if ($userId) {
                        echo json_encode(['success' => true, 'userId' => $userId]);
                    } else {
                        echo json_encode(['success' => false, 'userId' => null]);
                    }
                }
                break;
            default:
                echo json_encode(['success' => false, 'message' => "Méthode non reconnu"]);
        }
    } else {
        echo json_encode(['success' => false, 'message' => "Méthode non autorisée"]);
        return;
    }
    exit;
}

function getPost($postId)
{
    $post = Post::getById($postId);
    if ($post) {
        return $post;
    } else {
        return false;
    }
}

function getPostTime($postData)
{
    $timezone = new DateTimeZone('Europe/Paris');
    $now = new DateTime('now', $timezone);

    // Handle both DateTime objects and arrays
    if ($postData instanceof DateTime) {
        $postCreatedAt = $postData;
    } else {
        $postCreatedAt = new DateTime($postData['created_at'], $timezone);
    }

    $diffSeconds = $now->getTimestamp() - $postCreatedAt->getTimestamp();

    if ($diffSeconds < 60) {
        $relative_time = "à l'instant";
    } elseif ($diffSeconds < 3600) {
        $relative_time = floor($diffSeconds / 60) . 'm';
    } elseif ($diffSeconds < 86400) {
        $relative_time = floor($diffSeconds / 3600) . 'h';
    } elseif ($diffSeconds < 604800) {
        $relative_time = floor($diffSeconds / 86400) . 'j';
    } else {
        $relative_time = floor($diffSeconds / 604800) . 'sem';
    }

    // If $postData is an array, maintain the original behavior
    if (!($postData instanceof DateTime)) {
        $postData['relative_time'] = $relative_time;
        return $postData['relative_time'];
    }

    // Otherwise just return the calculated relative time
    return $relative_time;
}

function getAllPost($user, $offset = 0, $limit = 10)
{
    if ($user) {
        $follingList = $user->getAllFollowing($user->getId());
        if ($follingList === false) {
            echo json_encode(['success' => true, 'message' => 'Pas de tweet']);
            return;
        }

        $followingIds = array_map(function ($item) {
            return $item['following_id'];
        }, $follingList);

        $followingIds[] = $user->getId();

        $paginatedPosts = Post::getPaginatedPosts($followingIds, $offset, $limit);

        if (empty($paginatedPosts) && $offset === 0) {
            echo json_encode(['success' => true, 'message' => 'Pas de tweet']);
            return;
        }

        foreach ($paginatedPosts as &$post) {
            $post['media'] = Post::getPostMediaByPostId($post['post_id']);
            $post['nbr_retweet'] = count(Post::getRetweetPosts($post['post_id']));
        }

        $totalCount = Post::countTotalPosts($followingIds);
        $hasMore = ($offset + $limit) < $totalCount;

        echo json_encode([
            'success' => true,
            'posts' => $paginatedPosts,
            'hasMore' => $hasMore,
            'offset' => $offset + count($paginatedPosts)
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Utilisateur non trouvé']);
    }
}

/**
 * Handles the upload and processing of media files with associated post creation
 *
 * @param array $mediaFile The uploaded file array from $_FILES
 * @param array $postData The post data array containing userId and content
 * @return Post|bool Returns the created Post if successful, false otherwise
 */
function handleMediaUpload($mediaFiles, $postData, $id)
{
    if (count($mediaFiles['name']) > 4) {
        echo json_encode(['success' => false, 'message' => 'Maximum 4 images autorisées']);
        return false;
    }

    $user = User::fetch($id);
    $post = Post::create($user, $postData['content']);

    if (!$post) {
        echo json_encode(['success' => false, 'message' => 'Échec de la création du post']);
        return false;
    }

    $mediaResults = [];

    for ($i = 0; $i < count($mediaFiles['name']); $i++) {
        $singleMedia = [
            'name' => $mediaFiles['name'][$i],
            'type' => $mediaFiles['type'][$i],
            'tmp_name' => $mediaFiles['tmp_name'][$i],
            'error' => $mediaFiles['error'][$i],
            'size' => $mediaFiles['size'][$i]
        ];

        $uploadedMedia = processMediaUpload($singleMedia);

        if ($uploadedMedia) {
            $mediaPath = $uploadedMedia[0];
            $mediaShortCode = $uploadedMedia[1];
            $media = Media::create($mediaPath, $mediaShortCode);

            if ($media) {
                $isLinked = Post::attachMedia($post, $media);
                if ($isLinked) {
                    $mediaResults[] = [
                        'mediaId' => $media->getId(),
                        'fileName' => $media->getFileName(),
                        'shortUrl' => $media->getShortUrl(),
                        'createdAt' => $media->getCreatedAt()->format('Y-m-d H:i:s')
                    ];
                }
            }
        }
    }

    if (!empty($mediaResults)) {
        echo json_encode([
            'success' => true,
            'post' => [
                'postId' => $post->getId(),
                'userId' => $post->getUserId(),
                'content' => $post->getContent(),
                'createdAt' => $post->getCreatedAt()->format('Y-m-d H:i:s')
            ],
            'media' => $mediaResults
        ]);
        return $post;
    }

    echo json_encode(['success' => false, 'message' => 'Échec du traitement des médias']);
    return false;
}


/**
 * Processes and validates an uploaded media file
 *
 * @param array $mediaFile The uploaded file array from $_FILES
 * @return array|bool Returns array containing [destinationPath, shortCode] if successful, false otherwise
 */
function processMediaUpload($mediaFile)
{
    $uploadDirectory = __DIR__ . "/../../assets/upload/";

    $allowedMimeTypes = ['image/jpeg', 'image/png', 'image/gif'];
    if (!in_array($mediaFile['type'], $allowedMimeTypes)) {
        return false;
    }

    $fileExtension = pathinfo($mediaFile['name'], PATHINFO_EXTENSION);
    $shortCode = generateRandomCode();
    $uniqueFileName = $shortCode . '.' . $fileExtension;
    $destinationPath = $uploadDirectory . $uniqueFileName;

    $relativePath = "../../assets/upload/" . $uniqueFileName;

    if (!is_dir($uploadDirectory)) {
        mkdir($uploadDirectory, 0755, true);
    }
    if (move_uploaded_file($mediaFile['tmp_name'], $destinationPath)) {
        return [$relativePath, $shortCode];
    }
    return false;
}

/**
 * Generates a random alphanumeric code
 *
 * @param int $length Length of the code to generate (default: 6)
 * @return string The generated random code
 */
function generateRandomCode($length = 6)
{
    return substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, $length);
}

/**
 * Creates a new post in the database
 *
 * @param HomeController $homeController The home controller instance
 * @param string $content The content of the post
 * @return int|bool Returns the post ID if successful, false otherwise
 */
function createPost($content): int|null
{
    $result = Post::create(User::fetch($_SESSION["user_id"]), $content);
    if ($result instanceof Post) {
        $response = [
            'success' => true,
            'postId' => $result->getId(),
            'userId' => $result->getUserId(),
            'content' => $result->getContent(),
            'createdAt' => $result->getCreatedAt()->format('Y-m-d H:i:s')
        ];
        echo json_encode($response);
        return $result->getId();
    } else {
        echo json_encode(['success' => false]);
        return null;
    }
}

function createReply($postId, $content, $userId)
{
    $user = User::fetch($userId);
    $result = Post::createReply($postId, $content, $user);
    if ($result) {
        $response = [
            'success' => true,
            'postId' => $result->getId(),
            'userId' => $result->getUserId(),
            'content' => $result->getContent(),
            'createdAt' => getPostTime($result->getCreatedAt()),
            'userDisplayName' => $user->getDisplayName(),
            'userName' => $user->getUsername()
        ];
        echo json_encode(['success' => true, "data" => $response]);
        return $result;
    } else {
        echo json_encode(['success' => false]);
        return null;
    }
}

function getPostReply($postId)
{
    $result = Post::getReplyFromPost($postId);
    if ($result) {
        foreach ($result as &$reply) {
            $reply['media'] = Post::getPostMediaByPostId($reply['post_id']);
        }
    }
    return $result;
}

function handleHashtag($postId, $postData): void
{
    $content = str_replace(search: "'", replace: "'", subject: $postData['content']);
    preg_match_all('/#([\w]+)/', subject: $content, matches: $matches);
    if (!empty($matches[1])) {
        $uniqueHashtags = array_unique($matches[1]);
        foreach ($uniqueHashtags as $hashtag) {
            if (Post::checkExistingHashtag(hashtag: $hashtag) == false) {
                Post::insertHashtagIntoDatabase(hashtag: $hashtag);
            }
            $hashtagId = Post::getHashtagId(hashtag: $hashtag);
            Post::insertIntoPostHashtag(postId: $postId, hashtagId: $hashtagId);
        }
    }
}

include_once('../Views/home/home.php');

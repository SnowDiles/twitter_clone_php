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
                getAllPost($user);
                break;
            case 'retweet':
                if (Post::repost(idUser: $_SESSION["user_id"], idPosts: $_POST['postId']) == false) {
                    Post::deleteRepost($_SESSION["user_id"], idPosts: $_POST['postId']);
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

function getAllPost($user)
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

        $allPosts = [];
        foreach ($followingIds as $followingId) {
            $posts = $user->getAllPosts($followingId);
            if ($posts) {
                foreach ($posts as &$post) {
                    $media = $user->getPostMedia($followingId);
                    $post['media'] = array_filter($media, function ($m) use ($post) {
                        return $m['post_id'] == $post['post_id'];
                    });
                    $post['nbr_retweet'] = count(Post::getRetweetPosts($post['post_id']));
                }
                $allPosts = array_merge($allPosts, $posts);
            }
        }

        usort($allPosts, function ($a, $b) {
            return strtotime($b['created_at']) - strtotime($a['created_at']);
        });

        if (!empty($allPosts)) {
            echo json_encode(['success' => true, 'posts' => $allPosts]);
        } else {
            echo json_encode(['success' => true, 'message' => 'Pas de tweet']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Erreur lors de la création de l\'user.']);
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

function handleHashtag($post, $postData): void
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
            Post::insertIntoPostHashtag(postId: $post, hashtagId: $hashtagId);
        }
    }
}

include_once('../Views/home/home.php');

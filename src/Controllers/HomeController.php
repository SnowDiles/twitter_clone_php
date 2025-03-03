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
                $content = htmlspecialchars(string: $_POST['content']);
                $post = createPost(content: $content);
                preg_match(pattern: '/#(\w+)/', subject: $content, matches: $matches);
                if (!empty($matches[1])) {
                    if (Post::checkExistingHashtag(hashtag: $matches[1]) === false) {
                        Post::insertHashtagIntoDatabase(hashtag: $matches[1]);
                    }
                    $hashtagId = Post::getHashtagId(hashtag: $matches[1]);
                    Post::insertIntoPostHashtag($post, $hashtagId);
                }
                break;

            case 'addPostsMedia':
                handleMediaUpload($_FILES['image'], $_POST, htmlspecialchars($_SESSION['user_id']));
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
            default:
                echo json_encode(['success' => false, 'message' => "Méthode non reconnu"]);
        }
    } else {
        echo json_encode(['success' => false, 'message' => "Méthode non autorisée", 'data' => $data]);
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
 * @return bool Returns true if the upload and post creation was successful, false otherwise
 */
function handleMediaUpload($mediaFile, $postData, $id)
{
    $uploadedMedia = processMediaUpload($mediaFile);

    if ($uploadedMedia) {
        $user = User::fetch($id);
        $post = Post::create($user, $postData['content']);

        if ($post) {
            $mediaPath = $uploadedMedia[0];
            $mediaShortCode = $uploadedMedia[1];
            $media = Media::create($mediaPath, $mediaShortCode);

            if ($media) {
                $isLinked = Post::attachMedia($post, $media);

                if ($isLinked) {
                    echo json_encode([
                        'success' => true,
                        'post' => [
                            'postId' => $post->getId(),
                            'userId' => $post->getUserId(),
                            'content' => $post->getContent(),
                            'createdAt' => $post->getCreatedAt()->format('Y-m-d H:i:s')
                        ],
                        'media' => [
                            'mediaId' => $media->getId(),
                            'fileName' => $media->getFileName(),
                            'shortUrl' => $media->getShortUrl(),
                            'createdAt' => $media->getCreatedAt()->format('Y-m-d H:i:s')
                        ]
                    ]);
                    return;
                }
            }
        }
    }
    echo json_encode(['success' => false]);
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


include_once('../Views/home/home.php');

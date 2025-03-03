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
                $content = htmlspecialchars($_POST['content']);
                createPost($content);
                preg_match(pattern: '/#(\w+)/', subject: $content, matches: $matches);
                if (!empty($matches[1])) {
                    if (Post::checkExistingHashtag($matches[1]) === false) {
                        Post::insertHashtagIntoDatabase($matches[1]);
                    }
                }
                break;

            case 'addPostsMedia':
                handleMediaUpload($_FILES['image'], $_POST);
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
        }
    } else {
        echo json_encode(['success' => false, 'message' => "Méthode non autorisée", 'data' => $data]);
        return;
    }
    exit;
}

/**
 * Handles the upload and processing of media files with associated post creation
 *
 * @param HomeController $homeController The home controller instance
 * @param array $mediaFile The uploaded file array from $_FILES
 * @param array $postData The post data array containing userId and content
 * @return bool Returns true if the upload and post creation was successful, false otherwise
 */
function handleMediaUpload($mediaFile, $postData)
{
    $uploadedMedia = processMediaUpload($mediaFile);
    if ($uploadedMedia) {
        $user = User::fetch($postData['userId']);
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
function createPost($content)
{
    $result = Post::create(User::fetch($_SESSION["user_id"]), $content);

    if ($result instanceof Post) {
        echo json_encode([
            'success' => true,
            'postId' => $result->getId(),
            'userId' => $result->getUserId(),
            'content' => $result->getContent(),
            'createdAt' => $result->getCreatedAt()->format('Y-m-d H:i:s')
        ]);
    } else {
        echo json_encode(['success' => false]);
    }
}

include_once('../Views/home/home.php');

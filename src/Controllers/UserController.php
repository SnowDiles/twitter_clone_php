<?php

require_once('../Models/PostModel.php');
require_once('../Models/MediaModel.php');
require_once('../Models/UserModel.php');

use Model\Post;
use Model\User;
use Model\Auth;

session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['user_id'] === null || empty($_SESSION['user_id'])) {
    header("Location: AuthController.php");
    exit;
}

if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
    header('Content-Type: application/json');
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'getAllPosts':
                if (isset($_POST['userId'])) {
                    getPostsById($_POST['userId']);
                } else {
                    getPostsById($_SESSION['user_id']);
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

            case 'getConnections':
                handleConnectionsRequest($_POST['userId']);
                break;

            case 'retweet':
                if (Post::repost(idUser: $_SESSION["user_id"], idPosts: $_POST['postId']) == false) {
                    Post::deleteRepost($_SESSION["user_id"], idPosts: $_POST['postId']);
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
            case 'toggleFollow':
                handleFollowToggle($_POST['userId'], $_POST['isFollowing']);
                break;
            case 'userEditProfile':
                $userData = json_decode($_POST['data']);
                updateUserData($userData);
                break;
            case 'updateUserTheme':
                $theme = $_POST['data'];
                updateUserTheme($theme);
                break;
            default:
                echo json_encode(['success' => false, 'message' => "Méthode non autorisée ou non reconnue"]);
                break;
        }
    } else {
        echo json_encode(['success' => false, 'message' => "Action non spécifiée"]);
    }
    exit;
}

if (isset($_GET['page'])) {
    $page = $_GET['page'];
    if ($page === "following" || $page === "follower") {
        $CurrentUser = User::fetch($_GET['userId'] ?? $_SESSION['user_id']);
        include_once("../Views/user/connections.php");
        exit;
    }
}

if (isset($_GET['userId'])) {
    $id = $_GET['userId'];
    if ($id == $_SESSION['user_id']) {
        $CurrentUser = User::fetch($_SESSION['user_id']);
        include_once('../Views/user/currentProfile.php');
        exit;
    } else {
        $otherUser = User::fetch($id);
        $isFollowing = $otherUser->isFollowing($_SESSION['user_id'], $otherUser->getId());
        include_once('../Views/user/otherProfile.php');
        exit;
    }
} else {
    $CurrentUser = User::fetch($_SESSION['user_id']);
    include_once('../Views/user/currentProfile.php');
    exit;
}

function getPostsById($id)
{
    if ($id) {
        $posts = Post::getAllPostsByIdUser($id);
        $reposts = Post::getRepostByUserId($id);

        foreach ($reposts as &$repost) {
            $repost['repost'] = 'Vous avez retweeté';
            $repost['created_at'] = $repost['repost_created_at'];
        }
        unset($repost);

        $posts = array_merge($reposts, $posts);

        usort($posts, function ($a, $b) {
            return strtotime($b['created_at']) - strtotime($a['created_at']);
        });

        if ($posts) {
            foreach ($posts as &$post) {
                $post['nbr_retweet'] = count(Post::getRetweetPosts($post['post_id']));
                $media = Post::getPostMediaByPostId($post['post_id']);
                $post['media'] = $media;
            }
            unset($post);


            if (!empty($posts)) {
                echo json_encode(['success' => true, 'posts' => $posts]);
            } else {
                echo json_encode(['success' => true, 'message' => 'Pas de tweet']);
            }
        } else {
            echo json_encode(['success' => true, 'message' => 'Pas de tweet']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'ID utilisateur non spécifié']);
    }
}
function handleConnectionsRequest(int $userId): void
{
    $targetUser = User::fetch($userId ?? $_SESSION['user_id']);
    if (!$targetUser) {
        echo json_encode(['success' => false, 'message' => 'Utilisateur non trouvé']);
        exit;
    }

    $type = $_POST['type'] ?? 'following';
    $currentUserId = $_SESSION['user_id'];

    $connections = $targetUser->getConnections($targetUser->getId(), $type);
    $processedConnections = processConnections($connections, $targetUser, $currentUserId);

    echo json_encode([
        'success' => true,
        'data' => [
            'connection' => $processedConnections,
            'type' => $type
        ]
    ]);
    exit;
}

function processConnections(?array $connections, User $targetUser, int $currentUserId): array
{
    if (!$connections) {
        return [];
    }

    foreach ($connections as &$connection) {
        $connection['showButton'] = determineButtonVisibility(
            $connection['user_id'],
            $targetUser,
            $currentUserId
        );
        $connection['isFollowing'] = $targetUser->isFollowing($currentUserId, $connection['user_id']);
    }

    return $connections;
}

function determineButtonVisibility(int $connectionUserId, User $targetUser, int $currentUserId): bool
{
    if ($connectionUserId === $currentUserId) {
        return false;
    }

    return true;
}

function handleFollowToggle($userId, $isFollowing)
{
    $currentUser = User::fetch($_SESSION['user_id']);
    $targetUser = User::fetch($userId);

    if (!$targetUser || !$currentUser) {
        echo json_encode(['success' => false, 'message' => 'Utilisateur non trouvé']);
        exit;
    }

    if ($isFollowing === 'false') {
        $handle = $currentUser->addFollow($targetUser->getId());
    } else {
        $handle = $currentUser->removeFollow($targetUser->getId());
    }

    if ($handle) {
        echo json_encode([
            'success' => true
        ]);
    } else {
        echo json_encode([
            'success' => false
        ]);
    }
    exit;
}

function updateUserData($userData)
{
    $userId = $_SESSION['user_id'];
    $params = [];
    $setQueryParams = [];
    $paramsToBind = [':user_id' => $userId];

    foreach ($userData as $key => $value) {
        if (!empty($value)) {
            $params[$key] = $value;
        }
    }

    foreach ($params as $key => $value) {
        if ($key === 'name') {
            $setQueryParams[] = "`display_name` = :display_name";
        }
        if ($key === 'bio') {
            $setQueryParams[] = "`bio` = :bio";
        }
        if ($key === 'email') {
            $setQueryParams[] = "`email` = :email";
        }
        if ($key === 'newPassword') {
            $setQueryParams[] = "`password_hash` = :new_password";
        }
    }

    if (isset($params['newPassword'])) {
        $auth = new Auth(htmlspecialchars($params['newPassword']), null, null);
    }

    if (isset($params['name'])) {
        $paramsToBind[':display_name'] = htmlspecialchars($params['name']);
    }
    if (isset($params['bio'])) {
        $paramsToBind[':bio'] = htmlspecialchars($params['bio']);
    }
    if (isset($params['email'])) {
        $paramsToBind[':email'] = htmlspecialchars($params['email']);
    }
    if (isset($params['newPassword'])) {
        $paramsToBind[':new_password'] = $auth->getPasswordHash();
    }

    $result = User::updateInformations($setQueryParams, $paramsToBind);

    if ($result) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'There has been an issue while trying to update your informations'
        ]);
    }
}

function updateUserTheme($theme)
{
    $userId = $_SESSION['user_id'];
    $result = User::updateTheme($userId, htmlspecialchars($theme));

    if ($result) {
        $_SESSION['theme'] = $theme;
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'There has been an issue while trying to update your theme'
        ]);
    }
}

<?php

require_once('../Models/PostModel.php');
require_once('../Models/MediaModel.php');
require_once('../Models/UserModel.php');

use Model\Post;
use Model\User;

session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['user_id'] === null || empty($_SESSION['user_id'])) {
    header("Location: AuthController.php");
    exit;
}

if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
    header('Content-Type: application/json');
    if (isset($_POST['action']) && $_POST['action'] === 'getAllPosts') {
        if (isset($_POST['userId'])) {
            getPostsById($_POST['userId']);
        } else {
            getPostsById($_SESSION['user_id']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => "Méthode non autorisée ou non reconnue"]);
    }
    exit;
}

if (isset($_GET['userId'])) {
    $id = $_GET['userId'];
    if ($id == $_SESSION['user_id']) {
        $CurrentUser = User::fetch($_SESSION['user_id']);
        include_once('../Views/user/currentProfile.php');
        exit;
    } else {
        $otherUser = User::fetch($id);
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
            $repost['repost'] = 'vous avez retweete';
        }
        unset($repost);
        
        $posts = array_merge($posts, $reposts);
        
        if ($posts) {
            foreach ($posts as &$post) {
                $media = Post::getPostMediaByPostId($post['post_id']);
                $post['media'] = $media;
            }
            unset($post);

            usort($posts, function($a, $b) {
                return strtotime($b['created_at']) - strtotime($a['created_at']);
            });

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

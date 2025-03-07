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

if (isset($_GET['userId'])) {
    $id = $_GET['userId'];
    if ($id == $_SESSION['user_id']) {
        $CurrentUser = User::fetch($_SESSION['user_id']);
        fetch($_SESSION['user_id']);

        include_once('../Views/user/currentProfile.php');
        exit;
    } else {
        $otherUser = User::fetch($id);
        fetch($id);
        include_once('../Views/user/otherProfile.php');
        exit;
    }
} else {
    echo "Je suis l'utilisateur actuel depuis navbar";
    echo $id;

    $CurrentUser = User::fetch($_SESSION['user_id']);
    fetch($_SESSION['user_id']);
    include_once('../Views/user/currentProfile.php');
    exit;
}









function fetch($id){


    if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
        header('Content-Type: application/json');
    
        if (isset($_POST['action'])) {
            switch ($_POST['action']) {
               
               
                case 'getAllPosts':
                    getPostsById($id);
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
}











































function getPostsById($id)
{
    if ($id) {
        $posts = Post::getAllPostsByIdUser($id);
        if ($posts) {
            // Utiliser une référence pour modifier directement les éléments du tableau
            foreach ($posts as &$post) {
                $media = Post::getPostMediaByPostId($post['post_id']);
                $post['media'] = $media; // Ajouter les médias au post
            }
            unset($post); // Détruire la référence après la boucle pour éviter des effets secondaires

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
<?php

require_once('../Models/UserModel.php');
require_once('../Models/PostModel.php');

use Model\User;
use Model\Post;

session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_id'] === null || empty($_SESSION['user_id'])) {
    header("Location: AuthController.php");
    exit;
}
if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
    header('Content-Type: application/json');
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'autoCompletation':
                if (isset($_POST['hashtag'])) {
                    $hashtag = htmlspecialchars($_POST['hashtag']);
                    $hashtags = User::searchHashtag($hashtag);
                    if ($hashtags !== false) {
                        echo json_encode(['success' => true, 'data' => ['hashtag' => $hashtags]]);
                        die();
                    } else {
                        echo json_encode(['success' => false]);
                        die();
                    }
                }
                break;
            case 'getAllPosts':
                getPostsByHashtag($_POST['hashtag']);
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
        echo json_encode(['success' => false, 'message' => "Méthode non autorisée", 'data' => $data]);
        return;
    }
    exit;
}

function getPostsByHashtag($hashtag)
{
    if ($hashtag) {
        $posts = Post::getPostsByHashtag($hashtag);
        if ($posts) {
            $media = Post::getPostMediaByHashtag($hashtag);
            foreach ($posts as &$post) {
                $post['media'] = array_filter($media, function ($m) use ($post) {
                    return $m['post_id'] == $post['post_id'];
                });
            }
          
            if (!empty($posts)) {
                echo json_encode(['success' => true, 'posts' => $posts]);
            } else {
                echo json_encode(['success' => true, 'message' => 'Pas de tweet']);
            }
        } else {
            echo json_encode(['success' => true, 'message' => 'Pas de tweet']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Hashtag non spécifié']);
    }
}
include_once('../Views/search/search.php');

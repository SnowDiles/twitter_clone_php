<?php

require_once('../Models/UserModel.php');
use Model\User;

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
        }
    } else {
        echo json_encode(['success' => false, 'message' => "Méthode non autorisée", 'data' => $data]);
        return;
    }
    exit;
}
include_once('../Views/search/search.php');

<?php

require_once('../Models/UserModel.php');
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['user_id'] === null || empty($_SESSION['user_id'])) {
    header("Location: AuthController.php");
    exit;
}

use Model\User;


if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
    header('Content-Type: application/json');
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'getConnections':

                connections($_POST['userId']);
                break;
        }
    }
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
        include_once('../Views/user/otherProfile.php');
        exit;
    }
} else {
    $CurrentUser = User::fetch($_SESSION['user_id']);
    include_once('../Views/user/currentProfile.php');
    exit;
}


function connections($userId)
{
    $CurrentUser = User::fetch($userId ?? $_SESSION['user_id']);
    if (!$CurrentUser) {
        echo json_encode(['success' => false, 'message' => 'Utilisateur non trouvé']);
        exit;
    }

    $type = $_POST['type'] ?? 'following';
    
    if ($type === 'follower') {
        $connections = $CurrentUser->getFollowers($CurrentUser->getId());
    } else {
        $connections = $CurrentUser->getFollowing($CurrentUser->getId());
    }

    if ($connections) {
        $currentUserId = $_SESSION['user_id'];
        foreach ($connections as &$connection) {
            $connection['isFollowing'] = $CurrentUser->isFollowing($currentUserId, $connection['user_id']);
        }
    }

    echo json_encode([
        'success' => true, 
        'data' => [
            'connection' => $connections ?? [],
            'type' => $type
        ]
    ]);
    exit;
}
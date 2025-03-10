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
                handleConnectionsRequest($_POST['userId']);
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

    if ($targetUser->getId() === $currentUserId) {
        return true;
    }

    return !$targetUser->isFollowing($currentUserId, $targetUser->getId());
}

<?php

require_once('../Models/MessageModel.php');
require_once('../Models/MediaModel.php');
require_once('../Models/UserModel.php');

use Model\Message;
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
            case 'sendMessage':
                sendMessage();
                break;
            default:
                echo json_encode(['success' => false, 'message' => "Méthode non reconnue"]);
        }
    } else {
        echo json_encode(['success' => false, 'message' => "Méthode non autorisée"]);
        return;
    }
    exit;
}

require_once('../Views/message/message.php');

function sendMessage(): void
{
    $content = htmlspecialchars(str_replace(search: "'", replace: "'", subject: $_POST['content']));
    $sender = User::fetch($_SESSION["user_id"]);
    $receiverUsername = htmlspecialchars($_POST['receiver']);
    $receiverId = User::retrieveIdWithUsername($receiverUsername);

    if ($receiverId === null) {
        echo json_encode(
            [
                'success' => false,
                'message' => "L'utilisateur @$receiverUsername n'existe pas"
            ]
        );
        return;
    }

    if ($receiverId === $sender->getId()) {
        echo json_encode(
            [
                'success' => false,
                'message' => "Vous ne pouvez pas envoyer de message à vous même"
            ]
        );
        return;
    }

    $receiver = User::fetch($receiverId);

    $message = Message::create($sender, $receiver, $content);
    echo json_encode(['success' => true]);
}

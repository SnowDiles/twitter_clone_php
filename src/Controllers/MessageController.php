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
            case 'getConversations':
                getConversations();
                break;
            case 'getMessages':
                $otherId = htmlspecialchars($_POST['otherId']);
                getMessages($otherId);
                die();
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

function getMessages($otherId)
{
    $currentUserId = $_SESSION['user_id'];

    $messages = Message::getConversationMessages($currentUserId, $otherId);

    if ($messages) {
        echo json_encode([
            'success' => true, 
            'messages' => array_map(function($message) use ($currentUserId) {
                return [
                    'id' => $message['message_id'],
                    'content' => $message['content'],
                    'timestamp' => $message['sent_at'],
                    'isSelf' => $message['sender_id'] == $currentUserId,
                    'username' => $message['sender_id'] == $currentUserId ? 
                        'Moi' : $message['sender_username']
                ];
            }, $messages)
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => "Aucun message trouvé"
        ]);
    }
}

function getConversations()
{
    $user = User::fetch($_SESSION["user_id"]);
    $allConversation = $user->getAllConversation($user->getId());

    if ($allConversation) {
        echo json_encode((['success' => true, 'conversations' => $allConversation]));
    } else {
        echo json_encode((['success' => false, 'message' => "Aucune conversation"]));
    }
    return;
}

function sendMessage(): void
{
    $content = htmlspecialchars($_POST['content'], ENT_NOQUOTES);
    $sender = User::fetch($_SESSION["user_id"]);
    $receiverInput = htmlspecialchars($_POST['receiver']);
    
    $receiverId = is_numeric($receiverInput) ? 
        intval($receiverInput) : 
        User::retrieveIdWithUsername($receiverInput);

    if ($receiverId === null) {
        echo json_encode(
            [
                'success' => false,
                'message' => "L'utilisateur spécifié n'existe pas"
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

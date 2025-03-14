<?php

namespace Model;

require_once __DIR__ . "/../Controllers/PDOConnection.php";

use Environment\DB;
use DateTime;
use PDO;

class Message
{
    private int $id;
    private int $senderId;
    private int $receiverId;
    private string $content;
    private \DateTime $sentAt;

    private function __construct(
        int $id,
        int $senderId,
        int $receiverId,
        string $content,
        DateTime $sentAt = new DateTime()
    ) {
        $this->id = $id;
        $this->senderId = $senderId;
        $this->receiverId = $receiverId;
        $this->content = $content;
        $this->sentAt = $sentAt;
    }

    public static function create(User $sender, User $receiver, string $content): ?self
    {
        $pdo = DB::connection();
        $sqlQuery = "INSERT INTO Messages (sender_id, receiver_id, content)
        VALUES (:sender_id, :receiver_id, :content)";

        $stmt = $pdo->prepare($sqlQuery);
        $params = [
            ":sender_id" => $sender->getId(),
            ":receiver_id" => $receiver->getId(),
            ":content" => $content,
        ];

        if (!$stmt->execute($params)) {
            return null;
        }
        $postId = $pdo->lastInsertId();
        return new self($postId, $sender->getId(), $receiver->getId(), $content);
    }

    public static function getConversationMessages($userId, $otherUserId): array
    {
        $pdo = DB::connection();

        $sqlQuery = "SELECT 
                        m.message_id,
                        m.sender_id,
                        m.receiver_id,
                        m.content,
                        m.sent_at,
                        u_sender.username as sender_username,
                        u_receiver.username as receiver_username
                    FROM Messages m
                    JOIN Users u_sender ON m.sender_id = u_sender.user_id
                    JOIN Users u_receiver ON m.receiver_id = u_receiver.user_id
                    WHERE (m.sender_id = :userId1 AND m.receiver_id = :otherUserId1)
                    OR (m.sender_id = :userId2 AND m.receiver_id = :otherUserId2)
                    ORDER BY m.sent_at DESC
                ";

        $stmt = $pdo->prepare($sqlQuery);
        $params = [
            ":userId1" => $userId,
            ":otherUserId1" => $otherUserId,
            ":userId2" => $otherUserId,
            ":otherUserId2" => $userId
        ];

        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

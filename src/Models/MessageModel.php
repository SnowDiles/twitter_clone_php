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
        $this->userId = $userId;
        $this->userId = $receiverId;
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
}

<?php

namespace Model;

require_once __DIR__ . "/../Controllers/PDOConnection.php";

use Environment\DB;
use DateTime;

class Post
{
    private int $id;
    private int $userId;
    private string $content;
    private \DateTime $createdAt;

    private function __construct(int $id, int $userId, string $content, DateTime $createdAt = new DateTime())
    {
        $this->id = $id;
        $this->userId = $userId;
        $this->content = $content;
        $this->createdAt = $createdAt;
    }


    public static function create(User $user, string $content): ?Post
    {
        $pdo = DB::connection();
        $sqlQuery = "INSERT INTO Posts (user_id, content) VALUES (:userid, :content)";
        $stmt = $pdo->prepare($sqlQuery);

        $params = [
            ":userid" => $user->getId(),
            ":content" => $content
        ];

        if (!$stmt->execute($params)) {
            return null;
        }

        $postId = $pdo->lastInsertId();

        return new self($postId, $user->getId(), $content);
    }

    public static function attachMedia(Post $post, Media $media): bool
    {
        $pdo = DB::connection();
        $sqlQuery = "INSERT INTO PostMedia (post_id, media_id) VALUES (:postId, :mediaId)";
        $stmt = $pdo->prepare($sqlQuery);

        $params = [
            ":postId" => $post->getId(),
            ":mediaId" => $media->getId()
        ];

        return $stmt->execute($params);
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }
}

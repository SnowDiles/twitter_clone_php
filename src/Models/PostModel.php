<?php

namespace Model;

require_once __DIR__ . "/../Controllers/PDOConnection.php";

use Environment\DB;
use DateTime;
use PDO;
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



    public static function insertIntoPostHashtag($postId, $hashtagId): bool
    {
        $pdo = DB::connection();
        $sqlQuery = "INSERT INTO PostHashtag (post_id, hashtag_id) VALUES (:postId, :hashtagId)";
        $stmt = $pdo->prepare($sqlQuery);
        $params = [
            ":postId" => $postId,
            ":hashtagId" => $hashtagId
        ];
        return $stmt->execute($params);
    }

    public static function insertHashtagIntoDatabase(string $hashtag): bool
    {
        $pdo = DB::connection();
        $sqlQuery = "INSERT INTO Hashtags (tag) VALUES (:tag)";
        $stmt = $pdo->prepare($sqlQuery);
        $params = [
            ":tag" => $hashtag
        ];
        return $stmt->execute($params) !== false;
    }

    public static function checkExistingHashtag(string $hashtag): bool
    {
        $pdo = DB::connection();
        $sqlQuery = "SELECT * FROM Hashtags WHERE tag = :tag";
        $stmt = $pdo->prepare($sqlQuery);
        $params = [
            ":tag" => $hashtag
        ];
        $stmt->execute($params);
        return $stmt->fetch() !== false;
    }

    public static function getHashtagId(string $hashtag): ?int
    {
        $pdo = DB::connection();
        $sqlQuery = "SELECT hashtag_id FROM Hashtags WHERE tag = :tag";
        $stmt = $pdo->prepare($sqlQuery);
        $stmt->execute([":tag" => $hashtag]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? (int) $result['hashtag_id'] : null;
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

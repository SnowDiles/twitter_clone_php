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

    public static function createReply($postId, $content, $user): ?Post
    {
        $pdo = DB::connection();
        $query = "INSERT INTO Posts (user_id, content, reply_to) VALUES (:userid, :content, :postid)";
        $stmt = $pdo->prepare($query);
        $params = [
            ":userid" => $user->getId(),
            ":content" => $content,
            ":postid" => $postId
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

    public static function getAllPostsByIdUser(int $id): ?array
    {
        $pdo = DB::connection();

        $query = "SELECT
                p.post_id,
                p.content,
                u.username,
                u.display_name,
                p.created_at,
                u.user_id
              FROM
                Posts p
              JOIN
                Users u ON p.user_id = u.user_id
                
              WHERE
                p.user_id = :user_id
              AND
                p.reply_to IS NULL
              ORDER BY p.created_at DESC";
        $stmt = $pdo->prepare($query);

        $params = [
            ":user_id" => $id
        ];

        if (!$stmt->execute($params)) {
            return null;
        }

        return $stmt->fetchAll();
    }

    public static function getRepostByUserId(int $userId): ?array
    {
        $pdo = DB::connection();
        $query = "SELECT
                p.post_id,
                p.content,
                u.username,
                u.display_name,
                r.created_at AS repost_created_at, 
                u.user_id
              FROM 
                Reposts r
              JOIN
                Posts p ON r.post_id = p.post_id
              JOIN
                Users u ON p.user_id = u.user_id
              WHERE
                r.user_id = :user_id";

        $stmt = $pdo->prepare($query);

        $params = [":user_id" => $userId];

        if (!$stmt->execute($params)) {
            return null;
        }

        return $stmt->fetchAll();
    }


    public static function getPostMediaByPostId(int $postId): ?array
    {
        $pdo = DB::connection();

        $query = "SELECT
                p.post_id,
                p.content,
                m.media_id,
                m.file_name,
                m.short_url
              FROM
                Posts p
              JOIN
                PostMedia pm ON p.post_id = pm.post_id
              JOIN
                Media m ON pm.media_id = m.media_id
              WHERE
                p.post_id = :post_id
              ORDER BY
                p.created_at DESC";

        $stmt = $pdo->prepare($query);

        $params = [
            ":post_id" => $postId
        ];

        if (!$stmt->execute($params)) {
            return null;
        }

        return $stmt->fetchAll();
    }

    public static function getRetweetPosts($idPosts): ?array
    {
        $pdo = DB::connection();

        $query = "SELECT * FROM Reposts r WHERE r.post_id = ?";

        $stmt = $pdo->prepare($query);

        $params = [$idPosts];

        if (!$stmt->execute($params)) {
            return null;
        }

        return $stmt->fetchAll();
    }

    public static function repost(int $idUser, int $idPosts): bool
    {
        try {
            $pdo = DB::connection();

            $query = "INSERT INTO Reposts (post_id, user_id, created_at) 
            VALUES (:postId, :userId, current_timestamp())";

            $stmt = $pdo->prepare($query);

            $params = [
                ":postId" => $idPosts,
                ":userId" => $idUser
            ];

            return $stmt->execute($params);
        } catch (\PDOException $e) {
            return false;
        }
    }

    public static function deleteRepost(int $idUser, int $idPosts): bool
    {
        try {
            $pdo = DB::connection();

            $query = "DELETE FROM Reposts WHERE post_id = :postId AND user_id = :userId";

            $stmt = $pdo->prepare($query);

            $params = [
                ":postId" => $idPosts,
                ":userId" => $idUser
            ];

            return $stmt->execute($params);
        } catch (\PDOException $e) {
            return false;
        }
    }


    public static function getPostsByHashtag(string $hashtag): ?array
    {
        $pdo = DB::connection();

        $query = "SELECT
                p.post_id,
                p.content,
                u.username,
                u.display_name,
                p.created_at,
                u.user_id
              FROM
                Posts p
              JOIN
                Users u ON p.user_id = u.user_id
              JOIN
                PostHashtag ph ON p.post_id = ph.post_id
              JOIN
                Hashtags h ON ph.hashtag_id = h.hashtag_id
              WHERE
                h.tag = :hashtag
              ORDER BY p.created_at DESC";

        $stmt = $pdo->prepare($query);

        $params = [
            ":hashtag" => $hashtag
        ];

        if (!$stmt->execute($params)) {
            return null;
        }

        return $stmt->fetchAll();
    }


    public static function getTrendingHashtags(): array|null{

      $pdo = DB::connection();

      $query = "SELECT 
            p.hashtag_id, 
            h.tag, 
            COUNT(p.hashtag_id) AS nbr_use 
            FROM 
            PostHashtag p 
            JOIN 
            Hashtags h ON p.hashtag_id = h.hashtag_id 
            GROUP BY 
            p.hashtag_id 
            ORDER BY 
            COUNT(p.hashtag_id) DESC 
            LIMIT 5";

      $stmt = $pdo->prepare($query);

      if (!$stmt->execute()) {
        return null;
      }

      return $stmt->fetchAll(PDO::FETCH_ASSOC);

    }
    public static function getById(int $postId): ?array
    {
        $pdo = DB::connection();

        $query = "SELECT
                  p.post_id,
                  p.content,
                  u.username,
                  u.display_name,
                  p.created_at,
                  u.user_id
                FROM
                  Posts p
                JOIN
                  Users u ON p.user_id = u.user_id
                WHERE
                  p.post_id = :post_id
                LIMIT 1";

        $stmt = $pdo->prepare($query);

        $params = [
            ":post_id" => $postId
        ];

        if (!$stmt->execute($params)) {
            return null;
        }

        return $stmt->fetch();
    }

    public static function getHashtagId(string $hashtag): ?int
    {
        $pdo = DB::connection();
        $sqlQuery = "SELECT hashtag_id FROM Hashtags WHERE tag = :tag";
        $stmt = $pdo->prepare($sqlQuery);
        $params = [
            ":tag" => $hashtag
        ];
        $stmt->execute($params);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? (int) $result['hashtag_id'] : null;
    }
    public static function getPostMediaByHashtag(string $hashtag): ?array
    {
        $pdo = DB::connection();
        $query = "SELECT
                p.post_id,
                p.content,
                m.media_id,
                m.file_name,
                m.short_url
              FROM
                Posts p
              JOIN
                PostMedia pm ON p.post_id = pm.post_id
              JOIN
                Media m ON pm.media_id = m.media_id
              JOIN
                PostHashtag ph ON p.post_id = ph.post_id
              JOIN
                Hashtags h ON ph.hashtag_id = h.hashtag_id
              WHERE
                h.tag = :hashtag
              ORDER BY
                p.created_at DESC";

        $stmt = $pdo->prepare($query);

        $params = [
            ":hashtag" => $hashtag
        ];

        if (!$stmt->execute($params)) {
            return null;
        }
        return $stmt->fetchAll();
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

    public static function getReplyFromPost(string $postId): ?array
    {
        $pdo = DB::connection();
        $query = "SELECT
      p.post_id,
      p.content,
      u.username,
      u.display_name,
      p.created_at,
      u.user_id
      FROM
      Posts p
      JOIN
      Users u ON p.user_id = u.user_id
      WHERE
      p.reply_to = :postId
      ORDER BY p.created_at DESC";

        $stmt = $pdo->prepare($query);

        $params = [
            ":postId" => $postId
        ];

        if (!$stmt->execute($params)) {
            return null;
        }

        return $stmt->fetchAll();
    }

    public static function getPaginatedPosts(array $userIds, int $offset = 0, int $limit = 10): array
    {
        $pdo = DB::connection();

        $placeholders = implode(',', array_fill(0, count($userIds), '?'));

        $sql = "SELECT p.*, u.display_name, u.username
                FROM Posts p
                INNER JOIN Users u ON p.user_id = u.user_id
                WHERE p.user_id IN ({$placeholders})
                ORDER BY p.created_at DESC
                LIMIT ? OFFSET ?";

        $stmt = $pdo->prepare($sql);

        $paramIndex = 1;
        foreach ($userIds as $userId) {
            $stmt->bindValue($paramIndex++, $userId, PDO::PARAM_INT);
        }

        $stmt->bindValue($paramIndex++, $limit, PDO::PARAM_INT);
        $stmt->bindValue($paramIndex, $offset, PDO::PARAM_INT);

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function countTotalPosts(array $userIds): int
    {
        $pdo = DB::connection();

        $placeholders = implode(',', array_fill(0, count($userIds), '?'));

        $sql = "SELECT COUNT(*) FROM Posts WHERE user_id IN ({$placeholders})";
        $stmt = $pdo->prepare($sql);

        $paramIndex = 1;
        foreach ($userIds as $userId) {
            $stmt->bindValue($paramIndex++, $userId, PDO::PARAM_INT);
        }

        $stmt->execute();
        return (int)$stmt->fetchColumn();
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

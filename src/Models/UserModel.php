<?php

namespace Model;

require_once __DIR__ . "/../Controllers/PDOConnection.php";
require_once __DIR__ . "/../Models/AuthModel.php";

use Environment\DB;
use DateTime;
use PDO;
use PDOException;

class User
{
    private int $id;
    private string $username;
    private string $displayName;
    private string $email;
    private DateTime $dateOfBirth;
    private ?string $bio;
    private int $theme = 1;
    private DateTime $createdAt;

    private function __construct(
        int $id,
        string $username,
        string $displayName,
        string $email,
        DateTime $dateOfBirth,
        ?string $bio = null,
        int $theme = 1,
        DateTime $createdAt = new DateTime()
    ) {
        $this->id = $id;
        $this->username = $username;
        $this->displayName = $displayName;
        $this->email = $email;
        $this->dateOfBirth = $dateOfBirth;
        $this->bio = $bio;
        $this->createdAt = $createdAt;

        if ($theme) {
            $this->theme = $theme;
        }
    }

    public static function searchUsernames(string $username): ?array
    {
        $pdo = DB::connection();
        $query = "SELECT username FROM Users WHERE username LIKE ? LIMIT 5";
        $username .= '%';
        $stmt = $pdo->prepare($query);


        if (!$stmt->execute([$username])) {
            return null;
        }

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function searchHashtag(string $hashtag): ?array
    {
        $pdo = DB::connection();
        $query = "SELECT tag FROM Hashtags WHERE tag LIKE ? LIMIT 5";
        $hashtag .= '%';
        $stmt = $pdo->prepare($query);


        if (!$stmt->execute([$hashtag])) {
            return null;
        }

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function retrieveIdWithUsername(string $username): ?int
    {
        $pdo = DB::connection();
        $query = "SELECT user_id FROM Users WHERE username = ? LIMIT 1";
        $stmt = $pdo->prepare($query);
        if (!$stmt->execute([$username])) {
            return null;
        }
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? (int)$result['user_id'] : null;
    }

    public static function signUp(
        Auth $auth,
        string $displayName,
        DateTime $dateOfBirth,
        int $theme = 1
    ): ?User {
        $pdo = DB::connection();

        $checkEmailQuery = "SELECT COUNT(*) FROM Users WHERE email = ?";
        $checkStmt = $pdo->prepare($checkEmailQuery);

        if (!$checkStmt->execute([$auth->getEmail()])) {
            return null;
        }

        if ($checkStmt->fetchColumn() > 0) {
            return null;
        }

        $query = "INSERT INTO Users (username, display_name, password_hash, email, date_of_birth, theme_id)
        VALUES (:username, :display_name, :password_hash, :email, :date_of_birth, :theme_id);";

        $stmt = $pdo->prepare($query);

        $params = [
            ":username" => $auth->getUsername(),
            ":display_name" => $displayName,
            ":password_hash" => $auth->getPasswordHash(),
            ":email" => $auth->getEmail(),
            ":date_of_birth" => $dateOfBirth->format("Y-m-d"),
            ":theme_id" => $theme,
        ];

        if (!$stmt->execute($params)) {
            return null;
        }

        $id = $pdo->lastInsertId();
        return new User($id, $auth->getUsername(), $displayName, $auth->getEmail(), $dateOfBirth);
    }

    public static function fetch(int $id): ?User
    {
        $pdo = DB::connection();

        $query = "SELECT * FROM Users WHERE user_id = ?";
        $stmt = $pdo->prepare($query);

        if (!$stmt->execute([$id])) {
            return null;
        }

        $result = $stmt->fetch();

        if (!$result) {
            return null;
        }

        $user = new User(
            $id,
            $result["username"],
            $result["display_name"],
            $result["email"],
            new DateTime($result["date_of_birth"]),
            $result["bio"],
            $result["theme_id"],
            new DateTime($result["created_at"]),
        );

        return $user;
    }

    public function getAllFollowing(int $id): ?array
    {
        $pdo = DB::connection();

        $query = "SELECT following_id FROM Follows WHERE follower_id = :user_id";
        $stmt = $pdo->prepare($query);

        $params = [
            ":user_id" => $id
        ];

        if (!$stmt->execute($params)) {
            return null;
        }

        return $stmt->fetchAll();
    }

    public function getAllPosts(int $id): ?array
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

    public function getPostMedia(int $userId): ?array
    {
        $pdo = DB::connection();

        $query = " SELECT
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
                        p.user_id = :user_id
                    ORDER BY
                        p.created_at DESC";

        $stmt = $pdo->prepare($query);

        $params = [
            ":user_id" => $userId
        ];

        if (!$stmt->execute($params)) {
            return null;
        }

        return $stmt->fetchAll();
    }

    public function getConnections($userId, string $type = 'following'): ?array
    {
        $pdo = DB::connection();

        $whereCondition = $type === 'follower'
            ? 'F.following_id = :user_id'
            : 'F.follower_id = :user_id';

        $joinCondition = $type === 'follower'
            ? 'F.follower_id = U.user_id'
            : 'F.following_id = U.user_id';

        $query = "SELECT 
                    U.user_id,
                    U.username,
                    U.display_name
                FROM 
                    Users U
                INNER JOIN 
                    Follows F ON {$joinCondition}
                WHERE 
                    {$whereCondition}
                ORDER BY 
                    F.followed_at DESC";

        $stmt = $pdo->prepare($query);

        if (!$stmt->execute([':user_id' => $userId])) {
            return null;
        }

        return $stmt->fetchAll();
    }

    public function isFollowing($followerId, $followingId): bool
    {
        $pdo = DB::connection();

        $query = "SELECT COUNT(*) 
              FROM Follows 
              WHERE follower_id = :follower_id 
              AND following_id = :following_id";

        $stmt = $pdo->prepare($query);
        $stmt->execute([
            ':follower_id' => $followerId,
            ':following_id' => $followingId
        ]);

        return (bool) $stmt->fetchColumn();
    }

    public function getConnectionsCount(int $userId, string $type = 'following'): int
    {
        $pdo = DB::connection();

        $whereCondition = $type === 'follower'
            ? 'following_id = :user_id'
            : 'follower_id = :user_id';

        $query = "SELECT COUNT(*) 
                  FROM Follows 
                  WHERE {$whereCondition}";

        $stmt = $pdo->prepare($query);

        if (!$stmt->execute([':user_id' => $userId])) {
            return 0;
        }

        return (int) $stmt->fetchColumn();
    }

    public function addFollow(int $targetUserId): bool
    {
        $pdo = DB::connection();
        $query = "INSERT INTO Follows (follower_id, following_id) 
                VALUES (:follower_id, :following_id)";

        $stmt = $pdo->prepare($query);

        $params = [
            ":follower_id" => $this->id,
            ":following_id" => $targetUserId
        ];

        return $stmt->execute($params);
    }

    public function removeFollow(int $targetUserId): bool
    {
        $pdo = DB::connection();
        $query = "DELETE FROM Follows 
            WHERE follower_id = :follower_id 
            AND following_id = :following_id";

        $stmt = $pdo->prepare($query);

        $params = [
            ":follower_id" => $this->id,
            ":following_id" => $targetUserId
        ];

        return $stmt->execute($params);
    }

    public static function updateInformations($setQueryParams, $paramsToBind): bool
    {
        $pdo = DB::connection();

        $query = "UPDATE `Users` SET ";
        $query .= implode(', ', $setQueryParams);
        $query .= " WHERE `Users`.`user_id` = :user_id;";

        $stmt = $pdo->prepare($query);
        return $stmt->execute($paramsToBind);
    }

    public static function getPasswordHash(int $userId): ?string
    {
        $pdo = DB::connection();
        $query = "SELECT password_hash FROM Users WHERE user_id = :user_id LIMIT 1";
        $stmt = $pdo->prepare($query);

        if (!$stmt->execute([':user_id' => $userId])) {
            return null;
        }

        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? $result['password_hash'] : null;
    }

    public static function updateTheme(int $userId, string $theme): bool
    {
        $pdo = DB::connection();
        $query = "UPDATE Users 
        SET theme_id = COALESCE((SELECT theme_id FROM Themes WHERE theme_name = :theme), 1)  WHERE user_id = :user_id";

        $stmt = $pdo->prepare($query);

        return $stmt->execute([':user_id' => $userId, ':theme' => $theme]);
    }

    public static function getAllConversation(int $userId): array
    {
        $pdo = DB::connection();

        $sqlQuery = "SELECT DISTINCT
                        u.user_id,
                        u.username,
                        u.display_name,
                        (
                            SELECT content 
                            FROM Messages 
                            WHERE (sender_id = ? AND receiver_id = u.user_id) 
                            OR (sender_id = u.user_id AND receiver_id = ?)
                            ORDER BY sent_at DESC
                            LIMIT 1
                        ) as last_message,
                        (
                            SELECT sent_at 
                            FROM Messages 
                            WHERE (sender_id = ? AND receiver_id = u.user_id) 
                            OR (sender_id = u.user_id AND receiver_id = ?)
                            ORDER BY sent_at DESC
                            LIMIT 1
                        ) as last_message_time
                    FROM Users u
                    WHERE u.user_id IN (
                        SELECT sender_id FROM Messages WHERE receiver_id = ?
                        UNION
                        SELECT receiver_id FROM Messages WHERE sender_id = ?
                    )
                    ORDER BY last_message_time DESC
                ";

        $stmt = $pdo->prepare($sqlQuery);
        $stmt->execute([$userId, $userId, $userId, $userId, $userId, $userId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getDisplayName(): string
    {
        return $this->displayName;
    }

    public function getDateOfBirth(): DateTime
    {
        return $this->dateOfBirth;
    }

    public function getBio(): ?string
    {
        return $this->bio;
    }

    public function getTheme(): int
    {
        return $this->theme;
    }

    public function getEmail(): string
    {
        return $this->email;
    }
}

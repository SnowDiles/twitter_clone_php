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
    private DateTime $dateOfBirth;
    private ?string $bio;
    private int $theme = 1;
    private DateTime $createdAt;

    private function __construct(
        int $id,
        string $username,
        string $displayName,
        DateTime $dateOfBirth,
        ?string $bio = null,
        int $theme = 0,
        DateTime $createdAt = new DateTime()
    ) {
        $this->id = $id;
        $this->username = $username;
        $this->displayName = $displayName;
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

    public static function retrieveIdWithUsername($username): ?int
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
        DateTime $dateOfBirth
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

        $query = "INSERT INTO Users (username, display_name, password_hash, email, date_of_birth)
        VALUES (:username, :display_name, :password_hash, :email, :date_of_birth);";

        $stmt = $pdo->prepare($query);

        $params = [
            ":username" => $auth->getUsername(),
            ":display_name" => $displayName,
            ":password_hash" => $auth->getPasswordHash(),
            ":email" => $auth->getEmail(),
            ":date_of_birth" => $dateOfBirth->format("Y-m-d"),
        ];

        if (!$stmt->execute($params)) {
            return null;
        }

        $id = $pdo->lastInsertId();
        return new User($id, $auth->getUsername(), $displayName, $dateOfBirth);
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

    public function getFollows($userId)
    {
        $pdo = DB::connection();

        $query = " SELECT
                        U.username,
                        U.display_name
                    FROM
                        Follows F
                    JOIN
                        Users U ON F.following_id = U.user_id
                    WHERE
                        F.follower_id = :user_id";

        $stmt = $pdo->prepare($query);

        $params = [
            ":user_id" => $userId
        ];

        if (!$stmt->execute($params)) {
            return null;
        }

        return $stmt->fetchAll();
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
}

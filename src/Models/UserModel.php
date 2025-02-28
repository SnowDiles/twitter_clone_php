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

    public static function searchUsernames(string $username): array|null
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

    public static function searchHashtag(string $hashtag): array|null
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

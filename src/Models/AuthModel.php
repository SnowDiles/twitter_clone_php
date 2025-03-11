<?php

namespace Model;

require_once __DIR__ . "/../Controllers/PDOConnection.php";

use Environment\DB;
use Environment\Env;

class Auth
{
    // Can be either username or email for login
    private ?string $email;
    private ?string $username;
    private string $passwordHash;

    public function __construct(string $password, ?string $email, ?string $username)
    {
        $this->email = $email;
        $this->username = $username;
        $this->passwordHash = hash("ripemd160", $password . Env::get()->passwordSalt);
    }
    public function requestId(): int|false
    {
        $pdo = DB::connection();

        if (!$this->username && !$this->email) {
            return false;
        }

        $usedIdentifier = $this->username ? "username" : "email";
        $identifierValue = $this->username ?? $this->email;

        $query = "SELECT user_id FROM Users WHERE $usedIdentifier = :identifier AND password_hash = :passwordHash";
        $stmt = $pdo->prepare($query);

        $params = [
            ":identifier" => $identifierValue,
            ":passwordHash" => $this->passwordHash
        ];

        if (!$stmt->execute($params)) {
            return false;
        }

        $result = $stmt->fetch();
        return $result ? $result['user_id'] : false;
    }

    public function getEmail(): string
    {
        return $this->email;
    }
    public function getUsername(): string
    {
        return $this->username;
    }
    public function getPasswordHash(): string
    {
        return $this->passwordHash;
    }
}

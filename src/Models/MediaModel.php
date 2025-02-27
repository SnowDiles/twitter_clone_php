<?php

namespace Model;

require_once __DIR__ . "/../Controllers/PDOConnection.php";
use Environment\DB;

use DateTime;

class Media
{
    private int $id;
    private string $fileName;
    private string $shortUrl;
    private \DateTime $createdAt;

    private function __construct(int $id, string $fileName, string $shortUrl, \DateTime $createdAt = new \DateTime())
    {
        $this->id = $id;
        $this->fileName = $fileName;
        $this->shortUrl = $shortUrl;
        $this->createdAt = $createdAt;
    }

    public static function create(string $fileName, string $shortUrl): ?Media
    {
        $pdo = DB::connection();
        $sqlQuery = "INSERT INTO Media (file_name, short_url) VALUES (:fileName, :shortUrl)";
        $stmt = $pdo->prepare($sqlQuery);

        $params = [
            ":fileName" => $fileName,
            ":shortUrl" => $shortUrl
        ];

        if (!$stmt->execute($params)) {
            return null;
        }

        $mediaId = $pdo->lastInsertId();

        return new self($mediaId, $fileName, $shortUrl);
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getFileName(): string
    {
        return $this->fileName;
    }

    public function getShortUrl(): string
    {
        return $this->shortUrl;
    }

    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }
}

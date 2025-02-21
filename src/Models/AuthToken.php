<?php

namespace Model;

class AuthToken
{
    private string $token;
    private int $expirationTimestamp;
    private const EXPIRATION_TIME = 60 * 60 * 24 * 7;

    private function __construct(string $token, int $expirationTimestamp)
    {
        $this->token = $token;
        $this->expirationTimestamp = $expirationTimestamp;
    }

    private static function getJWTKey(): string
    {
        return $_ENV["JWT_PASSWORD"];
    }

    private static function base64UrlEncode(string $data): string
    {
        return rtrim(strtr(base64_encode($data), "+/", "-_"), "=");
    }

    private static function base64UrlDecode(string $data): string
    {
        return base64_decode(strtr($data, "-_", "+/") . str_repeat("=", 3 - (3 + strlen($data)) % 4));
    }

    public static function createFromUser(User $user): AuthToken
    {
        $header = ["alg" => "HS256", "typ" => "JWT"];

        $payload = ["id" => $user->getId() ];
        $expirationTimestamp = time() + self::EXPIRATION_TIME;
        $payload["exp"] = $expirationTimestamp;

        $base64Header = self::base64UrlEncode(json_encode($header));
        $base64Payload = self::base64UrlEncode(json_encode($payload));

        $signature = hash_hmac("sha256", "$base64Header.$base64Payload", self::getJWTKey(), true);
        $base64Signature = self::base64UrlEncode($signature);

        return new AuthToken("$base64Header.$base64Payload.$base64Signature", $expirationTimestamp);
    }

    public static function retrieveFromCookie(): int|false
    {
        if (!array_key_exists("token", $_COOKIE)) {
            return false;
        }

        $token = $_COOKIE["token"];

        $parts = explode(".", $token);
        if (count($parts) !== 3) {
            return false;
        }

        [$base64Header, $base64Payload, $base64Signature] = $parts;

        $header = json_decode(self::base64UrlDecode($base64Header), true);
        $payload = json_decode(self::base64UrlDecode($base64Payload), true);
        $signature = self::base64UrlDecode($base64Signature);

        if (!$header || !$payload || !$signature) {
            return false;
        }

        if (isset($payload["exp"]) && $payload["exp"] < time()) {
            return false;
        }

        $expectedSignature = hash_hmac("sha256", "$base64Header.$base64Payload", self::getJWTKey(), true);

        if (!hash_equals($expectedSignature, $signature)) {
            return false;
        }

        return $payload["id"];
    }

    public function store(): void
    {
        setcookie("token", $this->token, $this->expirationTimestamp, "", "", false, true);
    }
}

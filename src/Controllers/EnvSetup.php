<?php

namespace Environment;

require_once __DIR__ . '/../../vendor/autoload.php';

use Dotenv\Dotenv;

class Env
{
    private static ?Env $instance = null;

    public readonly string $dbName;
    public readonly string $dbServer;
    public readonly string $dbUsername;
    public readonly string $dbPassword;
    public readonly string $passwordSalt;

    private function __construct()
    {
        $dotenv = Dotenv::createImmutable(__DIR__ . '/../../');
        $dotenv->load();

        $this->dbName = $_ENV['DB_NAME'];
        $this->dbServer = $_ENV['DB_SERVER'];
        $this->dbUsername = $_ENV['DB_USERNAME'];
        $this->dbPassword = $_ENV['DB_PASSWORD'];
        $this->passwordSalt = $_ENV['PASSWORD_SALT'];
    }

    public static function get(): Env
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }
}

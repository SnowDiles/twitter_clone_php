<?php

namespace Environment;

require_once __DIR__ . '/EnvSetup.php';

use PDO;

class DB {
    private static ?PDO $instance = null;

    public static function connection(): PDO {
        if (self::$instance === null) {
            $env = Env::get();

            $dsn = "mysql:host=$env->dbServer;dbname=$env->dbName";

            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ];

            self::$instance = new PDO($dsn, $env->dbUsername, $env->dbPassword, $options);
        }

        return self::$instance;
    }
}
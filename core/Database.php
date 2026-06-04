<?php

namespace Core;

class Database
{
    private static ?\PDO $instance = null;

    public static function connect(): \PDO
    {
        if (self::$instance !== null) {
            return self::$instance;
        }

        $driver = env('DB_CONNECTION', 'mysql');

        self::$instance = match ($driver) {
            'mysql'  => self::connectMySQL(),
            'sqlite' => self::connectSQLite(),
            default  => throw new \Exception("Unsupported DB driver: {$driver}"),
        };

        return self::$instance;
    }

    private static function connectMySQL(): \PDO
    {
        $host     = env('DB_HOST', '127.0.0.1');
        $port     = env('DB_PORT', '3306');
        $database = env('DB_DATABASE', '');
        $username = env('DB_USERNAME', 'root');
        $password = env('DB_PASSWORD', '');

        if ($database === '') {
            throw new \RuntimeException('DB_DATABASE is not set.');
        }

        $dsn = "mysql:host={$host};port={$port};dbname={$database};charset=utf8mb4";

        return new \PDO($dsn, $username, $password, [
            \PDO::ATTR_ERRMODE            => \PDO::ERRMODE_EXCEPTION,
            \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
            \PDO::ATTR_EMULATE_PREPARES   => false,
            \PDO::ATTR_TIMEOUT            => 5,
        ]);
    }

    private static function connectSQLite(): \PDO
    {
        $database = env('DB_DATABASE', 'database.sqlite');
        $path     = BASE_PATH . '/' . $database;

        return new \PDO("sqlite:{$path}", null, null, [
            \PDO::ATTR_ERRMODE            => \PDO::ERRMODE_EXCEPTION,
            \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
        ]);
    }
}

<?php

class Database {
    private static $instance = null;

    public static function connect() {
        if (self::$instance === null) {
            $config = require __DIR__ . '/../../config/database.php';

            $dsn = "mysql:host={$config['host']};dbname={$config['dbname']};charset={$config['charset']}";

            self::$instance = new PDO($dsn, $config['username'], $config['password'], [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
            ]);
        }

        return self::$instance;
    }
}
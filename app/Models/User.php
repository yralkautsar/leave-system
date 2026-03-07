<?php

require_once __DIR__ . '/../Services/Database.php';

class User {

    public static function findByEmail($email) {
        $db = Database::connect();

        $stmt = $db->prepare("SELECT * FROM users WHERE email = :email AND is_active = 1 LIMIT 1");
        $stmt->execute(['email' => $email]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
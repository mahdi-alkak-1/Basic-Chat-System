<?php
// Backend/services/AuthService.php

require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/ResponseService.php';

class AuthService
{
    public static function registerUser(mysqli $connection, string $email, string $password)
    {
        $exists = User::findByEmail($connection, $email);

        if ($exists) {
            return ResponseService::response(400, "Account Already created");
        }

        $hashedpass = password_hash($password, PASSWORD_DEFAULT);
        $token      = bin2hex(random_bytes(32));

        $data = [
            'email'      => $email,
            'password'   => $hashedpass,
            'auth_token' => $token,
        ];

        $userId = User::create($connection, $data);

        $userData = [
            'id'    => $userId,
            'email' => $email,
            'token' => $token,
        ];

        return ResponseService::response(200, "User registered", $userData);
    }

    public static function loginUser(mysqli $connection, string $email, string $password)
    {
        $user = User::findByEmail($connection, $email);

        if (!$user) {
            return ResponseService::response(404, "User not found");
        }

        $checkpass = password_verify($password, $user->getPassword());
        if (!$checkpass) {
            return ResponseService::response(401, "Invalid Credentials");
        }

        $userData = [
            'id'    => $user->getId(),
            'email' => $user->getEmail(),
            'token' => $user->getAuthToken(),
        ];

        return ResponseService::response(200, "Loging Successful", $userData);
    }

    public static function getUserByToken(mysqli $connection, string $token)
    {
        $sql   = "SELECT * FROM users WHERE auth_token = ? LIMIT 1";
        $query = $connection->prepare($sql);
        $query->bind_param('s', $token);
        $query->execute();

        $row = $query->get_result()->fetch_assoc();

        if (!$row) {
            return null;
        }

        return new User($row);
    }
}

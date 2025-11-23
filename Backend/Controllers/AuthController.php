<?php

require_once __DIR__ . '/../services/AuthService.php';
require_once __DIR__ . '/../services/ResponseService.php';

class AuthController {

    public function register(mysqli $connection, ?string $token, array $data): string {

        $email = $data['email'] ?? null;
        $pass  = $data['password'] ?? null;

        if (!$email || !$pass) {
            return ResponseService::response(400, "Email and password required");
        }

        return AuthService::registerUser($connection, $email, $pass);
    }

    public function login(mysqli $connection, ?string $token, array $data): string {

        $email = $data['email'] ?? null;
        $pass  = $data['password'] ?? null;

        if (!$email || !$pass) {
            return ResponseService::response(400, "Email and password required");
        }

        return AuthService::loginUser($connection, $email, $pass);
    }
}

<?php

require_once __DIR__ . '/../services/ResponseService.php';
require_once __DIR__ . '/../services/AuthService.php';
require_once __DIR__ . '/../routes/web.php';
require_once __DIR__ . '/../config/connection.php';

// Route from frontend
$request = $_GET['route'] ?? '/';

// Token from headers
$token = $_SERVER['HTTP_X_AUTH_TOKEN'] ?? null;

// Read JSON body
$raw = file_get_contents("php://input");
$data = json_decode($raw, true) ?? [];

// Find matching route
if (!isset($apis[$request])) {
    echo ResponseService::response(404, "Route Not Found: $request");
    exit;
}

$controllerName = $apis[$request]['controller'];
$method = $apis[$request]['method'];

// Load controller
require_once __DIR__ . "/../controllers/{$controllerName}.php";
$controller = new $controllerName();

// Ensure method exists
if (!method_exists($controller, $method)) {
    echo ResponseService::response(
        500,
        "Method $method not found in controller $controllerName"
    );
    exit;
}

// Dispatch controller method
$response = $controller->$method($connection, $token, $data);
echo $response;

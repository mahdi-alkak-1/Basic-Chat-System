<?php
require_once "../services/AuthService.php";
require_once "../services/ResponseService.php";
require_once "../models/Message.php";
require_once "../config/connection.php";

// Decode JSON sent from Axios

$data = json_decode(file_get_contents("php://input"), true);

// READ TOKEN FROM AXIOS
$token = $_SERVER['HTTP_X_AUTH_TOKEN'] ?? null;

$conversationId = $data['conversation_id'] ?? 0;
$text = $data['message'] ?? "";

// Check token
$user = AuthService::getUserByToken($connection, $token);
if (!$user) {
    echo json_encode(["status" => 401, "message" => "Unauthorized"]);
    exit;
}

$userId = $user->getId();

// Insert message
$ok = Message::sendMessage($connection, $conversationId, $userId, $text);

if ($ok) {
    echo json_encode(["status" => 200, "message" => "sent"]);
} else {
    echo json_encode(["status" => 500, "message" => "Error while saving message"]);
}

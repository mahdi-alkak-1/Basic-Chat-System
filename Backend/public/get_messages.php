<?php

require_once "../services/AuthService.php";
require_once "../services/ResponseService.php";
require_once "../models/Message.php";
require_once "../config/connection.php";

$token = $_SERVER['HTTP_X_AUTH_TOKEN'] ?? null;
$conversationId = $_GET['conversation_id'] ?? 0;

$user = AuthService::getUserByToken($connection, $token);
if (!$user) {
    echo ResponseService::response(401, "Unauthorized");
    exit;
}

$userId = $user->getId();

// ✔ SECURITY CHECK: user must belong to conversation
$sql = "SELECT 1 FROM conversation_participants WHERE conversation_id = ? AND user_id = ?";
$check = $connection->prepare($sql);
$check->bind_param("ii", $conversationId, $userId);
$check->execute();
$valid = $check->get_result()->fetch_assoc();

if (!$valid) {
    echo ResponseService::response(403, "Access denied: Not your conversation");
    exit;
}

$messages = Message::getMessages($connection, $conversationId);

echo json_encode([
    "status" => 200,
    "messages" => array_map(function($msg){
        return [
            "id"        => $msg->getId(),
            "sender_id" => $msg->getSenderId(),
            "text"      => $msg->getText(),
            "sent_at"   => $msg->getSentAt(),
        ];
    }, $messages)
]);

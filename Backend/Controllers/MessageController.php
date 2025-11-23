<?php

require_once __DIR__ . '/../services/AuthService.php';
require_once __DIR__ . '/../services/ResponseService.php';
require_once __DIR__ . '/../models/Message.php';

class MessageController {

    public function sendMessage(mysqli $connection, ?string $token, array $data): string {

        $user = AuthService::getUserByToken($connection, $token);
        if (!$user)
            return ResponseService::response(401, "Unauthorized");

        $conversationId = $data['conversation_id'] ?? 0;
        $text = $data['message'] ?? "";

        if (!$text)
            return ResponseService::response(400, "Message text required");

        $ok = Message::sendMessage($connection, $conversationId, $user->getId(), $text);

        return ResponseService::response(200, "Message sent");
    }

    public function getMessages(mysqli $connection, ?string $token, array $data): string {

        $user = AuthService::getUserByToken($connection, $token);
        if (!$user)
            return ResponseService::response(401, "Unauthorized");

        $conversationId = $_GET['conversation_id'] ?? 0;

        $messages = Message::getMessages($connection, $conversationId);

        return ResponseService::response(200, "Messages loaded", [
            "messages" => array_map(fn($m) => [
                "id"         => $m->getId(),
                "sender_id"  => $m->getSenderId(),
                "text"       => $m->getText(),
                "sent_at"    => $m->getSentAt(),
                "delivered_at" => $m->getDeliveredAt(),
                "read_at"      => $m->getReadAt()
            ], $messages)
        ]);
    }

    public function markDelivered(mysqli $connection, ?string $token, array $data): string {

        $user = AuthService::getUserByToken($connection, $token);
        if (!$user)
            return ResponseService::response(401, "Unauthorized");

        $conversationId = $data['conversation_id'] ?? 0;

        Message::markDelivered($connection, $conversationId, $user->getId());

        return ResponseService::response(200, "Delivered set");
    }

    public function markRead(mysqli $connection, ?string $token, array $data): string {

        $user = AuthService::getUserByToken($connection, $token);
        if (!$user)
            return ResponseService::response(401, "Unauthorized");

        $conversationId = $data['conversation_id'] ?? 0;

        Message::markRead($connection, $conversationId, $user->getId());

        return ResponseService::response(200, "Read set");
    }
}

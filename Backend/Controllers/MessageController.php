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
        
        if (!$token) {
            return ResponseService::response(401, "Missing token");
        }
        $user = AuthService::getUserByToken($connection, $token);
        if (!$user)
            return ResponseService::response(401, "Unauthorized");

        $userId = $user->getId();
        $conversationId = $_GET['conversation_id'] ?? 0;

        if (!$conversationId) {
            return ResponseService::response(400, "conversation_id required");
        }

        $messages = Message::getMessages($connection, $conversationId);

            // 2️⃣ Count unread messages (only messages NOT sent by current user)
        $sql1 = "
            SELECT COUNT(*) AS unread
            FROM messages
            WHERE conversation_id = ?
            AND sender_id != ?
            AND read_at IS NULL
        ";
        $query = $connection->prepare($sql1);
        $query ->bind_param('ii', $conversationId, $userId);
        $query ->execute();
        $unread = $query->get_result()->fetch_assoc()['unread'];


        return ResponseService::response(200, "Messages loaded", [
            "messages" => array_map(fn($m) => [
                "id"         => $m->getId(),
                "sender_id"  => $m->getSenderId(),
                "text"       => $m->getText(),
                "sent_at"    => $m->getSentAt(),
                "delivered_at" => $m->getDeliveredAt(),
                "read_at"      => $m->getReadAt(),
            "unread_count"  => (int)$unread,
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

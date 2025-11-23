<?php

require_once __DIR__ . '/../services/ResponseService.php';
require_once __DIR__ . '/../services/AuthService.php';
require_once __DIR__ . '/../models/User.php';

class ConversationController {

    public function startConversation(mysqli $connection, ?string $token, array $data): string {

        if (!$token)
            return ResponseService::response(401, "Missing token");

        $currentUser = AuthService::getUserByToken($connection, $token);
        if (!$currentUser)
            return ResponseService::response(401, "Unauthorized");

        $friendEmail = $data['email'] ?? null;
        if (!$friendEmail)
            return ResponseService::response(400, "Email required");

        $friend = User::findByEmail($connection, $friendEmail);
        if (!$friend)
            return ResponseService::response(404, "User not found");

        $currentId = $currentUser->getId();
        $otherId   = $friend->getId();

        // Check if conversation already exists
        $sql = "
            SELECT c.id
            FROM conversations c
            JOIN conversation_participants p1 ON c.id = p1.conversation_id
            JOIN conversation_participants p2 ON c.id = p2.conversation_id
            WHERE p1.user_id = ? AND p2.user_id = ?
        ";

        $q = $connection->prepare($sql);
        $q->bind_param("ii", $currentId, $otherId);
        $q->execute();
        $existing = $q->get_result()->fetch_assoc();

        if ($existing)
            return ResponseService::response(200, "Existing chat", [
                "conversation_id" => $existing['id']
            ]);

        // Create new conversation
        $connection->query("INSERT INTO conversations () VALUES ()");
        $convId = $connection->insert_id;

        // Add participants
        $sql2 = "INSERT INTO conversation_participants (conversation_id, user_id) VALUES (?, ?), (?, ?)";
        $q2 = $connection->prepare($sql2);
        $q2->bind_param("iiii", $convId, $currentId, $convId, $otherId);
        $q2->execute();

        return ResponseService::response(200, "Chat created", [
            "conversation_id" => $convId
        ]);
    }

    public function listConversations(mysqli $connection, ?string $token, array $data): string {

        if (!$token)
            return ResponseService::response(401, "Missing token");

        $user = AuthService::getUserByToken($connection, $token);
        if (!$user)
            return ResponseService::response(401, "Unauthorized");

        $userId = $user->getId();

        $sql = "
            SELECT c.id, u.email AS other_email
            FROM conversations c
            JOIN conversation_participants p1 ON c.id = p1.conversation_id
            JOIN conversation_participants p2 ON c.id = p2.conversation_id
            JOIN users u ON u.id = p2.user_id
            WHERE p1.user_id = ? AND p2.user_id != ?
        ";

        $q = $connection->prepare($sql);
        $q->bind_param("ii", $userId, $userId);
        $q->execute();

        $res = $q->get_result();
        $rows = [];
        while ($row = $res->fetch_assoc())
            $rows[] = $row;

        return ResponseService::response(200, "Chats found", [
            "conversations" => $rows
        ]);
    }
    
}

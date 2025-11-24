<?php

require_once __DIR__ . '/../services/ResponseService.php';
require_once __DIR__ . '/../services/AuthService.php';
require_once __DIR__ . '/../services/ConversationService.php';
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
    
        $existing = ConversationService::CheckIfExist($connection,$currentId, $otherId);
        if ($existing)
            return ResponseService::response(200, "Existing chat", [
                "conversation_id" => $existing['id']
            ]);

        // Create new conversation
        $convId = ConversationService::CreateNewConv($connection);

        // Add participants
        ConversationService::AddParticipants($connection, $convId, $currentId,$otherId);

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

        $rows = ConversationService::ListConv($connection, $userId);

        return ResponseService::response(200, "Chats found", [
            "conversations" => $rows
        ]);
    }
    
}

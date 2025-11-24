<?php

require_once __DIR__ . '/../services/ResponseService.php';
require_once __DIR__ . '/../services/AuthService.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../config/apikey.php';

class AiController
{
    public function aiCatchUp(mysqli $connection, ?string $token, array $data): string
    {
        if (!$token)
            return ResponseService::response(401, "Missing Token");

        $user = AuthService::getUserByToken($connection, $token);
        if (!$user)
            return ResponseService::response(401, "Unauthorized");

        $email = $data['email'] ?? null;
        if (!$email)
            return ResponseService::response(400, "Email required");

        // Find the target user
        $friend = User::findByEmail($connection, $email);
        if (!$friend)
            return ResponseService::response(404, "User not found");

        $userId = $user->getId();
        $friendId = $friend->getId();

        // Look up an existing conversation
        $sql = "
            SELECT c.id
            FROM conversations c
            JOIN conversation_participants p1 ON c.id = p1.conversation_id
            JOIN conversation_participants p2 ON c.id = p2.conversation_id
            WHERE p1.user_id = ? AND p2.user_id = ?
        ";

        $q = $connection->prepare($sql);
        $q->bind_param("ii", $userId, $friendId);
        $q->execute();
        $row = $q->get_result()->fetch_assoc();

        if (!$row)
            return ResponseService::response(200, "No conversation", [
                "show_summary" => false,
                "summary" => "No conversation found with this user."
            ]);

        $conversationId = $row["id"];

        // Fetch unread messages
        $sql2 = "
            SELECT body
            FROM messages
            WHERE conversation_id = ?
            AND sender_id = ?
            AND read_at IS NULL
        ";

        $q2 = $connection->prepare($sql2);
        $q2->bind_param("ii", $conversationId, $friendId);
        $q2->execute();

        $res = $q2->get_result();
        $msgs = [];

        while ($m = $res->fetch_assoc())
            $msgs[] = $m["body"];

        if (count($msgs) < 3) {
            return ResponseService::response(200, "Not enough unread messages", [
                "show_summary" => false,
                "summary" => "Need at least 3 unread messages for a catch-up summary."
            ]);
        }

        // Prepare prompt
        $input = implode("\n", $msgs);

        // Call OpenAI
        $ch = curl_init("https://api.openai.com/v1/chat/completions");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Content-Type: application/json",
            "Authorization: Bearer " . OPENAI_API_KEY,
        ]);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
            "model" => "gpt-4o-mini",
            "messages" => [
                ["role" => "system", "content" => "Summarize these unread messages."],
                ["role" => "user", "content" => $input]
            ]
        ]));

        $response = curl_exec($ch);
        curl_close($ch);

        $decoded = json_decode($response, true);
        $summary = $decoded["choices"][0]["message"]["content"] ?? "AI failed to respond.";

        return ResponseService::response(200, "AI Summary Ready", [
            "show_summary" => true,
            "summary" => $summary
        ]);
    }
}

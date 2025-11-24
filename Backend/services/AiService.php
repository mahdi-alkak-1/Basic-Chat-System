<?php
require_once __DIR__ . '/../config/apikey.php';
class AiService{

    public static function ExistingConv(mysqli $connection, int $userId, int $friendId){


        
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



        return $row["id"];
    }
    
    public static function UnreadMessages(mysqli $connection,int $conversationId, int $friendId){
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

        return $msgs;

    }

    public static function CallAi(string $input){

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
        return $decoded["choices"][0]["message"]["content"] ?? "AI failed to respond.";

    }
}




?>
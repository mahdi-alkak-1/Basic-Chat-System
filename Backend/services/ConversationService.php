<?php

class ConversationService{
    public static function CheckIfExist(mysqli $connection, int $currentId, int $otherId){
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
        return $q->get_result()->fetch_assoc();
    }

    public static function CreateNewConv(mysqli $connection){
        $connection->query("INSERT INTO conversations () VALUES ()");
        return $connection->insert_id;
    }

    public static function AddParticipants(mysqli $connection,$convId, $currentId,$otherId){
        $sql2 = "INSERT INTO conversation_participants (conversation_id, user_id) VALUES (?, ?), (?, ?)";
        $q2 = $connection->prepare($sql2);
        $q2->bind_param("iiii", $convId, $currentId, $convId, $otherId);
        $q2->execute();
    }
    public static function ListConv(mysqli $connection, $userId){
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
        return $rows;
    }
}




?>
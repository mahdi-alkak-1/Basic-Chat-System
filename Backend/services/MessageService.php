<?php

class MessageService{
    public static function CountUnreadMessages(mysqli $connection, $conversationId,$userId){
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
        return $query->get_result()->fetch_assoc()['unread'];
    }
}





?>
<?php

require_once __DIR__ . '/Model.php';

class conversation extends Model{

    private int $id;
    private string $created_at;

    public function __construct(array $data)
    {
       if(!empty($data)){
            $this->id = $data['id'] ?? 0;
            $this->created_at = $data['created_at'] ?? null;
       }
    }


    public function getId(){
        return $this->id;
    }
    public function getCreatedAt(){
        return $this->created_at;
    }

   public static function createOrGet(mysqli $connection, int $u1, int $u2) {

        // Check existing conversation
        $sql = "
            SELECT c.id
            FROM conversations c
            JOIN conversation_participants p1 ON p1.conversation_id = c.id
            JOIN conversation_participants p2 ON p2.conversation_id = c.id
            WHERE p1.user_id = ? AND p2.user_id = ?
            LIMIT 1
        ";

        $q = $connection->prepare($sql);
        $q->bind_param("ii", $u1, $u2);
        $q->execute();

        $found = $q->get_result()->fetch_assoc();
        if ($found) return $found["id"];

        // Create conversation
        $connection->query("INSERT INTO conversations () VALUES ()");
        $convId = $connection->insert_id;

        // Add participants
        $sql = "INSERT INTO conversation_participants (conversation_id,user_id) VALUES (?,?,?,?)";
        $q = $connection->prepare($sql);
        $q->bind_param("iiii", $convId, $u1, $convId, $u2);
        $q->execute();

        return $convId;
    }
}


?>
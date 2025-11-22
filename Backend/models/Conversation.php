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

    public static function createConversation(mysqli $connection, array $userIds){
        $sql = sprintf('INSERT INTO conversations() VALUES()');
        $connection->query($sql);

        $conversationId = $connection->insert_id;

        $sql2 = sprintf('INSERT INTO conversation_participants (conversation_id, user_id) VALUES (?,?)');
        $query = $connection->prepare($sql2);

        foreach($userIds as $uid){
            $query->bind_param('ii', $conversationId, $uid);
            $query->execute();
        }
        return $conversationId;
    }
}


?>
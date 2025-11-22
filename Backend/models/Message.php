<?php
require_once __DIR__ . '/Model.php';
require_once __DIR__ . '/../services/ResponseService.php';

class Message extends Model{
    private int $id;
    private int $conversation_id;
    private int $sender_id;
    private string $body;
    private ?string $sent_at;
    private ?string $delivered_at;
    private ?string $read_at;

    public function __construct(array $data) {
        if (!empty($data)) {

            $this->id              = $data['id'] ?? 0;
            $this->conversation_id = $data['conversation_id'] ?? 0;
            $this->sender_id       = $data['sender_id'] ?? 0;
            $this->body            = $data['body'] ?? "";
            $this->sent_at         = $data['sent_at'] ?? null;
            $this->delivered_at    = $data['delivered_at'] ?? null;
            $this->read_at         = $data['read_at'] ?? null;
        }
    }

    public function getId(): int        { return $this->id; }
    public function getConversationId(): int    { return $this->conversation_id; }
    public function getSenderId(): string   { return $this->sender_id; }
    public function getText(): string { return $this->body; }
    public function getSentAt(): string   { return $this->sent_at; }
    public function getDeliveredAt(): string   { return $this->delivered_at; }
    public function getReadAt(): string   { return $this->read_at; }

    public static function sendMessage(mysqli $connection, int $conversationId, int $senderId,string $text){
        $sql = sprintf('INSERT INTO messages conversation_id, sender_id,body,sent_at VALUES (?,?,?');
        $query = $connection->prepare($sql);
        $query->bind_param('iis', $conversationId,$senderId,$text);
        
        if($query->execute()){
            return ResponseService::response(200, 'Message sent successfully');
        }
    }

    public static function getMessages(mysqli $connection, int $conversationId){
        $sql = sprintf('SELECT * FROM messages WHERE conversation_id = ? ORDER BY id ASC');
        $query = $connection->prepare($sql);
        $query->bind_param('i', $conversationId);
        $query->execute();

        $res = $query->get_result();
        $messages = [];

        while($row = $res->fetch_assoc()){
            $messages[] = new Message($row);
        }
        return $messages;
    }

    public static function markDelivered(mysqli $connection, int $conversationId, int $userId){
        $sql = sprintf('UPDATE messages 
                        SET delivered_at = NOW()
                        WHERE conversation_id = ? 
                        AND sender_id != ?
                        AND delivered_at IS NULL');

        $query = $connection->prepare($sql);
        $query->bind_param('ii', $conversation_id,$userId);
        if($query->execute()){
            return ResponseService::response(200,'message is Delivered');
        }
    }

    public static function markRead(mysqli $connection, int $conversationId, int $userId){
        $sql = sprintf('UPDATE messages 
                        SET read_at = NOW()
                        WHERE conversation_id = ? 
                        AND sender_id != ?
                        AND read_at IS NULL');

        $query = $connection->prepare($sql);
        $query->bind_param('ii', $conversation_id,$userId);
        if($query->execute()){
            return ResponseService::response(200,'message is read');
        }
    }
}



?>
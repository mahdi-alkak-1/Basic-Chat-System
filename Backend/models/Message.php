<?php
require_once __DIR__ . '/Model.php';

class Message extends Model {

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

    // ==== GETTERS ====

    public function getId(): int { return $this->id; }
    public function getConversationId(): int { return $this->conversation_id; }
    public function getSenderId(): int { return $this->sender_id; }
    public function getText(): string { return $this->body; }
    public function getSentAt(): ?string { return $this->sent_at; }
    public function getDeliveredAt(): ?string { return $this->delivered_at; }
    public function getReadAt(): ?string { return $this->read_at; }


    // ==== INSERT MESSAGE ====

    public static function sendMessage(mysqli $connection, int $conversationId, int $senderId, string $text): bool {

        $sql = "
            INSERT INTO messages (conversation_id, sender_id, body, sent_at)
            VALUES (?, ?, ?, NOW())
        ";

        $query = $connection->prepare($sql);
        $query->bind_param('iis', $conversationId, $senderId, $text);

        return $query->execute();
    }


    // ==== GET ALL MESSAGES ====

    public static function getMessages(mysqli $connection, int $conversationId): array {

        $sql = "SELECT * FROM messages WHERE conversation_id = ? ORDER BY id ASC";

        $query = $connection->prepare($sql);
        $query->bind_param('i', $conversationId);
        $query->execute();

        $res = $query->get_result();

        $messages = [];
        while ($row = $res->fetch_assoc()) {
            $messages[] = new Message($row);
        }

        return $messages;
    }


    // ==== MARK AS DELIVERED ====

    public static function markDelivered(mysqli $connection, int $conversationId, int $userId): bool {

        $sql = "
            UPDATE messages
            SET delivered_at = NOW()
            WHERE conversation_id = ?
            AND sender_id != ?
            AND delivered_at IS NULL
        ";

        $query = $connection->prepare($sql);
        $query->bind_param('ii', $conversationId, $userId);

        return $query->execute();
    }


    // ==== MARK AS READ ====

    public static function markRead(mysqli $connection, int $conversationId, int $userId): bool {

        $sql = "
            UPDATE messages
            SET read_at = NOW()
            WHERE conversation_id = ?
            AND sender_id != ?
            AND read_at IS NULL
        ";

        $query = $connection->prepare($sql);
        $query->bind_param('ii', $conversationId, $userId);

        return $query->execute();
    }
}
?>

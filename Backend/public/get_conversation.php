<?php
require_once "../services/AuthService.php";
require_once "../services/ResponseService.php";
require_once "../config/connection.php";

$token = $_SERVER['HTTP_X_AUTH_TOKEN'] ?? null;
$data = json_decode(file_get_contents("php://input"), true);

$otherUserId = $data['other_user_id'] ?? 0;

$user = AuthService::getUserByToken($connection, $token);
if (!$user) {
    echo json_encode(["status" => 401, "message" => "Unauthorized"]);
    exit;
}

$currentUserId = $user->getId();

/*
    Step 1: Check if conversation already exists
*/
$sql = "
    SELECT c.id 
    FROM conversations c
    JOIN conversation_participants p1 ON p1.conversation_id = c.id
    JOIN conversation_participants p2 ON p2.conversation_id = c.id
    WHERE p1.user_id = ? AND p2.user_id = ?
";

$q = $connection->prepare($sql);
$q->bind_param("ii", $currentUserId, $otherUserId);
$q->execute();
$res = $q->get_result()->fetch_assoc();

if ($res) {
    echo json_encode(["status" => 200, "conversation_id" => $res['id']]);
    exit;
}

/*
    Step 2: Create conversation
*/
$connection->query("INSERT INTO conversations () VALUES ()");
$newId = $connection->insert_id;

/*
    Step 3: Add both users
*/
$sql2 = "
    INSERT INTO conversation_participants (conversation_id, user_id)
    VALUES (?, ?), (?, ?)
";

$q2 = $connection->prepare($sql2);
$q2->bind_param("iiii", $newId, $currentUserId, $newId, $otherUserId);
$q2->execute();

echo json_encode(["status" => 200, "conversation_id" => $newId]);

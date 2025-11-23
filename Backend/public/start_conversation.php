<?php

require_once "../services/AuthService.php";
require_once "../services/ResponseService.php";
require_once "../config/connection.php";
require_once "../models/User.php";

// Read token
$token = $_SERVER['HTTP_X_AUTH_TOKEN'] ?? null;
if (!$token) {
    echo ResponseService::response(401, "Unauthorized user");
    exit;
}

// Decode JSON as associative array
$input = file_get_contents("php://input");
$data = json_decode($input, true);

$friendEmail = $data['email'] ?? null;
if (!$friendEmail) {
    echo ResponseService::response(400, "Email is required");
    exit;
}

// Get current logged in user
$currentUser = AuthService::getUserByToken($connection, $token);
if (!$currentUser) {
    echo ResponseService::response(401, "Invalid token");
    exit;
}

$currentUserId = $currentUser->getId();

// Find the other user
$friend = User::findByEmail($connection, $friendEmail);
if (!$friend) {
    echo ResponseService::response(404, "User with email $friendEmail not found");
    exit;
}

$otherUserId = $friend->getId();

// Check if conversation exists
$sql = "
    SELECT c.id
    FROM conversations c
    JOIN conversation_participants p1 ON p1.conversation_id = c.id
    JOIN conversation_participants p2 ON p2.conversation_id = c.id
    WHERE p1.user_id = ? AND p2.user_id = ?
";
$q2 = $connection->prepare($sql);
$q2->bind_param("ii", $currentUserId, $otherUserId);
$q2->execute();
$existing = $q2->get_result()->fetch_assoc();

if ($existing) {
    echo json_encode(["status" => 200, "conversation_id" => $existing["id"]]);
    exit;
}

// Create conversation
$connection->query("INSERT INTO conversations () VALUES ()");
$newId = $connection->insert_id;

// Add participants
$sql2 = "
    INSERT INTO conversation_participants (conversation_id, user_id)
    VALUES (?, ?), (?, ?)
";
$q3 = $connection->prepare($sql2);
$q3->bind_param("iiii", $newId, $currentUserId, $newId, $otherUserId);
$q3->execute();

// Return success
echo json_encode(["status" => 200, "conversation_id" => $newId]);
exit;

?>

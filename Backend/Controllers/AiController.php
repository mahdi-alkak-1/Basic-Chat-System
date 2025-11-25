    <?php

    require_once __DIR__ . '/../services/ResponseService.php';
    require_once __DIR__ . '/../services/AuthService.php';
    require_once __DIR__ . '/../services/AiService.php';
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


            $conversationId = AiService::ExistingConv($connection,$userId,$friendId);//get id of same conversations
                if (!$conversationId){
                    return ResponseService::response(200, "No conversation", [
                        "show_summary" => false,
                        "summary" => "No conversation found with this user."
                    ]);
                }    
            // Fetch unread messages
            $msgs = AiService::UnreadMessages($connection, $conversationId,$friendId);//get unread messages > 3

            if (!$msgs || count($msgs) < 3) {
                return ResponseService::response(200, "Not enough unread messages", [
                    "show_summary" => false,
                    "summary" => "Need at least 3 unread messages for a catch-up summary."
                ]);
            }
            // Prepare prompt
            $input = implode("\n", $msgs);

            // Call OpenAI
            $summary = AiService::CallAi($input);

            return ResponseService::response(200, "AI Summary Ready", [
                "show_summary" => true,
                "summary" => $summary
            ]);
        }
    }

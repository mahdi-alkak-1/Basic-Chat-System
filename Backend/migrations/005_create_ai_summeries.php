
<?php
require_once('../config/connection.php');

$sql = "
CREATE TABLE ai_summaries (
    id INT AUTO_INCREMENT PRIMARY KEY,
    conversation_id INT,
    user_id INT,
    summary_text TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (conversation_id) REFERENCES conversations(id),
    FOREIGN KEY (user_id) REFERENCES users(id)
)";
$query = $connection->prepare($sql);

$query->execute();
if(!$query){
    echo"error in creating ai summeries table";
}else{
    echo"ai summeries table created succ";
}
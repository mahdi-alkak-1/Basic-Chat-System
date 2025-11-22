<?php
require_once('../config/connection.php');
$sql = "
CREATE TABLE conversation_participants (
    id INT AUTO_INCREMENT PRIMARY KEY,
    conversation_id INT,
    user_id INT,
    FOREIGN KEY (conversation_id) REFERENCES conversations(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
)";
$query=$connection->prepare($sql);
$query->execute();
if(!$query){
    echo"error in creating participants table";
}else{
    echo"participants table created succ";
}
?>
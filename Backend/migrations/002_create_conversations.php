<?php
require_once('../config/connection.php');
$sql = "
CREATE TABLE conversations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";
$query =  $connection->prepare($sql);
$query->execute();
if(!$query){
    echo"error in creating conversations table";
}else{
    echo"converstaions table created succ";
}
?>
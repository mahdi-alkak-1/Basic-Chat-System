<?php
$sql = "
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) UNIQUE,
    password VARCHAR(255),
    auth_token VARCHAR(255), 
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";
$query = $connection->prepare($sql);
$query->execute();
if(!$query){
    echo"error in creating users table";
}else{
    echo"users table created succ";
}

?>
<?php 
require_once('headers.php');

$connection = new mysqli ("localhost", "root", "", "chat_db");

if($connection->connect_error){
    die("connection error" . $connection->connect_error);
}

?>  
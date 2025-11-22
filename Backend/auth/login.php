<?php 

require('../config/connection.php');
require('../services/AuthService.php');

$input =file_get_contents('php://input');
$data = json_decode($input, true);

$email = $data['email'] ?? null;
$password = $data['password'] ?? null;
    
if($email === null || $password === null){
    echo ResponseService::response(400, "Email or password missing");
    exit;
}

$response = AuthService::loginUser($connection, $email, $password);

echo $response;
?>  
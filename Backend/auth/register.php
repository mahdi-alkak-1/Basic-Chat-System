<?php 

require('../config/connection.php');
require('../services/AuthService.php');

$input = file_get_contents("php://input");//get data as json array
$data = json_decode($input, true); // convert data from json array to associative array

$email = $data['email'] ?? null;
$password = $data['password'] ?? null;

if ($email === null || $password === null) {
    echo ResponseService::response(400, "Email or password missing");
    exit;
}

$response = AuthService::registerUser($connection, $email, $password);

echo $response;
?>

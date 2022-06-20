<?php

header('Access-Control-Allow-Origin:*');
header('Content-Type:application/json');
header('Access-Control-Allow-Methods:POST');

include_once '../../config/DB.php';
include_once '../../Models/User.php';






if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    echo json_encode(array('message' => 'use post method'), 401);
    exit;
}

if (!isset($_POST['email']) || !isset($_POST['pwd'])) {
    http_response_code(206);
    echo 'insert all data';
    die(203);
}


if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
    echo 'enter a real email';
    die(203);
}



$database = new Database();

$db = $database->connect();
$user = new User($db);

$user->email = $_POST['email'];
$user->pwd = $_POST['pwd'];

$user = $user->login();

$userArr['name']=$user['name'];
$userArr['email']=$user['email'];
$userArr['uid']=$user['uid'];




if ($user) {
    // http_response_code(201);
    echo json_encode($userArr, 201);
} else {
    echo json_encode(array('message' => 'error'), 401);

}

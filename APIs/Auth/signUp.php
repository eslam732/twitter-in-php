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

if (!isset($_POST['name']) || !isset($_POST['email']) || !isset($_POST['uid']) ||
    !isset($_POST['pwd']) || !isset($_POST['cpwd']) || !isset($_POST['privacy'])) {
    http_response_code(206);
    echo 'insert all data';
    die(203);
}

if (!preg_match("/^[a-zA-Z0-9]*$/", $_POST['uid'])) {
    echo 'enter a real UID';
    die(203);
}

if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
    echo 'enter a real email';
    die(203);
}

if ($_POST['pwd'] !== $_POST['cpwd']) {
    echo 'password and confirm password must be the same';
    die(203);
}
$hasedPwd=password_hash($_POST['pwd'],PASSWORD_DEFAULT);

$database = new Database();

$db = $database->connect();
$user = new User($db);

$user->name = $_POST['name'];
$user->email = $_POST['email'];
$user->uid = $_POST['uid'];
$user->privacy = $_POST['privacy'];
$user->pwd = $hasedPwd;

$res = $user->create();

if ($res) {
    // http_response_code(201);
    echo json_encode(array('message' => 'user created'), 201);
} else {
    echo json_encode(array('message' => 'user not created'), 401);

}

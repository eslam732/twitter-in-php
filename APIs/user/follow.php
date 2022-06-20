<?php

header('Access-Control-Allow-Origin:*');
header('Content-Type:application/json');
header('Access-Control-Allow-Methods:POST');

include_once '../../config/DB.php';
include_once '../../Models/Follow.php';
include_once '../../Helpers/IsAuthinticated.php';
include_once '../../Helpers/cretaeNotification.php';


isLoggedIn();




if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    echo json_encode(array('message' => 'use post method'), 401);
    exit;
}

if (!isset($_POST['followed_user'])) {
    http_response_code(206);
    echo 'insert followed_user';
    die(203);
}


$database = new Database();

$db = $database->connect();

$follow = new Follow($db);

$follow->followed_user = $_POST['followed_user'];

$follow=$follow->follow();

echo $follow;

//createNotification($_POST['followed_user'],)
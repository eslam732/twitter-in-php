<?php

header('Access-Control-Allow-Origin:*');
header('Content-Type:application/json');
header('Access-Control-Allow-Methods:POST');

include_once '../../config/DB.php';
include_once '../../Models/Follow.php';
include_once '../../Helpers/IsAuthinticated.php';


isLoggedIn();


if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    echo json_encode(array('message' => 'use post method'), 401);
    exit;
}

if (!isset($_POST['reqId'])) {
    http_response_code(206);
    echo 'insert request id';
    die(203);
}

$database = new Database();

$db = $database->connect();

$follow = new Follow($db);

// $follow=array();

$follow=$follow->acceptRequest($_POST['reqId']);

http_response_code(200);

echo $follow;



//return json_encode(['datat is '.$follow],http_response_code(200));
<?php

header('Access-Control-Allow-Origin:*');
header('Content-Type:application/json');
header('Access-Control-Allow-Methods:POST');

include_once '../../config/DB.php';
include_once '../../Models/Follow.php';
include_once '../../Helpers/IsAuthinticated.php';


isLoggedIn();



$database = new Database();

$db = $database->connect();

$follow = new Follow($db);

// $follow=array();

$follow=$follow->getRequests();

http_response_code(200);

echo json_encode($follow);

//return json_encode(['datat is '.$follow],http_response_code(200));
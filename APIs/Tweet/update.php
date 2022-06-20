<?php

header('Access-Control-Allow-Origin:*');
header('Content-Type:application/json');
header('Access-Control-Allow-Methods:POST');

include_once '../../config/DB.php';
include_once '../../Models/Tweet.php';

include_once '../../Helpers/IsAuthinticated.php';

isLoggedIn();


$database = new Database();

$db = $database->connect();
$tweet=new Tweet($db);





if (!isset($_REQUEST['body'])||!isset($_REQUEST['id'])) {
    http_response_code(206);
    echo 'insert all data';
    die(203);
}

$tweet->body = $_REQUEST['body'];



if($tweet->update($_REQUEST['id'])){
    // http_response_code(201);
    echo json_encode(array('message'=>'tweet updated'),201);
}
else{
    echo json_encode(array('message'=>'tweet not updated'),401);

}
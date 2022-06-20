<?php

header('Access-Control-Allow-Origin:*');
header('Content-Type:application/json');
header('Access-Control-Allow-Methods:POST');

include_once '../../config/DB.php';
include_once '../../Models/Tweet.php';

include_once '../../Helpers/IsAuthinticated.php';



isLoggedIn();

if($_SERVER['REQUEST_METHOD']!='POST'){
    echo json_encode(array('message' => 'use post method'), 401);
    exit;
}

$database = new Database();

$db = $database->connect();
$tweet=new Tweet($db);


$qoutOn=null;


if (!isset($_POST['body'])) {
    http_response_code(206);
    echo 'insert all data';
    die(203);
}
if (isset($_POST['qouteOn'])) {
   $qoutOn=$_POST['qouteOn'];
}


$tweet->body = $_POST['body'];



if($tweet->create($qoutOn)){
    // http_response_code(201);
    echo json_encode(array('message'=>'tweet created'),201);
}
else{
    echo json_encode(array('message'=>'tweet not created'),401);

}
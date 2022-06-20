<?php


header('Access-Control-Allow-Origin:*');
header('Content-Type:application/json');
header('Access-Control-Allow-Methods:POST');




include_once '../../config/DB.php';
include_once '../../Models/Comment.php';
include_once '../../Helpers/IsAuthinticated.php';

isLoggedIn();



if (!isset($_REQUEST['tweet_id'])) {
    http_response_code(206);
    echo 'insert all data';
    die(203);
}


$database = new Database();

$db = $database->connect();

$comment=new Comment($db);


$comment->replyingTo = $_REQUEST['tweet_id'];
$comments=$comment->tweetComments();

echo json_encode(array('data'=>$comments),201);

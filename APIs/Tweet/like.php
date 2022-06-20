<?php

header('Access-Control-Allow-Origin:*');
header('Content-Type:application/json');
header('Access-Control-Allow-Methods:POST');

include_once '../../config/DB.php';
include_once '../../Models/Like.php';

include_once '../../Helpers/IsAuthinticated.php';
include_once '../../Helpers/cretaeNotification.php';

isLoggedIn();
// $v=5;
// $c=3;
// createNotification(object_id:$c,creator_id:$v);

$database = new Database();

$db = $database->connect();
$like=new Like($db);





if (!isset($_REQUEST['likeOn'])) {
    http_response_code(206);
    echo 'insert all data';
    die(203);
}

$like->likeOn=$_REQUEST['likeOn'];
$like->user_id=$_SESSION['user_id'];

if($like=$like->likeTweet()){
     http_response_code(201);
    echo json_encode(array('message'=>'tweet liked'),201);

    createNotification($like[1],$_REQUEST['likeOn'],'tweetLike');
   

    exit;
}
else{
    echo json_encode(array('message'=>'tweet not liked'),401);

}
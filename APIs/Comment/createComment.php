<?php

header('Access-Control-Allow-Origin:*');
header('Content-Type:application/json');
header('Access-Control-Allow-Methods:POST');

include_once '../../config/DB.php';
include_once '../../Models/Comment.php';
include_once '../../Models/Tweet.php';

include_once '../../Helpers/IsAuthinticated.php';

isLoggedIn();



if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    echo json_encode(array('message' => 'use post method'), 401);
    exit;
}

$database = new Database();

$db = $database->connect();
$comment = new Comment($db);

$tweet = new Tweet($db);

if (!isset($_POST['body']) || !isset($_POST['replyingTo'])) {
    http_response_code(206);
    echo 'insert all data';
    die(203);
}

$tweet = $tweet->getOne($_POST['replyingTo']);

$comment->body = $_POST['body'];
$comment->replyingTo = $_POST['replyingTo'];

if ($comment->create()) {
    // http_response_code(201);
    echo json_encode(array('message' => 'comment created'), 201);
} else {
    echo json_encode(array('message' => 'comment not created'), 401);

}

<?php


header('Access-Control-Allow-Origin:*');
header('Content-Type:application/json');
header('Access-Control-Allow-Methods:POST');




include_once '../../config/DB.php';
include_once '../../Models/Tweet.php';

include_once '../../Helpers/IsAuthinticated.php';

isLoggedIn();



echo $_SESSION['user_id'];

if(!isset($_GET['id'])){
    echo('enter id');
    die();
}

$database = new Database();

$db = $database->connect();

$tweet=new Tweet($db);
//$tweet->id=$_GET['id'];
$tweet=$tweet->getOne($_GET['id']);

echo json_encode($tweet);


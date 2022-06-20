<?php

header('Access-Control-Allow-Origin:*');
header('Content-Type:application/json');

include_once '../../config/DB.php';
include_once '../../Models/Notifications.php';
include_once '../../Helpers/IsAuthinticated.php';

isLoggedIn();


$database = new Database();

$db = $database->connect();
$noti = new Notification($db);


$noti=$noti->getNotification();

http_response_code(200);

echo json_encode($noti);
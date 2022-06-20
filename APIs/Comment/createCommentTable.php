<?php


header('Access-Control-Allow-Origin:*');
header('Content-Type:application/json');
header('Access-Control-Allow-Methods:POST');




include_once '../../config/DB.php';
include_once '../../Models/Comment.php';

$database = new Database();

$db = $database->connect();

$comment=new Comment($db);

$comment->createTable();


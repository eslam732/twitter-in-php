<?php


header('Access-Control-Allow-Origin:*');
header('Content-Type:application/json');
header('Access-Control-Allow-Methods:POST');




include_once '../../config/DB.php';
include_once '../../Models/Tweet.php';

$database = new Database();

$db = $database->connect();

$tweet=new Tweet($db);

$tweet->createTable();


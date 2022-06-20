<?php


function isLoggedIn()
{
    
    session_start();
if(!isset($_SESSION['user_id'])){
    http_response_code(401);
    echo 'not authorised';
    die();
}

return true;

}




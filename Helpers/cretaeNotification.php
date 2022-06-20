<?php

include_once '../../config/DB.php';
include_once '../../Models/Notifications.php';

function createNotification($notifiable_id, $object_id, $type)
{$database = new Database();

    $db = $database->connect();
    $noti = new Notification($db);

    $noti->notifiable_id = $notifiable_id;
    $noti->object_id = $object_id;
    $noti->type = $type;

    $noti->sendNotification();

}

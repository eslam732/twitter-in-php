<?php

class Notification
{

    private $__conn;
    private $__table = 'notifications';

    public $id;
    public $creator_id;
    public $notifiable_id;
    public $type;
    public $object_id;
    public $read_at;

    public function __construct($db)
    {
        $this->__conn = $db;
    }

    public function sendNotification()
    {
        // if ($_SESSION['user_id'] == $this->notifiable_id) {
        //     return;
        // }
        $query = '
        INSERT INTO ' . $this->__table . '
         SET
         creator_id=:creator_id,
         notifiable_id=:notifiable_id,
         object_id=:object_id,
         type=:type

        ;';

        $stmt = $this->__conn->prepare($query);

        $stmt->bindParam(':creator_id', $_SESSION['user_id']);
        $stmt->bindParam(':notifiable_id', $this->notifiable_id);
        $stmt->bindParam(':object_id', $this->object_id);
        $stmt->bindParam(':type', $this->type);

        try {
            $stmt = $stmt->execute();
            if ($stmt) {

                return true;
            }
        } catch (PDOException $e) {
            echo 'error in sending notification' . $e->getMessage();
            return false;
        }
    }

    public function getNotification()
    {
        $query = 'select n.id as notificationId,n.created_at ,n.type,n.object_id,n.created_at,
        u.email,u.name from ' . $this->__table . ' n
        left join users u
        on u.id=n.creator_id
        where n.notifiable_id = ' . $_SESSION['user_id'];

        $stmt = $this->__conn->prepare($query);
        try {
            $stmt->execute();
            $noti = $stmt->fetchAll(PDO::FETCH_ASSOC);

            for ($i = 0; $i < count($noti); $i++) {

                if ($noti[$i]['type'] == 'tweetLike') {

                    $query =
                        'SELECT t.body , t.id,t.likesNum , u.name,u.email
                     FROM tweets t
                     LEFT JOIN
                     users u ON t.creator_id=u.id
                     WHERE
                     t.id=? ';

                    $stmt = $this->__conn->prepare($query);
                    $stmt->bindParam(1, $noti[$i]['object_id']);
                    $stmt->execute();

                    $dataRows = $stmt->fetch(PDO::FETCH_ASSOC);

                    $noti[$i]['data'] = $dataRows;
                    continue;
                }
                if($noti[$i]['type'] == 'follow'){
                    
                }

            }
            return $noti;
        } catch (PDOException $e) {
            echo 'error in geting notifications' . $e->getMessage();
            return false;
        }
    }

}

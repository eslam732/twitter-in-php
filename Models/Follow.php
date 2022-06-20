<?php

class Follow
{

    private $__conn;
    private $__table = 'follows';

    public $id;
    public $following_user;
    public $followed_user;

    public function __construct($db)
    {
        $this->__conn = $db;
    }

    public function followFun($acceptFlag)
    {
        $message='followed';
        $query = '
        INSERT INTO ' . $this->__table . '
         SET
         followed_user=:followed_user,
         following_user=' . $this->following_user . '
        ;';

        if($acceptFlag){
            $queryFollowedUser = 'UPDATE `users`
             SET `follow_requests`=follow_requests-1  ,`followers`=followers+1
             where id =:followed_user';
             $message='accepted follow';
        }
        else {
            $queryFollowedUser = 'UPDATE `users` SET `followers`=followers+1 where id =:followed_user';
        }
        $queryFollowingUser = 'UPDATE `users` SET `followings`=followings+1 where id =:following_user';

        $stmt1 = $this->__conn->prepare($queryFollowingUser);
        $stmt1->bindParam(':following_user', $this->following_user);

        

        $stmt2 = $this->__conn->prepare($queryFollowedUser);
        $stmt2->bindParam(':followed_user', $this->followed_user);
        $queryfollow = '
        INSERT INTO ' . $this->__table . '
         SET
         followed_user=:followed_user,
         following_user=:following_user
        ;';
        $stmt3 = $this->__conn->prepare($queryfollow);
        $stmt3->bindParam(':followed_user', $this->followed_user);
        $stmt3->bindParam(':following_user', $this->following_user);

        try {
            $stmt1 = $stmt1->execute();
            $stmt2 = $stmt2->execute();
            $stmt3 = $stmt3->execute();
            if ($stmt1 && $stmt2 && $stmt3) {

                return $message;
            }
        } catch (PDOException $e) {
            echo 'error in follwing' . $e->getMessage();
            return false;
        }
    }

    public function follow()
    {
        $this->following_user = $_SESSION['user_id'];

        $query = 'SELECT id from users where id =:id';

        $stmt = $this->__conn->prepare($query);
        $stmt->bindParam(':id', $this->followed_user);
        $stmt->execute();
        $existFollow = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$existFollow) {
            http_response_code(401);
            echo 'user not found ';
            die(203);
        }

        $query = 'SELECT id from ' . $this->__table . ' where following_user =:following_user
        and followed_user =:followed_user ';

        $stmt = $this->__conn->prepare($query);
        $stmt->bindParam(':followed_user', $this->followed_user);
        $stmt->bindParam(':following_user', $this->following_user);
        $stmt->execute();
        $existFollow = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($existFollow) {
            http_response_code(401);
            echo 'you already follow this user ';
            die(203);
        }
        $query = 'SELECT * from users where id =:followed_user ';
        $stmt = $this->__conn->prepare($query);
        $stmt->bindParam(':followed_user', $this->followed_user);
        $stmt->execute();
        $followedUser = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($followedUser['privacy'] == 'private') {
            $checkQuery = 'select id from followrequests where request_from=:request_from and
                           request_to =:request_to ';
            $stmtcheck = $this->__conn->prepare($checkQuery);
            $stmtcheck->bindParam(':request_from', $this->following_user);
            $stmtcheck->bindParam(':request_to', $this->followed_user);
            try {
                $stmtcheck->execute();
                $existReq = $stmtcheck->fetchAll(PDO::FETCH_ASSOC);

                if ($existReq) {
                    echo 'already requested';
                    exit;
                }
            } catch (PDOException $e) {
                echo 'error in follwing' . $e->getMessage();
                die(400);
            }

            $query = '
            INSERT INTO followrequests
             SET
             request_from=:request_from,
             request_to=:request_to
            ;';

            $stmt = $this->__conn->prepare($query);
            $stmt->bindParam(':request_from', $this->following_user);
            $stmt->bindParam(':request_to', $this->followed_user);

            $queryFollowedUser = 'UPDATE `users` SET `follow_requests`=follow_requests+1
            where id =:followed_user';

            $stmt2 = $this->__conn->prepare($queryFollowedUser);
            $stmt2->bindParam(':followed_user', $this->followed_user);
            try {
                $stmt = $stmt->execute();
                $stmt2 = $stmt2->execute();

                if ($stmt && $stmt2) {
                    return 'requested';
                }
            } catch (PDOException $e) {
                echo 'error in follwing' . $e->getMessage();
                die(400);
            }

        }

        if ($followedUser['privacy'] == 'public') {

            $this->followFun(0);
            //     $query = '
            // INSERT INTO ' . $this->__table . '
            //  SET
            //  followed_user=:followed_user,
            //  following_user=' . $this->following_user . '
            // ;';

            //     $queryFollowingUser = 'UPDATE `users` SET `followings`=followings+1 where id =:following_user';

            //     $stmt1 = $this->__conn->prepare($queryFollowingUser);
            //     $stmt1->bindParam(':following_user', $this->following_user);

            //     $queryFollowedUser = 'UPDATE `users` SET `followers`=followers+1 where id =:followed_user';

            //     $stmt2 = $this->__conn->prepare($queryFollowedUser);
            //     $stmt2->bindParam(':followed_user', $this->followed_user);
            //     $queryfollow = '
            // INSERT INTO ' . $this->__table . '
            //  SET
            //  followed_user=:followed_user,
            //  following_user=:following_user
            // ;';
            //     $stmt3 = $this->__conn->prepare($queryfollow);
            //     $stmt3->bindParam(':followed_user', $this->followed_user);
            //     $stmt3->bindParam(':following_user', $this->following_user);

            //     try {
            //         $stmt1 = $stmt1->execute();
            //         $stmt2 = $stmt2->execute();
            //         $stmt3 = $stmt3->execute();
            //         if ($stmt1 && $stmt2 && $stmt3) {

            //             echo ('followed');
            //             die(201);
            //         }
            //     } catch (PDOException $e) {
            //         echo 'error in follwing' . $e->getMessage();
            //         return false;
            //     }

        }

    }

    public function getRequests()
    {
        $query = 'select f.id as reqId ,u.id,u.email,u.name from followrequests f

        left join users u
        on u.id=f.request_from

        where f.request_to = ' . $_SESSION['user_id'];

        $stmt = $this->__conn->prepare($query);
        try {
            $stmt->execute();
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $data;
        } catch (PDOException $e) {
            echo 'error in follwing' . $e->getMessage();
            return false;
        }

    }

    public function acceptRequest($reqId)
    {
        $query = 'select * from followrequests where id = :reqId';

        $stmt = $this->__conn->prepare($query);
        $stmt->bindParam(':reqId', $reqId);
        $stmt->execute();
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if (!$data) {
            http_response_code(401);

            echo ('request not found');
            exit;
        }

        $this->followed_user = $data[0]['request_to'];
        $this->following_user = $data[0]['request_from'];
        $query = 'delete from followrequests where id = :reqId';

        $stmt = $this->__conn->prepare($query);
        $stmt->bindParam(':reqId', $reqId);
        try {
            $stmt->execute();

        } catch (PDOException $e) {
            echo 'error in deleting request' . $e->getMessage();
            return false;
        }
return $this->followFun(1);

        
    }

}

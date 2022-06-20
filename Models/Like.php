<?php

class Like
{

    private $__conn;
    private $__table = 'likes';

    public $id;
    public $user_id;
    public $likeOn;

    public function __construct($db)
    {
        $this->__conn = $db;
    }

    public function likeTweet()
    {
        $querySelect = 'SELECT * FROM tweets  WHERE  id=? ';
        $stmtST = $this->__conn->prepare($querySelect);
        $stmtST->bindParam(1, $this->likeOn);
        $stmtST->execute();
        $tweet = $stmtST->fetch(PDO::FETCH_ASSOC);

        if (!$tweet) {
            http_response_code(404);
            echo 'tweet not found';
            die(203);
        }
        $notifiable_id=$tweet['creator_id'];
        

        $querySelect = 'SELECT * FROM likes  WHERE  user_id=:user_id AND likeOn=:likeOn ';
        $stmtSL = $this->__conn->prepare($querySelect);
        $stmtSL->bindParam(':user_id', $this->user_id);
        $stmtSL->bindParam(':likeOn', $this->likeOn);
        $stmtSL->execute();
        $like = $stmtSL->fetch(PDO::FETCH_ASSOC);

        if (!$like) {
            $queryUpdate = '
        UPDATE tweets
         SET
         likesNum=:likesNum
        WHERE id=:id ';

            $this->likesNum = $tweet['likesNum'] + 1;
            $stmtLT = $this->__conn->prepare($queryUpdate);
            $stmtLT->bindParam(':likesNum', $this->likesNum);
            $stmtLT->bindParam(':id', $this->likeOn);
            $stmtLT->execute();

            $queryForLike = '
        INSERT INTO ' . $this->__table . '
         SET
         user_id=:user_id,
         likeOn=:likeOn
        ;';

            $stmtL = $this->__conn->prepare($queryForLike);

            $stmtL->bindParam(':user_id', $this->user_id);
            $stmtL->bindParam(':likeOn', $this->likeOn);

            try {

                $stmtL = $stmtL->execute();
                if ($stmtL) {

                    return [true,$notifiable_id];
                }
            } catch (PDOException $e) {

                echo 'error in liking this tweet' . $e->getMessage();

                return false;
            }

        } else {
            $queryUpdate = '
            UPDATE tweets
             SET
             likesNum=:likesNum

            WHERE id=:id ';

            $this->likesNum = $tweet['likesNum'] - 1;
            $stmt = $this->__conn->prepare($queryUpdate);
            $stmt->bindParam(':id', $this->likeOn);
            $stmt->bindParam(':likesNum', $this->likesNum);
            $stmt->execute();

            $query = 'Delete FROM ' . $this->__table . '  WHERE  user_id=:user_id AND likeOn=:likeOn ';

            $stmt = $this->__conn->prepare($query);

            $stmt->bindParam(':user_id', $this->user_id);
            $stmt->bindParam(':likeOn', $this->likeOn);

            try {
                $stmt = $stmt->execute();
                if ($stmt) {

                    echo json_encode(array('message' => 'tweet unliked'), 201);

                    die(201);
                }
            } catch (PDOException $e) {
                echo 'error ' . $e->getMessage();
                return false;
            }

            if ($stmt->execute()) {
                return 1;
            } else {printf("error : %s ./n", $stmt->error);

                return false;

            }
        }

    }

}

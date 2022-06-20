<?php

class Tweet
{

    private $__conn;
    private $__table = 'tweets';

    public $id;
    public $body;
    public $likesNum;
    public $commentsNum;
    public $creator_id;
    public $retweeter_id;
    public $retweet_id;
    public $created_at;

    public function __construct($db)
    {
        $this->__conn = $db;
    }

    public function tryFun($stmt, $errmsg)
    {
        try {
            $stmt = $stmt->execute();

        } catch (PDOException $e) {
            echo $errmsg . ' ' . $e->getMessage();
        }
    }

    public function create($qouteId)
    {$this->creator_id = $_SESSION['user_id'];

        if ($qouteId) {
            $query = '
            INSERT INTO ' . $this->__table . '
             SET
             body=:body,
             creator_id=' . $this->creator_id . ',
             likesNum=0,
             commentsNum=0,
             qouteOn=:qouteId
            ;';
            $stmt = $this->__conn->prepare($query);

            $this->body = htmlspecialchars(strip_tags($this->body));

            $stmt->bindParam(':body', $this->body);
            $stmt->bindParam(':qouteId', $qouteId);

        } else {
            $query = '
            INSERT INTO ' . $this->__table . '
             SET
             body=:body,
             creator_id=' . $this->creator_id . ',
             likesNum=0,
             commentsNum=0
            ;';
            $stmt = $this->__conn->prepare($query);

            $this->body = htmlspecialchars(strip_tags($this->body));

            $stmt->bindParam(':body', $this->body);

        }

        try {
            $stmt = $stmt->execute();
            if ($stmt) {

                return true;
            }
        } catch (PDOException $e) {
            echo 'error in creating this tweet' . $e->getMessage();
            exit;
        }

    }

    public function read()
    {$query = 'SELECT t.body , t.id,t.likesNum ,t.retweetsNum, u.name,u.email
     FROM ' . $this->__table . ' t
     LEFT JOIN
     users u ON t.creator_id=u.id

     ';

        $stmt = $this->__conn->prepare($query);
        $stmt->execute();
        $dataRows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        for ($i = 0; $i < count($dataRows); $i++) {
            $query = 'SELECT user_id FROM likes
            WHERE
            ' . $dataRows[$i]['id'] . '=likeOn';
            $stmt = $this->__conn->prepare($query);
            $stmt->execute();

            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $dataRows[$i]['likes'] = $data;
        }

        return $dataRows;

    }

    public function delete($id)
    {$query = 'SELECT *
     FROM ' . $this->__table . ' WHERE
     id=?';

        $stmt = $this->__conn->prepare($query);
        $stmt->bindParam(1, $id);
        $stmt->execute();

        $dataRows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (!$dataRows) {
            http_response_code(404);
            echo 'tweet not found';
            die(203);
        }
        if ($dataRows[0]['creator_id'] != $_SESSION['user_id']) {
            http_response_code(401);
            echo 'not authed to delete this tweet';
            die(401);
        }

        try {
            $querydelte = 'Delete FROM ' . $this->__table . '  WHERE  id=? ';
            $stmt = $this->__conn->prepare($querydelte);
            $stmt->bindParam(1, $id);
            $stmt->execute();
            echo 'tweet deleted';
            exit;
        } catch (PDOException $e) {
            echo 'error happened ' . $e->getMessage();
        }
        return $dataRows;

    }

    public function getOne($id)
    {$query =
        'SELECT t.body , t.id,t.likesNum , u.name,u.email
     FROM ' . $this->__table . ' t
     LEFT JOIN
     users u ON t.creator_id=u.id
     WHERE
     t.id=?
     ';

        $stmt = $this->__conn->prepare($query);
        $stmt->bindParam(1, $id);
        $stmt->execute();

        $dataRows = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$dataRows) {
            http_response_code(404);
            echo 'tweet not found';
            die(203);
        }

        return $dataRows;
    }

    public function update($id)
    {

        $querySelect = 'SELECT * FROM ' . $this->__table . '  WHERE  id=? ';
        $stmtS = $this->__conn->prepare($querySelect);
        $stmtS->bindParam(1, $id);
        $stmtS->execute();
        $row = $stmtS->fetch(PDO::FETCH_ASSOC);

        if (!$row) {
            http_response_code(404);
            echo 'tweet not found';
            die(203);
        }
        if ($row[0]['creator_id'] != $_SESSION['user_id']) {
            http_response_code(401);
            echo 'not authed to update this tweet';
            die(401);
        }

        $queryUpdate = '
        UPDATE ' . $this->__table . '
         SET
         body=:body

        WHERE id=:id ';

        $this->body = htmlspecialchars(strip_tags($this->body));

        $stmt = $this->__conn->prepare($queryUpdate);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':body', $this->body);

        if ($stmt->execute()) {

            $tweet = array();
            $tweet['data'] = array(
                'id' => $id,
                'body' => $this->body,
                'likesNum' => $this->likesNum,
                'commentsNum' => $this->commentsNum,

            );
            return $tweet;
        } else {printf("error : %s ./n", $stmt->error);

            return false;}

    }

    public function retweet($tweetId)
    {
        $queryt = 'select id from tweets where id=:tweetId';
        $stmt = $this->__conn->prepare($queryt);
        $stmt->bindParam(':tweetId', $tweetId);
        try {
            $stmt->execute();
            $exist = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if (!$exist) {

                http_response_code(401);
                echo 'tweet not found';
                exit;
            }
        } catch (PDOException $e) {
            echo 'error in creating this tweet' . $e->getMessage();
            return false;
        }
        $queryUpdateTweet = 'UPDATE `tweets` SET `retweetsNum`=retweetsNum+1  WHERE id=:id';

        $stmt = $this->__conn->prepare($queryUpdateTweet);
        $stmt->bindParam(':id', $tweetId);

        try {
            $stmt = $stmt->execute();

        } catch (PDOException $e) {
            echo 'error in retwweting also :)' . $e->getMessage();
            exit;
        }

        $queryInsert = 'insert into retweets SET
            retweeterId=:retweeterId,
            tweetId=:tweetId';
        $stmt1 = $this->__conn->prepare($queryInsert);
        $stmt1->bindParam(':retweeterId', $_SESSION['user_id']);
        $stmt1->bindParam(':tweetId', $tweetId);
        try {
            $stmt1 = $stmt1->execute();

        } catch (PDOException $e) {
            echo 'error in retweeting this tweet ' . $e->getMessage();
        }

       return true;

    }
    public function undoRetweet($retId)
    {
        $queryt = 'select id,tweetId from retweets where id=:retId';
        $stmt = $this->__conn->prepare($queryt);
        $stmt->bindParam(':retId', $retId);
        try {
            $stmt->execute();
            $exist = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (!$exist) {

                http_response_code(401);
                echo 'retweet not found';
                exit;
            }
        } catch (PDOException $e) {
            echo 'error in finding ret this tweet' . $e->getMessage();
            return false;
            exit;
        }

        $tweetId = $exist[0]['tweetId'];
        $queryundo = 'delete from retweets where id=:retId';
        $stmt = $this->__conn->prepare($queryundo);
        $stmt->bindParam(':retId', $retId);
        try {
            $stmt->execute();

        } catch (PDOException $e) {
            echo 'error in creating this tweet' . $e->getMessage();
            return false;
            exit;
        }

        $query = 'delete from retweets where id=:retId';
        $stmt = $this->__conn->prepare($query);
        $stmt->bindParam(':retId', $retId);
        try {
            $stmt->execute();

        } catch (PDOException $e) {
            echo 'error in deleting ret this tweet' . $e->getMessage();
            return false;
            exit;
        }
        $queryUpdateTweet = 'UPDATE `tweets` SET `retweetsNum`=retweetsNum-1  WHERE id=:id';

        $stmt = $this->__conn->prepare($queryUpdateTweet);
        $stmt->bindParam(':id', $tweetId);

        try {
            $stmt = $stmt->execute();

        } catch (PDOException $e) {
            echo 'error in retwweting also :)' . $e->getMessage();
            exit;
        }

        echo 'deleted retweet';

    }

    public function getQoutes()
    {
        $sql = "SELECT qt.body as qouteBody , qt.id,qt.likesNum ,qt.retweetsNum,qt.qouteOn ,qt.creator_id,
         t.body as tweetBody, t.likesNum as tweetlikes, t.commentsNum as tweetCommentsNum,
          qu.name as qoute_creator , qu.email as qouterMail,
           u.name as tweeterName , u.email as tweeterMail FROM tweets qt 
           JOIN tweets t on t.id=qt.qouteOn JOIN users qu on qu.id=qt.creator_id 
           JOIN users u on u.id=t.creator_id";

        $stmt = $this->__conn->prepare($sql);

        try {
            $stmt->execute();
            $data=$stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            echo 'error in retwweting also :)' . $e->getMessage();
            exit;
        }

        echo json_encode($data);
    }

    public function createTable()
    {

        $sql = "CREATE TABLE tweets (
            id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            body VARCHAR(50) NOT NULL,
            commentsNum INT(6),
            likesNum INT(6),

            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            )";

        try {if ($this->__conn->query($sql)) {
            echo "Table tweets created successfully";
            die();

        }
        } catch (PDOException $e) {
            echo 'error in creating this table' . $e->getMessage();
        }

    }
}

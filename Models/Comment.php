<?php

class Comment
{

    private $__conn;
    private $__table = 'comments';

    public $id;
    public $body;
    public $replyingTo;
    public $creator_id;

    public function __construct($db)
    {
        $this->__conn = $db;
    }
    public function create()
    {

        $query = '
        INSERT INTO ' . $this->__table . '
         SET
         body=:body,
         replyingTo=:replyingTo,
         creator_id=:creator_id

        ;';

        $stmt = $this->__conn->prepare($query);

        $this->body = htmlspecialchars(strip_tags($this->body));

        $stmt->bindParam(':body', $this->body);
        $stmt->bindParam(':replyingTo', $this->replyingTo);
        $stmt->bindParam(':creator_id', $_SESSION['user_id']);

        try {
            $stmt = $stmt->execute();
            if ($stmt) {

                return true;
            }
        } catch (PDOException $e) {
            echo 'error in creating this comment' . $e->getMessage();
            return false;
        }

    }

    public function tweetComments()
    {
        $query = 'SELECT * FROM ' . $this->__table . '
        WHERE
        replyingTo=?';

        $stmt = $this->__conn->prepare($query);
        $stmt->bindParam(1, $this->replyingTo);
        $stmt->execute();

        $dataRows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $dataRows;

    }

    public function createTable()
    {

        $sql = "CREATE TABLE comments (
            id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            body VARCHAR(50) NOT NULL,
            replyingTo INT(6),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            )";

        try {if ($this->__conn->query($sql)) {
            echo "Table comments created successfully";
            die();

        }
        } catch (PDOException $e) {
            echo 'error in creating this table' . $e->getMessage();
        }

    }
}

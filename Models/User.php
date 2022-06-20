<?php

class User
{

    private $__conn;
    private $__table = 'users';

    public $id;
    public $name;
    public $email;
    public $uid;
    public $pwd;
    public $privacy;
    public $created_at;

    public function __construct($db)
    {
        $this->__conn = $db;
    }

    public function create()
    {
        $queryCheck = 'SELECT * FROM ' . $this->__table . '  WHERE uid=:uid';

        $stmtc = $this->__conn->prepare($queryCheck);
        $stmtc->bindParam(':uid', $this->uid);

        $stmtc->execute();

        $exist = $stmtc->fetch();

        if ($exist) {
            http_response_code(401);
            echo 'uid exists';
            die(203);
        }

        $queryCheck = 'SELECT * FROM ' . $this->__table . '  WHERE email=:email';

        $stmtc = $this->__conn->prepare($queryCheck);

        $stmtc->bindParam(':email', $this->email);
        $stmtc->execute();

        $exist = $stmtc->fetch();

        if ($exist) {
            http_response_code(401);
            echo 'email exists';
            die(203);
        }

        $query = '
        INSERT INTO ' . $this->__table . '
         SET
         name=:name,
         email=:email,
         uid=:uid,
         privacy=:privacy,
         pwd=:pwd ;';

        $stmt = $this->__conn->prepare($query);

        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->uid = htmlspecialchars(strip_tags($this->uid));
        $this->privacy = htmlspecialchars(strip_tags($this->privacy));
        $this->pwd = htmlspecialchars(strip_tags($this->pwd));

        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':email', $this->email);
        $stmt->bindParam(':uid', $this->uid);
        $stmt->bindParam(':privacy', $this->privacy);
        $stmt->bindParam(':pwd', $this->pwd);

        try {
            $stmt = $stmt->execute();
            if ($stmt) {

                return true;
            }
        } catch (PDOException $e) {
            echo 'error in creating this user' . $e->getMessage();
            return false;
        }

    }

    public function login()
    {

        $queryCheck = 'SELECT * FROM ' . $this->__table . '  WHERE email=:email';

        $stmtc = $this->__conn->prepare($queryCheck);

        $stmtc->bindParam(':email', $this->email);
        $stmtc->execute();

        $user = $stmtc->fetch();

        if (!$user) {
            http_response_code(401);
            echo 'email does not exist';
            die(401);
        }
       
        $hassedPwd=$user['pwd'];
        $checkPwd=password_verify($this->pwd,$hassedPwd);

        if($checkPwd===false){
            http_response_code(401);
            echo 'inncorrect password';
            die(401); 
        }

        session_start();
        $_SESSION["user_id"]=$user['id'];
        echo( 'user id is' . $_SESSION['user_id']);
        return $user;

    }

}

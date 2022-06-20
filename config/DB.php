<?php

class Database
{

    private $__host = 'localhost';
    private $__db_name = 'train';
    private $__username = 'eslam';
    private $__password = 'eslam123';
    private $__conn;

    public function connect()
    {
        $this->conn = null;

        try { 
            $this->__conn = new PDO('mysql:host=' . $this->__host . ';dbname=' . $this->__db_name,
                $this->__username, $this->__password);
            $this->__conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            echo ('connection error' . $e->getMessage());
        }
        return $this->__conn;
    }

}

<?php

namespace app\config;

use PDOException;

class Database
{
    private $host = 'localhost';
    private $port = 3306;
    private $dbname = 'users';
    private $username = 'root';
    private $password = '';
    public $con;
    public function connect()
    {
        try {
            $this->con = new \PDO("mysql:host=$this->host;port=$this->port;dbname=$this->dbname", $this->username, $this->password);
            $this->con->setAttribute(\PDO::ERRMODE_EXCEPTION, \PDO::ATTR_ERRMODE);
            return $this->con;
        } catch (PDOException $e) {
            echo 'Connection to the Database failed' . $e->getMessage();
        }
    }
}
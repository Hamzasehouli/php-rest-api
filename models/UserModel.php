<?php

namespace app\models;

use app\config\Database;

class UserModel
{

    private $id = '';
    private $username = '';
    private $email = '';
    private $password = '';

    public function find()
    {
        $database = new Database();
        $con = $database->connect();
        $query = 'SELECT * FROM user';
        $stmt = $con->prepare($query);
        return $stmt;
    }
    public function create()
    {
        $database = new Database();
        $con = $database->connect();
        $query = 'INSERT INTO user(username, email, password) VALUES(:username, :email, :password)';
        $stmt = $con->prepare($query);
        return $stmt;
    }
    public function findOne()
    {
        $database = new Database();
        $con = $database->connect();
        $query = 'SELECT * FROM user WHERE email=:email';
        $stmt = $con->prepare($query);
        return $stmt;
    }
    public function findById()
    {
        $database = new Database();
        $con = $database->connect();
        $query = 'SELECT * FROM user WHERE id=:id';
        $stmt = $con->prepare($query);
        return $stmt;
    }

}
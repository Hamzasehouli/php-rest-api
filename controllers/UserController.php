<?php

namespace app\controllers;

use app\GenerateJwt;
use app\models\UserModel;

$User = new UserModel();

class UserController
{

    public static function getAllUsers()
    {
        $User = new UserModel();
        $stmt = $User->find();
        $stmt->execute();
        $users = $stmt->fetchAll();
        print_r(json_encode(['status' => 'success', 'results' => count($users), 'data' => $users]));
    }
    public static function signup()
    {
        $User = new UserModel();
        $stmt = $User->create();
        $data = json_decode(file_get_contents('php://input', true));
        if (empty($data->username) || empty($data->password) || empty($data->email)) {
            return print_r(json_encode(['status' => 'fail', 'message' => 'Please enter all required input to continue']));
        }
        $hashedPassword = password_hash($data->password, PASSWORD_DEFAULT);
        $stmt->bindValue(':username', $data->username);
        $stmt->bindValue(':email', $data->email);
        $stmt->bindValue(':password', $hashedPassword);
        if ($stmt->execute()) {
            $stmt1 = $User->findOne();
            $stmt1->bindValue(':username', $data->username);
            $stmt1->execute();
            $user1 = $stmt1->fetch();

            $gt = new GenerateJwt();
            $jwt = $gt->generateToken($user1->id);
            print_r(json_encode(['status' => 'success', 'message' => 'You signed up successfully', 'token' => $jwt]));
        } else {
            print_r(json_encode(['status' => 'success', 'message' => 'Something went wrong']));
        }

    }
    // public static function signup()
    // {
    //     echo 'signup';
    // }
    public static function login()
    {
        $User = new UserModel();
        $stmt = $User->findOne();
        $data = json_decode(file_get_contents('php://input', true));
        if (empty($data->username) || empty($data->password)) {
            return print_r(json_encode(['status' => 'fail', 'message' => 'Please enter all required input to continue']));
        }
        $stmt->bindValue(':username', $data->username);
        $stmt->execute();
        $user = $stmt->fetch();
        $row = $stmt->rowCount();
        if ($row < 1) {
            return print_r(json_encode(['status' => 'fail', 'message' => 'No user found or the enetered password is incorrect']));
        }
        extract($user);
        // if ($password) {
        //     return print_r(json_encode(['status' => 'fail', 'message' => 'No user found or the enetered password is incorrect']));
        // }
        $isPasswordCorrect = password_verify($data->password, $password);
        if (!$isPasswordCorrect) {
            return print_r(json_encode(['status' => 'fail', 'message' => 'No user found or the enetered password is incorrect']));
        }
        $gt = new GenerateJwt();
        $jwt = $gt->generateToken($id);
        print_r(json_encode(['status' => 'success', 'message' => 'You logged in successfully', 'token' => $jwt]));
        // $hashedPassword = password_hash($data->password, PASSWORD_DEFAULT);
        // password_verify();
        // $stmt->bindValue(':username', $data->username);
        // $stmt->bindValue(':password', $hashedPassword);
        // if ($stmt->execute()) {
        //     print_r(json_encode(['status' => 'success', 'message' => 'You signed up successfully']));
        // }
    }

}
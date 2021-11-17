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
            $user1 = $stmt1->fetch(\PDO::FETCH_ASSOC);
            extract($user1);

            $gt = new GenerateJwt();
            $jwt = $gt->generateToken($id);
            header("HTTP/1.1 200");
            print_r(json_encode(['status' => 'success', 'message' => 'You signed up successfully', 'token' => $jwt, 'email' => $email]));
        } else {
            header("HTTP/1.1 400");
            print_r(json_encode(['status' => 'success', 'message' => 'Something went wrong']));
        }

    }

    public static function login()
    {
        $User = new UserModel();
        $stmt = $User->findOne();
        $data = json_decode(file_get_contents('php://input', true));
        if (empty($data->username) || empty($data->password)) {
            header("HTTP/1.1 403");
            return print_r(json_encode(['status' => 'fail', 'message' => 'Please enter all required input to continue']));
        }
        $stmt->bindValue(':username', $data->username);
        $stmt->execute();
        $user = $stmt->fetch();
        $row = $stmt->rowCount();
        if ($row < 1) {
            header("HTTP/1.1 404");
            return print_r(json_encode(['status' => 'fail', 'message' => 'No user found or the enetered password is incorrect']));
        }
        extract($user);
        // if ($password) {
        //     return print_r(json_encode(['status' => 'fail', 'message' => 'No user found or the enetered password is incorrect']));
        // }
        $isPasswordCorrect = password_verify($data->password, $password);
        if (!$isPasswordCorrect) {
            header("HTTP/1.1 403");
            return print_r(json_encode(['status' => 'fail', 'message' => 'No user found or the enetered password is incorrect']));
        }
        $gt = new GenerateJwt();
        $jwt = $gt->generateToken($id);
        header("HTTP/1.1 200");
        print_r(json_encode(['status' => 'success', 'message' => 'You logged in successfully', 'token' => $jwt, 'email' => $email]));
        // $hashedPassword = password_hash($data->password, PASSWORD_DEFAULT);
        // password_verify();
        // $stmt->bindValue(':username', $data->username);
        // $stmt->bindValue(':password', $hashedPassword);
        // if ($stmt->execute()) {
        //     print_r(json_encode(['status' => 'success', 'message' => 'You signed up successfully']));
        // }
    }

    public static function protect()
    {
        $User = new UserModel();
        $d = json_decode(file_get_contents('php://input'), true);
        if (isset($d['jwt'])) {

            $resp = explode('=', $d['jwt']);
            $jwt = $resp[1];
        } else {
            extract($_COOKIE);
            print_r($jwt);
        }
        // split the jwt
        $tokenParts = explode('.', $jwt);
        $header = base64_decode($tokenParts[0]);
        $payload = base64_decode($tokenParts[1]);
        $signature_provided = $tokenParts[2];

        // check the expiration time - note this will cause an error if there is no 'exp' claim in the jwt
        $expiration = json_decode($payload)->exp;
        $id = json_decode($payload)->user_id;

        $is_token_expired = ($expiration - time()) < 0;

        if ($expiration - time() < 0) {
            header("HTTP/1.1 400");
            return print_r(json_encode(['status' => 'fail', 'message' => 'your are logged out', 'isLoggedin' => false]));
        }

        // build a signature based on the header and payload using the secret
        // Encode Header to Base64Url String {
        $base64UrlHeader = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($header));

        // Encode Payload to Base64Url String
        $base64UrlPayload = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($payload));
        // Create Signature Hash
        $signature = hash_hmac('sha256', $base64UrlHeader . "." . $base64UrlPayload, 'abC123!', true);

        // Encode Signature to Base64Url String
        $base64UrlSignature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));

        // verify it matches the signature provided in the jwt
        $is_signature_valid = ($base64UrlSignature === $signature_provided);
        // $isTokenValid = null;
        // if ($is_token_expired || !$is_signature_valid) {
        //     $isTokenValid = false;
        // } else {
        //     $isTokenValid = true;
        // }
        // print_r($is_signature_valid);
        $stmt1 = $User->findById();
        $stmt1->bindValue(':id', $id);
        $stmt1->execute();
        $row = $stmt1->rowCount();
        if ($row < 0) {
            header("HTTP/1.1 404");
            return print_r(json_encode(['status' => 'fail', 'message' => 'User not found', 'isLoggedin' => false]));
        }
        $user1 = $stmt1->fetch(\PDO::FETCH_ASSOC);
        extract($user1);

        if ($is_signature_valid) {
            header("HTTP/1.1 200");
            print_r(json_encode(['status' => 'success', 'message' => 'You logged in successfully', 'isLoggedin' => true, 'email' => $email]));

        } else {
            header("HTTP/1.1 403");
            print_r(json_encode(['status' => 'fail', 'message' => 'your ere not logged in to perform the task', 'isLoggedin' => false]));

        }
    }

}
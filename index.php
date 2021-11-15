<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");
header('Access-Control-Allow-Credentials: true');
header('content-type:application/json');

require_once $_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php';

use app\config\Database;
use app\controllers\UserController;
use app\Router;

$db = new Database();
$con = $db->connect();

$router = new Router();
$router->get('/api/v1/auth', [UserController::class, 'getAllUsers']);
$router->post('/api/v1/auth/signup', [UserController::class, 'signup']);
$router->post('/api/v1/auth/login', [UserController::class, 'login']);
// $router->post('/api/v1/auth', [UserController::class, 'createUser']);
$router->call();
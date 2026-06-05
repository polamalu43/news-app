<?php

use Core\Router;

$router = Router::getInstance();

$router->get('/news', ['App\Controllers\NewsController', 'index']);
$router->post('/news', ['App\Controllers\NewsController', 'sync']);
$router->get('/mypage', ['App\Controllers\MypageController', 'index']);
$router->post('/auth/login', ['App\Controllers\AuthController', 'login']);
$router->post('/auth/me', ['App\Controllers\AuthController', 'me']);
$router->post('/user', ['App\Controllers\UserController', 'regist']);
$router->post('/favorite', ['App\Controllers\FavoriteController', 'add']);
$router->delete('/favorite', ['App\Controllers\FavoriteController', 'remove']);

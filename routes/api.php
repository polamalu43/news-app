<?php

use Core\Router;

$router = Router::getInstance();

$router->get('/api/news',               ['App\Controllers\NewsController', 'index']);
$router->post('/api/news/store',        ['App\Controllers\NewsController', 'store']);
$router->post('/api/auth/login',        ['App\Controllers\AuthController', 'login']);
$router->post('/api/register/confirm',  ['App\Controllers\RegistrationController', 'confirm']);
$router->post('/api/register/complete', ['App\Controllers\RegistrationController', 'complete']);

$router->group(['middleware' => ['App\Middlewares\AuthMiddleware']], function ($router) {
    $router->get('/api/mypage/{userId}', ['App\Controllers\MypageController', 'index']);
    $router->post('/api/auth/me',        ['App\Controllers\AuthController', 'me']);
    $router->post('/api/favorite',       ['App\Controllers\FavoriteController', 'add']);
    $router->delete('/api/favorite',     ['App\Controllers\FavoriteController', 'remove']);
});

<?php

namespace App\Middlewares;
use Core\Response;

class AuthMiddleware
{
    public function handle(): void
    {
        if (!isset($_SESSION['user_id'])) {
            Response::json(
              ['status' => 'error', 'message' => 'Unauthorized'],
              401
            )->send();
            exit;
        }
    }
}

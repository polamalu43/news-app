<?php

namespace App\Controllers;
use Core\Controller;
use App\Repositories\UserRepository;
use App\Requests\LoginRequest;

class LoginController extends Controller
{
    public function index(): void
    {
        $repository = new UserRepository();
        $request = new LoginRequest();

        if (!$request->validate()) {
            $this->json(['errors' => $request->errors()], 422);
        }
    }
}

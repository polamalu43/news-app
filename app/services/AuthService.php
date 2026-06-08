<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\UserRepository;

class AuthService
{
    public function __construct(
        private UserRepository $repository
    ) {}

    public function authenticate(string $email, string $password): ?User
    {
        return $this->repository->findAuthenticatedUser($email, $password);
    }
}

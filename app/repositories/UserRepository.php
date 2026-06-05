<?php

namespace App\Repositories;

use Core\QueryBuilder;
use App\Models\User;
use App\Repositories\Interfaces\UserRepositoryInterface;

class UserRepository
{
    private QueryBuilder $qb;

    public function __construct()
    {
        $this->qb = new QueryBuilder('users', User::class);
    }

    public function getLoginUser(string $email, string $password): ?User
    {
        $user = $this->qb
            ->select(['id', 'name', 'email'])
            ->where('email', $email)
            ->where('password', $password)
            ->first();
        return $user ?? null;
    }
}

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

    public function findAuthenticatedUser(string $email, string $password): ?User
    {
        $user = $this->qb
            ->select(['id', 'nickname', 'email', 'password'])
            ->where('email', $email)
            ->first();

        if ($user === null || !password_verify($password, $user->password)) {
            return null;
        }

        unset($user->password);

        return $user;
    }

    public function registrationUser(User $user): bool
    {
        return $this->qb->insert($user);
    }
}

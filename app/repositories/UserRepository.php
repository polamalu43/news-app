<?php

namespace App\Repositories;

use Core\QueryBuilder;
use App\Models\User;
use App\Repositories\Interfaces\UserRepositoryInterface;

class UserRepository implements UserRepositoryInterface
{
    private QueryBuilder $qb;

    public function __construct()
    {
        $this->qb = new QueryBuilder('users', User::class);
    }

    /** 全件取得 */
    public function findAll(): array
    {
        return $this->qb->findAll();
    }

    /** メールアドレスで1件取得 */
    public function findById(int|string $id): ?User
    {
        return $this->qb->findById($id);
    }
}

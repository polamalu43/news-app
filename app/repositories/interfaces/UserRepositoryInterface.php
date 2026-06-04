<?php

namespace App\Repositories\Interfaces;

use App\Models\User;

interface UserRepositoryInterface
{
    public function findAll(): array;
    public function findById(int|string $id): ?User;
}

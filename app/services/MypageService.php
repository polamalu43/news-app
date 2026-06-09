<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\UserRepository;
use App\Repositories\FavoriteRepository;

class MypageService
{
    public function __construct(
        private UserRepository $userRepository,
        private FavoriteRepository $favoriteRepository
    ) {}

    public function findUserById(int $userId): ?User
    {
        return $this->userRepository->findById($userId, ['id', 'nickname', 'email']);
    }

    public function getFavoriteList(int $userId): ?array
    {
        return $this->favoriteRepository->getFavoriteList($userId);
    }
}

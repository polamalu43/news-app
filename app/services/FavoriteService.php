<?php

namespace App\Services;

use App\Repositories\FavoriteRepository;

class FavoriteService
{
    public function __construct(
        private FavoriteRepository $repository
    ) {}

    public function addFavorite(int $userId, int $newsId): void
    {
        $success = $this->repository->addFavorite($userId, $newsId);
        if (!$success) {
            throw new \Exception("お気に入り登録に失敗しました");
        }
    }

    public function removeFavorite(int $userId, int $newsId): void
    {
        $success = $this->repository->removeFavorite($userId, $newsId);
        if (!$success) {
            throw new \Exception("お気に入り削除に失敗しました");
        }
    }
}

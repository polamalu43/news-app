<?php

namespace App\Repositories;

use Core\Database;
use Core\QueryBuilder;
use App\Models\Favorite;

class FavoriteRepository
{
    private QueryBuilder $qb;

    public function __construct(
        private Database $db,
    ){
        $this->qb = new QueryBuilder(Favorite::$table, Favorite::class);
    }

    public function getFavoriteList(int $userId): ?array
    {
        $pdo = $this->db::connect();
        $sql = "SELECT
            favorites.user_id, favorites.news_id, news.title
            FROM favorites
            JOIN users ON users.id = favorites.user_id
            JOIN news ON news.id = favorites.news_id
            WHERE users.id = :user_id;";

        $stmt = $pdo->prepare($sql);
        $stmt->execute(['user_id' => $userId]);

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function addFavorite(int $userId, int $newsId): bool
    {
        $favorite = new Favorite();
        $favorite->user_id = $userId;
        $favorite->news_id = $newsId;
        return $this->qb->insert($favorite, true);
    }

    public function removeFavorite(int $userId, int $newsId): bool
    {
        return $this->qb->delete(Favorite::$table, [
            'user_id' => $userId,
            'news_id' => $newsId
        ]);
    }
}

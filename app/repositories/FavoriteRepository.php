<?php

namespace App\Repositories;

use Core\Database;
use App\Models\Favorite;

class FavoriteRepository
{
    public function __construct(private Database $db)
    {}

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
}
